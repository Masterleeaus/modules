<link rel="stylesheet" href="{{ asset('vendor/css/dropzone.min.css') }}">

<div class="row">
    <div class="col-sm-12">
        <x-form id="save-inventory-form">

            <div class="add-client bg-white rounded">
                <h4 class="mb-0 p-20 f-21 font-weight-normal  border-bottom-grey">
                    @lang('purchase::app.addInventory')</h4>
                <div class="row p-20">
                    <div class="col-lg-12">

                        <div class="row">
                            <div class="col-md-3">
                                <x-forms.label class="my-3" fieldId="" :fieldLabel="__('purchase::modules.product.modeOfAdjustment')">
                                </x-forms.label><sup class="text-red f-14 mr-1">*</sup>
                                <div class="form-group">
                                    <div class="d-flex">
                                        <x-forms.radio class="quantity mode_of_adjustment" fieldId="quantity" :fieldLabel="__('purchase::modules.product.quantityAdjustment')"
                                            fieldValue="quantity" fieldName="type" :checked="true"></x-forms.radio>

                                        <x-forms.radio class="value mode_of_adjustment" fieldId="value" :fieldLabel="__('purchase::modules.product.valueAdjustment')"
                                            fieldValue="value" fieldName="type"></x-forms.radio>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-4">
                                <x-forms.text fieldId="reference_number" :fieldLabel="__('purchase::modules.product.referenceNumber')" fieldName="reference_number">
                                </x-forms.text>
                            </div>

                            <div class="col-md-4">
                                <x-forms.text :fieldLabel="__('app.date')" fieldName="date" fieldId="date" :fieldPlaceholder="__('app.date')"
                                    :fieldValue="now(company()->timezone)->translatedFormat(company()->date_format)" fieldRequired />
                            </div>

                            <div class="col-md-4">
                                <x-forms.label class="mt-3" fieldId="reason" :fieldLabel="__('purchase::modules.product.reason')" fieldRequired>
                                </x-forms.label>
                                <x-forms.input-group>
                                    <select class="form-control select-picker" name="reason_id"
                                        id="adjustment_reason_id" data-live-search="true">
                                        <option value="">--</option>
                                        @foreach ($reasons as $reason)
                                            <option value="{{ $reason->id }}">
                                                {{ mb_ucwords($reason->name) }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <x-slot name="append">
                                        <button id="addReason" type="button"
                                            class="btn btn-outline-secondary border-grey" data-toggle="tooltip"
                                            data-original-title="{{ __('purchase::modules.inventory.addReason') }}">@lang('app.add')</button>
                                    </x-slot>
                                </x-forms.input-group>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <x-forms.label class="my-3" fieldId="description-text" :fieldLabel="__('app.description')">
                                    </x-forms.label>
                                    <textarea name="description" id="description-text" rows="4" class="form-control"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-3 d-none product-category-filter">
                                    <div class="form-group c-inv-select mb-4">
                                        <x-forms.input-group>
                                            <select class="form-control select-picker" name="category_id"
                                                id="product_category_id" data-live-search="true">
                                                <option value="">
                                                    {{ __('app.select') . ' ' . __('app.product') . ' ' . __('app.category') }}
                                                </option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}">
                                                        {{ $category->category_name }}</option>
                                                @endforeach
                                            </select>
                                        </x-forms.input-group>
                                    </div>
                                </div>
                            <div class="col-md-3">
                                <div class="form-group c-inv-select mb-4">
                                    <x-forms.input-group>
                                        <select class="form-control select-picker" data-live-search="true"
                                            data-size="8" id="add-products">
                                            <option value="">{{ __('app.select') . ' ' . __('app.product') }}
                                            </option>
                                            @foreach ($products as $item)
                                                <option data-content="{{ $item->name }}"
                                                    value="{{ $item->id }}">
                                                    {{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                        <x-slot name="preappend">
                                            <a href="javascript:;"
                                                class="btn btn-outline-secondary border-grey toggle-product-category"
                                                data-toggle="tooltip"
                                                data-original-title="{{ __('modules.productCategory.filterByCategory') }}"><i
                                                    class="fa fa-filter"></i></a>
                                        </x-slot>
                                    </x-forms.input-group>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <x-alert id="alertMessage" type="danger">@lang('messages.addItem')</x-alert>
                            </div>
                        </div>

                        <div id="sortable">
                        </div>

                        <div class="row mt-3">
                            <div class="col-lg-12">
                                <x-forms.file-multiple class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('purchase::app.menu.addFiles')"
                                    fieldName="file" fieldId="file-upload-dropzone" />

                                <input type="hidden" name="inventory_id" id="inventory_id">
                            </div>
                        </div>

                    </div>
                </div>
                <x-form-actions>
                    <x-forms.button-primary class="mr-3 save-form" icon="check" data-type="save">@lang('app.save')
                    </x-forms.button-primary>
                    <x-forms.button-secondary data-type="draft" class="save-form mr-3" data-type="draft">@lang('app.saveDraft')
                    </x-forms.button-secondary>
                    <x-forms.button-cancel :link="route('purchase-inventory.index')" class="border-0">@lang('app.cancel')
                    </x-forms.button-cancel>
                </x-form-actions>
            </div>

        </x-form>
    </div>

</div>

<script>
    $(document).ready(function() {

        const dp1 = datepicker('#date', {
            position: 'bl',
            ...datepickerConfig
        });

        $("#reason").selectpicker();

        let defaultImage = '';
        let lastIndex = 0;

        Dropzone.autoDiscover = false;
        // Dropzone class
        inventoryDropzone = new Dropzone("div#file-upload-dropzone", {
            dictDefaultMessage: "{{ __('app.dragDrop') }}",
            url: "{{ route('inventory-files.store') }}",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            paramName: "file",
            maxFilesize: DROPZONE_MAX_FILESIZE,
            maxFiles: 10,
            autoProcessQueue: false,
            uploadMultiple: true,
            addRemoveLinks: true,
            parallelUploads: 10,
            acceptedFiles: DROPZONE_FILE_ALLOW,
            init: function() {
                inventoryDropzone = this;
            }
        });
        inventoryDropzone.on('sending', function(file, xhr, formData) {
            const inventoyID = $('#inventory_id').val();
            formData.append('inventory_id', inventoyID);
            formData.append('default_image', defaultImage);
            $.easyBlockUI();
        });
        inventoryDropzone.on('uploadprogress', function() {
            $.easyBlockUI();
        });
        inventoryDropzone.on('completemultiple', function() {
            window.location.href = '{{ route('purchase-inventory.index') }}';
        });
        inventoryDropzone.on('addedfile', function(file) {
            lastIndex++;

            const div = document.createElement('div');
            div.className = 'form-check-inline custom-control custom-radio mt-2 mr-3';
            const input = document.createElement('input');
            input.className = 'custom-control-input';
            input.type = 'radio';
            input.name = 'default_image';
            input.id = 'default-image-' + lastIndex;
            input.value = file.name;
            if (lastIndex == 1) {
                input.checked = true;
            }
            div.appendChild(input);

            var label = document.createElement('label');
            label.className = 'custom-control-label pt-1 cursor-pointer';
            label.innerHTML = "@lang('modules.makeDefaultImage')";
            label.htmlFor = 'default-image-' + lastIndex;
            div.appendChild(label);

            file.previewTemplate.appendChild(div);
        });

        $('.toggle-product-category').click(function() {
            $('.product-category-filter').toggleClass('d-none');
        });

        $('#product_category_id').change(function(e) {
            let categoryId = $(this).val();
            let url = "{{ route('get_product_sub_categories', ':id') }}";

            url = (categoryId) ? url.replace(':id', categoryId) : url.replace(':id', null);

            $.easyAjax({
                url: url,
                type: "GET",
                success: function(response) {
                    if (response.status == 'success') {
                        var options = [];
                        var rData;
                        rData = response.data;
                        $.each(rData, function(index, value) {
                            var selectData;
                            selectData = '<option value="' + value.id + '">' + value
                                .category_name + '</option>';
                            options.push(selectData);
                        });

                        $('#sub_category_id').html('<option value="">--</option>' +
                            options);
                        $('#sub_category_id').selectpicker('refresh');
                    }
                }
            })
        });

        $('.save-form').click(function()
        {
            let formType = $(this).data('type');
            let buttonSelector = "#save-inventory";
            let data = $('#save-inventory-form').serialize();
            let url = "{{ route('purchase-inventory.store') }}" + "?formType=" + formType;

            $.easyAjax({
                url: url,
                container: '#save-inventory-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                buttonSelector: buttonSelector,
                file: true,
                data: data,
                success: function(response) {
                    if (response.status === 'success') {
                        if (inventoryDropzone.getQueuedFiles().length > 0) {
                            inventoyID = response.inventoyID
                            defaultImage = response.defaultImage;
                            $('#inventory_id').val(inventoyID);
                            inventoryDropzone.processQueue();
                        } else {
                            window.location.href = "{{ route('purchase-inventory.index') }}";
                        }
                    }

                    if (typeof showTable !== 'undefined' && typeof showTable === 'function') {
                        showTable();
                    }

                }
            });
        });

        const resetAddProductButton = () => {
            $("#add-products").val('').selectpicker("refresh");
        };

        $('#add-products').on('changed.bs.select', function(e, clickedIndex, isSelected, previousValue) {
            e.stopImmediatePropagation()
            var id = $(this).val();

            if (previousValue != id && id != '') {
                addProduct(id);
                resetAddProductButton();
            }
        });

        function addProduct(id) {

            let adjustmentVal = $('input[name="type"]:checked').val();

            $.easyAjax({
                url: "{{ route('purchase_inventory.adjust_inventory') }}",
                type: "GET",
                data: {
                    id: id,
                    val: adjustmentVal
                },
                blockUI: true,
                success: function(response) {

                    if ($('input[name="item_name[]"]').val() == '') {
                        $("#sortable .item-row").remove();
                    }

                    $(response.view).hide().appendTo("#sortable").fadeIn(500);
                    $('.selectpicker').selectpicker('refresh');
                    calculateTotal();
                    $('.dropify').dropify();
                    $('#alertMessage').hide().fadeOut(500);
                    var noOfRows = $(document).find('#sortable .item-row').length;
                    var i = $(document).find('.item_name').length - 1;
                    var itemRow = $(document).find('#sortable .item-row:nth-child(' + noOfRows +
                        ') select.type');
                    itemRow.attr('id', 'multiselect' + i);
                    itemRow.attr('name', 'taxes[' + i + '][]');
                    $(document).find('#multiselect' + i).selectpicker();

                    $(document).find('#dropify' + i).dropify({
                        messages: dropifyMessages
                    });
                }
            });
        }

        $('#save-inventory-form').on('click', '.remove-item', function() {
            $(this).closest('.item-row').fadeOut(300, function() {
                $(this).remove();
                $('select.customSequence').each(function(index) {
                    $(this).attr('name', 'taxes[' + index + '][]');
                    $(this).attr('id', 'multiselect' + index + '');
                });
            });
        });

        $('input[type=radio][name=type]').change(function() {
            $("#sortable .item-row").remove();
        });

        $('#addReason').click(function() {
            const url = "{{ route('adjustment-reasons.create') }}";
            $(MODAL_DEFAULT + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_DEFAULT, url);
        });

        init(RIGHT_MODAL);

    });
</script>
