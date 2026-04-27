@php
    $editPermission = user()->permission('edit_job');
    $deletePermission = user()->permission('delete_job');
@endphp

@push('styles')
    <style>
        .w-14 {
            width: 14%;
        }

        .margin-bottom {
            margin-bottom: 27px;
        }
        .p1-margin p{
            margin: 0 !important;
        }
    </style>
@endpush
<div class="row">

    <div class="col-xl-7 col-lg-6 col-md-12">
        <div class="row">
            <div class="col-xl-6 col-sm-12 mb-4">
                <x-cards.widget :title="__('recruit::app.job.openings')"
                    :value="$openingsCount"
                    icon="tasks" />
            </div>
            <div class="col-xl-6 col-sm-12 mb-4">
                <x-cards.widget :title="__('recruit::app.job.inProgress')"
                    :value="$inProgressCount"
                    icon="clock" />
            </div>
            <div class="col-xl-6 col-sm-12 mb-4">
                <x-cards.widget :title="__('recruit::modules.email.subject')"
                    :value="$scheduledCount"
                    icon="calendar" />
            </div>
            <div class="col-xl-6 col-sm-12 mb-4">
                <x-cards.widget :title="__('recruit::app.job.offerReleased')"
                    :value="$offerReleasedCount"
                    icon="layer-group" />
            </div>
        </div>
    </div>
    <div class="col-sm-12 col-lg-4 bg-white margin-bottom">
            <x-pie-chart id="task-chart" :labels="$applicationStatus['labels']"
                         :values="$applicationStatus['values']" :colors="$applicationStatus['colors']"
                         height="200" width="225"/>
    </div>
</div>

