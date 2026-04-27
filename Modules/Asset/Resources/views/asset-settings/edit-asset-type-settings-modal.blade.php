@php
    $editAssetTypePermission = user()->permission('edit_assets_type');
@endphp

<div class="modal-header">
    <h5 class="modal-title">@lang('asset::app.editAsset')</h5>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
<x-form id="editType" method="POST" class="ajax-form">
    <div class="modal-body">
        <div class="portlet-body">
            <div class="row">

                <div class="col-sm-12">
                    <x-forms.text :fieldLabel="__('app.name')"
                                  fieldName="name"
                                  fieldId="name"
                                  fieldRequired="true"
                                  :fieldValue="$assetType->name"/>
                </div>

            </div>
        </div>
    </div>
    <div class="modal-footer">
        @if ($editAssetTypePermission == 'all' || $editAssetTypePermission == 'added')
            <x-forms.button-cancel data-dismiss="modal" class="mr-3 border-0">@lang('app.cancel')</x-forms.button-cancel>
            <x-forms.button-primary id="edit-type" icon="check">@lang('app.save')</x-forms.button-primary>
        @endif
    </div>
</x-form>


<script>
    $('#edit-type').click(function () {
        $.easyAjax({
            container: '#editType',
            type: "PUT",
            disableButton: true,
            blockUI: true,
            buttonSelector: "#save-type",
            url: "{{ route('asset-type.update', $assetType->id) }}",
            data: $('#editType').serialize(),
            success: function (response) {
                if (response.status == 'success') {
                    window.location.reload();
                }
            }
        })
    });
</script>
