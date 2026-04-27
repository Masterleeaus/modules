@php
    $addAssetTypePermission = user()->permission('add_assets_type');
@endphp

@extends('layouts.app')

@section('content')

    <!-- SETTINGS START -->
    <div class="w-100 d-flex ">

        <x-setting-sidebar :activeMenu="$activeSettingMenu"/>

        <x-setting-card>
            <x-slot name="header">
                <div class="s-b-n-header" id="tabs">
                    <h2 class="mb-0 p-20 f-21 font-weight-normal  border-bottom-grey">
                        @lang('asset::app.assetType')
                    </h2>
                </div>
            </x-slot>

            <x-slot name="buttons">
                <div class="row">

                    <div class="mb-2 col-md-12">
                        @if ($addAssetTypePermission == 'all' || $addAssetTypePermission == 'added')
                            <x-forms.button-primary icon="plus" id="addAssetType"
                                        class="mb-2 type-btn actionBtn"> @lang('asset::app.addAssetType')
                            </x-forms.button-primary>
                        @endif
                    </div>

                </div>
            </x-slot>

            @include($view)

        </x-setting-card>

    </div>
    <!-- SETTINGS END -->
@endsection

@push('scripts')
    <script>

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
                    showBtn(response.activeTab);
                    if (response.status == "success") {
                        $('#nav-tabContent').html(response.html);
                        init('#nav-tabContent');
                    }
                }
            });
        });

        function showBtn(activeTab) {
            $('.' + activeTab + '-btn').removeClass('d-none');
        }

        showBtn(activeTab);
    </script>
@endpush
