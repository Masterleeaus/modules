<?php

namespace App\Extensions\Chatbot\System\Http\Controllers\Api;

use App\Extensions\Chatbot\System\Enums\InteractionType;
use App\Extensions\Chatbot\System\Http\Requests\ChatbotHistoryStoreRequest;
use App\Extensions\Chatbot\System\Http\Resources\Api\ChatbotConversationResource;
use App\Extensions\Chatbot\System\Http\Resources\Api\ChatbotHistoryResource;
use App\Extensions\Chatbot\System\Http\Resources\Api\ChatbotResource;
use App\Extensions\Chatbot\System\Models\Chatbot;
use App\Extensions\Chatbot\System\Models\ChatbotConversation;
use App\Extensions\Chatbot\System\Models\ChatbotCustomer;
use App\Extensions\Chatbot\System\Models\ChatbotHistory;
use App\Extensions\Chatbot\System\Models\ChatbotKnowledgeBaseArticle;
use App\Extensions\Chatbot\System\Services\ConversationExportService;
use App\Extensions\Chatbot\System\Services\GeneratorService;
use App\Extensions\ChatbotAgent\System\Services\ChatbotForPanelEventAbly;
use App\Helpers\Classes\Helper;
use App\Helpers\Classes\MarketplaceHelper;
use App\Helpers\Classes\RateLimiter\RateLimiter;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class ChatbotApplicationController extends Controller
{
    public Setting $setting;

    public function __construct(
        public GeneratorService $service,
    ) {
        $this->setting = Setting::getCache();
    }

    public function index(Chatbot $chatbot): ChatbotResource
    {
        return ChatbotResource::make($chatbot);
    }

    public function enableSound(Chatbot $chatbot, string $sessionId): JsonResponse
    {
        $customer = ChatbotCustomer::query()
            ->where('session_id', $sessionId)
            ->where('chatbot_id', $chatbot->getAttribute('id'))
            ->firstOrFail();

        $customer->update([
            'enabled_sound' => ! $customer->getAttribute('enabled_sound'),
        ]);

        return response()->json([
            'enabled_sound' => $customer->getAttribute('enabled_sound'),
        ]);
    }

    public function articles(Request $request, Chatbot $chatbot)
    {
        return ChatbotKnowledgeBaseArticle::query()
            ->whereRaw('JSON_CONTAINS(chatbots, ?)', ['"' . $chatbot->getKey() . '"'])
            ->select(columns: [
                'id',
                'title',
                'description as excerpt',
                'is_featured',
                DB::raw('"#" as link'),
            ])
            ->when($request->get('search'), function ($query, $search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            })->get();
    }

    public function showArticles(Request $request, Chatbot $chatbot, $id)
    {
        return ChatbotKnowledgeBaseArticle::query()
            ->whereRaw('JSON_CONTAINS(chatbots, ?)', ['"' . $chatbot->getKey() . '"'])
            ->select(columns: [
                'id',
                'title',
                'description as excerpt',
                'content',
                'is_featured',
                DB::raw('"#" as link'),
            ])
            ->where('id', $id)
            ->get();
    }

    public function storeFile(
        Request $request,
        Chatbot $chatbot,
        string $sessionId,
        $conversationId = null
    ): ChatbotHistoryResource {
        $request->validate([
            'message'         => 'sometimes|nullable|string',
            'media'           => 'required|mimes:' . setting('media_allowed_types', 'jpg,png,gif,webp,svg,mp4,avi,mov,wmv,flv,webm,mp3,wav,m4a,pdf,doc,docx,xls,xlsx') . '|max:20480',
        ]);

        $chatbotConversation = ChatbotConversation::query()
            ->findOrFail($conversationId);

        $mediaUrl = null;
        $mediaName = null;

        if ($request->hasFile('media')) {
            $mediaName = $request->file('media')->getClientOriginalName();
            $mediaUrl = '/uploads/' . $request->file('media')->store('chatbot-media', 'public');
        }

        $message = $this->insertMessage(
            conversation: $chatbotConversation,
            message: $request['message'] ?: '',
            role: 'user',
            model: $chatbot->getAttribute('ai_model'),
            forcePanelEvent: (bool) $chatbotConversation->getAttribute('connect_agent_at'),
            mediaUrl: $mediaUrl,
            mediaName: $mediaName,
        );

        return ChatbotHistoryResource::make($message)->additional([
            'collect_email' => false,
        ]);
    }

    public function sendEmail(Chatbot $chatbot, string $sessionId, Request $request): ChatbotConversationResource
    {
        $request->validate([
            'email'   => 'required|email',
            'message' => 'required|string',
        ]);

        $customer = ChatbotCustomer::query()->where('session_id', $sessionId)
            ->where('chatbot_id', $chatbot->getAttribute('id'))
            ->firstOrFail();

        $customer->update([
            'email' => $request->get('email'),
        ]);

        $chatbotConversation = ChatbotConversation::query()
            ->create([
                'chatbot_channel' 			      => 'frame',
                'is_showed_on_history'     => false,
                'country_code'             => Helper::getRequestCountryCode(),
                'ip_address'               => Helper::getRequestIp(),
                'conversation_name'        => 'Anonymous User',
                'chatbot_id'               => $chatbot->getAttribute('id'),
                'session_id'               => $sessionId,
                'chatbot_customer_id'      => $customer?->getKey(),
                'connect_agent_at'         => now(),
                'last_activity_at'         => now(),
                'send_email_at'            => now(),
            ]);

        $history = $this->insertMessage(
            conversation: $chatbotConversation,
            message: 'Customer email: ' . $request->get('email') . "\n\n" . $request->get('message'),
            role: 'user',
            model: $chatbot->getAttribute('ai_model'),
            forcePanelEvent: (bool) $chatbotConversation->getAttribute('connect_agent_at')
        );

        $this->insertMessage(
            conversation: $chatbotConversation,
            message: trans('Your message has been received, and you will get a response shortly.'),
            role: 'assistant',
            model: $chatbot->getAttribute('ai_model'),
            forcePanelEvent: (bool) $chatbotConversation->getAttribute('connect_agent_at')
        );

        try {
            ChatbotForPanelEventAbly::dispatch($chatbot, $chatbotConversation, $history);
        } catch (Exception $e) {
        }

        return ChatbotConversationResource::make($chatbotConversation);

    }

    public function collectEmail(Chatbot $chatbot, string $sessionId, Request $request): JsonResponse
    {
        $request->validate([
            'email'   => 'required|email',
        ]);

        $customer = ChatbotCustomer::query()->where('session_id', $sessionId)
            ->where('chatbot_id', $chatbot->getAttribute('id'))
            ->firstOrFail();

        $customer->update([
            'email' => $request->get('email'),
        ]);

        return response()->json([
            'message' => 'Email collected successfully.',
            'email'   => $customer->email,
        ]);

    }

    public function indexSession(Chatbot $chatbot, string $sessionId): ChatbotResource
    {
        $conversations = ChatbotConversation::query()
            ->where('chatbot_id', $chatbot->getAttribute('id'))
            ->where('session_id', $sessionId)
            ->with('lastMessage')
            ->orderByDesc('updated_at')
            ->get();

        $cart = [];

        if (MarketplaceHelper::isRegistered('chatbot-ecommerce')) {
            $cart = \App\Extensions\ChatbotEcommerce\System\Models\ChatbotCart::query()
                ->where('chatbot_id', $chatbot->getAttribute('id'))
                ->where('session_id', $sessionId)
                ->get();
        }

        return ChatbotResource::make($chatbot)->additional([
            'conversations' => ChatbotConversationResource::collection($conversations),
            'cart'          => MarketplaceHelper::isRegistered('chatbot-ecommerce')
                ? \App\Extensions\ChatbotEcommerce\System\Http\Resources\Api\ChatbotCartResource::collection($cart)
                : [],
        ]);
    }

    public function connectSupport(Request $request, Chatbot $chatbot, string $sessionId)
    {
        if (MarketplaceHelper::isRegistered('chatbot-agent')) {
            $request->validate(['conversation_id' => 'required|integer|exists:ext_chatbot_conversations,id']);

            /** @var ChatbotConversation $conversation */
            $conversation = ChatbotConversation::find($request->get('conversation_id'));

            if ($chatbot->getAttribute('interaction_type') === InteractionType::SMART_SWITCH) {
                $conversation->update(['connect_agent_at' => now()]);

                $chatbotHistory = null;

                if ($chatbot->getAttribute('connect_message')) {
                    $chatbotHistory = $this->insertMessage(
                        conversation: $conversation,
                        message: trans($chatbot->getAttribute('connect_message')),
                        role: 'assistant',
                        model: $chatbot->getAttribute('ai_model'),
                        forcePanelEvent: true
                    );
                }

                try {
                    ChatbotForPanelEventAbly::dispatch($chatbot, $conversation, $chatbotHistory);
                } catch (Exception $e) {
                    Log::error($e->getMessage());
                }

                return ChatbotConversationResource::make($conversation)->additional([
                    'history' => $chatbotHistory ? ChatbotHistoryResource::make($chatbotHistory) : null,
                ]);
            }

            abort(404);
        }
    }

    public function conversionStore(Chatbot $chatbot, string $sessionId): ChatbotConversationResource
    {
        $customer = ChatbotCustomer::query()->where('session_id', $sessionId)
            ->where('chatbot_id', $chatbot->getAttribute('id'))
            ->first();

        $chatbotConversation = ChatbotConversation::query()
            ->create([
                'conversation_name'    => $customer?->name ?: 'Anonymous User',
                'chatbot_channel'      => 'frame',
                'is_showed_on_history' => false,
                'ip_address'           => Helper::getRequestIp(),
                'country_code'         => Helper::getRequestCountryCode(),
                'chatbot_id'           => $chatbot->getAttribute('id'),
                'session_id'           => $sessionId,
                'chatbot_customer_id'  => $customer?->getKey(),
                'connect_agent_at'     => $chatbot->getAttribute('interaction_type') === InteractionType::HUMAN_SUPPORT ? now() : null,
                'last_activity_at'     => now(),
            ]);

        $this->insertMessage(
            conversation: $chatbotConversation,
            message: $chatbot->getAttribute('welcome_message'),
            role: 'assistant',
            model: $chatbot->getAttribute('ai_model'),
            forcePanelEvent: (bool) $chatbotConversation->getAttribute('connect_agent_at')
        );

        return ChatbotConversationResource::make($chatbotConversation);
    }

    public function conversion(Chatbot $chatbot, string $sessionId, ChatbotConversation $chatbotConversation): ChatbotConversationResource
    {
        if ($chatbotConversation->getAttribute('chatbot_id') !== $chatbot->getAttribute('id')) {
            abort(404);
        }

        if ($chatbotConversation->getAttribute('session_id') !== $sessionId) {
            abort(404);
        }

        return ChatbotConversationResource::make($chatbotConversation);
    }

    public function review(Request $request, Chatbot $chatbot, string $sessionId, ChatbotConversation $chatbotConversation): ChatbotConversationResource
    {
        if (! MarketplaceHelper::isRegistered('chatbot-review')) {
            abort(404);
        }

        if ($chatbotConversation->getAttribute('chatbot_id') !== $chatbot->getAttribute('id')) {
            abort(404);
        }

        if ($chatbotConversation->getAttribute('session_id') !== $sessionId) {
            abort(404);
        }

        if (! $chatbot->getAttribute('is_review_enabled')) {
            abort(404);
        }

        $request->validate([
            'action'            => ['required', 'string', Rule::in(['request', 'submit'])],
            'reason'            => ['required_if:action,request', 'string', 'max:160'],
            'message'           => ['required_if:action,submit', 'string'],
            'selected_response' => ['sometimes', 'nullable', 'string'],
        ]);

        $reviewService = app(\App\Extensions\ChatbotReview\System\Services\ChatbotReviewService::class);

        $action = $request->string('action')->toString();

        if ($action === 'request') {
            $reviewService->requestReview(
                $chatbotConversation,
                $request->string('reason')->toString()
            );
        } else {
            $message = trim((string) $request->input('message', ''));

            if ($message === '') {
                throw ValidationException::withMessages([
                    'message' => [trans('Please enter your review before submitting.')],
                ]);
            }

            $reviewService->submitReview(
                $chatbotConversation,
                $message,
                $request->input('selected_response')
            );
        }

        return ChatbotConversationResource::make($chatbotConversation->fresh());
    }

    public function export(Request $request, Chatbot $chatbot, string $sessionId, ChatbotConversation $chatbotConversation): SymfonyResponse
    {
        $request->validate([
            'format' => ['sometimes', 'string', Rule::in(['txt', 'csv', 'pdf', 'json'])],
        ]);

        $format = $request->query('format', 'txt');

        return app(ConversationExportService::class)
            ->export($chatbotConversation, $format);
    }

    public function messages(Chatbot $chatbot, string $sessionId, ChatbotConversation $chatbotConversation): AnonymousResourceCollection
    {
        if ($chatbotConversation->getAttribute('chatbot_id') !== $chatbot->getAttribute('id')) {
            abort(404);
        }

        if ($chatbotConversation->getAttribute('session_id') !== $sessionId) {
            abort(404);
        }

        // Mark assistant messages as read when visitor views the conversation
        ChatbotHistory::query()
            ->where('conversation_id', $chatbotConversation->getAttribute('id'))
            ->where('role', 'assistant')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = ChatbotHistory::query()
            ->where('conversation_id', $chatbotConversation->getAttribute('id'))
            ->where('is_internal_note', false)
            ->orderByDesc('id')
            ->paginate(perPage: request('per_page', 10));

        return ChatbotHistoryResource::collection($messages);
    }

    public function storeMessage(ChatbotHistoryStoreRequest $request, Chatbot $chatbot, string $sessionId, ChatbotConversation $chatbotConversation): ChatbotHistoryResource
    {
        if ($chatbotConversation->getAttribute('chatbot_id') !== $chatbot->getAttribute('id')) {
            abort(404);
        }

        if ($chatbotConversation->getAttribute('session_id') !== $sessionId) {
            abort(404);
        }

        $mediaUrl = null;
        $mediaName = null;

        if ($request->hasFile('media')) {
            $mediaName = $request->file('media')->getClientOriginalName();
            $mediaUrl = '/uploads/' . $request->file('media')->store('chatbot-media', 'public');
        }

        $prompt = $request->validated('prompt') ?? '';

        $userMessage = $this->insertMessage(
            conversation: $chatbotConversation,
            message: $prompt,
            role: 'user',
            model: $chatbot->getAttribute('ai_model'),
            forcePanelEvent: false,
            mediaUrl: $mediaUrl,
            mediaName: $mediaName,
        );

        if (! $chatbotConversation->getAttribute('is_showed_on_history')) {
            $chatbotConversation->update(['is_showed_on_history' => true]);
        }

        if ($chatbotConversation->getAttribute('connect_agent_at')) {
            return ChatbotHistoryResource::make($userMessage)->additional([
                'connection'    => 'panel',
                'collect_email' => false,
                'needs_human'   => false,
                'needs_booking' => false,
            ]);
        }

        $clientIp = Helper::getRequestIp();
        $rateLimiter = new RateLimiter('chatbot-extension', 100);

        if (Helper::appIsDemo() && $mediaUrl) {
            $imageRateLimiter = new RateLimiter('chatbot-image-upload', 2);

            if (! $imageRateLimiter->attempt($clientIp)) {
                $response = trans('You have reached the image upload limit for today in demo mode.');
            }
        }

        if (! isset($response) && Helper::appIsDemo() && ! $rateLimiter->attempt($clientIp)) {
            $response = 'This feature is disabled in the demo version. You have reached the maximum request limit for today.';
        }

        if (! isset($response)) {
            try {
                $response = $this->service
                    ->setChatbot($chatbot)
                    ->setConversation($chatbotConversation)
                    ->setPrompt($prompt)
                    ->generate();

                if (empty($response)) {
                    $response = trans('Sorry, I can\'t answer right now.');
                }
            } catch (Exception $e) {
                Log::error('Chatbot vision error: ' . $e->getMessage());

                $response = $mediaUrl
                    ? trans('Sorry, I couldn\'t analyze the image. Please try again.')
                    : trans('Sorry, I can\'t answer right now.');
            }
        }

        $needsHuman = false;
        $needsHumanDirect = false;

        $needsBooking = false;

        $originalResponse = $response;

        $messageToUser = $response;

        if ($chatbot->getAttribute('interaction_type') === InteractionType::SMART_SWITCH) {
            $needsHumanDirect = (bool) preg_match('/\s*\[human-agent-direct\]\s*$/u', $response);

            $response = $needsHumanDirect
                ? preg_replace('/\s*\[human-agent\]\s*$/u', '', $response)
                : $response;

            $response = rtrim($response);

            $needsHuman = (bool) preg_match('/\s*\[human-agent\]\s*$/u', $response);
            $messageToUser = $needsHuman
                ? preg_replace('/\s*\[human-agent\]\s*$/u', '', $response)
                : $response;
            $messageToUser = rtrim($messageToUser);

            if ($needsHumanDirect) {
                $messageToUser = trans('Connecting you to a human agent…');
            }

            if ($needsHuman) {
                $needsHumanDirect = false;
                $messageToUser = trans('Sorry, I’m not able to help with this. Let me connect you to a human agent.');
            }
        }

        if ($chatbot->getAttribute('is_booking_assistant')) {
            $needsBooking = (bool) preg_match('/\s*\[booking-assistant\]\s*$/u', $response);
            $messageToUser = $needsBooking
                ? preg_replace('/\s*\[booking-assistant\]\s*$/u', '', $response)
                : $response;
        }

        $message = $this->insertMessage(
            conversation: $chatbotConversation,
            message: $messageToUser,
            role: 'assistant',
            model: $chatbot->getAttribute('ai_model'),
            forcePanelEvent: false
        );

        $customer = ! $chatbotConversation?->getAttribute('customer')?->getAttribute('email');

        $collectEmail = ChatbotHistory::query()
            ->where('conversation_id', $chatbotConversation->getAttribute('id'))
            ->where('role', '!=', 'user')
            ->count() === 2 && $customer;

        $isHTML = str_contains($response, 'lqd-ext-chatbot-html-response');

        return ChatbotHistoryResource::make($message)->additional([
            'connection'                          => 'ai',
            'collect_email'                       => $collectEmail && $chatbot->getAttribute('is_email_collect'),
            'needs_human'                         => $needsHuman,
            'needs_human_direct'                  => $needsHumanDirect,
            'original_response'                   => $originalResponse,
            'needs_booking'                       => $needsBooking,
            'isHTML'                              => $isHTML,
        ]);
    }

    public function appendMessage(ChatbotHistoryStoreRequest $request, Chatbot $chatbot, string $sessionId, ChatbotConversation $chatbotConversation)
    {
        if ($chatbotConversation->getAttribute('chatbot_id') !== $chatbot->getAttribute('id')) {
            abort(404);
        }

        if ($chatbotConversation->getAttribute('session_id') !== $sessionId) {
            abort(404);
        }

        ChatbotHistory::query()->create([
            'chatbot_id'      => $chatbotConversation->getAttribute('chatbot_id'),
            'conversation_id' => $chatbotConversation->getAttribute('id'),
            'role'            => $request->validated('role'),
            'model'           => $this->setting->openai_default_model ?: $model,
            'message'         => $request->validated('prompt'),
            'created_at'      => now(),
            'read_at'         => $chatbotConversation->getAttribute('connect_agent_at') ? null : now(),
        ]);
    }

    protected function insertMessage(
        ChatbotConversation|Model $conversation,
        ?string $message,
        string $role,
        string $model,
        bool $forcePanelEvent = false,
        ?string $mediaUrl = null,
        ?string $mediaName = null
    ) {
        $chatbot = $conversation->getAttribute('chatbot');

        $defaultEngine = \App\Domains\Engine\Enums\EngineEnum::fromSlug(
            setting('default_external_chatbot_engine', \App\Domains\Engine\Enums\EngineEnum::OPEN_AI->slug())
        );

        $chatModel = match ($defaultEngine) {
            \App\Domains\Engine\Enums\EngineEnum::OPEN_AI   => $this->setting->openai_default_model ?: \App\Domains\Entity\Enums\EntityEnum::GPT_4_O->slug(),
            \App\Domains\Engine\Enums\EngineEnum::ANTHROPIC => setting('anthropic_default_model', \App\Domains\Entity\Enums\EntityEnum::CLAUDE_3_OPUS->slug()),
            \App\Domains\Engine\Enums\EngineEnum::GEMINI    => setting('gemini_default_model', \App\Domains\Entity\Enums\EntityEnum::GEMINI_3_FLASH->slug()),
            \App\Domains\Engine\Enums\EngineEnum::DEEP_SEEK => setting('deepseek_default_model', \App\Domains\Entity\Enums\EntityEnum::DEEPSEEK_CHAT->slug()),
            \App\Domains\Engine\Enums\EngineEnum::X_AI      => setting('xai_default_model', \App\Domains\Entity\Enums\EntityEnum::GROK_2_1212->slug()),
            default                                         => $this->setting->openai_default_model ?: \App\Domains\Entity\Enums\EntityEnum::GPT_4_O->slug(),
        };

        $chatbotHistory = ChatbotHistory::query()->create([
            'chatbot_id'      => $conversation->getAttribute('chatbot_id'),
            'conversation_id' => $conversation->getAttribute('id'),
            'role'            => $role,
            'model'           => $chatModel ?: $model,
            'message'         => $message,
            'created_at'      => now(),
            'read_at'         => $conversation->getAttribute('connect_agent_at') ? null : now(),
            'media_url'       => $mediaUrl,
            'media_name'      => $mediaName,
        ]);

        $sendEvent = $conversation->getAttribute('connect_agent_at') && $chatbot->getAttribute('interaction_type') !== InteractionType::AUTOMATIC_RESPONSE && $role === 'user';

        if ($sendEvent || $forcePanelEvent) {
            $conversation->touch();
            if (MarketplaceHelper::isRegistered('chatbot-agent')) {
                ChatbotForPanelEventAbly::dispatch(
                    $chatbot,
                    $conversation->load('lastMessage'),
                    $chatbotHistory
                );
            }
        }

        $conversation->forceFill([
            'last_activity_at' => now(),
        ])->save();

        return $chatbotHistory;
    }
}
