<div>
    <div class="mb-7 flex items-center gap-3 text-3xs">
        <span class="h-px grow bg-current opacity-5"></span>
        {{ __('Enter your email and message — we’ll reply soon.') }}
        <span class="h-px grow bg-current opacity-5"></span>
    </div>

    <form
        class="space-y-6"
        @submit.prevent="onContactFormSubmit"
    >
        <label
            class="block w-full text-xs font-normal text-current"
            for="lqd-contact-form-email"
        >
            <p class="mb-2">
                {{ __('Email Address') }}
            </p>
            <input
                class="h-11 w-full rounded-full bg-black/5 px-4 py-1 text-xs text-current"
                id="email"
                type="email"
                name="email"
                placeholder="youremail@example.com"
            >
        </label>
        <label
            class="block w-full text-xs font-normal text-current"
            for="lqd-contact-form-message"
        >
            <p class="mb-2">
                {{ __('Your message') }}
            </p>
            <textarea
                class="w-full rounded-2xl bg-black/5 p-4 text-xs text-current"
                id="message"
                rows="6"
                placeholder="{{ __('Your message') }}"
                name="message"
            ></textarea>
        </label>

        <button
            class="flex w-full items-center justify-center gap-3 px-4 text-center text-xs text-[--lqd-ext-chat-primary] underline underline-offset-4"
            type="submit"
        >
            {{-- blade-formatter-disable --}}
			<svg width="15" height="17" viewBox="0 0 15 17" fill="currentColor" xmlns="http://www.w3.org/2000/svg" > <path d="M12.75 5C12.125 5 11.5938 4.78125 11.1562 4.34375C10.7188 3.90625 10.5 3.375 10.5 2.75C10.5 2.125 10.7188 1.59375 11.1562 1.15625C11.5938 0.71875 12.125 0.5 12.75 0.5C13.375 0.5 13.9062 0.71875 14.3438 1.15625C14.7812 1.59375 15 2.125 15 2.75C15 3.375 14.7812 3.90625 14.3438 4.34375C13.9062 4.78125 13.375 5 12.75 5ZM0 17V3.5C0 3.0875 0.146875 2.73438 0.440625 2.44063C0.734375 2.14688 1.0875 2 1.5 2H9.075C9.025 2.25 9 2.5 9 2.75C9 3 9.025 3.25 9.075 3.5C9.25 4.375 9.68125 5.09375 10.3687 5.65625C11.0562 6.21875 11.85 6.5 12.75 6.5C13.15 6.5 13.5438 6.4375 13.9313 6.3125C14.3188 6.1875 14.675 6 15 5.75V12.5C15 12.9125 14.8531 13.2656 14.5594 13.5594C14.2656 13.8531 13.9125 14 13.5 14H3L0 17Z" /> </svg>
			{{-- blade-formatter-enable --}}
            {{ __('Send email') }}
        </button>
    </form>
</div>
