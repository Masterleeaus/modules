<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{ asset('vendor/css/all.min.css') }}">

    <!-- Simple Line Icons -->
    <link rel="stylesheet" href="{{ asset('vendor/css/simple-line-icons.css') }}">

    <!-- Template CSS -->
    <link type="text/css" rel="stylesheet" media="all" href="{{ asset('css/main.css') }}">

    <title>@lang($pageTitle)</title>
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{ $company->favicon_url }}">
    <meta name="theme-color" content="#ffffff">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ $company->favicon_url }}">
    <!-- Datepicker -->
    <link rel="stylesheet" href="{{ asset('vendor/css/datepicker.min.css') }}">

    @isset($activeSettingMenu)
        <style>
            .preloader-container {
                margin-left: 510px;
                width: calc(100% - 510px)
            }

        </style>
    @endisset

    @stack('styles')
    @include('sections.theme_css', ['companySpecific' => $company])
    <style>
        :root {
            --fc-border-color: #E8EEF3;
            --fc-button-text-color: #99A5B5;
            --fc-button-border-color: #99A5B5;
            --fc-button-bg-color: #ffffff;
            --fc-button-active-bg-color: #171f29;
            --fc-today-bg-color: #f2f4f7;
        }

        .preloader-container {
            height: 100vh;
            width: 100%;
            margin-left: 0;
            margin-top: 0;
        }

        .fc a[data-navlink] {
            color: #99a5b5;
        }

    </style>
    <style>
        #logo {
            height: 50px;
        }

        .imgnew {
            height: 100px !important;
            width: 100px !important;
        }

        .new {
            height: 100% !important;
            width: 100% !important;
        }

        .rightaligned {
            margin-right: 0;
            margin-left: auto;
        }

        .mt-0 {
            margin-top: 0px;
        }

        .signature_wrap {
            position: relative;
            height: 150px;
            -moz-user-select: none;
            -webkit-user-select: none;
            -ms-user-select: none;
            user-select: none;
            width: 400px;
        }

        .signature-pad {
            position: absolute;
            left: 0;
            top: 0;
            width: 400px;
            height: 150px;
        }

    </style>


    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/jquery/modernizr.min.js') }}"></script>

    <script>
        var checkMiniSidebar = localStorage.getItem("mini-sidebar");
    </script>

</head>

