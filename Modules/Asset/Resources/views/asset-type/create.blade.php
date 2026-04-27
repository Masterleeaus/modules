@php
    $deleteAssetTypePermission = user()->permission('delete_assets_type');
    $editAssetTypePermission = user()->permission('edit_assets_type');
@endphp

<div class="modal-header">
    <h5 class="modal-title" id="modelHeading">@lang('asset::app.assetType')</h5>
    <button type="button" onclick="removeOpenModal()" class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">×</span></button>
</div>
<div class="modal-body">
    <x-table class="table-bordered" headType="thead-light">
        <x-slot name="thead">
            <th>#</th>
            <th class="w-75">@lang('app.name')</th>
            <th class="text-right">@lang('app.action')</th>
        </x-slot>

        @forelse($assetTypes as $key=>$type)
            <tr id="row-{{ $type->id }}">
                <td>{{ $key + 1 }}</td>
                <td data-row-id="{{ $type->id }}"
                    @if($editAssetTypePermission == 'all' || $editAssetTypePermission == 'added')
                    contenteditable="true"
                    @endif
                >{{ $type->name }}
                </td>
                <td class="text-right">
                    @if ($deleteAssetTypePermission == 'all' || $deleteAssetTypePermission == 'added')
                        <x-forms.button-secondary data-row-id="{{ $type->id }}" icon="trash" class="delete-row">
                            @lang('app.delete')</x-forms.button-secondary>
                @endif
            </tr>
        @empty
            <tr>
                <td colspan="3">@lang('asset::app.noAssetType')</td>
            </tr>
        @endforelse
    </x-table>

    <x-form id="create-asset-type">
        <div class="row border-top-grey ">
            <div class="col-sm-12">
                <x-forms.text fieldId="name" :fieldLabel="__('app.name')" fieldName="name"
                              fieldRequired="true" :fieldPlaceholder="__('app.name')">
                </x-forms.text>
            </div>

        </div>
    </x-form>
</div>
<div class="modal-footer">
    <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.close')</x-forms.button-cancel>
    <x-forms.button-primary id="save-asset-type" icon="check">@lang('app.save')</x-forms.button-primary>
</div>

<script>
    $('.delete-row').click(function () {

        var id = $(this).data('row-id');
        var url = "{{ route('asset-type.destroy', ':id') }}";
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
                    blockUI: true,
                    data: {
                        '_token': token,
                        '_method': 'DELETE'
                    },
                    success: function (response) {
                        if (response.status == "success") {
                            $('#row-' + id).fadeOut();
                            $('#asset_type_id').html(response.data);
                            $('#asset_type_id').selectpicker('refresh');
                        }
                    }
                });
            }
        });

    });

    $('#save-asset-type').click(function () {
        var url = "{{ route('asset-type.store') }}";
        $.easyAjax({
            url: url,
            container: '#create-asset-type',
            type: "POST",
            data: $('#create-asset-type').serialize(),
            disableButton: true,
            blockUI: true,
            buttonSelector: "#save-asset-type",
            success: function (response) {
                if (response.status == 'success') {
                    if (response.status == 'success') {
                        $('#asset_type_id').html(response.data);
                        $('#asset_type_id').selectpicker('refresh');
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

            var url = "{{ route('asset-type.update', ':id') }}";
            url = url.replace(':id', id);

            var token = "{{ csrf_token() }}";

            $.easyAjax({
                url: url,
                container: '#row-' + id,
                type: "POST",
                data: {
                    'name': value,
                    '_token': token,
                    '_method': 'PUT'
                },
                blockUI: true,
                success: function (response) {
                    if (response.status == 'success') {
                        $('#asset_type_id').html(response.data);
                        $('#asset_type_id').selectpicker('refresh');
                    }
                }
            })
        }
    });

</script>
