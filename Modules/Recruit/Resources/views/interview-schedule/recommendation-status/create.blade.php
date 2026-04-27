@php
    $deletePermission = user()->permission('delete_recommendation_status');
@endphp
<div class="modal-header">
    <h5 class="modal-title" id="modelHeading">@lang('recruit::modules.jobApplication.status')</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">×</span></button>
</div>
<div class="modal-body">
    <x-table class="table-bordered" headType="thead-light">
        <x-slot name="thead">
            <th>#</th>
            <th class="w-75">@lang('recruit::modules.jobApplication.status')</th>
            <th class="text-right">@lang('app.action')</th>
        </x-slot>

        @forelse($statuses as $key=>$item)
            <tr id="row-{{ $item->id }}">
                <td>{{ $key + 1 }}</td>
                <td data-row-id="{{ $item->id }}" contenteditable="true">{{ $item->status }}</td>
                <td class="text-right">
                    @if ($deletePermission == 'all')
                        <x-forms.button-secondary data-row-id="{{ $item->id }}" icon="trash" class="delete-row">
                            @lang('app.delete')</x-forms.button-secondary>
                @endif
            </tr>
        @empty
            <x-cards.no-record-found-list />
        @endforelse
    </x-table>

    <x-form id="createRecommStatus">
        <div class="row border-top-grey ">
            <div class="col-sm-12">
                <x-forms.text fieldId="status" :fieldLabel="__('recruit::modules.jobApplication.status')"
                              fieldName="status"
                              fieldRequired="true"
                              :fieldPlaceholder="__('recruit::app.interviewSchedule.statusPlaceholder')">
                </x-forms.text>
            </div>
        </div>
    </x-form>
</div>
<div class="modal-footer">
    <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.close')</x-forms.button-cancel>
    <x-forms.button-primary id="save-status" icon="check">@lang('app.save')</x-forms.button-primary>
</div>

<script>
    $('body').on('click', '.delete-row', function () {
        var id = $(this).data('row-id');
        var url = "{{ route('recommendation-status.destroy', ':id') }}";
        url = url.replace(':id', id);

        var token = "{{ csrf_token() }}";

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
                    success: function (response) {
                        if (response.status == "success") {
                            $('#row-' + id).fadeOut();
                            $('#recomm_status').html(response.data);
                            $('#recomm_status').selectpicker('refresh');
                        }
                    }
                });
            }
        });

    });

    $('body').off('click', "#save-status").on('click', '#save-status', function () {

        var url = "{{ route('recommendation-status.store') }}";
        $.easyAjax({
            url: url,
            container: '#createRecommStatus',
            type: "POST",
            data: $('#createRecommStatus').serialize(),
            disableButton: true,
            blockUI: true,
            buttonSelector: "#save-status",
            success: function (response) {
                if (response.status == 'success') {
                    if (response.status == 'success') {
                        $('#recomm_status').html(response.data);
                        $('#recomm_status').selectpicker('refresh');
                        $(MODAL_LG).modal('hide');
                    }
                }
            }
        })
    });


    $('[contenteditable=true]').focus(function () {
        $(this).data("initialText", $(this).html());
        let rowId = $(this).data('row-id');
    }).blur(function () {
        if ($(this).data("initialText") !== $(this).html()) {
            let id = $(this).data('row-id');
            let value = $(this).html();

            var url = "{{ route('recommendation-status.update', ':id') }}";
            url = url.replace(':id', id);

            var token = "{{ csrf_token() }}";

            $.easyAjax({
                url: url,
                container: '#row-' + id,
                type: "POST",
                data: {
                    'status': value,
                    '_token': token,
                    '_method': 'PUT'
                },
                blockUI: true,
                success: function (response) {
                    if (response.status == 'success') {
                        $('#recomm_status').html(response.data);
                        $('#recomm_status').selectpicker('refresh');
                    }
                }
            })
        }
    });

</script>
