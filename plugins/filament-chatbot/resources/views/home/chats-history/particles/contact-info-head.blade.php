<div class="hidden shrink-0 justify-evenly gap-3 border-b px-4 py-4 lg:flex">
    <x-button
        class="active gap-2.5 p-2 text-base font-semibold opacity-50 [&.active]:opacity-100"
        variant="link"
        size="none"
        ::class="{ 'active': contactInfo.activeTab === 'details' }"
        @click.prevent="contactInfo.activeTab = 'details'"
    >
        <svg
            width="20"
            height="21"
            viewBox="0 0 20 21"
            fill="none"
            stroke="currentColor"
            stroke-width="1.5"
            stroke-linecap="round"
            stroke-linejoin="round"
            xmlns="http://www.w3.org/2000/svg"
        >
            <path
                d="M4 18.55V18.5C4 17.4391 4.42143 16.4217 5.17157 15.6716C5.92172 14.9214 6.93913 14.5 8 14.5H12C13.0609 14.5 14.0783 14.9214 14.8284 15.6716C15.5786 16.4217 16 17.4391 16 18.5V18.55M10 11.5C10.7956 11.5 11.5587 11.1839 12.1213 10.6213C12.6839 10.0587 13 9.29565 13 8.5C13 7.70435 12.6839 6.94129 12.1213 6.37868C11.5587 5.81607 10.7956 5.5 10 5.5C9.20435 5.5 8.44129 5.81607 7.87868 6.37868C7.31607 6.94129 7 7.70435 7 8.5C7 9.29565 7.31607 10.0587 7.87868 10.6213C8.44129 11.1839 9.20435 11.5 10 11.5ZM10 1.5C17.2 1.5 19 3.3 19 10.5C19 17.7 17.2 19.5 10 19.5C2.8 19.5 1 17.7 1 10.5C1 3.3 2.8 1.5 10 1.5Z"
            />
        </svg>
        {{ __('Details') }}
    </x-button>

    {{-- <x-button
        class="gap-2.5 p-2 text-base font-semibold opacity-50 [&.active]:opacity-100"
        variant="link"
        size="none"
        ::class="{ 'active': contactInfo.activeTab === 'history' }"
        @click.prevent="contactInfo.activeTab = 'history'"
    >
        <svg
            width="21"
            height="22"
            viewBox="0 0 21 22"
            fill="none"
            stroke="currentColor"
            stroke-width="1.5"
            stroke-linecap="round"
            stroke-linejoin="round"
            xmlns="http://www.w3.org/2000/svg"
        >
            <path
                d="M19 10.5C18.9999 8.76145 18.4962 7.06014 17.5499 5.60171C16.6035 4.14329 15.255 2.99017 13.6674 2.28174C12.0797 1.5733 10.3208 1.33988 8.60333 1.60967C6.88584 1.87947 5.28325 2.64094 3.98927 3.80205C2.69529 4.96316 1.7653 6.4742 1.31171 8.15254C0.858119 9.83088 0.900347 11.6047 1.43329 13.2595C1.96623 14.9144 2.96707 16.3795 4.31484 17.4777C5.66261 18.5759 7.29962 19.2602 9.028 19.448C9.348 19.482 9.672 19.5 10 19.5M10 5.5V10.5L12 12.5M16.42 14.11C16.615 13.915 16.8465 13.7603 17.1013 13.6548C17.3561 13.5492 17.6292 13.4949 17.905 13.4949C18.1808 13.4949 18.4539 13.5492 18.7087 13.6548C18.9635 13.7603 19.195 13.915 19.39 14.11C19.585 14.305 19.7397 14.5365 19.8452 14.7913C19.9508 15.0461 20.0051 15.3192 20.0051 15.595C20.0051 15.8708 19.9508 16.1439 19.8452 16.3987C19.7397 16.6535 19.585 16.885 19.39 17.08L16 20.5H13V17.5L16.42 14.11Z"
            />
        </svg>
        {{ __('History') }}
    </x-button> --}}
</div>
