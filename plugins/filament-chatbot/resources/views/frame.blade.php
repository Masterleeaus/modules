@if (!$chatbot['active'])
    <p>
        @lang('This chatbot is not active.')
    </p>
@else
    @include('chatbot::frontend-ui.frontend-ui', [
        'is_editor' => false,
        'is_iframe' => true,
        'session' => $session,
        'chatbot' => $chatbot,
        'conversations' => $conversations,
		'cart' => $cart ?? null,
        'routes' => [
            'index' => route('api.v2.chatbot.index', [$chatbot->getAttribute('uuid'), $session]),
            'getSession' => route('api.v2.chatbot.index.session', [$chatbot->getAttribute('uuid'), $session]),
            'conversations' => route('api.v2.chatbot.conversion.store', [$chatbot->getAttribute('uuid'), $session]),
            'conversation' => route('api.v2.chatbot.conversion.show', [$chatbot->getAttribute('uuid'), $session, '__conversation__']),
            'send-email' => route('api.v2.chatbot.send-email.store', [$chatbot->getAttribute('uuid'), $session]),
            'collect-email' => route('api.v2.chatbot.collect.email', [$chatbot->getAttribute('uuid'), $session]),
            'articles' => route('api.v2.chatbot.articles', [$chatbot->getAttribute('uuid')]),
            'enable-sound' => route('api.v2.chatbot.enable-sound', [$chatbot->getAttribute('uuid'), $session]), // Enabled and disabled route
            'review' => \App\Helpers\Classes\MarketplaceHelper::isRegistered('chatbot-review') ? route('api.v2.chatbot.conversion.review', [$chatbot->getAttribute('uuid'), $session, '__conversation__']) : '',
			'product-addToCart' => \App\Helpers\Classes\MarketplaceHelper::isRegistered('chatbot-ecommerce') ? route('api.v2.chatbot.product.addToCart', [$chatbot->getAttribute('uuid'), $session]) : '',
            'product-updateQuantity' => \App\Helpers\Classes\MarketplaceHelper::isRegistered('chatbot-ecommerce') ? route('api.v2.chatbot.product.UpdateQuantity', [$chatbot->getAttribute('uuid'), $session]) : '',
            'product-cartCheckout' => \App\Helpers\Classes\MarketplaceHelper::isRegistered('chatbot-ecommerce') ? route('api.v2.chatbot.product.cartCheckout', [$chatbot->getAttribute('uuid'), $session]) : '',
            'product-getCart' => \App\Helpers\Classes\MarketplaceHelper::isRegistered('chatbot-ecommerce') ? route('api.v2.chatbot.product.getCart', [$chatbot->getAttribute('uuid'), $session]) : '',
        ],
    ])
@endif
