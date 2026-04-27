<?php

namespace Modules\InstantAds\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Modules\InstantAds\Models\AiImagePro;
use Modules\InstantAds\Services\AIImageProService;
use Modules\InstantAds\Services\RealtimeGenerationService;

class InstantAdsController extends Controller
{
    public function __construct(
        protected AIImageProService $service,
        protected RealtimeGenerationService $realtimeService
    ) {}

    public function index(Request $request)
    {
        $activeImageModels = AIImageProService::getActiveImageModels();
        $imageStats        = $this->getUserImageStats($request);

        return view('instantads::index', compact('activeImageModels', 'imageStats'));
    }

    public function generate(Request $request): JsonResponse
    {
        $request->validate([
            'prompt'       => ['required', 'string', 'max:2000'],
            'model'        => ['nullable', 'string'],
            'image_count'  => ['nullable', 'integer', 'min:1', 'max:4'],
            'style'        => ['nullable', 'string'],
            'aspect_ratio' => ['nullable', 'string'],
        ]);

        $userId  = auth()->id();
        $guestIp = $request->ip();

        // Check guest daily limit
        if (! $userId) {
            $limitCheck = $this->checkGuestDailyLimit($request, 1);
            if (! $limitCheck['allowed']) {
                return response()->json([
                    'success' => false,
                    'message' => __('Daily generation limit reached. Please sign in to continue.'),
                ], 429);
            }
        }

        // Check entity credits when module is available
        $driver = null;
        if ($userId && class_exists(\App\Domains\Entity\Facades\Entity::class)) {
            try {
                $driver = \App\Domains\Entity\Facades\Entity::driver($request->input('model', 'dall-e-3'));

                if (method_exists($driver, 'calculate') && method_exists($driver, 'hasCredits')) {
                    $cost = $driver->calculate();
                    if (! $driver->hasCredits($cost)) {
                        return response()->json([
                            'success' => false,
                            'message' => __('Insufficient credits. Please upgrade your plan.'),
                        ], 402);
                    }
                    $driver->decreaseCredit($cost);
                }
            } catch (\Throwable) {
                $driver = null;
            }
        }

        $params = [
            'prompt'       => $request->input('prompt'),
            'model'        => $request->input('model', 'dall-e-3'),
            'image_count'  => (int) $request->input('image_count', 4),
            'style'        => $request->input('style'),
            'aspect_ratio' => $request->input('aspect_ratio', '1:1'),
        ];

        try {
            $recordId = $this->service->dispatchImageGenerationJob($params, $userId, $driver);

            return response()->json([
                'success'   => true,
                'message'   => __('Your ad creative is being generated!'),
                'record_id' => $recordId,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => __('Generation could not be started. Please try again.'),
            ], 500);
        }
    }

    public function getImages(Request $request): JsonResponse
    {
        return $this->getCompletedImages($request);
    }

    public function getCompletedImages(Request $request): JsonResponse
    {
        $userId  = auth()->id();
        $guestIp = $request->ip();

        $query = AiImagePro::where('status', 'completed')
            ->orderByDesc('created_at');

        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->whereNull('user_id')->where('guest_ip', $guestIp);
        }

        $images = $query->paginate(20);

