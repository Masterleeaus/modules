@php
    $addAssetTypePermission = user()->permission('add_assets_type');
@endphp

<div class="row">
    <div class="col-sm-12">
        <x-form id="save-asset-form">
            <div class="add-client bg-white rounded">
                <h4 class="mb-0 p-20 f-21 font-weight-normal  border-bottom-grey">
                    @lang('asset::app.createTitle')</h4>
                <div class="row p-20">
                    <div class="col-lg-8 col-xl-9">
                        <div class="row">
                            <div class="col-md-6">
                                <x-forms.text fieldId="name" :fieldLabel="__('asset::app.assetName')" fieldName="name"
                                              fieldRequired="true" :fieldPlaceholder="__('asset::modules.assets.name')">
                                </x-forms.text>
                            </div>
                            <div class="col-md-6">
                                <x-forms.label class="my-3" fieldId="asset_type_id"
                                               :fieldLabel="__('asset::app.assetType')" fieldRequired="true">
                                </x-forms.label>
                                <x-forms.input-group>
                                    <select class="form-control select-picker" name="asset_type_id" id="asset_type_id"
                                            data-live-search="true">
                                        <option value="">--</option>
                                        @foreach ($assetType as $type)
                                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                                        @endforeach
                                    </select>

                                    @if ($addAssetTypePermission == 'all' || $addAssetTypePermission == 'added')
                                        <x-slot name="append">
                                            <button id="asset-type-setting" type="button"
                                                    class="btn btn-outline-secondary border-grey">@lang('app.add')</button>
                                        </x-slot>
                                    @endif
                                </x-forms.input-group>
                            </div>
                            <div class="col-md-6">
                                <x-forms.text fieldId="serial_number" :fieldLabel="__('asset::app.serialNumber')"
                                              fieldName="serial_number" fieldRequired="true"
                                              :fieldPlaceholder="__('asset::app.serialNumber')">
                                </x-forms.text>
                            </div>
                            <div class="col-md-6">
                                <x-forms.text fieldId="value" :fieldLabel="__('asset::app.value')" fieldName="value"
                                              :fieldPlaceholder="__('asset::app.value')">
                                </x-forms.text>
                            </div>
                            <div class="col-md-6">
                                <x-forms.text fieldId="location" :fieldLabel="__('asset::app.location')"
                                              fieldName="location" :fieldPlaceholder="__('asset::app.location')">
                                </x-forms.text>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-forms.label class="my-3" fieldId="status" :fieldLabel="__('asset::app.status')">
                                    </x-forms.label>
                                    <div class="d-flex">

                                        @foreach(array_diff(array_keys(\Modules\Asset\Entities\Asset::STATUSES),['lent']) as $status)
                                            <x-forms.radio :fieldId="'status-'.$status"
                                                           :fieldValue="$status"
                                                           :checked="$status === 'available'"
                                                           :fieldLabel="__('asset::app.'.$status)"
                                                           fieldName="status"/>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="col-lg-4 col-xl-3">
                            <x-forms.file allowedFileExtensions="png jpg jpeg svg" class="mr-0 mr-lg-2 mr-md-2 cropper"
                                          :fieldLabel="__('asset::app.assetPicture')" fieldName="image" fieldId="image"
                                          fieldHeight="119" :popover="__('messages.fileFormat.ImageFile')" />
                    </div>
                    <div class="col-md-12">
                        <div class="form-group my-3">
                            <x-forms.textarea class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('asset::app.description')"
                                              fieldName="description" fieldId="description"
                                              :fieldPlaceholder="__('placeholders.invoices.description')">
                            </x-forms.textarea>
                        </div>
                    </div>

                </div>

                <div class="w-100 border-top-grey d-flex justify-content-start px-4 py-3">
                    <x-forms.button-primary class="mr-3" id="save-asset" icon="check">@lang('app.save')
                    </x-forms.button-primary>

                    <x-forms.button-cancel :link="route('assets.index')" class="border-0">@lang('app.cancel')
                    </x-forms.button-cancel>
                </div>
            </div>
        </x-form>

    </div>
</div>

<script>
    $(document).ready(function () {

        $('#save-asset').click(function () {
            const url = "{{ route('assets.store') }}";

            $.easyAjax({
                url: url,
                container: '#save-asset-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                buttonSelector: "#save-asset",
                file: true,
                data: $('#save-asset-form').serialize(),
                success: function (response) {
                    if (response.status == 'success') {
                        if ($(MODAL_XL).hasClass('show')) {
                            $(MODAL_XL).modal('hide');
                            window.location.reload();
                        } else {
                            window.location.href = response.redirectUrl;
                        }
                    }
                }
            });
        });

        $('#asset-type-setting').click(function () {
            const url = "{{ route('asset-type.create') }}";
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });

        init(RIGHT_MODAL);
    });
</script>
