@php($pageTitle = $pageTitle ?? __('accountings::app.menu.accounting'))
@extends('layouts.app')

@section('content')
    <!-- SETTINGS START -->
    <div class="w-100 d-flex ">

        <x-setting-sidebar :activeMenu="$activeSettingMenu" />

        <x-setting-card>
            <x-slot name="header">
                <div class="s-b-n-header" id="tabs">
                    <nav class="tabs px-4 border-bottom-grey">
                        <div class="nav" id="nav-tab" role="tablist">
                            <a class="nav-item nav-link f-15 active typeunit" href="{{ route('acc-settings.index') }}"
                                role="tab" aria-controls="nav-unit-settings" aria-selected="true">@lang('accountings::modules.bs.balanceSheet')
                            </a>

                            <a class="nav-item nav-link f-15 pnl" href="{{ route('acc-settings.index') }}?tab=pnl"
                                role="tab" aria-controls="nav-pnl" aria-selected="true">@lang('accountings::modules.pnl.pnl')
                            </a>

                            <a class="nav-item nav-link f-15 type" href="{{ route('acc-settings.index') }}?tab=type"
                                role="tab" aria-controls="nav-type" aria-selected="true">@lang('accountings::modules.acc.journalType')
                            </a>
                        </div>
                    </nav>
                </div>
            </x-slot>

            <x-slot name="buttons">
                <div class="row">
                    <div class="col-md-12 mb-2">
                        <x-forms.button-primary icon="plus" id="add-bs" class="typeunit-btn mb-2 d-none actionBtn">
                            @lang('app.addNew')@lang('accountings::modules.bs.balanceSheet')
                        </x-forms.button-primary>

                        <x-forms.button-primary icon="plus" id="add-pnl" class="pnl-btn mb-2 d-none actionBtn">
                            @lang('app.addNew')@lang('accountings::modules.pnl.pnl')
                        </x-forms.button-primary>

                        <x-forms.button-primary icon="plus" id="add-type" class="type-btn mb-2 d-none actionBtn">
                            @lang('app.addNew')@lang('accountings::modules.acc.journalType')
                        </x-forms.button-primary>
                    </div>
                </div>
            </x-slot>

            {{-- include tabs here --}}
            @include($view)

        </x-setting-card>

    </div>
    <!-- SETTINGS END -->
@endsection

@push('scripts')
    <script>
        /* manage menu active class */
        $('.nav-item').removeClass('active');
        const activeTab = "{{ $activeTab }}";
        $('.' + activeTab).addClass('active');

        showBtn(activeTab);

        function showBtn(activeTab) {
            $('.actionBtn').addClass('d-none');
            $('.' + activeTab + '-btn').removeClass('d-none');
        }

        $("body").on("click", "#editSettings .nav a", function(event) {
            event.preventDefault();

            $('.nav-item').removeClass('active');
            $(this).addClass('active');

            const requestUrl = this.href;

            $.easyAjax({
                url: requestUrl,
                blockUI: true,
                container: "#nav-tabContent",
                historyPush: true,
                success: function(response) {
                    if (response.status == "success") {
                        showBtn(response.activeTab);

                        $('#nav-tabContent').html(response.html);
                        init('#nav-tabContent');
                    }
                }
            });
        });

        $('#add-bs').click(function() {
            const url = "{{ route('balance-sheet.create') }}";
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });

        $('#add-pnl').click(function() {
            const url = "{{ route('pnl.create') }}";
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });

        $('#add-type').click(function() {
            const url = "{{ route('journal-type.create') }}";
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });
    </script>
@endpush
