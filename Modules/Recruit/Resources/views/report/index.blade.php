@extends('layouts.app')

@push('datatable-styles')
    @include('sections.daterange_css')
    <script src="{{ asset('vendor/jquery/frappe-charts.min.iife.js') }}"></script>
    <script src="{{ asset('vendor/jquery/Chart.min.js') }}"></script>
@endpush

@section('filter-section')
    <x-filters.filter-box>
        <!-- DATE START -->
        <div class="select-box d-flex pr-2 border-right-grey border-right-grey-sm-0">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('app.duration')</p>
            <div class="select-status d-flex">
                <input type="text" class="position-relative text-dark form-control border-0 p-2 text-left f-14 f-w-500 border-additional-grey"
                       id="datatableRange2" placeholder="@lang('placeholders.dateRange')">
            </div>
        </div>
        <!-- DATE END -->

        <!-- RESET START -->
        <div class="select-box d-flex py-1 px-lg-2 px-md-2 px-0">
            <x-forms.button-secondary class="btn-xs d-none" id="reset-filters" icon="times-circle">
                @lang('app.clearFilters')
            </x-forms.button-secondary>
        </div>
        <!-- RESET END -->

    </x-filters.filter-box>
@endsection
@section('content')
    <!-- CONTENT WRAPPER START -->
    <div class="content-wrapper">
        <!-- Add Task Export Buttons Start -->
        <div class="d-flex flex-column">
            <div class="row mb-4">
                <div class="col-lg-3">
                    <a href="{{ route('job-appboard.index') }}" data-status="pending" class="widget-filter-status">
                        <x-cards.widget :title="__('recruit::app.report.jobapplication')" value="{{ $jobApplication }}"
                                        icon="coins" widgetId="jobApp"/>
                    </a>
                </div>

                <div class="col-lg-3">
                    <a href="{{ route('jobs.index') }}" data-status="pending" class="widget-filter-status">
                        <x-cards.widget :title="__('recruit::app.report.jobposted')" value="{{ $job }}"
                                        icon="coins" widgetId="jobPosted"/>
                    </a>
                </div>
                <div class="col-lg-3">
                    <a href="{{ route('job-appboard.index') }}" data-status="pending" class="widget-filter-status">
                        <x-cards.widget :title="__('recruit::app.report.candidatehired')" value="{{ $candidatesHired }}"
                                        icon="coins" widgetId="candidateHired"/>
                    </a>
                </div>
                <div class="col-lg-3">
                    <a href="{{ route('interview-schedule.index') }}" data-status="pending"
                       class="widget-filter-status">
                        <x-cards.widget :title="__('recruit::app.report.interviewschedule')"
                                        value="{{ $interviewScheduled }}" icon="coins" widgetId="interview"/>
                    </a>
                </div>
            </div>

            <!-- TASK STATUS START -->
            <x-cards.data id="task-chart-card" :title="__('app.menu.tasks')" padding="false" style="min-height: 400px;">
            </x-cards.data>
            <!-- TASK STATUS END -->

            <div id="table-actions" class="flex-grow-1 align-items-center mt-4">
            </div>

        </div>

    </div>
    <!-- CONTENT WRAPPER END -->
@endsection

@push('scripts')
    @include('sections.daterange_js')

    <script type="text/javascript">
        $(function () {

            var start = moment().clone().startOf('month');
            var end = moment();

            function cb(start, end) {
                $('#datatableRange2').val(start.format('{{ $company->moment_format }}') +
                    ' @lang("app.to") ' + end.format(
                        '{{ $company->moment_format }}'));
                $('#reset-filters').removeClass('d-none');
            }

            $('#datatableRange2').daterangepicker({
                locale: daterangeLocale,
                linkedCalendars: false,
                startDate: start,
                endDate: end,
                ranges: daterangeConfig
            }, cb);


            $('#datatableRange2').on('apply.daterangepicker', function (ev, picker) {
                pieChart();
            });


            $('body').on('click', '#reset-filters', function () {
                $('#filter-form')[0].reset();

                $('.filter-box .select-picker').selectpicker("refresh");
                $('#reset-filters').addClass('d-none');
                pieChart();
            });

            function pieChart() {

                const dateRangePicker = $('#datatableRange2').data('daterangepicker');
                let startDate = $('#datatableRange2').val();

                let endDate;

                if (startDate == '') {
                    startDate = null;
                    endDate = null;
                } else {
                    startDate = dateRangePicker.startDate.format('{{ $company->moment_format }}');
                    endDate = dateRangePicker.endDate.format('{{ $company->moment_format }}');
                }

                var url = "{{ route('jobreport.chart') }}";

                $.easyAjax({
                    url: url,
                    container: '#task-chart-card',
                    blockUI: true,
                    type: "POST",
                    data: {
                        _token: '{{ csrf_token() }}',
                        startDate: startDate,
                        endDate: endDate
                    },
                    success: function (response) {
                        $('#task-chart-card').html(response.html);
                        $('#jobApp').html(response.jobApp);
                        $('#jobPosted').html(response.jobPosted);
                        $('#candidateHired').html(response.candidateHired);
                        $('#interview').html(response.interview);
                        console.log(response);
                    }
                });
            }

            @if (request('start') && request('end'))
            $('#datatableRange2').data('daterangepicker').setStartDate("{{ request('start') }}");
            $('#datatableRange2').data('daterangepicker').setEndDate("{{ request('end') }}");
            @endif


            pieChart();
        });
    </script>

@endpush
