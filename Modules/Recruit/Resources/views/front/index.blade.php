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
            background-color: {{ $setting->background_color}};
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

        .front-background {
            background-color: #F2F4F7;
        }

        .site-footer {
            font-size: 0.75rem;
            border-top: 1px solid #f1f2f3;
            padding: 20px 0;
            position: absolute;
        }

    </style>
    <style>
        /* Set the size of the div element that contains the map */
        #map {
            height: 400px;
            /* The height is 400 pixels */
            width: 100%;
            /* The width is the width of the web page */
        }

        #description {
            font-family: Roboto;
            font-size: 15px;
            font-weight: 300;
        }

        #infowindow-content .title {
            font-weight: bold;
        }

        #infowindow-content {
            display: none;
        }

        #map #infowindow-content {
            display: inline;
        }

        .pac-card {
            background-color: #fff;
            border: 0;
            border-radius: 2px;
            box-shadow: 0 1px 4px -1px rgba(0, 0, 0, 0.3);
            margin: 10px;
            padding: 0 0.5em;
            font: 400 18px Roboto, Arial, sans-serif;
            overflow: hidden;
            font-family: Roboto;
            padding: 0;
        }

        #pac-container {
            padding-bottom: 12px;
            margin-right: 12px;
        }

        .pac-controls {
            display: inline-block;
            padding: 5px 11px;
        }

        .pac-controls label {
            font-family: Roboto;
            font-size: 13px;
            font-weight: 300;
        }

        #pac-input {
            background-color: #fff;
            font-size: 15px;
            font-weight: 300;
            margin-left: 12px;
            padding: 0 11px 0 13px;
            text-overflow: ellipsis;
            width: 400px;
        }

        #pac-input:focus {
            border-color: #4d90fe;
        }

        #title {
            font-size: 18px;
            font-weight: 500;
            padding: 10px 12px;
        }

    </style>
    <!-- Header Start -->
    @section('content')

        <header class="sticky-top bg-white">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 py-2 front_header d-flex justify-content-between align-items-center">
                        <img class="mr-2 rounded" src="{{ $company->logo_url }}">
                        <h3 class="mb-0 pl-1 heading-h3">{{ $companyName }}</h3>
                        <div class="row">
                            <div class="col-md-12">
                                @if ($setting->job_alert_status != 'no')
                                    <x-forms.button-primary class="mb-2 mb-lg-0 mb-md-0" id="job-alter-create">
                                        @lang('recruit::modules.front.createJobAlert')
                                    </x-forms.button-primary>
                                @endif

                                @if (auth()->user() && user()->permission('view_dashboard') === 'all')
                                    <x-forms.link-secondary :link="route('recruit-dashboard.index')"
                                                            class="mb-2 mb-lg-0 mb-md-0">
                                        @lang('recruit::app.menu.goToDashboard')
                                    </x-forms.link-secondary>
                                @else
                                    <div class="mb-2 mb-lg-0 mb-md-0">
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <!-- Header End -->

        <!-- Content Start -->
        <section class="front-background py-3 main-content">
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
                                <div class="header-banner-logo rounded">
                                    <img src={{ $setting->getLogoUrlAttribute() }} />
                                </div>
                            </div>
                            <div
                                class="col-md-12 mt-5 pb-4 d-block d-lg-flex d-md-flex  justify-content-between align-items-end">
                                <div class="">
                                    <h3>{{ $setting->company_name }}</h3>
                                    <p class="mb-0">{{ $setting->company_website }}</p>
                                    {{-- <p class="text-dark-grey mb-0">{{ $company->address }}</p> --}}
                                </div>

                                <div class="mt-3 mt-lg-0 mt-md-0">
                                    <a href="{{ route('job_opening', $company->hash) }}" class="btn btn-primary f-14" data-toggle="tooltip"
                                    data-original-title="@lang('recruit::app.menu.job')"><i
                                            class="fa fa-briefcase mr-1"></i>@lang('recruit::app.menu.job')</a>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- Banner End -->
                <!-- Overview Start -->
                <div class="row">
                    <div class="col-md-12 mt-3">
                        <div class="bg-white rounded overflow-auto border-grey">
                            <div class="col-md-12 mt-3 pb-4 success-message">
                                <p class="text-dark-grey mb-0 text-justify">{!! $setting->about !!}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Overview End -->
            </div>
        </section>

    @endsection
    <!-- Content End -->
@endif
