<div
	class="col-12 col-sm-6 space-y-5"
	x-data="{
		limitValue: @entangle('plan.chatbot_limit').live,
		hasLimit: false,
		init() {
			this.hasLimit = this.limitValue !== null && this.limitValue !== undefined;
		},
	}"
	x-effect="hasLimit = limitValue !== null && limitValue !== undefined"
>
	<x-form.group
		no-group-label
		error="plan.chatbot_limit"
	>
			<x-form.checkbox
				class:container="mb-4"
				label="{{ __('Set Chatbot Limit') }}"
				switcher
				x-model="hasLimit"
				@change="
					if (! hasLimit) {
						limitValue = null;
					} else if (limitValue === null || limitValue === undefined) {
						limitValue = 1;
					}
				"
			/>
		</x-form.group>
		<div
			x-show="hasLimit"
			x-cloak
	>
		<x-form.group
			label="{{ __('Chatbot Limit') }}"
			tooltip="{{ __('Define how many chatbots users on this plan can create.') }}"
		>
				<x-form.stepper
					wire:model="plan.chatbot_limit"
					type="number"
					step="1"
					min="1"
					placeholder="{{ __('Chatbot Limit') }}"
				/>
				<span class="text-2xs text-muted">
					{{ __('Turn off the toggle above to allow unlimited chatbots for this plan.') }}
				</span>
			</x-form.group>
		</div>
	</div>
