<?php

namespace Modules\InstantAds\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\InstantAds\Models\AiImagePro;

class AdminInstantAdsController extends Controller
{
    public function settings(Request $request)
    {
        $config = config('instantads');

        return view('instantads::admin.settings', compact('config'));
    }

    public function updateSettings(Request $request): JsonResponse
    {
        $request->validate([
            'guest_daily_limit' => ['nullable', 'integer', 'min:0'],
            'max_batch_size'    => ['nullable', 'integer', 'min:1', 'max:8'],
        ]);

        // Settings are stored in the app settings table when available
        if (function_exists('setting')) {
            setting([
                'instant_ads_guest_daily_limit' => $request->input('guest_daily_limit', 3),
                'instant_ads_max_batch_size'    => $request->input('max_batch_size', 4),
            ]);
        }

        return response()->json(['success' => true, 'message' => __('Settings updated.')]);
    }

    public function communityImages(Request $request)
    {
        $images = AiImagePro::where('published', true)
            ->orderByDesc('created_at')
            ->paginate(30);

        return view('instantads::admin.community', compact('images'));
    }

    public function publishRequests(Request $request)
    {
        $requests = AiImagePro::whereNotNull('publish_requested_at')
            ->whereNull('publish_reviewed_at')
            ->orderBy('publish_requested_at')
            ->paginate(30);

        return view('instantads::admin.publish-requests', compact('requests'));
    }

    public function approveRequest(int $id): JsonResponse
    {
        $record = AiImagePro::findOrFail($id);

        $record->update([
            'published'            => true,
            'publish_reviewed_at'  => now(),
            'publish_reviewed_by'  => auth()->id(),
        ]);

        return response()->json(['success' => true, 'message' => __('Publish request approved.')]);
    }

    public function rejectRequest(int $id): JsonResponse
    {
        $record = AiImagePro::findOrFail($id);

        $record->update([
            'published'            => false,
            'publish_reviewed_at'  => now(),
            'publish_reviewed_by'  => auth()->id(),
        ]);

        return response()->json(['success' => true, 'message' => __('Publish request rejected.')]);
    }
}