        return response()->json([
            'success' => true,
            'images'  => $images->map(fn ($img) => $this->formatImageData($img, $userId, $guestIp)),
            'hasMore' => $images->hasMorePages(),
            'total'   => $images->total(),
        ]);
    }

    public function getCommunityImages(Request $request): JsonResponse
    {
        $userId  = auth()->id();
        $guestIp = $request->ip();

        $images = AiImagePro::where('published', true)
            ->whereNotNull('generated_images')
            ->orderByDesc('likes_count')
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'images'  => $images->map(fn ($img) => $this->formatImageData($img, $userId, $guestIp)),
            'hasMore' => $images->hasMorePages(),
        ]);
    }

    public function toggleLike(Request $request): JsonResponse
    {
        $imageId = $request->input('image_id');
        $record  = AiImagePro::find($imageId);

        if (! $record) {
            return response()->json(['success' => false, 'message' => __('Image not found.')], 404);
        }

        $userId  = auth()->id();
        $guestIp = $request->ip();

        $liked = $record->toggleLike($userId, $guestIp);

        return response()->json([
            'success'     => true,
            'liked'       => $liked,
            'likes_count' => $record->fresh()->likes_count,
        ]);
    }

    public function togglePublish(Request $request): JsonResponse
    {
        $imageId = $request->input('image_id');
        $record  = AiImagePro::find($imageId);

        if (! $record) {
            return response()->json(['success' => false, 'message' => __('Image not found.')], 404);
        }

        $userId = auth()->id();

        if ($userId && $record->user_id !== $userId) {
            return response()->json(['success' => false, 'message' => __('Unauthorized.')], 403);
        }

        $published = ! $record->published;
        $record->update([
            'published'            => $published,
            'publish_requested_at' => $published ? now() : null,
        ]);

        return response()->json([
            'success'   => true,
            'published' => $published,
        ]);
    }

    public function generateShareLink(Request $request): JsonResponse
    {
        $imageId = $request->input('image_id');
        $record  = AiImagePro::find($imageId);

        if (! $record) {
            return response()->json(['success' => false, 'message' => __('Image not found.')], 404);
        }

        if (! $record->share_token) {
            $record->update(['share_token' => Str::random(32)]);
        }

        $shareUrl = route('instant-ads.share.view', ['token' => $record->share_token]);

        return response()->json([
            'success'   => true,
            'share_url' => $shareUrl,
        ]);
    }

    public function viewSharedImage(Request $request, string $token)
    {
        $record = AiImagePro::where('share_token', $token)->firstOrFail();
        $record->incrementViews();

        return view('instantads::shared-image', compact('record'));
    }

    public function realtimeIndex(Request $request)
    {
        $activeImageModels = AIImageProService::getActiveImageModels();

        return view('instantads::realtime', compact('activeImageModels'));
    }

    public function generateRealtimeImage(Request $request): JsonResponse
    {
        $request->validate([
            'prompt' => ['required', 'string', 'max:2000'],
            'style'  => ['nullable', 'string'],
        ]);

        $userId = auth()->id();

        if (! $userId) {
            $limitCheck = $this->checkGuestDailyLimit($request, 1);
            if (! $limitCheck['allowed']) {
                return response()->json([
                    'success' => false,
                    'message' => __('Daily generation limit reached.'),
                ], 429);
            }
        }

        $record = AiImagePro::create([
            'user_id'  => $userId,
            'guest_ip' => $userId ? null : $request->ip(),
            'model'    => 'flux-1-schnell',
            'engine'   => 'fal_ai',
            'prompt'   => $request->input('prompt'),
            'params'   => [
                'style'      => $request->input('style'),
                'is_realtime' => true,
            ],
            'status' => 'pending',
        ]);

        try {
            $result = $this->realtimeService->generate($record);

            if ($result->isCompleted()) {
                return response()->json([
                    'success' => true,
                    'images'  => $result->generated_images ?? [],
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => __('Generation did not complete. Please try again.'),
            ], 500);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => __('Realtime generation failed.'),
            ], 500);
        }
    }

    public function getRealtimeImages(Request $request): JsonResponse
    {
        $userId  = auth()->id();
        $guestIp = $request->ip();

        $query = AiImagePro::where('status', 'completed')
            ->whereNotNull('generated_images')
            ->where(function ($q) use ($request) {
                $q->whereJsonContains('params->is_realtime', true);
            })
            ->orderByDesc('created_at')
            ->limit(20);

        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->whereNull('user_id')->where('guest_ip', $guestIp);
        }

        $images = $query->get();

        return response()->json([
            'success' => true,
            'images'  => $images->map(fn ($img) => $this->formatImageData($img, $userId, $guestIp)),
        ]);
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function checkGuestDailyLimit(Request $request, int $count): array
    {
        $ip    = $request->ip();
        $date  = now()->toDateString();
        $key   = "instant_ads_guest_daily:{$ip}:{$date}";
        $limit = config('instantads.guest_daily_limit', 3);

        $current = (int) Cache::get($key, 0);

        if ($current + $count > $limit) {
            return ['allowed' => false, 'current' => $current, 'limit' => $limit];
        }

        Cache::put($key, $current + $count, now()->endOfDay());

        return ['allowed' => true, 'current' => $current + $count, 'limit' => $limit];
    }

    private function getUserImageStats(Request $request): array
    {
        $userId  = auth()->id();
        $guestIp = $request->ip();

        $baseQuery = AiImagePro::when($userId, fn ($q) => $q->where('user_id', $userId))
            ->when(! $userId, fn ($q) => $q->whereNull('user_id')->where('guest_ip', $guestIp));

        $completedCount = (clone $baseQuery)->where('status', 'completed')->count();

        $completedImages = (clone $baseQuery)
            ->where('status', 'completed')
            ->orderByDesc('created_at')
            ->limit(20)
            ->get()
            ->map(fn ($img) => $this->formatImageData($img, $userId, $guestIp))
            ->toArray();

        $inProgressImages = (clone $baseQuery)
            ->whereIn('status', ['pending', 'processing'])
            ->orderByDesc('created_at')
            ->limit(8)
            ->get()
            ->map(fn ($img) => [
                'id'     => $img->id,
                'prompt' => $img->prompt,
                'status' => $img->status,
            ])
            ->toArray();

        return [
            'completed_count'   => $completedCount,
            'completed_images'  => $completedImages,
            'in_progress_images' => $inProgressImages,
        ];
    }

    private function formatImageData(AiImagePro $image, ?int $userId, ?string $guestIp): array
    {
        $images = $image->generated_images ?? [];

        return [
            'id'               => $image->id,
            'prompt'           => $image->prompt,
            'model'            => $image->model,
            'status'           => $image->status,
            'generated_images' => $images,
            'url'              => $images[0] ?? null,
            'likes_count'      => $image->likes_count,
            'views_count'      => $image->views_count,
            'is_liked'         => $image->isLikedBy($userId ?? $guestIp),
            'published'        => $image->published,
            'share_token'      => $image->share_token,
            'created_at'       => $image->created_at?->toIso8601String(),
        ];
    }
}