<div id="notice-detail-section">
    <div class="row">
        <div class="col-sm-12">
            <div class="card bg-white border-0 b-shadow-4">
                <div class="card-header bg-white  border-bottom-grey  justify-content-between p-20">
                    <div class="row">
                        <div class="col-lg-10 col-10">
                            <h3 class="heading-h1 mb-3 mt-2">{{ucwords($job->title) ?? '--'}}</h4>
                            </h3>
                        </div>
                        <div class="col-lg-2 col-2 text-right">
                            @if ($editPermission == 'all'
                            || ($editPermission == 'added' && $job->added_by == user()->id)
                            || ($editPermission == 'owned' && user()->id == $job->recruiter_id)
                            || ($editPermission == 'both' && user()->id == $job->recruiter_id
                            || $job->added_by == user()->id) ||
                                ($deletePermission == 'all'
                            || ($deletePermission == 'added' && $job->added_by == user()->id)
                            || ($deletePermission == 'owned' && user()->id == $job->recruiter_id)
                            || ($deletePermission == 'both' && user()->id == $job->recruiter_id
                            || $job->added_by == user()->id)))
                                <div class="dropdown">
                                    <button
                                        class="btn btn-lg f-14 px-2 py-1 text-dark-grey  rounded  dropdown-toggle"
                                        type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-ellipsis-h"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right border-grey rounded b-shadow-4 p-0"
                                         aria-labelledby="dropdownMenuLink" tabindex="0">
                                        @if ($editPermission == 'all'
                                                || ($editPermission == 'added' && $job->added_by == user()->id)
                                                || ($editPermission == 'owned' && user()->id == $job->recruiter_id)
                                                || ($editPermission == 'both' && user()->id == $job->recruiter_id
                                                || $job->added_by == user()->id))
                                            <a class="dropdown-item openRightModal"
                                               href="{{ route('jobs.edit', $job->id) }}">@lang('app.edit')</a>
                                        @endif

                                        @if ($deletePermission == 'all'
                                                || ($deletePermission == 'added' && $job->added_by == user()->id)
                                                || ($deletePermission == 'owned' && user()->id == $job->recruiter_id)
                                                || ($deletePermission == 'both' && user()->id == $job->recruiter_id
                                                || $job->added_by == user()->id))
                                            <a class="dropdown-item delete-table-row">@lang('app.delete')</a>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="row card-body">
                    <div class="col-md-6">
                        <x-cards.data-row :label="__('app.category')"
                                          :value="$job->category ? ucwords($job->category->category_name) : '--'"/>
                        <x-cards.data-row :label="__('recruit::modules.job.subCategory')"
                                          :value="$job->subcategory ? ucwords($job->subcategory->sub_category_name) : '--'"/>
                        <x-cards.data-row :label="__('app.department')"
                                          :value="ucwords($job->team->team_name) ?? '--'"/>
                        <x-cards.data-row :label="__('recruit::modules.job.totalOpening')"
                                          :value="$job->total_positions ?? '--'"/>
                        <x-cards.data-row :label="__('recruit::modules.job.startDate')"
                                          :value="$job->start_date->format($company->date_format) ?? '--'"/>
                        <x-cards.data-row :label="__('recruit::modules.job.endDate')"
                                          :value="$job->end_date ? $job->end_date->format($company->date_format) : __('recruit::modules.job.noEndDate')"/>
                        <div class="col-12 px-0 pb-3 d-flex">
                            <p class="mb-0 text-lightest f-14 w-30 d-inline-block ">@lang('app.status')</p>
                            <p class="mb-0 text-dark-grey f-14">
                                <x-status :value="($job->status)" :color="$statusSymbol"/>
                            </p>
                        </div>

                    </div>
                    <div class="col-md-6">
                        <div class="col-12 px-0 pb-3 d-flex">
                            <p class="text-lightest f-14 w-30  ">
                                @lang('recruit::app.job.recruiter')</p>
                            <x-employee :user="$job->employee"/>
                        </div>
                        <x-cards.data-row :label="__('recruit::app.job.jobtype')"
                                          :value="$job->jobType == null ? '--' : ucWords($job->jobType->job_type)"/>
                        <x-cards.data-row :label="__('recruit::app.job.workexperience')"
                                          :value="ucwords($job->workExperience->work_experience)"/>

                        @if ($job->pay_type == 'Maximum')
                            <x-cards.data-row :label="__('recruit::app.job.paytype')"
                                              :value="__('recruit::app.job.endamt')"/>
                        @elseif ($job->pay_type == 'Starting')
                            <x-cards.data-row :label="__('recruit::app.job.paytype')"
                                              :value="__('recruit::app.job.Startingamt')"/> @elseif ($job->pay_type == 'Exact Amount')
                            <x-cards.data-row :label="__('recruit::app.job.paytype')"
                                              :value="__('recruit::app.job.exactamt')"/>
                        @elseif ($job->pay_type == 'Range')
                            <x-cards.data-row :label="__('recruit::app.job.paytype')" :value="ucwords($job->pay_type)"/>
                        @endif
                        @php
                            $type = ($job->pay_type == 'Range') ? 'start' : $job->pay_type;
                        @endphp
                        <x-cards.data-row :label="__('recruit::app.job.minsal')"
                                          :value="$currencySymbol ? currency_format($job->start_amount, $currencySymbol->id) :ucwords($job->end_amount)"/>
                        @if($job->pay_type == 'Range')
                            <x-cards.data-row :label="__('recruit::app.job.maxsal')"
                                              :value="$currencySymbol ? currency_format($job->end_amount, $currencySymbol->id) :ucwords($job->end_amount)"/>
                        @endif
                        <x-cards.data-row :label="__('recruit::app.job.payaccording')"
                                          :value="__('recruit::modules.joboffer.payAcc') .' '. ucwords($job->pay_according)"/>
                    </div>
                    <div class="col-md-12">
                        <div class="col-12 px-0 pb-3 d-lg-flex d-md-flex d-block">
                            <p class="mb-0 text-lightest f-14 w-14 ">{{ __('app.description') }}</p>
                            <div
                                class="mb-0 text-dark-grey f-14 w-70 text-wrap ql-editor p1-margin p-0 ">{!! nl2br($job->job_description ?? '--') !!}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

    $(document).ready(function () {

        $('body').on('click', '.delete-table-row', function () {
            var id = $(this).data('user-id');
            console.log(id);
            Swal.fire({
                title: "@lang('messages.sweetAlertTitle')",
                text: "@lang('messages.recoverRecord')",
                icon: 'warning',
                showCancelButton: true,
                focusConfirm: false,
                confirmButtonText: "@lang('messages.confirmDelete')",
                cancelButtonText: "@lang('app.cancel')",
                customClass: {
                    confirmButton: 'btn btn-primary mr-3',
                    cancelButton: 'btn btn-secondary'
                },
                showClass: {
                    popup: 'swal2-noanimation',
                    backdrop: 'swal2-noanimation'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    var url = "{{ route('jobs.destroy', $job->id) }}";
                    url = url.replace(':id', id);
                    var token = "{{ csrf_token() }}";
                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        blockUI: true,
                        data: {
                            '_token': token,
                            '_method': 'DELETE'
                        },
                        success: function (response) {
                            if (response.status == "success") {
                                window.location.href = response.redirectUrl;
                            }
                        }
                    });
                }
            });
        });

    });

</script>
