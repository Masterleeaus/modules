{{-- This is the frontend ui --}}

@if ($is_editor)
    @include('chatbot::frontend-ui.frontend-ui-editor')
@else
    @include('chatbot::frontend-ui.frontend-ui-frontpage')
@endif
