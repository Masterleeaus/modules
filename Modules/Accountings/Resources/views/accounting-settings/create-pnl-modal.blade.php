@php($pageTitle = $pageTitle ?? __('accountings::app.menu.accounting'))
<div class="modal-header">
    <h5 class="modal-title" id="modelHeading">@lang('app.add') @lang('accountings::app.menu.pnl')</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
</div>
<div class="modal-body">
    <div class="portlet-body">
        <x-table class="table-bordered" headType="thead-light">
            <x-slot name="thead">
                <th>#</th>
                <th>@lang('accountings::app.menu.seq')</th>
                <th>@lang('accountings::app.menu.pnlName')</th>
                <th>@lang('accountings::app.menu.pnlType')</th>
                <th>@lang('accountings::app.menu.pnlGroup')</th>
                <th class="text-right">@lang('app.action')</th>
            </x-slot>

            @forelse($pnl as $key => $items)
                <tr id="cat-{{ $items->id }}">
                    <td>{{ $key + 1 }}</td>
                    <td data-row-id="{{ $items->id }}" contenteditable="true" class="edit-seq">
                        {{ mb_ucwords($items->seq) }}</td>

                    <td data-row-id="{{ $items->id }}" contenteditable="true" class="edit-name">
                        {{ mb_ucwords($items->pnl_name) }}</td>
                    <td>
                        <select class="form-control select-picker height-35 f-14" name="pnlType" id="edit_type"
                            data-live-search="true" data-row-id="{{ $items->id }}">
                            <option value="{{ $items->pnl_type }}"> {{ $items->pnl_type }}</option>
                            <option value="aktiva"> --</option>
                            <option value="income"> Income</option>
                            <option value="expense"> Expense</option>
                            <option value="other-income"> Other Income</option>
                            <option value="other-expense"> Other Expense</option>
                        </select>
                    </td>
                    <td>
                        <select class="form-control select-picker height-35 f-14" name="pnlType" id="edit_group"
                            data-live-search="true" data-row-id="{{ $items->id }}">
                            <option value="{{ $items->pnl_group }}"> {{ $items->pnl_group }}</option>
                            <option value="aktiva"> --</option>
                            <option value="revenue"> Revenue</option>
                            <option value="operational-expense"> Operational Expense</option>
                            <option value="other-income"> Other Income</option>
                            <option value="other-expense"> Other Expense</option>
                        </select>
                    </td>
                    <td class="text-right">
                        <x-forms.button-secondary data-cat-id="{{ $items->id }}" icon="trash"
                            class="delete-category">
                            @lang('app.delete')
                        </x-forms.button-secondary>
                    </td>
                </tr>
            @empty
                <x-cards.no-record-found-list colspan="6" />
            @endforelse
        </x-table>
        <x-form id="addTicketChannel" method="POST" class="ajax-form">
            <div class="form-body">
                <div class="row">
                    <div class="col-lg-6">
                        <x-forms.number fieldId="seq" :fieldLabel="__('accountings::app.menu.seq')" fieldName="seq" fieldRequired="true"
                            fieldPlaceholder="e.g. 01, 02, etc.">
                        </x-forms.number>
                    </div>
                    <div class="col-md-6">
                        <x-forms.text fieldId="pnl_name" :fieldLabel="__('accountings::app.menu.pnlName')" fieldName="pnl_name" fieldRequired="true"
                            :fieldPlaceholder="__('')">
                        </x-forms.text>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <x-forms.label class="mt-3" fieldId="parent_label" :fieldLabel="__('accountings::app.menu.pnlType')" fieldName="parent_label">
                        </x-forms.label>
                        <x-forms.input-group>
                            <select class="form-control select-picker height-35 f-14" name="pnl_type" id="pnl_type">
                                <option value="income"> Income</option>
                                <option value="expense"> Expense</option>
                                <option value="other-income"> Other Income</option>
                                <option value="other-expense"> Other Expense</option>
                            </select>
                        </x-forms.input-group>
                    </div>
                    <div class="col-md-6">
                        <x-forms.label class="mt-3" fieldId="parent_label" :fieldLabel="__('accountings::app.menu.pnlGroup')" fieldName="parent_label">
                        </x-forms.label>
                        <x-forms.input-group>
                            <select class="form-control select-picker height-35 f-14" name="pnl_group" id="pnl_group">
                                <option value="revenue"> Revenue</option>
                                <option value="operational-expense"> Operational Expense</option>
                                <option value="other-income"> Other Income</option>
                                <option value="other-expense"> Other Expense</option>
                            </select>
                        </x-forms.input-group>
                    </div>
                </div>
            </div>
        </x-form>
    </div>
