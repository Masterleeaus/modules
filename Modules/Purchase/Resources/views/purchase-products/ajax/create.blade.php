@php
    $addProductCategoryPermission = user()->permission('manage_product_category');
    $addProductSubCategoryPermission = user()->permission('manage_product_sub_category');
@endphp

<link rel="stylesheet" href="{{ asset('vendor/css/dropzone.min.css') }}">
<style>
    .product_type{
        margin-top: 0px !important;
    }
    .track_inventory_label {
        margin-left: 30px !important;
    }

    #purchase_price_div {
        margin-top: 46px !important;
    }

    #salePrice {
        margin-top: 38px !important;
    }

</style>

<div class="row">
    <div class="col-sm-12">
        <x-form id="save-product-form">
            @include('sections.password-autocomplete-hide')

            <div class="add-client bg-white rounded">
                <h4 class="mb-0 p-20 f-21 font-weight-normal  border-bottom-grey">
                    @lang('app.menu.addProducts')</h4>
                <div class="row p-20">
                    <div class="col-lg-12">
                        <div class="row">

                            <input type="hidden" id="hiddenProductId">
                            <input type="hidden" value={{}} name="purchase_vendor_id">

                            <div class="col-lg-4 col-md-6">
                                <x-forms.label class="my-3" fieldId="product_type"
                                            :fieldLabel="__('purchase::modules.product.type')">
                                </x-forms.label><sup class="text-red f-14 mr-1">*</sup>
                                <div class="form-group">
                                    <div class="d-flex">
                                        <x-forms.radio fieldId="goods_type" class="product_type"
                                            :fieldLabel="__('purchase::modules.product.goods')" fieldName="type"
                                            fieldValue="goods" :checked="(($product && $product->type == 'goods') ? 'true' : 'true')">
                                        </x-forms.radio>

                                        <x-forms.radio class="product_type" fieldId="service_type" :fieldLabel="__('purchase::modules.product.service')"
                                            fieldValue="service" fieldName="type" :checked="(($product && $product->type == 'service') ? 'true' : '')">
                                        </x-forms.radio>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <x-forms.text fieldId="name" :fieldLabel="__('app.name')" fieldName="name" :fieldValue="$product ? $product->name : ''"
                                              fieldRequired="true" :fieldPlaceholder="__('placeholders.productName')">
                                </x-forms.text>
                            </div>

                            <div class="col-lg-4 col-md-6" id="sku_id">
                                <x-forms.text fieldId="sku" :fieldLabel="__('purchase::app.sku')" fieldName="sku"
                                :fieldValue="$product ? $product->sku : ''" :fieldPlaceholder="__('placeholders.hsnSac')">
                                </x-forms.text>
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <x-forms.label class="my-3" fieldId="" :fieldLabel="__('modules.unitType.unitType')">
                                </x-forms.label>
                                <x-forms.input-group>
                                    <select class="form-control unit_type select-picker" name="unit_type" id="unit_type_id"
                                            data-live-search="true">
                                        @foreach ($unit_types as $unit_type)
                                            <option @if ($product && ($unit_type->id == $product->unit_id)) selected @elseif ($unit_type->default == 1) selected @endif value="{{ $unit_type->id }}">{{ ucwords($unit_type->unit_type) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </x-forms.input-group>
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <x-forms.label class="my-3" fieldId=""
                                               :fieldLabel="__('modules.productCategory.productCategory')">
                                </x-forms.label>
                                <x-forms.input-group>
                                    <select class="form-control select-picker" name="category_id"
                                            id="product_category_id" data-live-search="true">
                                        <option value="">--</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" @if ($product && $category->id == $product->category_id) selected @endif>
                                                {{ mb_ucwords($category->category_name) }}</option>
                                        @endforeach
                                    </select>

                                    @if ($addProductCategoryPermission == 'all' || $addProductCategoryPermission == 'added')
                                        <x-slot name="append">
                                            <button id="add-category" type="button"
                                                    data-toggle="tooltip"
                                                    data-original-title="{{ __('app.add').' '.__('modules.productCategory.productCategory') }}"
                                                    class="btn btn-outline-secondary border-grey">@lang('app.add')</button>
                                        </x-slot>
                                    @endif
                                </x-forms.input-group>
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <x-forms.label class="my-3" fieldId=""
                                               :fieldLabel="__('modules.productCategory.productSubCategory')">
                                </x-forms.label>
                                <x-forms.input-group>
                                    <select class="form-control select-picker" name="sub_category_id" id="sub_category_id" data-live-search="true">
                                        <option value="">@lang('messages.noProductSubCategoryAdded')</option>
                                        @foreach ($subCategories as $subCategory)
                                            <option value="{{ $subCategory->id }}" @if ($product && $category->id == $product->sub_category_id) selected @endif>
                                                {{ mb_ucwords($subCategory->category_name) }}</option>
                                        @endforeach
                                    </select>

                                    @if ($addProductSubCategoryPermission == 'all' || $addProductSubCategoryPermission == 'added')
                                        <x-slot name="append">
                                            <button id="add-sub-category" type="button" data-toggle="tooltip" data-original-title="{{ __('app.add').' '.__('modules.productCategory.productSubCategory') }}" class="btn btn-outline-secondary border-grey">@lang('app.add')</button>
                                        </x-slot>
                                    @endif
                                </x-forms.input-group>
                            </div>

                            <div class="col-md-6">
                                <div class="row mt-5">
                                    <div class="col-md-12">
                                        <x-forms.label class="" fieldId="sales_information"
                                                    :fieldLabel="__('purchase::app.salesInformation')">
                                        </x-forms.label>
                                    </div>
                                </div>
                                <div class="row" id="salePrice">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="f-14 text-dark-grey mb-12 "
                                                for="selling_price">@lang('purchase::app.sellingPrice')<sup class="text-red f-14 mr-1">*</sup></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend  height-35">
                                                    <span class="input-group-text border-grey f-15 bg-additional-grey px-3 text-dark"
                                                        id="basic-addon1">{{ company()->currency->currency_code }}</span>
                                                </div>
                                                <input type="number" name="selling_price" id="selling_price"
                                                    class="form-control height-35 f-15 readonly-background" value="{{ ($product && $product->price) ? $product->price :  null }}" placeholder="0"  aria-label="0019" aria-describedby="basic-addon1" min="0">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="row mt-5">
                                    <div class="col-md-12 purchase-info">
                                        <x-forms.checkbox :fieldLabel="__('purchase::app.purchaseInformation')"
                                            fieldName="purchase_information" fieldId="purchase_information" fieldValue="1"
                                            fieldRequired="true" :checked='true'/>
                                    </div>
                                </div>
                                <div class="row purchase_information" id="purchase_price_div">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="f-14 text-dark-grey mb-12 "
                                                for="purchase_price">@lang('purchase::app.costPrice')<sup class="text-red f-14 mr-1">*</sup></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend  height-35 ">
                                                    <span class="input-group-text border-grey f-15 bg-additional-grey px-3 text-dark"
                                                        id="basic-addon1">{{ company()->currency->currency_code }}</span>
                                                </div>
                                                <input type="number" name="purchase_price" id="purchase_price"
                                                    class="form-control height-35 f-15 readonly-background" value="{{ ($product && $product->purchase_price) ? $product->purchase_price : null }}" placeholder="0" aria-label="0019" aria-describedby="basic-addon1" min="0">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 my-3">
                                <x-forms.label fieldId="" :fieldLabel="__('modules.invoices.tax')">
                                </x-forms.label>
                                <x-forms.input-group>
                                    <select class="form-control tax select-picker" name="tax[]" id="tax_id"
                                            data-live-search="true" multiple="true">
                                        @foreach ($taxes as $tax)
                                            <option value="{{ $tax->id }}" @if ($product && isset($product->taxes) && array_search($tax->id, json_decode($product->taxes)) !== false) selected @endif>{{ strtoupper($tax->tax_name) }}:
                                                {{ $tax->rate_percent }}%
                                            </option>
                                        @endforeach
                                    </select>

                                    @if (user()->permission('manage_tax') == 'all')
                                        <x-slot name="append">
                                            <button id="add-tax" type="button"
                                            data-toggle="tooltip"
                                            data-original-title="{{ __('app.add').' '.__('modules.invoices.tax') }}"
                                            class="btn btn-outline-secondary border-grey">@lang('app.add')</button>
                                        </x-slot>
                                    @endif
                                </x-forms.input-group>
                            </div>

                            <div class="col-md-4">
                                <x-forms.text fieldId="hsn_sac_code" :fieldLabel="__('app.hsnSac')"
                                              fieldName="hsn_sac_code" :fieldValue="$product ? $product->hsn_sac_code : ''"
                                              :fieldPlaceholder="__('placeholders.hsnSac')">
                                </x-forms.text>
                            </div>

                            <div class="col-md-2 mt-5">
                                <x-forms.checkbox class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('app.purchaseAllow')"
                                                  fieldName="purchase_allow" fieldId="purchase_allow" fieldValue="no"
                                                  fieldRequired="true" :checked="($product ? $product->allow_purchase = 1 : '')" />
                            </div>

                            <div class="col-md-2 mt-5">
                                <x-forms.checkbox class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('app.downloadable')"
                                            fieldName="downloadable" fieldId="downloadable" fieldValue="true"
                                            fieldRequired="true" :popover="__('messages.downloadable')"/>
                            </div>

                            <div class="col-md-12  mt-2 downloadable d-none">
                                <x-forms.file class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('app.downloadableFile')"
                                            fieldName="downloadable_file" fieldId="downloadable_file"
                                            fieldRequired="true"/>
                            </div>

                            <div class="col-md-12 mt-4 {{ ($product && $product->type == 'service') ? 'd-none' : ''}} track_inventory_div">
                                <x-forms.checkbox class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('purchase::app.trackInventory')" fieldName="track_inventory" fieldId="track_inventory" fieldValue="1"
                                    fieldRequired="true" />
                                <label for="" class="track_inventory_label text-dark-grey f-13" id="track_inventory_label">@lang('purchase::messages.trackInventoryMsg')</label>
                            </div>

                            <div class="col-md-12 mt-3 track_inventory d-none">
                                <div class="row">
                                    <div class="col-md-6">
                                        <x-forms.number fieldId="opening_stock" fieldRequired="true" :fieldLabel="__('purchase::app.openingStock')" fieldName="opening_stock" :fieldPlaceholder="__('purchase::placeholders.openingStock')"
                                         :popover="__('purchase::app.availableStock')">
                                        </x-forms.number>
                                    </div>
                                </div>
                            </div>

                           <div class="col-md-12 mt-3">
                                <div class="form-group">
                                    <x-forms.label class="my-3" fieldId="description-text"
                                                   :fieldLabel="__('app.description')">
                                    </x-forms.label>
                                    <textarea name="description" id="description-text" rows="4"
                                              class="form-control">{{ $product ? $product->description : '' }}</textarea>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <x-forms.file-multiple class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('purchase::modules.product.addImages')" fieldName="file" fieldId="file-upload-dropzones"/>
                            </div>
                            <input type ="hidden" name="add_more" value="false" id="add_more" />
                        </div>
                    </div>

                </div>

                <x-forms.custom-field :fields="$fields"></x-forms.custom-field>

                <x-form-actions>
                    <x-forms.button-primary id="save-product" class="mr-3 px-0" icon="check">@lang('app.save')
                    </x-forms.button-primary>
                    <x-forms.button-secondary class="mr-3" id="save-more-product" icon="check-double">@lang('app.saveAddMore')
                    </x-forms.button-secondary>
                    <x-forms.button-cancel :link="route('purchase-products.index')" class="border-0">@lang('app.cancel')
                    </x-forms.button-cancel>
                </x-form-actions>
            </div>
        </x-form>

    </div>
</div>

<script>
    $(document).ready(function () {

        $('.unit_type, .tax').selectpicker();

        let defaultImage = '';
        let lastIndex = 0;

        Dropzone.autoDiscover = false;
        //Dropzone class
        productDropzone = new Dropzone("div#file-upload-dropzones", {
            dictDefaultMessage: "{{ __('app.dragDrop') }}",
            url: "{{ route('product-files.store') }}",
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
            acceptedFiles: 'image/*',
            init: function () {
                productDropzone = this;
            }
        });
        productDropzone.on('sending', function (file, xhr, formData) {
            const productID = $('#hiddenProductId').val();
            formData.append('product_id', productID);
            formData.append('default_image', defaultImage);
            $.easyBlockUI();
        });
        productDropzone.on('uploadprogress', function () {
            $.easyBlockUI();
        });
        productDropzone.on('completemultiple', function () {
            window.location.href = '{{ route("purchase-products.index") }}';
        });
        productDropzone.on('addedfile', function (file) {
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

        $('#product_category_id').change(function (e) {
            let categoryId = $(this).val();
            let url = "{{ route('get_product_sub_categories', ':id') }}";

            url = (categoryId) ? url.replace(':id', categoryId) : url.replace(':id', null);

            $.easyAjax({
                url: url,
                type: "GET",
                success: function (response) {
                    if (response.status == 'success') {
                        var options = [];
                        var rData;
                        rData = response.data;
                        $.each(rData, function (index, value) {
                            var selectData;
                            selectData = '<option value="' + value.id + '">' + value
                                .category_name + '</option>';
                            options.push(selectData);
                        });

                        $('#sub_category_id').html('<option value="">--</option>' + options);
                        $('#sub_category_id').selectpicker('refresh');
                    }
                }
            })
        });

        $('#save-more-product').click(function () {

            $('#add_more').val(true);

            const url = "{{ route('purchase-products.store') }}";
            var data = $('#save-product-form').serialize();

            saveProduct(data, url, "#save-more-product");

        });

        $('#save-product').click(function() {

            const url = "{{ route('purchase-products.store') }}";
            var data = $('#save-product-form').serialize();

            saveProduct(data, url, "#save-product");

        });

        $('#service_type').on('click', function(){
            $('#sku_id').addClass('d-none');
        });

        $('#goods_type').on('click', function(){
            $('#sku_id').removeClass('d-none');
        });

        function saveProduct(data, url, buttonSelector) {
            $.easyAjax({
                url: url,
                container: '#save-product-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                buttonSelector: buttonSelector,
                file: true,
                data: data,
                success: function(response) {
                    if (productDropzone.getQueuedFiles().length > 0) {
                        productID = response.productID
                        defaultImage = response.defaultImage;
                        $('#hiddenProductId').val(productID);
                        productDropzone.processQueue();
                    }
                    else if(response.add_more == true) {

                        var right_modal_content = $.trim($(RIGHT_MODAL_CONTENT).html());

                        if(right_modal_content.length) {

                            $(RIGHT_MODAL_CONTENT).html(response.html.html);
                            $('#add_more').val(false);
                        }
                        else {

                            $('.content-wrapper').html(response.html.html);
                            init('.content-wrapper');
                            $('#add_more').val(false);
                        }
                    }

                    else{
                        if (response.redirectUrl == 'no') {
                            getProductOptions();
                            closeTaskDetail();
                        } else if ($(MODAL_XL).hasClass('show')) {
                            $(MODAL_XL).modal('hide');
                            window.location.reload();
                        } else {
                            window.location.href = response.redirectUrl;
                        }
                    }

                    if (typeof showTable !== 'undefined' && typeof showTable === 'function') {
                            showTable();
                    }

                }
            });
        }

        function getProductOptions() {
            $.easyAjax({
                url: "{{ route('purchase_products.options') }}",
                type: "GET",
                success: function (response) {
                    $('#add-products').html(response.products);
                    $('#add-products').val('');
                    $('#add-products').selectpicker('refresh');
                }
            })
        }

        $('#add-category').click(function () {
            const url = "{{ route('productCategory.create') }}";
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        })

        $('#add-sub-category').click(function () {
            const url = "{{ route('productSubCategory.create') }}";
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });

        $('#add-tax').click(function () {
            const url = "{{ route('taxes.create') }}";
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });

        $('input[type=radio][name=type]').change(function() {
            if (this.value == 'service') {
                $('#track_inventory').prop('checked', true);
                $('#track_inventory').prop('checked', false);

                $('#opening_stock, #rate_per_unit').val('');
                $('.track_inventory').addClass('d-none');
                $('.track_inventory_div').addClass('d-none');
            }
            else if (this.value == 'goods') {
                $('#opening_stock, #rate_per_unit').val('');
                $('.track_inventory').addClass('d-none');
                $('.track_inventory_div').removeClass('d-none');
            }
        });

        $('#downloadable').change(function () {
            if ($(this).is(':checked')) {
                $('.downloadable').removeClass('d-none');
            } else {
                $('.downloadable').addClass('d-none');
            }
        });

        $('#purchase_information').change(function () {
            if ($(this).is(':checked')) {
                $('.purchase_information').removeClass('d-none');
            } else {
                $('.purchase_information').addClass('d-none');
            }
        });

        $('#track_inventory').change(function () {
            if ($(this).is(':checked')) {
                $('.track_inventory').removeClass('d-none');
            } else {
                $('.track_inventory').addClass('d-none');
            }
        });

        init(RIGHT_MODAL);

    });
</script>
