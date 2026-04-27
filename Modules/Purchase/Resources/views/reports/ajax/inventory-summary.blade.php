@section('content')
<!-- CONTENT WRAPPER START -->
<div class="content-wrapper">
    <form action="" id="filter-form">
            <div class="d-flex my-3">
                <!-- DATE START -->
                <div class="py-2 px-0">
                    <div class="d-flex pr-2 border-right-grey border-right-grey-sm-0">
                        <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('app.duration')</p>
                        <div class="select-status d-flex">
                            <input type="text" class="position-relative text-dark form-control border-0 p-2 text-left f-14 f-w-500 border-additional-grey"
                                id="datatableRange" placeholder="@lang('placeholders.dateRange')">
                        </div>
                    </div>
                </div>
                <!-- DATE END -->

                <!-- RESET START -->
                <div class="py-2 px-lg-2 px-md-2 px-0">
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

    <script>
        $('#inventorysummary-table').on('preXhr.dt', function(e, settings, data) {

            var dateRangePicker = $('#datatableRange').data('daterangepicker');
            var startDate = $('#datatableRange').val();

            if (startDate == '') {
                startDate = null;
                endDate = null;
            } else {
                startDate = dateRangePicker.startDate.format('{{ company()->moment_date_format }}');
                endDate = dateRangePicker.endDate.format('{{ company()->moment_date_format }}');
            }

            data['startDate'] = startDate;
            data['endDate'] = endDate;
        });

        const showTable = () => {
        window.LaravelDataTables["inventorysummary-table"].draw(true);
        }

        $('#reset-filters,#reset-filters-2').click(function() {
            $('#filter-form')[0].reset();
            $('#reset-filters').addClass('d-none');
            showTable();
        });
    </script>
@endpush