</div>
<div class="modal-footer">
    <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.close')</x-forms.button-cancel>
    <x-forms.button-primary id="save-ticket-floor" icon="check">@lang('app.save')</x-forms.button-primary>
</div>

<script>
    // save floor
    $('#save-ticket-floor').click(function() {
        $.easyAjax({
            url: "{{ route('pnl.store') }}",
            container: '#addTicketChannel',
            type: "POST",
            blockUI: true,
            data: $('#addTicketChannel').serialize(),
            success: function(response) {
                if (response.status == "success") {
                    if ($('#ticket_floor_id').length > 0) {
                        $('#ticket_floor_id').html(response.optionData);
                        $('#ticket_floor_id').selectpicker('refresh');
                        $(MODAL_LG).modal('hide');
                    } else {
                        window.location.reload();
                    }
                }
            }
        })
    });

    init(MODAL_LG);

    $('.delete-category').click(function() {
        const id = $(this).data('cat-id');
        let url = "{{ route('pnl.destroy', ':id') }}";
        url = url.replace(':id', id);

        const token = "{{ csrf_token() }}";
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
                $.easyAjax({
                    type: 'POST',
                    url: url,
                    data: {
                        '_token': token,
                        '_method': 'DELETE'
                    },
                    success: function(response) {
                        if (response.status == "success") {
                            $('#cat-' + id).fadeOut();
                        }
                    }
                });
            }
        });

    });

    $('.edit-seq').focus(function() {
        $(this).data("initialText", $(this).html());
    }).blur(function() {
        if ($(this).data("initialText") !== $(this).html()) {
            let id = $(this).data('row-id');
            let value = $(this).html();
            let url = "{{ route('pnl.update', ':id') }}";
            url = url.replace(':id', id);

            const token = "{{ csrf_token() }}";
            $.easyAjax({
                url: url,
                container: '#row-' + id,
                type: "POST",
                data: {
                    'seq': value,
                    '_token': token,
                    '_method': 'PUT'
                },
                blockUI: true,
            })
        }
    });

    $('.edit-name').focus(function() {
        $(this).data("initialText", $(this).html());
    }).blur(function() {
        if ($(this).data("initialText") !== $(this).html()) {
            let id = $(this).data('row-id');
            let value = $(this).html();
            let url = "{{ route('pnl.update', ':id') }}";
            url = url.replace(':id', id);

            const token = "{{ csrf_token() }}";
            $.easyAjax({
                url: url,
                container: '#row-' + id,
                type: "POST",
                data: {
                    'pnl_name': value,
                    '_token': token,
                    '_method': 'PUT'
                },
                blockUI: true,
            })
        }
    });

    $(document).on('change', '#edit_type', function() {
        const id = $(this).data('row-id');
        const categoryId = $(this).val();
        if (id == undefined) {
            return false;
        }
        let url = "{{ route('pnl.update', ':id') }}";
        url = url.replace(':id', id);
        const token = "{{ csrf_token() }}";
        $.easyAjax({
            url: url,
            type: "POST",
            data: {
                'pnl_type': categoryId,
                '_token': token,
                '_method': 'PUT'
            },
            blockUI: true,
        })
    });

    $(document).on('change', '#edit_group', function() {
        const id = $(this).data('row-id');
        const categoryId = $(this).val();
        if (id == undefined) {
            return false;
        }
        let url = "{{ route('pnl.update', ':id') }}";
        url = url.replace(':id', id);
        const token = "{{ csrf_token() }}";
        $.easyAjax({
            url: url,
            type: "POST",
            data: {
                'pnl_group': categoryId,
                '_token': token,
                '_method': 'PUT'
            },
            blockUI: true,
        })
    });
</script>
