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
                id="title"
                size="lg"
                name="title"
                label="{{ __('Title') }}"
                placeholder="{{ __('Response Title') }}"
                value="{!! $item?->title !!}"
            />

            <x-forms.input
                id="content"
                size="lg"
                name="content"
                label="{{ __('Content') }}"
                placeholder="{{ __('Write the canned response content here...') }}"
                type="textarea"
                rows="12"
            >{{ $item?->content }}</x-forms.input>

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
