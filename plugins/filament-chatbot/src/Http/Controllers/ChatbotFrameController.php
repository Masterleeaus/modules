<?php

namespace App\Extensions\Chatbot\System\Http\Controllers\Api;

use App\Extensions\Chatbot\System\Models\Chatbot;
use App\Extensions\Chatbot\System\Models\ChatbotConversation;
use App\Extensions\Chatbot\System\Models\ChatbotCustomer;
use App\Extensions\Chatbot\System\Models\ChatbotPageVisit;
use App\Helpers\Classes\Helper;
use App\Helpers\Classes\MarketplaceHelper;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class ChatbotFrameController extends Controller
{
    public function frame(Request $request, Chatbot $chatbot): View
    {
        $session = $this->getVisitor();

        $customer = $this->createCustomer($chatbot, $session);

        $customerId = $customer->getKey();

        $conversations = ChatbotConversation::query()
            ->where('chatbot_id', $chatbot->getAttribute('id'))
            ->where('session_id', $session)
            ->get();

        $chatbot->setAttribute('enabled_sound', $customer->getAttribute('enabled_sound'));

        $this->updateChatbotConversation($conversations, $customerId);

        $cart = [
            'products'       => [],
            'product_data'   => [],
            'product_source' => 'shopify',
        ];

        if (MarketplaceHelper::isRegistered('chatbot-ecommerce')) {
            $cartQuery = \App\Extensions\ChatbotEcommerce\System\Models\ChatbotCart::query()
                ->where('chatbot_id', $chatbot->getAttribute('id'))
                ->where('session_id', $session)
                ->first();

            $products = $cartQuery->products ?? [];
            if (! is_array($products)) {
                $products = json_decode($products, true) ?: [];
            }

            $productData = $cartQuery->product_data ?? [];
            if (! is_array($productData)) {
                $productData = json_decode($productData, true) ?: [];
            }

            $cart['products'] = $products;
            $cart['product_data'] = $productData;
            $cart['product_source'] = $cartQuery->product_source ?? 'shopify';

            if ($cart['product_source'] != $chatbot->shop_source) {
                $cart['products'] = [];
                $cart['product_data'] = [];
            }
        }

        return view('chatbot::frame', compact('chatbot', 'session', 'conversations', 'cart'));
    }

    public function updateChatbotConversation(Collection $conversations, $customerId): void
    {
        if ($conversations->whereNull('chatbot_customer_id')?->count()) {
            ChatbotConversation::query()
                ->whereIn(
                    'id',
                    $conversations->whereNull('chatbot_customer_id')->pluck('id')->toArray()
                )
                ->update([
                    'chatbot_customer_id' => $customerId,
                ]);
        }
    }

    public function createCustomer(Chatbot $chatbot, string $session)
    {
        $customer = ChatbotCustomer::query()->firstOrCreate([
            'user_id'         => $chatbot->getAttribute('user_id'),
            'chatbot_id'      => $chatbot->getAttribute('id'),
            'session_id'      => $session,
            'chatbot_channel' => 'frame',
        ], [
            'name'            => 'Anonymous User',
            'ip_address'      => Helper::getRequestIp(),
            'country_code'    => Helper::getRequestCountryCode(),
            'enabled_sound'   => true,
        ]);

        $customer->update([
            'ip_address'      => Helper::getRequestIp(),
            'country_code'    => Helper::getRequestCountryCode(),
        ]);

        return $customer;
    }

    public function recordPageVisit(Request $request, Chatbot $chatbot, string $sessionId): JsonResponse
    {
        $request->validate([
            'page_url'   => 'required|string|max:2048',
            'page_title' => 'nullable|string|max:255',
        ]);

        ChatbotPageVisit::query()
            ->where('chatbot_id', $chatbot->getKey())
            ->where('session_id', $sessionId)
            ->whereNull('left_at')
            ->update(['left_at' => now()]);

        $visit = ChatbotPageVisit::query()->create([
            'chatbot_id' => $chatbot->getKey(),
            'session_id' => $sessionId,
            'page_url'   => $request->input('page_url'),
            'page_title' => $request->input('page_title'),
            'entered_at' => now(),
        ]);

        return response()->json(['data' => ['id' => $visit->getKey()]], 201);
    }

    public function leavePageVisit(Request $request, Chatbot $chatbot, string $sessionId): JsonResponse
    {
        ChatbotPageVisit::query()
            ->where('chatbot_id', $chatbot->getKey())
            ->where('session_id', $sessionId)
            ->whereNull('left_at')
            ->update(['left_at' => now()]);

        return response()->json(['data' => ['success' => true]]);
    }

    protected function getVisitor(): string
    {
        $cookie = Cookie::has('CHATBOT_VISITOR');

        if ($cookie) {
            return Cookie::get('CHATBOT_VISITOR');
        }

        $sessionId = md5(uniqid(mt_rand(), true));

        Cookie::queue('CHATBOT_VISITOR', $sessionId, 60 * 24 * 365);

        return $sessionId;
    }
}
