@extends('layouts.app')

@push('styles')
<style>
    .badge {
        display: inline;

    }
</style>
@endpush

@push('datatable-styles')
@include('sections.datatable_css')
@endpush

@section('filter-section')

<x-filters.filter-box>
    <!-- SEARCH BY TASK START -->
    <div class="task-search d-flex py-1 pr-lg-3 px-0 border-right-grey align-items-center">
        <form class="w-100 mr-1 mr-lg-0 mr-md-1 ml-md-1 ml-0 ml-lg-0">
            <div class="input-group bg-grey rounded">
                <div class="input-group-prepend">
                    <span class="input-group-text border-0 bg-additional-grey">
                        <i class="fa fa-search f-13 text-dark-grey"></i>
                    </span>
                </div>
                <input type="text" class="form-control f-14 p-1 border-additional-grey" id="search-text-field"
                    placeholder="@lang('app.startTyping')">
            </div>
        </form>
    </div>
    <!-- SEARCH BY TASK END -->

    <!-- EMPLOYEE FILTER START -->
    <div class="select-box d-flex py-2 pr-2 border-right-grey border-right-grey-sm-0">
        <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center pl-2">@lang('app.employee')</p>
        <div class="select-status">
            <select class="form-control select-picker" name="user_id" id="user_id" data-live-search="true" data-size="8">
                @if ($employees->count() > 1 || in_array('admin', user_roles()))
                    <option value="all">@lang('app.all')</option>
                @endif
                @forelse ($employees as $item)
                    <x-user-option :user="$item" :selected="request('employee_id') == $item->id"></x-user-option>
                @empty
                    <x-user-option :user="user()"></x-user-option>
                @endforelse
            </select>
        </div>
    </div>
    <!-- EMPLOYEE FILTER END -->

    <!-- MONTH FILTER START -->
    <div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
        <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('app.month')</p>
        <div class="select-status">
            <select class="form-control select-picker" name="month" id="month" data-live-search="true" data-size="8">
                <x-forms.months :selectedMonth="$month" fieldRequired="true"/>
            </select>
        </div>
    </div>
    <!-- MONTH FILTER END -->

    <!-- YEAR FILTER START -->
    <div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
        <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('app.year')</p>
        <div class="select-status">
            <select class="form-control select-picker" name="year" id="year" data-live-search="true" data-size="8">
                @for ($i = $year; $i >= $year - 4; $i--)
                    <option @if ($i == $year) selected @endif value="{{ $i }}">{{ $i }}</option>
                @endfor
            </select>
        </div>
    </div>
    <!-- YEAR FILTER END -->

    <!-- DATE FILTER START -->
     <!-- DATE START -->
     <div class="select-box d-flex pr-2 border-right-grey border-right-grey-sm-0">
        <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('app.date')</p>
        <div class="select-status d-flex">
            <input type="text" class="position-relative text-dark form-control border-0 p-2 text-left f-14 f-w-500 border-additional-grey"
                id="datatableRange" placeholder="@lang('placeholders.dateRange')">
        </div>
    </div>
    <!-- DATE FILTER END -->

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
    <div class="d-flex">
        <div id="table-actions" class="flex-grow-1 align-items-center">

        </div>
    </div>

    <!-- Add Task Export Buttons End -->
    <!-- Task Box Start -->
    <div class="d-flex flex-column w-tables rounded mt-3 bg-white">

        {!! $dataTable->table(['class' => 'table table-hover border-0 w-100']) !!}

    </div>
    <!-- Task Box End -->
</div>
<!-- CONTENT WRAPPER END -->

@endsection

@push('scripts')
@include('sections.datatable_js')

<script>
    
    "use strict"; // Enforces strict mode for the entire script
    $('#biometric-attendance-table').on('preXhr.dt', function(e, settings, data) {

        const dateRangePicker = $('#datatableRange').data('daterangepicker');
            let startDate = $('#datatableRange').val();

        let endDate;

        if (startDate == '') {
            startDate = null;
            endDate = null;
        } else {
            startDate = dateRangePicker.startDate.format('{{ company()->moment_date_format }}');
            endDate = dateRangePicker.endDate.format('{{ company()->moment_date_format }}');
        }

        var searchText = $('#search-text-field').val();
        var userId = $('#user_id').val();
        var department = $('#department').val();
        var designation = $('#designation').val();
        var month = $('#month').val();
        var year = $('#year').val();

        data['startDate'] = startDate;
        data['endDate'] = endDate;
        data['searchText'] = searchText;
        data['user_id'] = userId;
        data['department'] = department;
        data['designation'] = designation;
        data['month'] = month;
        data['year'] = year;
    });

    const showTable = () => {
        window.LaravelDataTables["biometric-attendance-table"].draw(true);
    }

    $('#search-text-field, #user_id, #department, #designation, #month, #year, #date_filter')
        .on('change keyup',
            function() {
                if ($('#search-text-field').val() != "" || $('#user_id').val() != "all" ||
                    $('#department').val() != "all" || $('#designation').val() != "all" ||
                    $('#month').val() != "" || $('#year').val() != "" || $('#date_filter').val() != "") {
                    $('#reset-filters').removeClass('d-none');
                    showTable();
                } else {
                    $('#reset-filters').addClass('d-none');
                    showTable();
                }
            });

    $('body').on('click', '#reset-filters', function() {
        $('#filter-form')[0].reset();
        $('#search-text-field').val('');
        $('#user_id').val('all');
        $('#department').val('all');
        $('#designation').val('all');
        $('#month').val('');
        $('#year').val('');
        $('#date_filter').val('');

        $('.filter-box .select-picker').selectpicker("refresh");
        $('#reset-filters').addClass('d-none');
        showTable();
    });

    $('body').on('click', '#reset-filters-2', function() {
        $('#filter-form')[0].reset();
        $('#search-text-field').val('');
        $('#user_id').val('all');
        $('#department').val('all');
        $('#designation').val('all');
        $('#month').val('');
        $('#year').val('');
        $('#date_filter').val('');

        $('.filter-box .select-picker').selectpicker("refresh");
        $('#reset-filters').addClass('d-none');
        showTable();
    });
</script>
@endpush
