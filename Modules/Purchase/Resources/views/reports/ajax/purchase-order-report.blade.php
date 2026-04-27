@section('content')
<!-- CONTENT WRAPPER START -->
<div class="content-wrapper">
    <form action="" id="filter-form">
            <div class="d-flex ">
                <!-- DATE START -->
                <div class="py-2 px-0">
                    <div class="d-flex pr-2 border-right-grey border-right-grey-sm-0">
                        <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('app.duration')</p>
                        <div class="select-status d-flex">
                            <input type="text" class="position-relative text-dark form-control border-0 p-2 text-left f-14 f-w-500 border-additional-grey"
                                id="datatableRange2" placeholder="@lang('placeholders.dateRange')">
                        </div>
                    </div>
                </div>
                <!-- DATE END -->
                <div class="d-flex  py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
                    <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('purchase::app.vendor')</p>
                    <div class="select-status d-flex">
                        <select class="form-control select-picker" name="vendor" id="vendor">
                            <option value="all">@lang('app.all')</option>
                            @foreach ($vendors as $vendor)
                                <option value="{{$vendor->id}}">{{$vendor->primary_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <!-- RESET START -->
                <div class="py-2 px-lg-2 px-md-2 px-0 h-25">
                    <x-forms.button-secondary class="btn-xs d-none" id="reset-filters" icon="times-circle">
                        @lang('app.clearFilters')
                    </x-forms.button-secondary>
                </div>
                <!-- RESET END -->
            </div>
    </form>
    <!-- Task Box Start -->
    <div class="d-flex flex-column w-tables rounded mt-4 bg-white">

        {!! $dataTable->table(['class' => 'table table-hover border-0 w-100']) !!}

    </div>
    <!-- Task Box End -->
</div>
<!-- CONTENT WRAPPER END -->

@endsection

@push('scripts')
    @include('sections.datatable_js')


    <script type="text/javascript">

    function initializeDateRangePicker() {
        var start = moment().clone().startOf('month');
        var end = moment();

        $('#datatableRange2').daterangepicker({
            locale: daterangeLocale,
            linkedCalendars: false,
            startDate: start,
            endDate: end,
            ranges: daterangeConfig
        }, function(start, end, label) {
            showTable(start.format('{{ company()->moment_date_format }}'), end.format('{{ company()->moment_date_format }}'));
            toggleClearButtonVisibility();

                $('#reset-filters').removeClass('d-none');

        });
    }

    function toggleClearButtonVisibility() {
        var startDate = $('#datatableRange2').data('daterangepicker').startDate;
        var endDate = $('#datatableRange2').data('daterangepicker').endDate;
        if (startDate && endDate) {
            $('#clear-button').removeClass('d-none');
        } else {
            $('#clear-button').addClass('d-none');
        }
    }

    $(function() {
        initializeDateRangePicker();

        $('#purchase-order-report-table').on('preXhr.dt', function(e, settings, data) {
            var startDate = $('#datatableRange2').data('daterangepicker').startDate;
            var endDate = $('#datatableRange2').data('daterangepicker').endDate;
            var vendor = $('#vendor').val();

            data['startDate'] = startDate.format('{{ company()->moment_date_format }}');
            data['endDate'] = endDate.format('{{ company()->moment_date_format }}');
            data['vendor'] = vendor;
        });

        $('#vendor').on('change keyup', function() {
            var vendor = $(this).val();
            if (vendor !== "all") {
                $('#reset-filters').removeClass('d-none');
            } else {
                $('#reset-filters').addClass('d-none');
            }
            showTable();
            toggleClearButtonVisibility();
        });

        $('#reset-filters').click(function() {
            $('#filter-form')[0].reset();
            $('#vendor').val('all').trigger('change');
            $('.select-picker').selectpicker("refresh");
            $('#reset-filters').addClass('d-none');
            initializeDateRangePicker();
            showTable();
            toggleClearButtonVisibility();
        });

        $('#reset-filters-2').click(function() {
            $('#filter-form')[0].reset();
            $('.filter-box .select-picker').selectpicker("refresh");
            $('#reset-filters').addClass('d-none');
            initializeDateRangePicker();
            showTable();
            toggleClearButtonVisibility();
        });

        $('#clear-button').click(function() {
            $('#filter-form')[0].reset();
            $('#vendor').val('all').trigger('change');
            $('#datatableRange2').data('daterangepicker').setStartDate(moment().clone().startOf('month'));
            $('#datatableRange2').data('daterangepicker').setEndDate(moment());
            $('.select-picker').selectpicker("refresh");
            $('#reset-filters').addClass('d-none');
            showTable();
            initializeDateRangePicker();
            toggleClearButtonVisibility();
        });
    });

    function showTable() {
        window.LaravelDataTables["purchase-order-report-table"].draw(true);
    }

    function showTable(startDate, endDate) {
        window.LaravelDataTables["purchase-order-report-table"].draw(true);
    }
    </script>

@endpush
