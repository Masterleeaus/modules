<div class="lqd-ext-chatbot-window-foot relative z-3 flex w-full shrink-0 items-center justify-center gap-2.5 py-4 text-center text-3xs text-foreground/50">
    <img
        width="16"
        height="16"
        src="{{ custom_theme_url($setting->logo_collapsed_path, true) }}"
        @if (isset($setting->logo_collapsed_2x_path) && !empty($setting->logo_collapsed_2x_path)) srcset="/{{ $setting->logo_collapsed_2x_path }} 2x" @endif
        alt="{{ $setting->site_name }}"
    >
    <p class="m-0">
        @lang('Powered by')
        <u class="underline-offset-2">
            <a
                href="{{ $chatbot->footer_link ?? request()->getSchemeAndHttpHost() }}"
                target="_blank"
            >
                {{ $setting->site_name }}
            </a>
        </u>
    </p>
</div>
