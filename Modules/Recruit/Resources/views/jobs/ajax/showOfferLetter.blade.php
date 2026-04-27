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

</style>

<div class="body-wrapper clearfix">
    <section class="bg-additional-grey" id="fullscreen">

        <x-app-title class="d-block d-lg-none" :pageTitle="$pageTitle"></x-app-title>

        <div class="content-wrapper">
            <div class="card border-0 invoice">
                <div class="card-body">
                    <div class="invoice-table-wrapper">
                        <table width="100%">
                            <tr class="inv-logo-heading">
                                <td>
                                    <img src="{{ invoice_setting()->logo_url }}"
                                         alt="{{ $company->company_name }}" id="logo"/>
                                </td>

                                <td align="right"
                                    class="font-weight-bold f-21 text-dark mt-4 mt-lg-0 mt-md-0">
                                    @if ($jobOffer->status != 'expired' ||$jobOffer->status != 'draft')
                                        <span class="{{ $label_class }}">{{ $msg }}</span>
                                    @endif
                                    <x-forms.link-secondary icon="download" class="ml-3" :link="route('jobOffer.download', [$jobOffer->id, $company->hash])">@lang('app.download')</x-forms.link-secondary>
                                </td>
                            </tr>
                            <tr class="inv-num">
                                <td class="f-14 text-dark">
                                    <p class="mt-3 mb-0">
                                        {{ $company->company_name ?? '--' }}
                                        <br>
                                        @if (!is_null($settings))
                                            {{ $company->company_phone ?? '--' }}
                                        @endif
                                    </p>
                                    <br>
                                </td>
                                <td>
                                    <table class="text-black b-collapse rightaligned mr-4 mt-3">
                                        <tr>
                                            <td>
                                                @if ($jobOffer->jobApplication->photo ?? false)
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
                                    {{ $jobOffer->jobApplication ? ($jobOffer->jobApplication->full_name) : '--' }}</p>
                            </div>
                            <div class="col-sm-12 pb-2 d-block d-lg-flex d-md-flex">
                                <p class="mb-0 text-lightest f-14 w-30 d-inline-block ">
                                    @lang('recruit::modules.jobApplication.email')</p>
                                <p class="mb-0 text-dark-grey f-14 w-70">
                                    {{ $jobOffer->jobApplication ? ($jobOffer->jobApplication->email) : '--' }}</p>
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
                                    {{ ($jobOffer->job->title) ?? '--' }}</p>
                            </div>

                            <div class="col-sm-12 pb-2 d-block d-lg-flex d-md-flex">
                                <p class="mb-0 text-lightest f-14 w-30 d-inline-block ">
                                    @lang('recruit::modules.joboffer.workExperience')</p>
                                <p class="mb-0 text-dark-grey f-14 w-70">
                                    {{ $jobOffer->job->workExperience ?$jobOffer->job->workExperience->work_experience : '--' }}</p>
                            </div>
                            <div class="col-sm-12 pb-2 d-block d-lg-flex d-md-flex">
                                <p class="mb-0 text-lightest f-14 w-30 d-inline-block ">
                                    @lang('recruit::modules.jobApplication.location')</p>
                                <p class="mb-0 text-dark-grey f-14 w-70">
                                    {{ $jobOffer->jobApplication->location ? $jobOffer->jobApplication->location->location : '' }}</p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <h5 class="heading-h4 text-grey ">@lang('recruit::modules.joboffer.offerDetail')</h5>

                                <div class="row mb-2">
                                    <div class="col-sm-12 pb-2 d-block d-lg-flex d-md-flex">
                                        <p class="mb-0 text-lightest f-14 w-30 d-inline-block ">
                                            @lang('app.department')</p>
                                        <p class="mb-0 text-dark-grey f-14 w-70">
                                            {{ $jobOffer->job->team ? $jobOffer->job->team->team_name : '--'}}</p>
                                    </div>
                                    @if(is_null($salaryStructure))
                                        <div class="col-sm-12 pb-2 d-block d-lg-flex d-md-flex">
                                            <p class="mb-0 text-lightest f-14 w-30 d-inline-block ">
                                                @lang('recruit::modules.joboffer.offerPer')</p>
                                            <p class="mb-0 text-dark-grey f-14 w-70">
                                                @if($jobOffer->comp_amount)
                                                    {{ currency_format((int)$jobOffer->comp_amount, ($currency ? $currency->id : company()->currency->id )) }} @lang('recruit::modules.joboffer.per') {{ $jobOffer->job->pay_according }}
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
                                            {{ $jobOffer->expected_joining_date ? $jobOffer->expected_joining_date->translatedFormat($company->date_format) : '--' }}
                                    </div>
                                    <div class="col-sm-12 pb-2 d-block d-lg-flex d-md-flex">
                                        <p class="mb-0 text-lightest f-14 w-30 d-inline-block ">
                                            @lang('recruit::modules.joboffer.lastDate')</p>
                                        <p class="mb-0 text-dark-grey f-14 w-70">
                                            {{ $jobOffer->job_expire ? $jobOffer->job_expire->translatedFormat($company->date_format) : '--' }}</p>
                                    </div>
                                    <div class="col-sm-12 pb-2 d-block d-lg-flex d-md-flex">
                                        <p class="mb-0 text-lightest f-14 w-30 d-inline-block ">
                                            @lang('recruit::app.menu.description')</p>
                                            <div class="mb-0 text-dark-grey f-14 w-70 text-wrap ql-editor p-0">{!! $jobOffer->description ? nl2br($jobOffer->description) : '--' !!}</div>
                                    </div>
                                </div>
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
                                                    {{ currency_format((int)$salaryStructure->basic_salary, ($currency ? $currency->id : company()->currency->id )) }}</td>
                                            </tr>
                                            @foreach ($selectedEarningsComponent as $item)
                                                <tr>
                                                    <td>{{ ($item->component_name) }}</td>
                                                    <td class="text-right">{{ currency_format((int)$item->component_value, ($currency ? $currency->id : company()->currency->id ))  }}</td>
                                                </tr>
                                            @endforeach

                                            <tr>
                                                <td>@lang('recruit::modules.joboffer.fixedAllowance')</td>
                                                <td class="text-right text-uppercase">
                                                    {{ currency_format((int)$fixedAllowance, ($currency ? $currency->id : company()->currency->id )) }}</td>
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
                                                    <td class="text-right">{{ currency_format((int)$item->component_value, ($currency ? $currency->id : company()->currency->id ))  }}</td>
                                                </tr>
                                            @endforeach
                                        </x-table>
                                    </div>
                                </div>

                                <div class="col-3">
                                    <h5 class="heading-h5 ml-3">@lang('recruit::modules.joboffer.grossEarning')</h5>
                                </div>
                                <div class="col-3 text-right">
                                    <h5 class="heading-h5">{{ currency_format((int)$grossSalary, ($currency ? $currency->id : company()->currency->id )) }}</h5>
                                </div>

                                <div class="col-3">
                                    <h5 class="heading-h5">@lang('recruit::modules.joboffer.totalDeductions')</h5>
                                </div>
                                <div class="col-3 text-right">
                                    <h5 class="heading-h5">{{ currency_format((int)$totalDeduction, ($currency ? $currency->id : company()->currency->id )) }}</h5>
                                </div>

                                <div class="col-sm-12 p-20 mt-3">
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

                        @if ($jobOffer->files == '')
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
                                    <h6>@lang('modules.estimates.signature')</h6>
                                    <img src="{{ $jobOffer->file_url }}" style="width: 200px;">
                                    <p>
                                        ({{ $jobOffer->jobApplication->full_name ?? '--' }}  @lang("recruit::app.menu.signedOffer")
                                        {{$jobOffer->offer_accept_at->format(company()->date_format)}})</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
