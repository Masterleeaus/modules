@if (\App\Extensions\Chatbot\System\Helpers\ChatbotHelper::existChannels())
	@php
		$allowedChatbotChannels = \App\Extensions\Chatbot\System\Helpers\ChatbotHelper::allowedChannelKeys();
	@endphp

	{{-- Editing Step 3 - Train --}}
	<div
		class="col-start-1 col-end-1 row-start-1 row-end-1 transition-all"
		data-step="3"
		x-data="externalChatbotChannel"
		x-show="editingStep === 5"
		x-transition:enter-start="opacity-0 -translate-x-3"
		x-transition:enter-end="opacity-100 translate-x-0"
		x-transition:leave-start="opacity-100 translate-x-0"
		x-transition:leave-end="opacity-0 translate-x-3"
	>
		<h2 class="mb-3.5">
			@lang('Chatbot Channel')
		</h2>
		<p class="mb-3 text-xs/5 opacity-60 lg:max-w-[360px]">
			@lang('This step is optional, but you can enhance your chatbot experience through different channels.')
		</p>

		<div class="mt-10 lqd-social-media-cards-grid gap-3 flex ">
			@if (in_array('telegram', $allowedChatbotChannels, true))
				@includeIf('telegram-channel::channel-card')
			@endif
			@if (in_array('whatsapp', $allowedChatbotChannels, true))
				@includeIf('whatsapp-channel::channel-card')
			@endif
			@if (in_array('messenger', $allowedChatbotChannels, true))
				@includeIf('messenger-channel::channel-card')
			@endif
			@if (in_array('instagram', $allowedChatbotChannels, true))
				@includeIf('instagram-channel::channel-card')
			@endif
		</div>

		@include('chatbot::partials.channel-table')
	</div>
	@push('script')
		<script>

			(() => {
			document.addEventListener('alpine:init', () => {
				Alpine.data('externalChatbotChannel', () => ({
					activeTab: 'channel',
					fetching: false,
					storeChannelFetch: false,
					channelFetch: false,
					chatbotChannels: [],
					instagramCredentials: {},
					instagramStatus: null,
					instagramPopupOpen: false,
					init() {
						this.$watch('editingStep', currentStep => {
							if (currentStep === 5) {
								this.fetchChannels();
							}
						})

						window.addEventListener('message', event => {
							if (event.origin !== window.location.origin) {
								return;
							}

							if (event.data?.type === 'chatbot-instagram:authorized') {
								this.instagramPopupOpen = false;

								const payload = event.data.payload || {};

								if (!payload.access_token) {
									this.instagramStatus = '{{ __('Instagram data could not be retrieved. Please try again.') }}';
									toastr.error('{{ __('Instagram data could not be retrieved. Please try again..') }}');

									return;
								}

								this.instagramCredentials = payload;
								this.instagramStatus = '{{ __('Instagram authorisation has been verified. Channel is being added...') }}';

								this.storeChannel('storeForm-instagram');
							}
						});
					},
					openInstagramOauth() {

						@if(\App\Helpers\Classes\MarketplaceHelper::isRegistered('chatbot-instagram'))
							if (!this.activeChatbot || !this.activeChatbot.id) {
								toastr.error('{{ __('First, you must select a chatbot.') }}');

								return;
							}

							const url = new URL('{{ route('chatbot.instagram.oauth.redirect') }}', window.location.origin);

							url.searchParams.set('chatbot_id', this.activeChatbot.id);
							url.searchParams.set('return_url', window.location.href);

							const popup = window.open(url.toString(), 'instagram-oauth', 'width=520,height=780');

							if (!popup) {
								toastr.error('{{ __('The browser has blocked pop-ups. Please allow them.') }}');

								return;
							}

							this.instagramPopupOpen = true;
							popup.focus();
						@else

							return;

						@endif

					},
					async deleteToClipboard(id) {
						@if($app_is_demo)
						       return toastr.error('{{ trans('This feature is disabled in demo mode.') }}')
						@endif
						if(confirm('If you delete the channel, all conversations linked to it will stop receiving messages. This may lead to some issues.')) {
							this.storeChannelFetch = true;

							const formData = new FormData();
							formData.append('channel_id', id)

							fetch('{{ route('dashboard.chatbot-multi-channel.delete') }}' , {
								method: 'POST',
								headers: {
									'Accept': 'application/json',
									'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
								},
								body: formData
							}).then(async response => {
								if (!response.ok) {
									const error = await response.json();
									toastr.error(error.message || 'Bir hata oluştu');
									this.storeChannelFetch = false;
									return;
								}

								const data = await response.json();

								if (data.status === 'success') {

									this.chatbotChannels = this.chatbotChannels.filter(channel => channel.id !== id);

									toastr.success(data.message);

									this.storeChannelFetch = false;
								}
								else {
									toastr.error(data.message || 'An error has occurred');
									this.storeChannelFetch = false;
								}
							}).catch(error => {
								this.storeChannelFetch = false;
								toastr.error(error.message || 'An error occurred while sending the request');
							});
						}
					},
					copyToClipboard(text) {
						navigator.clipboard.writeText(text)
							.then(() => {
								toastr.success('The webhook has been copied to the clipboard.');
							})
							.catch(err => {
								console.error('Copy error:', err);
								toastr.error('The webhook could not be copied.');
							});
					},
					setActiveTab(tab) {
						if (tab === this.activeTab) return;

						this.activeTab = tab;
					},
					async fetchChannels() {
						this.channelFetch = true;

						const formData = new FormData();

						formData.append('chatbot_id', this.activeChatbot.id)

						fetch('{{ route('dashboard.chatbot-multi-channel.index') }}', {
							method: 'POST',
							headers: {
								'Accept': 'application/json',
								'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
							},
							body: formData
						}).then(async response => {
							if (!response.ok) {
								const error = await response.json();
								toastr.error(error.message || '{{ trans('An error occurred') }}');
								this.channelFetch = false;
								return;
							}

							const data = await response.json();

							if (data.status === 'success') {

								this.chatbotChannels = data.data;

								this.channelFetch = false;
							}
						})
							.catch(error => {
								this.channelFetch = false;
								toastr.error(error.message || '{{ trans('An error occurred while sending the request.') }}');
							});
					},
					storeChannel(id) {
						this.storeChannelFetch = true;

						const form = document.getElementById(id);
						const formData = new FormData(form);
						formData.append('chatbot_id', this.activeChatbot.id)

						fetch(form.action, {
							method: 'POST',
							headers: {
								'Accept': 'application/json',
								'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
							},
							body: formData
						}).then(async response => {

							if (!response.ok) {
								const error = await response.json();

								toastr.error(error.message || '{{ trans('An error occurred') }}');

								this.storeChannelFetch = false;

								return;
							}

							const data = await response.json();

							if (data.status === 'success') {

								this.chatbotChannels.push(data.data);

								if (id === 'storeForm-instagram') {
									this.instagramStatus = '{{ __('Instagram kanalı bağlandı.') }}';
									this.instagramCredentials = {};
								}

								toastr.success(data.message);

								this.storeChannelFetch = false;
							}
							else {
								if (id === 'storeForm-instagram') {
									this.instagramStatus = data.message || '{{ __('Instagram kanalı eklenirken hata oluştu.') }}';
								}

								toastr.error(data.message || '{{ trans('An error occurred') }}');
								this.storeChannelFetch = false;
							}
						}).catch(error => {
							this.storeChannelFetch = false;

							if (id === 'storeForm-instagram') {
								this.instagramStatus = error.message || '{{ __('Instagram kanalı eklenirken hata oluştu.') }}';
							}

							toastr.error(error.message || '{{ trans('An error occurred while sending the request.') }}');
						});
					}

				}));
			});
		})();
	</script>
	@endpush
@endif
