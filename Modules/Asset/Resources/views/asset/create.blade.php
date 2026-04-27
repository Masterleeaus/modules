@extends('layouts.app')


@section('content')

    <div class="content-wrapper">
        @include($view)
    </div>

@endsection

@push('scripts')
    <script>
        $('body').on('click', '.lend', function () {
            let id = $(this).data('asset-id');
            let url = "{{ route('history.create', ':id') }}";
            url = url.replace(':id', id);
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });

        $('body').on('click', '.returnAsset', function () {
            let id = $(this).data('asset-id');
            let historyId = $(this).data('history-id');
            let url = "{{ route('assets.return', [':asset', ':history']) }}";
            url = url.replace(':asset', id);
            url = url.replace(':history', historyId);
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });
    </script>
@endpush
