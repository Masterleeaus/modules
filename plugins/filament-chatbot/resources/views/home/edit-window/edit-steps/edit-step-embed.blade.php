{{-- Editing Step 4 - Embed --}}
<div
    class="col-start-1 col-end-1 row-start-1 row-end-1 transition-all"
    data-step="4"
    x-show="editingStep === 4"
    x-transition:enter-start="opacity-0 -translate-x-3"
    x-transition:enter-end="opacity-100 translate-x-0"
    x-transition:leave-start="opacity-100 translate-x-0"
    x-transition:leave-end="opacity-0 translate-x-3"
>
    <h2 class="mb-3.5">
        @lang('Test and Embed')
    </h2>
    <p class="mb-14 text-xs/5 opacity-60 lg:max-w-[360px]">
        @lang('Your external AI chatbot has been successfully created! You can now integrate it into your website and start engaging with your audience.')
    </p>

    <div class="mb-14">
        <x-forms.input
            class:container="mb-5"
            class:label="mb-4 block text-xs font-semibold text-heading-foreground"
            class="font-mono text-[12px] [word-break:break-word;]"
            placeholder="example.com"
            label="{{ __('Trusted Domains') }}"
            tooltip="{{ __('Add trusted domains separated by commas (e.g., example.com, anotherdomain.com). Trusted domains are websites allowed to access or interact with this application without being blocked by security restrictions. Only requests from these domains will be accepted, helping protect your system from unauthorized access. Leave this field empty to remove all domain restrictions.') }}"
            name="instructions"
            size="lg"
            type="textarea"
            rows="4"
            x-model="activeChatbot.trusted_domains"
            @keydown.enter.prevent=""
        />

        <label class="mb-4 block text-xs font-semibold text-heading-foreground">
            @lang('Embed Code')
        </label>
        <div
            class="mb-5 rounded-xl border bg-heading-foreground/5 p-7 font-mono text-[12px] [word-break:break-word;]"
            id="embed-code-wrapper"
            disabled
        >
            &lt;script
            defer
            src="{{ url('vendor/chatbot/js/external-chatbot.js') }}"
            data-chatbot-uuid="<span x-text="activeChatbot.uuid"></span>"
            data-iframe-width="<span x-text="testIframeWidth"></span>"
            data-iframe-height="<span x-text="testIframeHeight"></span>"
            data-language="en"
            &gt;&lt;/script&gt;
        </div>

        <div class="mb-8 flex flex-col gap-4">
            <div
                class="flex w-full flex-col gap-3 text-heading-foreground"
                x-data="{ width: testIframeWidth, height: testIframeHeight }"
            >
                <div class="flex flex-col gap-2">
                    <label
                        class="block w-full text-xs font-medium text-heading-foreground"
                        for="iframe_width"
                    >
                        @lang('Width')
                    </label>
                    <div class="flex items-center gap-3">
                        <input
                            class="h-2 w-full cursor-ew-resize appearance-none rounded-full bg-heading-foreground/5 focus:outline-secondary [&::-moz-range-thumb]:size-4 [&::-moz-range-thumb]:appearance-none [&::-moz-range-thumb]:rounded-full [&::-moz-range-thumb]:border-none [&::-moz-range-thumb]:bg-heading-foreground active:[&::-moz-range-thumb]:scale-110 [&::-webkit-slider-thumb]:size-4 [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:border-none [&::-webkit-slider-thumb]:bg-heading-foreground active:[&::-webkit-slider-thumb]:scale-110"
                            type="range"
                            min="100"
                            max="1000"
                            step="50"
                            name="iframe_width"
                            @input="testIframeWidth = $event.target.value;"
                            x-modelable="width"
                        />
                        <span
                            class="ms-2 min-w-10 shrink-0 text-2xs font-medium"
                            x-text="parseInt(testIframeWidth, 10) + 'px'"
                        ></span>
                    </div>
                </div>

                <div class="flex flex-col gap-3">
                    <label
                        class="block w-full text-xs font-medium text-heading-foreground"
                        for="iframe_height"
                    >
                        @lang('Height')
                    </label>
                    <div class="flex items-center gap-3">
                        <input
                            class="h-2 w-full cursor-ew-resize appearance-none rounded-full bg-heading-foreground/5 focus:outline-secondary [&::-moz-range-thumb]:size-4 [&::-moz-range-thumb]:appearance-none [&::-moz-range-thumb]:rounded-full [&::-moz-range-thumb]:border-none [&::-moz-range-thumb]:bg-heading-foreground active:[&::-moz-range-thumb]:scale-110 [&::-webkit-slider-thumb]:size-4 [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:border-none [&::-webkit-slider-thumb]:bg-heading-foreground active:[&::-webkit-slider-thumb]:scale-110"
                            type="range"
                            min="100"
                            max="1000"
                            step="50"
                            name="iframe_height"
                            @input="testIframeHeight = $event.target.value;"
                            x-modelable="height"
                        />
                        <span
                            class="ms-2 min-w-10 shrink-0 text-2xs font-medium"
                            x-text="parseInt(testIframeHeight, 10) + 'px'"
                        ></span>
                    </div>
                </div>
            </div>
        </div>

        <x-button
            class="w-full"
            size="lg"
            variant="secondary"
            @click.prevent="navigator.clipboard.writeText(document.getElementById('embed-code-wrapper').innerText); toastr.success('{{ __('Embed code copied to clipboard!') }}'); updateChatbot();"
        >
            @lang('Save and Copy to Clipboard')
            <x-tabler-copy class="size-5" />
        </x-button>
    </div>

    <div>
        <label class="mb-4 block text-xs font-semibold text-heading-foreground underline decoration-heading-foreground/10 decoration-dashed underline-offset-4">
            @lang('Need help?')
        </label>
        <p class="mb-3 text-xs/5 opacity-60 lg:max-w-[360px]">
            @lang('Paste this code just before the closing &lt;/body&gt; tag in your HTML file, then save the changes. Refresh your site to ensure your chatbot works correctly.')
        </p>
    </div>
</div>
