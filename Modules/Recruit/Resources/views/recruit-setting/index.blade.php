@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="{{ asset('vendor/css/bootstrap-colorpicker.css') }}"/>

    <!-- SETTINGS START -->
    <div class="w-100 d-flex ">

        <x-setting-sidebar :activeMenu="$activeSettingMenu"/>

        <x-setting-card>

            <x-slot name="header">
                <h4 class="mb-0 p-20 f-21 font-weight-normal  border-bottom-grey">
                    @lang('recruit::modules.job.job') @lang('app.details')</h4>
            </x-slot>

            <x-slot name="header">
                <div class="s-b-n-header" id="tabs">
                    <nav class="tabs px-4 border-bottom-grey">
                        <div class="nav" id="nav-tab" role="tablist">
                            <a class="nav-item nav-link f-15 active general-setting"
                               href="{{ route('recruit-settings.index') }}" role="tab"
                               aria-controls="nav-recruit-setting"
                               aria-selected="true">@lang('recruit::modules.front.generalSetting')
                            </a>

                            <a class="nav-item nav-link f-15 footer-settings"
                               href="{{ route('recruit-settings.index') }}?tab=footer-settings" role="tab"
                               aria-controls="nav-recruit-setting" aria-selected="true"
                               ajax="false">@lang('recruit::app.menu.footerSettings')
                            </a>

                            <a class="nav-item nav-link f-15 recruit-setting"
                               href="{{ route('recruit-settings.index') }}?tab=recruit-setting" role="tab"
                               aria-controls="nav-recruit-setting" aria-selected="true"
                               ajax="false">@lang('recruit::app.menu.recruitersSettings')
                            </a>

                            <a class="nav-item nav-link f-15 recruit-email-notification-setting"
                               href="{{ route('recruit-settings.index') }}?tab=recruit-email-notification-setting"
                               role="tab"
                               aria-controls="nav-recruit-setting" aria-selected="true"
                               ajax="false">@lang('recruit::modules.emailNotification.notificationSetting')
                            </a>

                            <a class="nav-item nav-link f-15 job-application-status-settings"
                               href="{{ route('recruit-settings.index') }}?tab=job-application-status-settings"
                               role="tab"
                               aria-controls="nav-recruit-setting" aria-selected="true"
                               ajax="false">@lang('recruit::modules.setting.statusSettings')
                            </a>

                            <a class="nav-item nav-link f-15 recruit-custom-question-setting"
                               href="{{ route('recruit-settings.index') }}?tab=recruit-custom-question-setting"
                               role="tab"
                               aria-controls="nav-recruit-setting" aria-selected="true"
                               ajax="false">@lang('recruit::modules.setting.customQuestionSettings')
                            </a>

                            <a class="nav-item nav-link f-15 recruit-source-setting"
                               href="{{ route('recruit-settings.index') }}?tab=recruit-source-setting"
                               role="tab"
                               aria-controls="nav-recruit-setting" aria-selected="true"
                               ajax="false">@lang('Source')
                            </a>
                        </div>
                    </nav>
                </div>
            </x-slot>

            {{-- include tabs here --}}
            @include($view)

        </x-setting-card>

    </div>
    <!-- SETTINGS END -->
@endsection

@push('scripts')
    <script src="{{ asset('vendor/jquery/bootstrap-colorpicker.js') }}"></script>
    <script>

        $('.nav-item').removeClass('active');
        const activeTab = "{{ $activeTab }}";
        $('.' + activeTab).addClass('active');

        showBtn(activeTab);

        function showBtn(activeTab) {
            $('.actionBtn').addClass('d-none');
            $('.' + activeTab + '-btn').removeClass('d-none');
        }

        $("body").on("click", "#editSettings .nav a", function (event) {
            event.preventDefault();

            $('.nav-item').removeClass('active');
            $(this).addClass('active');

            const requestUrl = this.href;

            $.easyAjax({
                url: requestUrl,
                blockUI: true,
                container: "#nav-tabContent",
                historyPush: true,
                success: function (response) {
                    if (response.status == "success") {
                        showBtn(response.activeTab);

                        $('#nav-tabContent .flex-wrap').html(response.html);

                        init('#nav-tabContent');
                    }
                }
            });

        });

    </script>
@endpush
