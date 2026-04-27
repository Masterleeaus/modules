<?php

namespace App\Extensions\Chatbot\System\Models;

use App\Extensions\Chatbot\System\Enums\BubbleDesign;
use App\Extensions\Chatbot\System\Enums\ColorModeEnum;
use App\Extensions\Chatbot\System\Enums\InteractionType;
use App\Extensions\Chatbot\System\Enums\PositionEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Chatbot extends Model
{
    protected $table = 'ext_chatbots';

    protected $fillable = [
        'uuid',
        'user_id',
        'interaction_type',
        'title',
        'bubble_message',
        'welcome_message',
        'connect_message',
        'instructions',
        'do_not_go_beyond_instructions',
        'suggested_prompts',
        'suggested_prompts_enabled',
        'language',
        'ai_model',
        'ai_embedding_model',
        'limit_per_minute',
        'show_pre_defined_questions',
        'pre_defined_questions',
        // customization start
        'logo',
        'avatar',
        'trigger_avatar_size',
        'trigger_background',
        'trigger_foreground',
        'color_mode',
        'color',
        'show_logo',
        'show_date_and_time',
        'show_average_response_time',
        'position',
        // customization end
        'active',
        'footer_link',
        'is_demo',
        'is_favorite',
        // new options
        'is_email_collect',
        'is_contact',
        'is_attachment',
        'is_emoji',
        'is_articles',
        'is_links',
        'is_gdpr',
        // links
        'whatsapp_link',
        'telegram_link',
        'facebook_link',
        'instagram_link',
        'watch_product_tour_link',
        'privacy_policy_link',
        'terms_of_service_link',
        'show_social_links_in_first_message',
        // header bg
        'header_bg_type',
        'header_bg_color',
        'header_bg_gradient',
        'header_bg_image',
        'welcome_bg_image',
        'human_agent_conditions',
        // voice call
        'voice_call_enabled',
        'voice_call_first_message',
        'voice_call_agent_id',
        'trusted_domains',
        'bubble_design',
        'promo_banner_image',
        'promo_banner_title',
        'promo_banner_description',
        'promo_banner_btn_label',
        'promo_banner_btn_link',
        'is_review_enabled',
        'review_prompt',
        'review_responses',
        // booking assistant
        'is_booking_assistant',
        'booking_assistant_conditions',
        'booking_assistant_iframe',
        // shop feature
        'is_shop',
        'shop_source',
        'shop_features',
        'shopify_domain',
        'shopify_access_token',
        'woocommerce_domain',
        'woocommerce_consumer_key',
        'woocommerce_consumer_secret',
    ];

    protected $casts = [
        'color_mode'                         => ColorModeEnum::class,
        'position'                           => PositionEnum::class,
        'interaction_type'                   => InteractionType::class,
        'do_not_go_beyond_instructions'      => 'boolean',
        'limit_per_minute'                   => 'integer',
        'show_pre_defined_questions'         => 'boolean',
        'pre_defined_questions'              => 'array',
        'active'                             => 'boolean',
        'user_id'                            => 'integer',
        'is_demo'                            => 'boolean',
        'human_agent_conditions'             => 'json',
        'voice_call_enabled'                 => 'boolean',
        'trusted_domains'                    => 'json',
        'suggested_prompts'                  => 'array',
        'suggested_prompts_enabled'          => 'boolean',
        'is_gdpr'                            => 'boolean',
        'show_social_links_in_first_message' => 'boolean',
        'bubble_design' 				                 => BubbleDesign::class,
        'is_review_enabled'                  => 'boolean',
        'review_responses'                   => 'array',
        'is_booking_assistant'               => 'boolean',
        'booking_assistant_conditions'       => 'json',
        'booking_assistant_iframe'           => 'string',
        'is_shop'                            => 'boolean',
        'shop_features'                      => 'json',
    ];

    public function conversations(): HasMany
    {
        return $this->hasMany(ChatbotConversation::class, 'chatbot_id', 'id');
    }

    public function embeddings(): HasMany
    {
        return $this->hasMany(ChatbotEmbedding::class, 'chatbot_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isActive(): bool
    {
        return (bool) $this->active;
    }

    public function channels(): HasMany
    {
        return $this->hasMany(ChatbotChannel::class, 'chatbot_id', 'id');
    }

    public function pageVisits(): HasMany
    {
        return $this->hasMany(ChatbotPageVisit::class, 'chatbot_id', 'id');
    }

    public function articles(): \Illuminate\Database\Eloquent\Collection|array
    {
        return ChatbotKnowledgeBaseArticle::query()
            ->whereRaw('JSON_CONTAINS(chatbots, ?)', ['"' . $this->getKey() . '"'])
            ->select(columns: [
                'id',
                'title',
                'description as excerpt',
                'is_featured',
                DB::raw('"#" as link'),
            ])
            ->get();
    }
}
