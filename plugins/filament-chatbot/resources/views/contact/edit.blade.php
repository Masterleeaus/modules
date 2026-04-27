@extends('panel.layout.settings', ['disable_tblr' => true])
@section('title', $title)
@section('titlebar_subtitle', $description)
@section('titlebar_actions', '')

@section('settings')
    <form
        class="flex flex-col gap-10"
        action="{{ $action }}"
        method="post"
    >
        @csrf
        @method($method)

        <div class="mt-4 space-y-6">
            <x-forms.input
                id="name"
                size="lg"
                name="name"
                label="{{ __('name') }}"
                placeholder="{{ __('Name') }}"
                value="{!! $item?->name !!}"
            />
            <x-forms.input
                id="email"
                size="lg"
                name="email"
                label="{{ __('Email') }}"
                placeholder="{{ __('Email') }}"
                value="{!! $item?->email !!}"
            />

            <x-forms.input
                id="phone"
                size="lg"
                name="phone"
                label="{{ __('Phone') }}"
                placeholder="{{ __('Phone') }}"
                value="{!! $item?->phone !!}"
            />


            @if ($app_is_demo)
                <x-button
                    class="w-full"
                    size="lg"
                    onclick="return toastr.info('This feature is disabled in Demo version.')"
                >
                    {{ __('Save') }}
                </x-button>
            @else
                <x-button
                    class="w-full"
                    size="lg"
                    type="submit"
                >
                    {{ __('Save') }}
                </x-button>
            @endif
        </div>
    </form>
@endsection

@push('script')
@endpush
