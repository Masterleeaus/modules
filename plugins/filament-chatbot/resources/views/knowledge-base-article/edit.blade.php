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
                placeholder="{{ __('Article Title') }}"
                value="{!! $item?->title !!}"
            />

            <x-forms.input
                id="description"
                size="lg"
                name="description"
                label="{{ __('Excerpt') }}"
                placeholder="{{ __('Enter a short description') }}"
                type="textarea"
                rows="3"
            >{{ $item?->description }}</x-forms.input>

            <div class="space-y-3">
                <x-forms.input
                    id="content"
                    size="lg"
                    name="content"
                    label="{{ __('Content') }}"
                    placeholder="## This is a heading.&#10;&#10;- List Item #1&#10;- List Item #2&#10;&#10;![This is an image!](https://example.com/link-to-image.jpg)&#10;&#10;This is an inline iframe video&#10;&lt;iframe width=&quot;560&quot; height=&quot;315&quot; src=&quot;https://www.youtube.com/embed/aEiQ4T3XkNY?si=THuGPLSdlJFlHcx5&quot;&gt;&lt;/iframe&gt;"
                    type="textarea"
                    rows="15"
                >{{ $item?->content }}</x-forms.input>

                <p class="text-3xs">
                    {{ __('Use Markdown to add content.') }}
                    <a
                        class="underline"
                        href="https://www.markdownguide.org/cheat-sheet/"
                        target="_blank"
                    >
                        {{ __('Here is a guide how to use Markdown.') }}
                    </a>
                </p>
            </div>

            <x-forms.input
                class:label="text-heading-foreground"
                type="select"
                size="lg"
                name="chatbots[]"
                label="{{ __('Chatbots') }}"
                x-model="chatbots"
                multiple=""
            >
                @foreach ($chatbots as $chatbot)
                    <option
                        value="{{ $chatbot->id }}"
                        {{ in_array($chatbot->id, $item?->chatbots ?? []) ? 'selected' : '' }}
                    > {{ $chatbot->title }}</option>
                @endforeach
            </x-forms.input>

            <x-forms.input
                id="is_featured"
                name="is_featured"
                type="checkbox"
                switcher
                type="checkbox"
                :checked="(bool) $item?->is_featured"
                label="{{ __('Feature') }}"
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
