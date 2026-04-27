@php
    $addAssetTypePermission = user()->permission('add_assets_type');
@endphp

<div class="modal-header">
    <h5 class="modal-title">@lang('asset::app.addAssetType')</h5>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
<x-form id="create-asset-type" method="POST" class="ajax-form">
    <div class="modal-body">
        <div class="row">
            <div class="col-sm-12">
                <x-forms.text fieldId="name" :fieldLabel="__('app.name')" fieldName="name"
                              fieldRequired="true" :fieldPlaceholder="__('app.name')">
                </x-forms.text>
            </div>

        </div>
    </div>
    <div class="modal-footer">
        @if ($addAssetTypePermission == 'all' || $addAssetTypePermission == 'added')
            <x-forms.button-cancel data-dismiss="modal" class="mr-3 border-0">@lang('app.close')</x-forms.button-cancel>
            <x-forms.button-primary id="save-asset-type" icon="check">@lang('app.save')</x-forms.button-primary>
        @endif
    </div>
</x-form>


<script>

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
                    $('#asset_type_id').html(response.data);
                    $('#asset_type_id').selectpicker('refresh');
                    $(MODAL_LG).modal('hide');
                    window.location.reload();
                }
            }
        })
    });
</script>
