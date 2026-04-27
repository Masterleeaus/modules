<div class="row gap-y-7">
	<div class="col-12">
		<x-form-step
			class="mb-0"
			step="3"
			label="{{ __('Chatbot Options') }}"
		/>
		<div class="form-label mt-5">
			{{ __('Configure chatbot-related allowances such as creation limits and API usage requirements.') }}
		</div>
		<x-alert
			class="mt-3"
			variant="warn"
			size="sm"
			icon="tabler-info-circle"
		>
			<p class="text-xs">
				{{ __('You can enable/disable AI bot access on the next page.') }}
			</p>
		</x-alert>
	</div>

	<div class="col-12 col-sm-6 space-y-5">
		<x-form.group
			class:container="mb-4"
			no-group-label
			error="plan.user_api"
		>
			<x-form.checkbox
				class:container="w-full mt-4"
				wire:model="plan.user_api"
				label="{{ __('Require User API Keys for Chatbot') }}"
				tooltip="{{ __('When enabled, users must supply their own API key to use chatbot features.') }}"
				switcher
			/>
		</x-form.group>
	</div>

	<div class="col-12 col-sm-6 space-y-5">
		<x-form.group
			class:container="mb-4"
			no-group-label
			error="plan.chatbot_human_agent"
		>
			<x-form.checkbox
				class:container="w-full mt-4"
				wire:model="plan.chatbot_human_agent"
				label="{{ __('Enable Human Agent') }}"
				tooltip="{{ __('Allow subscribers on this plan to access the Human Agent inbox.') }}"
				switcher
			/>
		</x-form.group>
	</div>

	@includeIf('chatbot::partials.plan-chatbot-limit', ['plan' => $plan])

	@php
		$installedChatbotChannels = [];
		if (class_exists(\App\Extensions\Chatbot\System\Helpers\ChatbotHelper::class)) {
			$installedChatbotChannels = \App\Extensions\Chatbot\System\Helpers\ChatbotHelper::installedChannelKeys();
		}
	@endphp

	@if (!empty($installedChatbotChannels))
		<div class="col-12">
			<x-form-step
				class="mb-0"
				step="4"
				label="{{ __('Chatbot Channels') }}"
			/>
			<div class="form-label mt-5">
				{{ __('Enable or disable individual messaging channels for this plan.') }}
			</div>
		</div>

		@if (in_array('telegram', $installedChatbotChannels, true))
			@includeIf('telegram-channel::partials.plan-channel-toggle', ['plan' => $plan])
		@endif

		@if (in_array('whatsapp', $installedChatbotChannels, true))
			@includeIf('whatsapp-channel::partials.plan-channel-toggle', ['plan' => $plan])
		@endif

		@if (in_array('messenger', $installedChatbotChannels, true))
			@includeIf('messenger-channel::partials.plan-channel-toggle', ['plan' => $plan])
		@endif

		@if (in_array('instagram', $installedChatbotChannels, true))
			@includeIf('instagram-channel::partials.plan-channel-toggle', ['plan' => $plan])
		@endif
	@endif
</div>