<body id="body" class="h-100 bg-additional-grey">

    <!-- BODY WRAPPER START -->
    <div class="body-wrapper clearfix">
        <!-- MAIN CONTAINER START -->
        <section class="bg-additional-grey" id="fullscreen">

            <div class="preloader-container d-flex justify-content-center align-items-center">
                <div class="spinner-border" role="status" aria-hidden="true"></div>
            </div>

            <x-app-title class="d-block d-lg-none" :pageTitle="$pageTitle"></x-app-title>

            <div class="content-wrapper container">

                <!-- CARD START -->

                <div class="card border-0 invoice">
                    <!-- CARD BODY START -->
                    <div class="card-body">
                        <div class="invoice-table-wrapper">
                            <table width="100%" class="">
                                <tr class=" inv-logo-heading">
                                    <td><img src="{{ $company->logo_url }}"
                                            alt="{{ $company->company_name }}" id="logo"/></td>

                                    <td align="right"
                                        class=" f-21 text-dark mt-4 mt-lg-0 mt-md-0">

                                        @if ($jobOffer->status != 'expired' ||$jobOffer->status != 'draft')
                                            <span class="{{ $label_class }}">{{ $msg }}</span>
                                        @endif
                                        <x-forms.link-secondary class="ml-3" icon="download" :link="route('jobOffer.download', [$jobOffer->id, $company->hash])">@lang('app.download')</x-forms.link-secondary>

                                    </td>
                                </tr>
                                <tr class="inv-num">
                                    <td class="f-14 text-dark">
                                        <p class="mt-3 mb-0">
                                            {{ $company->company_name }}<br>
                                            @if (!is_null($settings))
                                                {{ $company->company_phone }}
                                            @endif

                                        </p><br>
                                    </td>
                                    <td>
                                        <table class="text-black b-collapse rightaligned mr-4 mt-3">
                                            <tr>
                                                <td>
                                                    @if (!is_null($jobOffer->jobApplication->photo))
                                                        <div class="jobApplicationImg mr-1">
                                                            <div class="imgnew">
                                                                <img data-toggle="tooltip" class="new"
                                                                    data-original-title="{{ $jobOffer->jobApplication->name }}"
                                                                    src="{{ $jobOffer->jobApplication->image_url }}">
                                                            </div>
                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td height="20"></td>
                                </tr>
                            </table>

                            <div class="row">
                                <div class="col-sm-12">
                                    <h5 class="heading-h4 text-grey ">@lang('recruit::modules.joboffer.candidateDetails')</h5>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-sm-12 pb-2 d-block d-lg-flex d-md-flex">
                                    <p class="mb-0 text-lightest f-14 w-30 d-inline-block ">
                                        @lang('recruit::modules.jobApplication.name')</p>
                                    <p class="mb-0 text-dark-grey f-14 w-70">
                                        {{ ($jobOffer->jobApplication->full_name) }}
                                    </p>
                                </div>
                                <div class="col-sm-12 pb-2 d-block d-lg-flex d-md-flex">
                                    <p class="mb-0 text-lightest f-14 w-30 d-inline-block ">
                                        @lang('recruit::modules.jobApplication.email')</p>
                                    <p class="mb-0 text-dark-grey f-14 w-70">
                                        {{ ($jobOffer->jobApplication->email) }}
                                    </p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <h5 class="heading-h4 text-grey ">@lang('recruit::modules.joboffer.jobDetails')</h5>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-sm-12 pb-2 d-block d-lg-flex d-md-flex">
                                    <p class="mb-0 text-lightest f-14 w-30 d-inline-block ">
                                        @lang('recruit::modules.job.jobTitle')</p>
                                    <p class="mb-0 text-dark-grey f-14 w-70">
                                        {{ ($jobOffer->job->title) }}
                                    </p>
                                </div>

                                <div class="col-sm-12 pb-2 d-block d-lg-flex d-md-flex">
                                    <p class="mb-0 text-lightest f-14 w-30 d-inline-block ">
                                        @lang('recruit::modules.joboffer.workExperience')
                                    </p>
                                    <p class="mb-0 text-dark-grey f-14 w-70">
                                        {{ $jobOffer->job->workExperience->work_experience }}
                                    </p>
                                </div>
                                <div class="col-sm-12 pb-2 d-block d-lg-flex d-md-flex">
                                    <p class="mb-0 text-lightest f-14 w-30 d-inline-block ">
                                        @lang('recruit::modules.jobApplication.location')
                                    </p>
                                    <p class="mb-0 text-dark-grey f-14 w-70">
                                        {{ $jobOffer->jobApplication->location->location ? $jobOffer->jobApplication->location->location : '' }}
                                    </p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <h5 class="heading-h4 text-grey ">@lang('recruit::modules.joboffer.offerDetail')</h5>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-sm-12 pb-2 d-block d-lg-flex d-md-flex">
                                    <p class="mb-0 text-lightest f-14 w-30 d-inline-block ">
                                        @lang('recruit::modules.joboffer.designation')</p>
                                    <p class="mb-0 text-dark-grey f-14 w-70">
                                        {{ $jobOffer->job->team->team_name }}
                                    </p>
                                </div>
                                @if(is_null($salaryStructure))
                                    <div class="col-sm-12 pb-2 d-block d-lg-flex d-md-flex">
                                        <p class="mb-0 text-lightest f-14 w-30 d-inline-block ">
                                            @lang('recruit::modules.joboffer.offerPer')</p>
                                        <p class="mb-0 text-dark-grey f-14 w-70">
                                            @if($jobOffer->comp_amount)
                                                {{ currency_format($jobOffer->comp_amount, $company->currency->id) }}@lang('recruit::modules.joboffer.per') {{ $jobOffer->job->pay_according }}
                                            @else
                                                @lang('recruit::app.menu.notDisclosed')
                                            @endif
                                        </p>
                                    </div>
                                @endif
                                <div class="col-sm-12 pb-2 d-block d-lg-flex d-md-flex">
                                    <p class="mb-0 text-lightest f-14 w-30 d-inline-block ">
                                        @lang('recruit::modules.joboffer.joiningDate')</p>
                                    <p class="mb-0 text-dark-grey f-14 w-70">
                                        {{ $jobOffer->expected_joining_date->format('d-M, Y (l)') }}
                                    </p>
                                </div>
                                <div class="col-sm-12 pb-2 d-block d-lg-flex d-md-flex">
                                    <p class="mb-0 text-lightest f-14 w-30 d-inline-block ">
                                        @lang('recruit::modules.joboffer.lastDate')</p>
                                    <p class="mb-0 text-dark-grey f-14 w-70">
                                        {{ $jobOffer->job_expire->format('d-M, Y (l)') }}
                                    </p>
                                </div>
                                <div class="col-sm-12 pb-2 d-block d-lg-flex d-md-flex">
                                    <p class="mb-0 text-lightest f-14 w-30 d-inline-block ">
                                        @lang('recruit::app.menu.description')</p>
                                    <p class="mb-0 text-dark-grey f-14 w-70">
                                        {!! $jobOffer->description !!}
                                    </p>
                                </div>
                            </div>

                            @if(!is_null($salaryStructure))
                                <h5 class="heading-h4 text-grey ">@lang('recruit::modules.joboffer.salaryStructure')</h5>
                                <div class="row">
                                    <div class="col-6">

                                        <div class="table-responsive">
                                            <x-table class="table-bordered" headType="thead-light">
                                                <x-slot name="thead">
                                                    <th>@lang('recruit::modules.joboffer.earning')</th>
                                                    <th class="text-right">@lang('app.amount')</th>
                                                </x-slot>

                                                <tr>
                                                    <td>@lang('recruit::modules.joboffer.basicPay')</td>
                                                    <td class="text-right text-uppercase">
                                                        {{ currency_format($salaryStructure->basic_salary, ($currency ? $currency->id : company()->currency->id )) }}</td>
                                                </tr>
                                                @foreach ($selectedEarningsComponent as $item)
                                                    <tr>
                                                        <td>{{ ($item->component_name) }}</td>
                                                        <td class="text-right">{{ currency_format($item->component_value, ($currency ? $currency->id : company()->currency->id ))  }}</td>
                                                    </tr>
                                                @endforeach

                                                <tr>
                                                    <td>@lang('recruit::modules.joboffer.fixedAllowance')</td>
                                                    <td class="text-right text-uppercase">
                                                        {{ currency_format($fixedAllowance, ($currency ? $currency->id : company()->currency->id )) }}</td>
                                                </tr>

                                            </x-table>
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="table-responsive">
                                            <x-table class="table-bordered" headType="thead-light">
                                                <x-slot name="thead">
                                                    <th>@lang('recruit::modules.joboffer.deduction')</th>
                                                    <th class="text-right">@lang('app.amount')</th>
                                                </x-slot>

                                                @foreach ($selectedDeductionsComponent as $item)
                                                    <tr>
                                                        <td>{{ ($item->component_name) }}</td>
                                                        <td class="text-right">{{ currency_format($item->component_value, ($currency ? $currency->id : company()->currency->id ))  }}</td>
                                                    </tr>
                                                @endforeach
                                            </x-table>
                                        </div>
                                    </div>

                                    <div class="col-3">
                                        <h5 class="heading-h5 ml-3">@lang('recruit::modules.joboffer.grossEarning')</h5>
                                    </div>
                                    <div class="col-3 text-right">
                                        <h5 class="heading-h5">{{ currency_format($grossSalary, ($currency ? $currency->id : company()->currency->id )) }}</h5>
                                    </div>

                                    <div class="col-3">
                                        <h5 class="heading-h5">@lang('recruit::modules.joboffer.totalDeductions')</h5>
                                    </div>
                                    <div class="col-3 text-right">
                                        <h5 class="heading-h5">{{ currency_format($totalDeduction, ($currency ? $currency->id : company()->currency->id )) }}</h5>
                                    </div>

                                    <div class="col-12 p-20 mt-3">
                                        <h3 class="text-center heading-h3">
                                            <span class="text-uppercase mr-3">@lang('recruit::modules.joboffer.netSalary'):</span>
                                            {{ currency_format(sprintf('%0.2f', $netSalary), ($currency ? $currency->id : company()->currency->id )) }}
                                        </h3>
                                        <h5 class="text-center text-lightest">@lang('recruit::modules.joboffer.netSalary') =
                                            @lang('recruit::modules.joboffer.grossEarning') -
                                            @lang('recruit::modules.joboffer.totalDeductions')</h5>
                                    </div>
                                </div>
                            @endif

                            @if (count($jobOffer->files) > null)
                                <div class="row">
                                    <div class="col-sm-12">
                                        <h4>Files</h4>
                                    </div>
                                </div>
                            @endif
                            <div class="d-flex flex-wrap p-20" id="aplication-file-list">
                                @foreach($jobOffer->files as $file)
                                    <x-file-card :fileName="$file->filename"
                                                :dateAdded="$file->created_at->diffForHumans()">
                                        @if ($file->icon == 'images')
                                            <img src="{{ $file->file_url }}">
                                        @else
                                            <i class="fa fa-file-pdf text-lightest"></i>
                                        @endif
                                        <x-slot name="action">
                                            <div class="dropdown ml-auto file-action">
                                                <button
                                                    class="btn btn-lg f-14 p-0 text-lightest  rounded  dropdown-toggle"
                                                    type="button" data-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false">
                                                    <i class="fa fa-ellipsis-h"></i>
                                                </button>

                                                <div
                                                    class="dropdown-menu dropdown-menu-right border-grey rounded b-shadow-4 p-0"
                                                    aria-labelledby="dropdownMenuLink" tabindex="0">
                                                    @if ($file->icon != 'images')
                                                        <a class="cursor-pointer d-block text-dark-grey f-13 pt-3 px-3 "
                                                        target="_blank"
                                                        href="{{ $file->file_url }}">@lang('app.view')</a>
                                                    @endif
                                                    <a class="cursor-pointer d-block text-dark-grey f-13 py-3 px-3 "
                                                    href="{{ route('job-offer-file.download', md5($file->id)) }}">@lang('app.download')</a>


                                                </div>
                                            </div>
                                        </x-slot>
                                    </x-file-card>
                                @endforeach
                            </div>
                            @if ($jobOffer->sign_require == 'on' && $jobOffer->sign_image != null)
                                <div class="row">
                                    <div class="col-sm-12 mt-4">
                                        <h6>@lang('recruit::modules.interviewSchedule.candidate') @lang('modules.estimates.signature')</h6>
                                        <img src="{{ $jobOffer->file_url }}" style="width: 200px;">
                                        <p>({{ $jobOffer->jobApplication->full_name }}
                                            @lang("recruit::app.menu.signedOffer")
                                            {{$jobOffer->offer_accept_at->format($company->date_format)}})</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <!-- CARD BODY END -->
                    <!-- CARD FOOTER START -->
                    <div
                        class="card-footer bg-white border-1  py-0 py-lg-4 py-md-4 mb-4 mb-lg-3 mb-md-3 ">

                            @if ($jobOffer->status == 'pending' && $job_not_expired == true)

                                <x-forms.button-primary id="signature-modal" icon="check">@lang('app.accept')</x-forms.button-primary>

                                <a class="f-14 btn btn-danger ml-3" data-toggle="modal"
                                data-target="#reason-modal" href="javascript:;">
                                    <i class="fa fa-times f-w-500 mr-2"></i> @lang('app.decline')
                                </a>

                            @endif
                    </div>
                    <!-- CARD FOOTER END -->
                </div>
                <!-- CARD END -->

                <div id="reason-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog d-flex justify-content-center align-items-center modal-md">
                        <div class="modal-content">
                            @include('recruit::jobs.offer-letter.decline-job-offer')
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>

    <!-- also the modal itself -->
    <div class="modal fade bs-modal-md in" id="addJobAlert" role="dialog" aria-labelledby="myModalLabel"              aria-hidden="true">
        <div class="modal-dialog modal-lg" id="modal-data-application">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><i class="icon-plus"></i> @lang('recruit::app.interviewSchedule.stages')</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                            class="fa fa-times"></i></button>
                </div>
                <div class="modal-body">
                    Loading...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn blue">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Global Required Javascript -->
    <script src="{{ asset('js/main.js') }}"></script>

    <script>

        const MODAL_LG = '#myModal';
        const MODAL_HEADING = '#modelHeading';
        const dropifyMessages = {
            default: '@lang("app.dragDrop")',
            replace: '@lang("app.dragDropReplace")',
            remove: '@lang("app.remove")',
            error: '@lang("app.largeFile")'
        };

        $(window).on('load', function () {
            // Animate loader off screen
            init();
            $(".preloader-container").fadeOut("slow", function () {
                $(this).removeClass("d-flex");
            });
        });

    </script>

    <script>


        $('body').on('click', '#decline-offer-letter', function () {
            var status = 'decline';
            var decline_reason = $('#reason').val();

            decline_reason = decline_reason.trim();
            if (!decline_reason) {
                Swal.fire({
                    icon: 'error',
                    text: '{{ __('recruit::messages.Reasonis') }}',

                    customClass: {
                        confirmButton: 'btn btn-primary',
                    },
                    showClass: {
                        popup: 'swal2-noanimation',
                        backdrop: 'swal2-noanimation'
                    },
                    buttonsStyling: false
                });
                return false;
            }

            $.easyAjax({
                url: "{{ route('front.job-offer.accept', $jobOffer->id) }}",
                container: '#decline',
                type: "POST",
                blockUI: true,
                data: {
                    status: status,
                    reason: decline_reason,
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    if (response.status == 'success') {
                        window.location.reload();
                    }
                }
            })
        });

        $('body').off('click', "#accept-letter").on('click', '#accept-letter', function () {
            var status = 'accept';

            $.easyAjax({
                url: "{{ route('front.job-offer.accept', $jobOffer->id) }}",
                container: '#accept',
                type: "POST",
                disableButton: true,
                blockUI: true,
                file: true,
                redirect: true,
                data: $('#accept').serialize(),
                success: function (response) {
                    if (response.status == 'success') {
                        window.location.reload();
                    }
                }
            })
        });

        $('body').on('click', '#signature-modal', function () {
            var url = "{{ route('front.accept_offer', $jobOffer->id) }}";
            $('.modal-title').html("@lang('recruit::modules.front.createJobAlert')");
            $.ajaxModal('#addJobAlert', url);
        });
    </script>

</body>

</html>
