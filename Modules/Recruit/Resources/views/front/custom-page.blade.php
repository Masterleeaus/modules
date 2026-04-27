@if ($messageforAdmin != null)
    <!-- Alert to admin Start -->
    <div class="row alert">
        <div class="col-md-12 mb-3">
            <div class="bg-white rounded overflow-auto border-grey">
                <div class="col-md-12 mt-3 pb-4 success-message">
                    <p class="text-dark-grey mb-0 text-justify">{{ $messageforAdmin }}</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Alert to admin End -->
@else
    @extends('recruit::layouts.front')

    <link rel="stylesheet" href="{{ asset('vendor/css/tagify.css') }}">
    <!-- Header Start -->
    <link rel="stylesheet" href="{{ asset('vendor/css/tagify.css') }}">

    <style>
        .banner-header {
            background-repeat: no-repeat;
            background-position: center;
            height: 200px;
        }

        .banner-color {
            background-color: {{ $setting->background_color }};
            background-repeat: no-repeat;
            background-position: center;
            height: 200px;
        }

        .header-banner-logo {
            position: absolute !important;
            background-color: white !important;
            width: 130px !important;
            height: 130px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            bottom: -49px !important;
        }
    </style>
    @section('content')
        <!-- Header Start -->
        <header class="sticky-top bg-white">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 py-2 front_header d-flex justify-content-between align-items-center">
                        <a href="{{ url('/careers', $company->hash) }}">
                            <img class="mr-2 rounded" src="{{ $company->logo_url }}">
                        </a>
                        <h3 class="mb-0 pl-1 heading-h3">{{ $companyName }}</h3>
                        @if (auth()->user())
                            <x-forms.link-secondary :link="route('recruit-dashboard.index')"
                                                    class="mb-2 mb-lg-0 mb-md-0">
                                @lang('recruit::app.menu.goToDashboard')
                            </x-forms.link-secondary>
                        @elseif ($setting->job_alert_status != 'no')
                            <x-forms.button-primary class="mb-2 mb-lg-0 mb-md-0" id="job-alter-create">
                                @lang('recruit::modules.front.createJobAlert')
                            </x-forms.button-primary>
                        @else
                            <div class="mb-2 mb-lg-0 mb-md-0">
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </header>
        <!-- Header End -->

        <!-- Content Start -->
        <section class="bg-grey py-3 main-content">
            <div class="container">
                <!-- Banner Start -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="bg-white rounded overflow-auto border-grey">
                            <div class="col-md-12
                                @if($setting->type == 'bg-image')
                                    banner-header
                                @else
                                    banner-color
                                @endif
                                    "
                                @if($setting->type == 'bg-image')
                                    style="background-image: url({{ $setting->getBgImageUrlAttribute() }})"
                                @endif
                                    id="bannerImg">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Banner End -->
                <!-- Overview Start -->
                <div class="row">
                    <div class="col-md-12 mt-3">
                        <div class="bg-white rounded overflow-auto border-grey">
                            <div class="col-md-12 mt-4 pb-4 success-message justify-content-center align-items-center">
                                @if (isset($customPage))
                                    <h3>@if(!is_null($customPage->title)) {{ ($customPage->title) }}  @endif</h3>
                                    <p>@if(!is_null($customPage->description)) {!! $customPage->description !!}  @endif</p>
                                @else
                                    <p class="text-center">@lang('recruit::messages.unsubscribeMsg')</p>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Overview End -->
            </div>
        </section>
        <!-- Content End -->
    @endsection
@endif
