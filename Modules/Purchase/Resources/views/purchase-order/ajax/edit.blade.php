@php
    $addProductPermission = user()->permission('add_product');
@endphp

<link rel="stylesheet" href="{{ asset('vendor/css/dropzone.min.css') }}">

    <!-- CREATE INVOICE START -->
<div class="bg-white rounded b-shadow-4 create-inv">
    <!-- HEADING START -->
    <div class="px-lg-4 px-md-4 px-3 py-3">
        <h4 class="mb-0 f-21 font-weight-normal ">@lang('purchase::app.menu.purchaseOrder')</h4>
    </div>
    <!-- HEADING END -->
    <hr class="m-0 border-top-grey">
    <!-- FORM START -->
    <x-form class="c-inv-form" id="saveOrderForm">
        @method('PUT')
        <!-- INVOICE NUMBER, DATE, DUE DATE, FREQUENCY START -->
        <div class="row px-lg-4 px-md-4 px-3 py-3">
            <!-- INVOICE NUMBER START -->
            <div class="col-md-6 col-lg-4">
                <div class="form-group mb-lg-0 mb-md-0 mb-4">
                    <x-forms.label class="mb-12" fieldId="purchase_order_number"
                                   :fieldLabel="__('app.orderNumber')" fieldRequired="true">
                    </x-forms.label>
                    <x-forms.input-group>
                        <x-slot name="prepend">
                            <span class="input-group-text border-grey f-15 bg-additional-grey px-3 text-dark"
                                id="basic-addon1">{{ $purchaseSetting->purchase_order_prefix }}{{ $purchaseSetting->purchase_order_number_separator }}</span>
                        </x-slot>
                        <input type="number" name="purchase_order_number" id="purchase_order_number" class="form-control height-35 f-15"
                               value="{{ $order->original_order_number }}" readonly>
                    </x-forms.input-group>
                </div>
            </div>
            <!-- INVOICE NUMBER END -->
            <!-- INVOICE DATE START -->
            <div class="col-md-6 col-lg-4">
                <div class="form-group c-inv-select mb-4">
                    <x-forms.label fieldId="vendor_id" :fieldLabel="__('app.select').' '.__('purchase::app.menu.vendor')">
                    </x-forms.label>

                    <div class="select-others height-35 rounded">
                        <select class="form-control select-picker" name="vendor_id" id="vendor_id" data-live-search="true">
                            <option value="">--</option>
                            @foreach ($vendors as $vendor)
                                <option value="{{ $vendor->id }}" @if ($order->vendor_id == $vendor->id) selected @endif>{{ $vendor->primary_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <!-- INVOICE DATE END -->

            <!-- CURRENCY START -->
            <div class="col-md-6 col-lg-4">
                <div class="form-group c-inv-select mb-lg-0 mb-4">
                    <x-forms.label fieldId="currency_id" :fieldLabel="__('modules.invoices.currency')">
                    </x-forms.label>

                    <div class="select-others height-35 rounded" id="select_currency_id">
                        <select class="form-control select-picker" name="currency_id" id="currency_id">
                            <option value="{{$order->currency->id}}">{{$order->currency->currency_code}} ({{$order->currency->currency_symbol}})</option>
                        </select>
                    </div>
                </div>
            </div>
            <!-- CURRENCY END -->

            <div class="col-md-6 col-lg-4 mt-3">
                <x-forms.label fieldId="exchange_rate" :fieldLabel="__('modules.currencySettings.exchangeRate')" fieldRequired="true">
                </x-forms.label>
                <input type="number" id="exchange_rate" name="exchange_rate"
                       class="px-6 position-relative text-dark font-weight-normal form-control height-35 rounded p-0 text-left f-15" value="{{$order->exchange_rate}}" @if($companyCurrency->id == $order->currency_id) readonly @endif>
                <small id="currency_exchange" class="form-text text-muted">( {{company()->currency->currency_code}} @lang('app.to') {{$order->currency->currency_code}} )</small>
            </div>

            <!-- PURCHASE DATE START -->
            <div class="col-md-6 col-lg-4 mt-3">
                <div class="form-group mb-4">
                    <x-forms.label fieldId="purchase_date" :fieldLabel="__('app.date')" fieldRequired="true">
                    </x-forms.label>
                    <div class="input-group">
                        <input type="text" id="purchase_date" name="purchase_date"
                            class="px-6 position-relative text-dark font-weight-normal form-control height-35 rounded p-0 text-left f-15"
                            placeholder="@lang('placeholders.date')"
                            value="{{ $order->purchase_date->translatedFormat(company()->date_format) }}">
                    </div>
                </div>
            </div>
            <!-- PURCHASE DATE END -->

            <div class="col-md-6 col-lg-4 mt-3">
                <div class="form-group mb-4">
                    <x-forms.label fieldId="expected_date" :fieldLabel="__('purchase::modules.purchaseOrder.expectedDeliveryDate')" fieldRequired="true">
                    </x-forms.label>
                    <div class="input-group">
                        <input type="text" id="expected_date" name="expected_date"
                            class="px-6 position-relative text-dark font-weight-normal form-control height-35 rounded p-0 text-left f-15"
                            placeholder="@lang('placeholders.date')"
                            value="{{ $order->expected_delivery_date->translatedFormat(company()->date_format)  }}">
                    </div>
                </div>
            </div>


            <div class="col-md-6 col-lg-4 mt-3">
                <div class="form-group c-inv-select mb-4">
                    <x-forms.label fieldId="address_id" :fieldLabel="__('purchase::app.deliveryAddresses')">
                    </x-forms.label>

                    <div class="select-others height-35 rounded">
                        <select class="form-control select-picker" name="address_id" id="address_id">
                            <option value="">--</option>
                            @foreach ($addresses as $address)
                                <option value="{{ $address->id }}" @if ($address->id == $order->address_id) selected @endif>{{ $address->location }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4 mt-3">
                <div class="form-group c-inv-select mb-4">
                    <x-forms.label fieldId="calculate_tax" :fieldLabel="__('modules.invoices.calculateTax')">
                    </x-forms.label>
                    <div class="select-others height-35 rounded">
                        <select class="form-control select-picker" data-live-search="true" data-size="8"
                                name="calculate_tax" id="calculate_tax">
                            <option value="after_discount" @if ($order->calculate_tax == 'after_discount') selected @endif>@lang('modules.invoices.afterDiscount')</option>
                            <option value="before_discount" @if ($order->calculate_tax == 'before_discount') selected @endif>@lang('modules.invoices.beforeDiscount')</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4 mt-3">
                <div class="form-group c-inv-select mb-4">
                    <x-forms.label fieldId="delivery_status" :fieldLabel="__('purchase::modules.purchaseOrder.deliveryStatus')">
                    </x-forms.label>
                    <div class="select-others height-35 rounded">
                        <select class="form-control select-picker" data-live-search="true" data-size="8"
                            name="delivery_status" id="delivery_status">
                            <option data-content="<i class='fa fa-circle mr-2 text-dark'></i> @lang('purchase::modules.purchaseOrder.notStarted')" value="not_started" @if ($order->delivery_status == 'not_started') selected @endif></option>
                            <option data-content="<i class='fa fa-circle mr-2 text-yellow'></i> @lang('purchase::modules.purchaseOrder.inTransaction')" value="in_transaction" @if ($order->delivery_status == 'in_transaction') selected @endif></option>
                            <option data-content="<i class='fa fa-circle mr-1 f-15 text-red'></i> @lang('purchase::modules.purchaseOrder.deliveryFailed')" value="delivery_failed" @if ($order->delivery_status == 'delivery_failed') selected @endif></option>
                            <option data-content="<i class='fa fa-circle mr-1 f-15 text-light-green'></i> @lang('purchase::modules.purchaseOrder.delivered')"value="delivered" @if ($order->delivery_status == 'delivered') selected @endif></option>
                        </select>
                    </div>
                </div>
            </div>

        </div>
        <!-- INVOICE NUMBER, DATE, DUE DATE, FREQUENCY END -->

        <hr class="m-0 border-top-grey">

        <div class="row px-lg-4 px-md-4 px-3 py-3">
            <div class="col-md-3 d-none product-category-filter">
                <div class="form-group c-inv-select mb-4">
                    <x-forms.input-group>
                        <select class="form-control select-picker" name="category_id"
                                id="product_category_id" data-live-search="true">
                            <option value="null">{{ __('app.select') . ' ' . __('app.product') . ' ' . __('app.category')  }}</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">
                                    {{ mb_ucwords($category->category_name) }}</option>
                            @endforeach
                        </select>
                    </x-forms.input-group>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group c-inv-select mb-4">
                <x-forms.input-group>
                    <select class="form-control select-picker" data-live-search="true" data-size="8"
                            id="add-products">
                        <option value="">{{ __('app.select') . ' ' . __('app.product') }}</option>
                        @foreach ($products as $item)
                            <option data-content="{{ $item->name }}" value="{{ $item->id }}">
                                {{ $item->name }}</option>
                        @endforeach
                    </select>
                    <x-slot name="preappend">
                        <a href="javascript:;"
                            class="btn btn-outline-secondary border-grey toggle-product-category"
                            data-toggle="tooltip" data-original-title="{{ __('modules.productCategory.filterByCategory') }}"><i class="fa fa-filter"></i></a>
                    </x-slot>
                    @if ($addProductPermission == 'all' || $addProductPermission == 'added')
                        <x-slot name="append">
                            <a href="{{ route('products.create') }}" data-redirect-url="{{ url()->full() }}"
                                class="btn btn-outline-secondary border-grey openRightModal"
                                data-toggle="tooltip" data-original-title="{{ __('app.add').' '.__('modules.dashboard.newproduct') }}">@lang('app.add')</a>
                        </x-slot>
                    @endif
                </x-forms.input-group>
                </div>
            </div>
        </div>

        <div id="sortable">
            @foreach ($order->items as $key => $item)
                <!-- DESKTOP DESCRIPTION TABLE START -->
                <div class="d-flex px-4 py-3 c-inv-desc item-row">

                    <div class="c-inv-desc-table w-100 d-lg-flex d-md-flex d-block">
                        <table width="100%">
                            <tbody>
                            <tr class="text-dark-grey font-weight-bold f-14">
                                <td width="{{ $invoiceSetting->hsn_sac_code_show ? '40%' : '50%' }}"
                                    class="border-0 inv-desc-mbl btlr">@lang('app.description')
                                    <input type="hidden" name="item_ids[]" value="{{ $item->id }}">
                                </td>
                                @if ($invoiceSetting->hsn_sac_code_show)
                                    <td width="10%" class="border-0" align="right">@lang("app.hsnSac")</td>
                                @endif
                                <td width="10%" class="border-0" align="right">@lang('modules.invoices.qty')</td>
                                <td width="10%" class="border-0" align="right">
                                    @lang("modules.invoices.unitPrice")</td>
                                <td width="13%" class="border-0" align="right">@lang('modules.invoices.tax')
                                </td>
                                <td width="17%" class="border-0 bblr-mbl" align="right">
                                    @lang('modules.invoices.amount')</td>
                            </tr>
                            <tr>
                                <td class="border-bottom-0 btrr-mbl btlr">
                                    <input type="text" class="form-control f-14 border-0 w-100 item_name"
                                           name="item_name[]" placeholder="@lang('modules.expenses.itemName')"
                                           value="{{ $item->item_name }}">
                                </td>
                                <td class="border-bottom-0 d-block d-lg-none d-md-none">
                                        <textarea class="f-14 border-0 w-100 mobile-description form-control"
                                                  placeholder="@lang('placeholders.invoices.description')"
                                                  name="item_summary[]">{{ $item->item_summary }}</textarea>
                                </td>
                                @if ($invoiceSetting->hsn_sac_code_show)
                                    <td class="border-bottom-0">
                                        <input type="text" class="f-14 border-0 w-100 text-right hsn_sac_code form-control"
                                               value="{{ $item->hsn_sac_code }}" name="hsn_sac_code[]">
                                    </td>
                                @endif
                                <td class="border-bottom-0">
                                    <input type="number" min="1"
                                           class="form-control f-14 border-0 w-100 text-right quantity mt-3"
                                           value="{{ $item->quantity }}" name="quantity[]">

                                    @if (!is_null($item->product_id) && $item->product_id != 0)
                                        <span class="text-dark-grey float-right border-0 f-12">{{ $item->unit->unit_type }}</span>
                                        <input type="hidden" name="product_id[]" value="{{ $item->product_id }}">
                                        <input type="hidden" name="unit_id[]" value="{{ $item->unit_id }}">
                                    @else
                                        <select class="text-dark-grey float-right border-0 f-12" name="unit_id[]">
                                            @foreach ($units as $unit)
                                                <option
                                                @if ($item->unit_id == $unit->id) selected @endif
                                                value="{{ $unit->id }}">{{ $unit->unit_type }}</option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="product_id[]" value="">
                                    @endif
                                </td>
                                <td class="border-bottom-0">
                                    <input type="number" min="1"
                                           class="f-14 border-0 w-100 text-right cost_per_item form-control" placeholder="0.00"
                                           value="{{ $item->unit_price }}" name="cost_per_item[]">
                                </td>
                                <td class="border-bottom-0">
                                    <div class="select-others height-35 rounded border-0">
                                        <select id="multiselect{{ $key }}"
                                                name="taxes[{{ $key }}][]" multiple="multiple"
                                                class="select-picker type customSequence border-0" data-size="3">
                                            @php
                                                $taxArray = $item->itemTaxes->pluck('tax_id')->toArray();
                                            @endphp
                                            @foreach ($taxes as $tax)
                                                <option data-rate="{{ $tax->rate_percent }}" data-tax-text="{{ strtoupper($tax->tax_name) .':'. $tax->rate_percent }}%"
                                                        @if (isset($item->itemTaxes) && in_array($tax->id, $taxArray) !== false) selected @endif value="{{ $tax->id }}">
                                                    {{ strtoupper($tax->tax_name) }}:
                                                    {{ $tax->rate_percent }}%</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </td>
                                <td rowspan="2" align="right" valign="top" class="bg-amt-grey btrr-bbrr">
                                        <span
                                            class="amount-html">{{ number_format((float) $item->amount, 2, '.', '') }}</span>
                                    <input type="hidden" class="amount" name="amount[]"
                                           value="{{ $item->amount }}">
                                </td>
                            </tr>
                            <tr class="d-none d-md-block d-lg-table-row">
                                <td colspan="{{ $invoiceSetting->hsn_sac_code_show ? '4' : '3' }}"
                                    class="dash-border-top bblr">
                                        <textarea class="f-14 border-0 w-100 desktop-description form-control" name="item_summary[]"
                                                  placeholder="@lang('placeholders.invoices.description')">{{ $item->item_summary }}</textarea>
                                </td>
                                <td class="border-left-0">
                                    <input type="file"
                                           class="dropify"
                                           name="order_item_image[]"
                                           data-allowed-file-extensions="png jpg jpeg"
                                           data-messages-default="test"
                                           data-height="70"
                                           data-id="{{ $item->id }}"
                                           id="{{ $item->id }}"
                                           data-default-file="{{ $item->purchaseItemImage ? $item->purchaseItemImage->file_url : '' }}"
                                           @if ($item->purchaseItemImage && $item->purchaseItemImage->external_link)
                                               data-show-remove="false"
                                        @endif
                                    />
                                    <input type="hidden" name="order_item_image_url[]" value="{{ $item->purchaseItemImage ? $item->purchaseItemImage->external_link : '' }}">
                                </td>
                            </tr>
                            </tbody>
                        </table>

                        <a href="javascript:;"
                           class="d-flex align-items-center justify-content-center ml-3 remove-item"><i
                                class="fa fa-times-circle f-20 text-lightest"></i></a>
                    </div>
                </div>
                <!-- DESKTOP DESCRIPTION TABLE END -->
            @endforeach
        </div>
        <!--  ADD ITEM START-->
        <div class="row px-lg-4 px-md-4 px-3 pb-3 pt-0 mb-3  mt-2">
            <div class="col-md-12">
                <a class="f-15 f-w-500" href="javascript:;" id="add-item"><i
                        class="icons icon-plus font-weight-bold mr-1"></i>@lang('modules.invoices.addItem')</a>
            </div>
        </div>
        <!--  ADD ITEM END-->

        <hr class="m-0 border-top-grey">

        <!-- TOTAL, DISCOUNT START -->
        <div class="d-flex px-lg-4 px-md-4 px-3 pb-3 c-inv-total">
            <table width="100%" class="text-right f-14 ">
                <tbody>
                <tr>
                    <td width="50%" class="border-0 d-lg-table d-md-table d-none"></td>
                    <td width="50%" class="p-0 border-0 c-inv-total-right">
                        <table width="100%">
                            <tbody>
                            <tr>
                                <td colspan="2" class="border-top-0 text-dark-grey">
                                    @lang('modules.invoices.subTotal')</td>
                                <td width="30%" class="border-top-0 sub-total">
                                    {{ number_format((float) $order->sub_total, 2, '.', '') }}</td>
                                <input type="hidden" class="sub-total-field" name="sub_total"
                                       value="{{ $order->sub_total }}">
                            </tr>
                            <tr>
                                <td width="20%" class="text-dark-grey">@lang('modules.invoices.discount')
                                </td>
                                <td width="40%" style="padding: 5px;">
                                    <table width="100%">
                                        <tbody>
                                        <tr>
                                            <td width="70%" class="c-inv-sub-padding">
                                                <input type="number" min="0" name="discount_value"
                                                       class="form-control f-14 border-0 w-100 text-right discount_value"
                                                       placeholder="0" value="{{ $order->discount }}">
                                            </td>
                                            <td width="30%" align="left" class="c-inv-sub-padding">
                                                <div
                                                    class="select-others select-tax height-35 rounded border-0">
                                                    <select class="form-control select-picker"
                                                            id="discount_type" name="discount_type">
                                                        <option @if ($order->discount_type == 'percent') selected @endif value="percent">%
                                                        </option>
                                                        <option @if ($order->discount_type == 'fixed') selected @endif value="fixed">
                                                            @lang('modules.invoices.amount')</option>
                                                    </select>
                                                </div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                                <td><span
                                        id="discount_amount">{{ number_format((float) $order->discount, 2, '.', '') }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td>@lang('modules.invoices.tax')</td>
                                <td colspan="2" class="p-0">
                                    <table width="100%" id="invoice-taxes">
                                        <tr>
                                            <td colspan="2"><span class="tax-percent">0.00</span></td>
                                        </tr>
                                    </table>
                                </td>

                            </tr>
                            <tr class="bg-amt-grey f-16 f-w-500">
                                <td colspan="2">@lang('modules.invoices.total')</td>
                                <td><span
                                        class="total">{{ number_format((float) $order->total, 2, '.', '') }}</span>
                                </td>
                                <input type="hidden" class="total-field" name="total"
                                       value="{{ round($order->total, 2) }}">
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <!-- TOTAL, DISCOUNT END -->

        <!-- NOTE AND TERMS AND CONDITIONS START -->
        <div class="d-flex flex-wrap px-lg-4 px-md-4 px-3 py-3">
            <div class="col-md-6 col-sm-12 c-inv-note-terms p-0 mb-lg-0 mb-md-0 mb-3">
                <label class="f-14 text-dark-grey mb-12  w-100"
                       for="usr">@lang('modules.invoices.note')</label>
                <textarea class="form-control" name="note" id="note" rows="4"
                          placeholder="@lang('placeholders.invoices.note')">{{ $order->note }}</textarea>
            </div>
            <div class="col-md-6 col-sm-12 p-0 c-inv-note-terms">
                <x-forms.label fieldId="" :fieldLabel="__('modules.invoiceSettings.invoiceTerms')">
                </x-forms.label>
                <p>
                    {!! nl2br($purchaseSetting->purchase_terms) !!}
                </p>
            </div>
        </div>
        <!-- NOTE AND TERMS AND CONDITIONS END -->

        <!-- UPLOAD MULTIPLE FILES START -->
        <div class="row px-lg-4 px-md-4 px-3 py-3">
            <!-- INVOICE NUMBER START -->
            <div class="col-md-12">
                <x-forms.file-multiple class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('app.menu.addFile')" fieldName="file" fieldId="file-upload-dropzone"/>
            </div>
            <input type="hidden" name="OrderID" id="OrderID">
        </div>
        <!-- UPLOAD MULTIPLE FILES END -->

        <!-- CANCEL SAVE SEND START -->
        <x-form-actions class="c-inv-btns">

            <div class="d-flex">

                <div class="inv-action dropup mr-3">
                    <button class="btn-primary dropdown-toggle" type="button" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                        @lang('app.save')
                        <span><i class="fa fa-chevron-up f-15 text-white"></i></span>
                    </button>
                    <!-- DROPDOWN - INFORMATION -->
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuBtn" tabindex="0">
                        <li>
                            <a class="dropdown-item f-14 text-dark save-form" href="javascript:;" data-type="save">
                                <i class="fa fa-save f-w-500 mr-2 f-11"></i>
                                @if ($order->status == 'draft')
                                    @lang('app.saveDraft')
                                @else
                                    @lang('app.save')
                                @endif
                            </a>
                        </li>
                        @if($order->status == 'draft' || $order->send_status == 0)
                            <li>
                                <a class="dropdown-item f-14 text-dark save-form" href="javascript:void(0);"
                                   data-type="send">
                                    <i class="fa fa-paper-plane f-w-500  mr-2 f-12"></i> @lang('app.saveSend')
                                </a>
                            </li>
                        @endif
                        @if($order->status == 'draft' || $order->send_status == 0)
                            <li>
                                <a class="dropdown-item f-14 text-dark save-form" href="javascript:void(0);"
                                   data-type="mark_as_send" data-toggle="tooltip" data-original-title="@lang('messages.markSentInfo')">
                                    <i class="fa fa-check-double f-w-500  mr-2 f-12"></i> @lang('app.saveMark')
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>

                <x-forms.button-cancel :link="route('purchase-order.index')" class="border-0">@lang('app.cancel')
                </x-forms.button-cancel>
            </div>


        </x-form-actions>
        <!-- CANCEL SAVE SEND END -->

    </x-form>
    <!-- FORM END -->
</div>
<!-- CREATE INVOICE END -->

<script src="{{ asset('vendor/jquery/dropzone.min.js') }}"></script>
<script>
    $(document).ready(function() {

        $('.toggle-product-category').click(function() {
            $('.product-category-filter').toggleClass('d-none');
        });

        $('#product_category_id').on('change', function(){
            var id = $(this).val();
            var url = "{{route('invoices.product_category', ':id')}}",
            url = url.replace(':id', id);
            $.easyAjax({
                url : url,
                type : "GET",
                container: '#saveInvoiceForm',
                blockUI: true,
                success: function (response) {
                    if (response.status == 'success') {
                        var options = [];
                        var rData = [];
                        rData = response.data;
                        $.each(rData, function(index, value) {
                            var selectData = '';
                            selectData = '<option value="' + value.id + '">' + value.name +
                                '</option>';
                            options.push(selectData);
                        });
                        $('#add-products').html(
                            '<option value="" class="form-control" >{{ __('app.select') . ' ' . __('app.product') }}</option>' +
                            options);
                        $('#add-products').selectpicker('refresh');
                    }
                }
            });
        });

        Dropzone.autoDiscover = false;
            //Dropzone class
        orderDropzone = new Dropzone("div#file-upload-dropzone", {
            dictDefaultMessage: "{{ __('app.dragDrop') }}",
            url: "{{ route('purchase-order-file.store') }}",
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
                orderDropzone = this;
            }
        });
        orderDropzone.on('sending', function(file, xhr, formData) {
            var ids = "{{ $order->id }}";
            formData.append('order_id', ids);
            $.easyBlockUI();
        });
        orderDropzone.on('uploadprogress', function() {
            $.easyBlockUI();
        });
        orderDropzone.on('completemultiple', function() {
            var msgs = "@lang('messages.recordSaved')";
            window.location.href = "{{ route('purchase-order.index') }}"
        });

        var file = $('#sortable .dropify').dropify({
            messages: dropifyMessages
        });

        file.on('dropify.beforeClear', function(event, element) {

            let purchase_item_id = $(this).data('id');
            let file_path = $(this).data('default-file');

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

                    var url = "{{ route('purchase_order.delete_image') }}";
                    var token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'get',
                        url: url,
                        blockUI: true,
                        data: {
                            '_token': token,
                            'purchase_item_id': purchase_item_id,
                            'file_path': file_path
                        },
                        success: function(response) {
                            if (response.status == "success") {
                                element.resetPreview();
                            }
                        }
                    });
                }
            });

            return false;
        });

        const dp1 = datepicker('#purchase_date', {
            position: 'bl',
            ...datepickerConfig
        });

        const dp2 = datepicker('#expected_date', {
            position: 'bl',
            ...datepickerConfig
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
            var currencyId = $('#currency_id').val();

            $.easyAjax({
                url: "{{ route('purchase_order.add_item') }}",
                type: "GET",
                data: {
                    id: id,
                    currencyId: currencyId
                },
                blockUI: true,
                success: function(response) {
                    if($('input[name="item_name[]"]').val() == ''){
                        $("#sortable .item-row").remove();
                    }
                    $(response.view).hide().appendTo("#sortable").fadeIn(500);
                    calculateTotal();

                    var noOfRows = $(document).find('#sortable .item-row').length;
                    var i = $(document).find('.item_name').length - 1;
                    var itemRow = $(document).find('#sortable .item-row:nth-child(' + noOfRows +
                        ') select.type');
                    itemRow.attr('id', 'multiselect' + i);
                    itemRow.attr('name', 'taxes[' + i + '][]');
                    $(document).find('#multiselect' + i).selectpicker();
                }
            });
        }

        $(document).on('click', '#add-item', function() {

            var i = $(document).find('.item_name').length;
            var item = ' <div class="d-flex px-4 py-3 c-inv-desc item-row">' +
                '<div class="c-inv-desc-table w-100 d-lg-flex d-md-flex d-block">' +
                '<table width="100%">' +
                '<tbody>' +
                '<tr class="text-dark-grey font-weight-bold f-14">' +
                '<td width="{{ $invoiceSetting->hsn_sac_code_show ? '40%' : '50%' }}" class="border-0 inv-desc-mbl btlr">@lang("app.description")</td>';

            @if ($invoiceSetting->hsn_sac_code_show)
                item += '<td width="10%" class="border-0" align="right">@lang("app.hsnSac")</td>';
            @endif

                item +=
                `<td width="10%" class="border-0" align="right">@lang("modules.invoices.qty")</td>
                <td width="10%" class="border-0" align="right">@lang("modules.invoices.unitPrice")</td>
                <td width="13%" class="border-0" align="right">@lang("modules.invoices.tax")</td>
                <td width="17%" class="border-0 bblr-mbl" align="right">@lang("modules.invoices.amount")</td>
                </tr>
                <tr>
                <td class="border-bottom-0 btrr-mbl btlr">
                <input type="text" class="f-14 border-0 w-100 item_name form-control" name="item_name[]" placeholder="@lang("modules.expenses.itemName")">
                </td>
                <td class="border-bottom-0 d-block d-lg-none d-md-none">
                <textarea class="f-14 border-0 w-100 mobile-description form-control" name="item_summary[]" placeholder="@lang("placeholders.invoices.description")"></textarea>
                </td>`;

            @if ($invoiceSetting->hsn_sac_code_show)
                item += '<td class="border-bottom-0">' +
                '<input type="text" min="1" class="f-14 border-0 w-100 text-right hsn_sac_code form-control" value="" name="hsn_sac_code[]">'
                +
                '</td>';
            @endif

                item += '<td class="border-bottom-0">' +
                '<input type="number" min="1" class="f-14 border-0 w-100 text-right quantity form-control mt-3" value="1" name="quantity[]">' +
                `<select class="text-dark-grey float-right border-0 f-12" name="unit_id[]">
                    @foreach ($units as $unit)
                        <option
                        @if ($unit->default == 1) selected @endif
                        value="{{ $unit->id }}">{{ $unit->unit_type }}</option>
                    @endforeach
                </select>
                <input type="hidden" name="product_id[]" value="">`+
                '</td>' +
                '<td class="border-bottom-0">' +
                '<input type="number" min="1" class="f-14 border-0 w-100 text-right cost_per_item form-control" placeholder="0.00" value="0" name="cost_per_item[]">' +
                '</td>' +
                '<td class="border-bottom-0">' +
                '<div class="select-others height-35 rounded border-0">' +
                '<select id="multiselect' + i + '" name="taxes[' + i +
                '][]" multiple="multiple" class="select-picker type customSequence" data-size="3">'
                @foreach ($taxes as $tax)
                +'<option data-rate="{{ $tax->rate_percent }}" data-tax-text="{{ strtoupper($tax->tax_name) .':'. $tax->rate_percent }}%" value="{{ $tax->id }}">'
                +'{{ strtoupper($tax->tax_name) }}:{{ $tax->rate_percent }}%</option>'
                @endforeach
                +
                '</select>' +
                '</div>' +
                '</td>' +
                '<td rowspan="2" align="right" valign="top" class="bg-amt-grey btrr-bbrr">' +
                '<span class="amount-html">0.00</span>' +
                '<input type="hidden" class="amount" name="amount[]" value="0">' +
                '</td>' +
                '</tr>' +
                '<tr class="d-none d-md-table-row d-lg-table-row">' +
                '<td colspan="{{ $invoiceSetting->hsn_sac_code_show ? 4 : 3 }}" class="dash-border-top bblr">' +
                '<textarea class="f-14 border-0 w-100 desktop-description" name="item_summary[]" placeholder="@lang("placeholders.invoices.description")"></textarea>' +
                '</td>' +
                '<td td class="border-left-0">' +
                '<input type="file" class="dropify" id="dropify'+i+'" name="order_item_image[]" data-allowed-file-extensions="png jpg jpeg" data-messages-default="test" data-height="70""/><input type="hidden" name="order_item_image_url[]">' +
                '</td>' +
                '</tr>' +
                '</tbody>' +
                '</table>' +
                '</div>' +
                '<a href="javascript:;" class="d-flex align-items-center justify-content-center ml-3 remove-item"><i class="fa fa-times-circle f-20 text-lightest"></i></a>' +
                '</div>';
            $(item).hide().appendTo("#sortable").fadeIn(500);
            $('#multiselect' + i).selectpicker();

            $(document).find('#dropify' + i).dropify({
                messages: dropifyMessages
            });
        });

        $('#saveOrderForm').on('click', '.remove-item', function() {
            $(this).closest('.item-row').fadeOut(300, function() {
                $(this).remove();
                $('select.customSequence').each(function(index) {
                    $(this).attr('name', 'taxes[' + index + '][]');
                    $(this).attr('id', 'multiselect' + index + '');
                });
                calculateTotal();
            });
        });

        $('.save-form').click(function() {
            var type = $(this).data('type');

            if (KTUtil.isMobileDevice()) {
                $('.desktop-description').remove();
            } else {
                $('.mobile-description').remove();
            }

            calculateTotal();

            var discount = $('#discount_amount').html();
            var total = $('.sub-total-field').val();

            if (parseFloat(discount) > parseFloat(total)) {
                Swal.fire({
                    icon: 'error',
                    text: "{{ __('messages.discountExceed') }}",

                    customClass: {
                        confirmButton: 'btn btn-primary',
                    },
                    showClass: {
                        popup: 'swal2-noanimation',
                        backdrop: 'swal2-noanimation'
                    },
                    buttonsStyling: false
                });
                return false;
            }

            $.easyAjax({
                url: "{{ route('purchase-order.update', $order->id) }}" + "?type=" + type,
                container: '#saveOrderForm',
                type: "POST",
                blockUI: true,
                redirect: true,
                file: true,  // Commented so that we dot get error of Input variables exceeded 1000
                data: $('#saveOrderForm').serialize(),
                success: function(response) {
                    console.log(response);
                    if (orderDropzone.getQueuedFiles().length > 0) {
                        orderDropzone.processQueue();
                    }
                    else {
                        if(response.status === 'fail'){
                            return;
                        }
                        window.location.href = response.redirectUrl;
                    }
                }
            })
        });

        $('#saveOrderForm').on('keyup', '.quantity,.cost_per_item,.item_name, .discount_value', function() {
            var quantity = $(this).closest('.item-row').find('.quantity').val();
            var perItemCost = $(this).closest('.item-row').find('.cost_per_item').val();
            var amount = (quantity * perItemCost);

            $(this).closest('.item-row').find('.amount').val(decimalupto2(amount));
            $(this).closest('.item-row').find('.amount-html').html(decimalupto2(amount));

            calculateTotal();
        });

        $('#saveOrderForm').on('change', '.type, #discount_type, #calculate_tax', function() {
            var quantity = $(this).closest('.item-row').find('.quantity').val();
            var perItemCost = $(this).closest('.item-row').find('.cost_per_item').val();
            var amount = (quantity * perItemCost);

            $(this).closest('.item-row').find('.amount').val(decimalupto2(amount));
            $(this).closest('.item-row').find('.amount-html').html(decimalupto2(amount));

            calculateTotal();
        });

        $('#saveOrderForm').on('input', '.quantity', function() {
            var quantity = $(this).closest('.item-row').find('.quantity').val();
            var perItemCost = $(this).closest('.item-row').find('.cost_per_item').val();
            var amount = (quantity * perItemCost);

            $(this).closest('.item-row').find('.amount').val(decimalupto2(amount));
            $(this).closest('.item-row').find('.amount-html').html(decimalupto2(amount));

            calculateTotal();
        });

        calculateTotal();

        init(RIGHT_MODAL);

    });

    function checkboxChange(parentClass, id){
        var checkedData = '';
        $('.'+parentClass).find("input[type= 'checkbox']:checked").each(function () {
            checkedData = (checkedData !== '') ? checkedData+', '+$(this).val() : $(this).val();
        });
        $('#'+id).val(checkedData);
    }

    $('#currency_id').change(function() {
        var curId = $(this).val();
        var companyCurrencyName = "{{$companyCurrency->currency_code}}";
        var currentCurrencyName = $('#currency_id option:selected').attr('data-currency-code');
        var companyCurrency = '{{ $companyCurrency->id }}';

        if(curId == companyCurrency){
            $('#exchange_rate').prop('readonly', true);
        } else{
            $('#exchange_rate').prop('readonly', false);
        }
        var token = "{{ csrf_token() }}";

        $.easyAjax({
            url: "{{ route('payments.account_list') }}",
            container: '#saveOrderForm',
            type: "GET",
            blockUI: true,
            data: { 'curId' : curId , _token: token},
            success: function(response) {
                if (response.status == 'success') {
                    $('#bank_account_id').html(response.data);
                    $('#bank_account_id').selectpicker('refresh');
                    $('#exchange_rate').val(response.exchangeRate);
                    $('#currency_exchange').html('( '+companyCurrencyName+' @lang('app.to') '+currentCurrencyName+' )');
                }
            }
        });
    });

    $('#vendor_id').change(function(){
        var vendorId = $(this).val();
        var url = "{{route('purchase_order.vendor_currency')}}" + "?id=" + vendorId;
        $.easyAjax({
            url: url,
            container: '#saveOrderForm',
            type: "GET",
            blockUI: true,
            success: function(response) {
                $('#currency_id').html('<option value="'+response.data.id+'">'+response.data.currency_code+ ' ('+response.data.currency_symbol+')'+'</option>');
                $('#currency_id').selectpicker('refresh');
            }
        });
    });

</script>
