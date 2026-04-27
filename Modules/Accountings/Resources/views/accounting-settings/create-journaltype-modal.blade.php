@php($pageTitle = $pageTitle ?? __('accountings::app.menu.accounting'))
<div class="modal-header">
    <h5 class="modal-title" id="modelHeading">@lang('app.add') @lang('accountings::app.menu.journalType')</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
</div>
<div class="modal-body">
    <div class="portlet-body">
        <x-table class="table-bordered" headType="thead-light">
            <x-slot name="thead">
                <th>#</th>
                <th>@lang('accountings::app.menu.journalCode')</th>
                <th>@lang('accountings::app.menu.journalType')</th>
                <th class="text-right">@lang('app.action')</th>
            </x-slot>

            @forelse($journaltype as $key => $jt)
                <tr id="cat-{{ $jt->id }}">
                    <td>{{ $key + 1 }}</td>
                    <td data-row-id="{{ $jt->id }}" contenteditable="true" class="edit-code">
                        {{ mb_ucwords($jt->type_journal_code) }}</td>

                    <td data-row-id="{{ $jt->id }}" contenteditable="true" class="edit-type">
                        {{ mb_ucwords($jt->type_journal) }}</td>
                    <td class="text-right">
                        <x-forms.button-secondary data-cat-id="{{ $jt->id }}" icon="trash"
                            class="delete-category">
                            @lang('app.delete')
                        </x-forms.button-secondary>
                    </td>
                </tr>
            @empty
                <x-cards.no-record-found-list colspan="4" />
            @endforelse
        </x-table>
        <x-form id="addTicketChannel" method="POST" class="ajax-form">
            <div class="form-body">
                <div class="row">
                    <div class="col-lg-6">
                        <x-forms.text fieldId="type_journal_code" :fieldLabel="__('accountings::app.menu.journalCode')" fieldName="type_journal_code" fieldRequired="true"
                            fieldPlaceholder="">
                        </x-forms.number>
                    </div>
                    <div class="col-md-6">
                        <x-forms.text fieldId="type_journal" :fieldLabel="__('accountings::app.menu.journalType')" fieldName="type_journal" fieldRequired="true"
                            :fieldPlaceholder="__('')">
                        </x-forms.text>
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
            url: "{{ route('journal-type.store') }}",
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
        let url = "{{ route('journal-type.destroy', ':id') }}";
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

    $('.edit-code').focus(function() {
        $(this).data("initialText", $(this).html());
    }).blur(function() {
        if ($(this).data("initialText") !== $(this).html()) {
            let id = $(this).data('row-id');
            let value = $(this).html();
            let url = "{{ route('journal-type.update', ':id') }}";
            url = url.replace(':id', id);

            const token = "{{ csrf_token() }}";
            $.easyAjax({
                url: url,
                container: '#row-' + id,
                type: "POST",
                data: {
                    'type_journal_code': value,
                    '_token': token,
                    '_method': 'PUT'
                },
                blockUI: true,
            })
        }
    });

    $('.edit-type').focus(function() {
        $(this).data("initialText", $(this).html());
    }).blur(function() {
        if ($(this).data("initialText") !== $(this).html()) {
            let id = $(this).data('row-id');
            let value = $(this).html();
            let url = "{{ route('journal-type.update', ':id') }}";
            url = url.replace(':id', id);

            const token = "{{ csrf_token() }}";
            $.easyAjax({
                url: url,
                container: '#row-' + id,
                type: "POST",
                data: {
                    'type_journal': value,
                    '_token': token,
                    '_method': 'PUT'
                },
                blockUI: true,
            })
        }
    });

</script>
