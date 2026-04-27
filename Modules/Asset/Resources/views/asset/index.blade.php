@extends('layouts.app')

@push('datatable-styles')
    @include('sections.datatable_css')
@endpush

@section('filter-section')

    <x-filters.filter-box>
        <!-- ASSET TYPE START -->
        <div class="select-box d-flex py-2 pr-2 border-right-grey border-right-grey-sm-0">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('asset::app.assetType')</p>
            <div class="select-status">
                <select class="form-control select-picker" name="asset_type" id="asset_type" data-live-search="true"
                        data-size="8">
                    <option value="all">@lang('app.all')</option>
                    @foreach ($assetType as $type)
                        <option value="{{ $type->id }}">{{$type->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <!-- ASSET TYPE END -->

        <!-- EMPLOYEE START -->
        <div class="select-box d-flex py-2 px-lg-3 px-md-3 px-0 border-right-grey border-right-grey-sm-0">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('asset::app.employees')</p>
            <div class="select-status">
                <select class="form-control select-picker" name="user_id" id="user_id" data-live-search="true"
                        data-size="8">

                    @if ($viewAssetPermission == 'all')
                        <option value="all">@lang('app.all')</option>
                        @foreach ($employees as $employee)
                            <x-user-option :user="$employee"></x-user-option>
                        @endforeach
                    @else
                        <x-user-option :user="user()"></x-user-option>
                    @endif
                </select>
            </div>
        </div>

        <!-- EMPLOYEE END -->

        <!-- STATUS START -->
        <div class="select-box d-flex py-2 px-lg-3 px-md-3 px-0 border-right-grey border-right-grey-sm-0">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('asset::app.status')</p>
            <div class="select-status">
                <select class="form-control select-picker" name="status" id="filter_status">
                    <option value="all">@lang('app.all')</option>
                    @foreach ($status as $type)
                        <option value="{{ $type }}">{{ __('asset::app.'.$type) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <!-- STATUS END -->


        <!-- SEARCH BY TASK START -->
        <div class="task-search d-flex  py-1 px-lg-3 px-0 border-right-grey align-items-center">
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

        <!-- RESET START -->
        <div class="select-box d-flex py-2 px-lg-3 px-md-3 px-0">
            <x-forms.button-secondary class="btn-xs d-none" id="reset-filters" icon="times-circle">
                @lang('app.clearFilters')
            </x-forms.button-secondary>
        </div>
        <!-- RESET END -->

    </x-filters.filter-box>

@endsection

@php
    $addAssetPermission = user()->permission('add_asset');
@endphp

@section('content')
    <!-- CONTENT WRAPPER START -->
    <div class="content-wrapper">
        <!-- Add Task Export Buttons Start -->
        <div class="d-flex" id="table-actions">
            @if ($addAssetPermission == 'all' || $addAssetPermission == 'added')
                <x-forms.link-primary :link="route('assets.create')" class="mr-3 openRightModal" icon="plus">
                    @lang('asset::app.addNewAsset')
                </x-forms.link-primary>
            @endif

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
        $('#assets-table').on('preXhr.dt', function (e, settings, data) {

            var asset_type = $('#asset_type').val();
            var user_id = $('#user_id').val();
            var status = $('#filter_status').val();
            var searchText = $('#search-text-field').val();
            data['asset_type'] = asset_type;
            data['user_id'] = user_id;
            data['status'] = status;
            data['searchText'] = searchText;
        });
        const showTable = () => {
            window.LaravelDataTables["assets-table"].draw(true);
        }

        $('#asset_type, #filter_status, #user_id, #search-text-field').on('change keyup',
            function () {
                if ($('#filter_status').val() != "all") {
                    $('#reset-filters').removeClass('d-none');
                    showTable();
                } else if ($('#user_id').val() != "all") {
                    $('#reset-filters').removeClass('d-none');
                    showTable();
                } else if ($('#asset_type').val() != "all") {
                    $('#reset-filters').removeClass('d-none');
                    showTable();
                } else if ($('#search-text-field').val() != "") {
                    $('#reset-filters').removeClass('d-none');
                    showTable();
                } else {
                    $('#reset-filters').addClass('d-none');
                    showTable();
                }
            });

        $('#reset-filters').click(function () {
            $('#filter-form')[0].reset();

            $('.filter-box .select-picker').selectpicker("refresh");
            $('#reset-filters').addClass('d-none');
            showTable();
        });

        $('body').on('click', '.delete-table-row', function () {
            var id = $(this).data('asset-id');
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
                    var url = "{{ route('assets.destroy', ':id') }}";
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
                                window.LaravelDataTables["assets-table"].draw(true);
                            }
                        }
                    });
                }
            });
        });

        $('body').on('click', '.lend', function () {
            let id = $(this).data('asset-id');
            let url = "{{ route('history.create', ':id') }}";
            url = url.replace(':id', id);
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });

        $('body').on('click', '.returnAsset', function () {
            let id = $(this).data('asset-id');
            let historyId = $(this).data('history-id');
            let url = "{{ route('assets.return', [':asset', ':history']) }}";
            url = url.replace(':asset', id);
            url = url.replace(':history', historyId);
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });
    </script>
@endpush
