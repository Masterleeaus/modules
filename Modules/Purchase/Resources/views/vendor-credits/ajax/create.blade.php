<link rel="stylesheet" href="{{ asset('vendor/css/dropzone.min.css') }}">

<!-- CREATE INVOICE START -->
<div class="bg-white rounded b-shadow-4 create-inv">

    <!-- HEADING START -->
    <div class="px-lg-4 px-md-4 px-3 py-3">
        <h4 class="mb-0 f-21 font-weight-normal ">@lang('purchase::app.menu.vendorCredit') @lang('app.details')</h4>
    </div>
    <!-- HEADING END -->
    <hr class="m-0 border-top-grey">
    <!-- FORM START -->
    <x-form class="c-inv-form" id="saveInvoiceForm">

        <!-- CLIENT, PROJECT, GST, BILLING, SHIPPING ADDRESS START -->
        <div class="row px-lg-4 px-md-4 px-3 pt-3">
            <input type = "hidden" value="{{ $getbill->id }}" name="billId" id="billId" />
            <!-- CLIENT START -->
            <div class="col-md-4 mb-4">
                <x-forms.label fieldId="vendor_id" :fieldLabel="__('purchase::app.menu.vendor')">
                </x-forms.label>
                <div class="select-others height-35 rounded">
                    <input type = "hidden" id="vendor_id" name="vendor_id" value = {{$getbill->purchase_vendor_id}} />
                    <select class="form-control select-picker vendors" data-live-search="true" data-size="8"
                        name="vendor_id" id="vendor_id" disabled>
                        <option value="">--</option>
                            @foreach ($vendor as $item)
                                <option @if (isset($item) && $getbill->purchase_vendor_id == $item->id)
                                    selected @endif value="{{ $item->id }}">
                                    {{ $item->primary_name }}</option>
                            @endforeach
                    </select>

                </div>
            </div>
            <div class="col-md-4 mb-4">
                <x-forms.label class="mb-12" fieldId="credit_note_no"
                    :fieldLabel="__('purchase::modules.vendor.vendorCreditNumber')" fieldRequired="true">
                </x-forms.label>

                <div class="select-others height-35 rounded">
                <x-forms.input-group>
                    <x-slot name="prepend">
                        <span
                            class="input-group-text">{{ $purchaseSetting->vendor_credit_prefix }}{{ $purchaseSetting->vendor_credit_separator }}{{ $zero }}</span>
                    </x-slot>
                    <input type="number" name="credit_note_no" id="credit_note_no" class="form-control height-35 f-15"
                        value="{{ is_null($lastVendorCredit) ? 1 : $lastVendorCredit }}" readonly>
                </x-forms.input-group>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <x-forms.label fieldId="due_date" :fieldLabel="__('purchase::modules.vendor.vendorCrediTDate')">
                </x-forms.label>
                <div class="input-group">
                    <input type="text" id="vendor_credit_date" name="vendor_credit_date"
                        class="px-6 position-relative text-dark font-weight-normal form-control height-35 rounded p-0 text-left f-15"
                        placeholder="@lang('placeholders.date')"
                        value="{{ now(company()->timezone)->translatedFormat(company()->date_format) }}">
                </div>
            </div>
        </div>
        <div class="row px-lg-4 px-md-4 px-3 pt-3">
            <div class="col-md-3">
                <div class="form-group c-inv-select mb-5">
                    <input type="hidden" id="currency_id" name="currency_id" value="{{company()->currency_id}}">
                    <x-forms.label fieldId="currency_id" :fieldLabel="__('modules.invoices.currency')">
                    </x-forms.label>
                    <select class="form-control select-picker vendors" data-live-search="true" data-size="8"
                    name="currency_id" id="currency_id">
                    @foreach ($currencies as $currency)
                        <option @if (isset($proposalTemplate) && $currency->id == $proposalTemplate->currency_id)
                                    selected
                                @elseif ($currency->id == company()->currency_id)
                                    selected
                                @endif
                                value="{{ $currency->id }}">
                            {{ $currency->currency_code . ' (' . $currency->currency_symbol . ')' }}
                        </option>
                    @endforeach
                </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group c-inv-select mb-5">
                <input type = "hidden" id="add-bills" name="bill_id" value="{{$getbill->id}}" />
                <x-forms.label fieldId="add-bills" :fieldLabel="__('app.bill')">
                </x-forms.label>
                <select class="form-control select-picker vendors" data-live-search="true" data-size="8"
                name="bill_id" id="add-bills" disabled>
                    <option value="">{{ __('app.select') . ' ' . __('app.bill') }}</option>
                    @foreach ($bills as $item)
                    <option @if (isset($item) && $getbill->id == $item->id)
                        selected @endif value="{{ $item->id }}" disabled>
                        {{ $item->bill_number }}</option>
                    @endforeach
                </select>
            </div>
            </div>
    </div>
    <input type="hidden" name="calculate_tax" id="calculate_tax" value="{{ $order->calculate_tax }}">
        <hr class="m-0 border-top-grey">
        <div id="sortable">
            @foreach ($items as $key => $item)
                <!-- DESKTOP DESCRIPTION TABLE START -->
                <div class="d-flex px-4 py-3 c-inv-desc item-row">
                    <div class="c-inv-desc-table w-100 d-lg-flex d-md-flex d-block">
                        <table width="100%">
                            <tbody>
                                <tr class="text-dark-grey font-weight-bold f-14">
                                    <td width="{{ $invoiceSetting->hsn_sac_code_show ? '40%' : '50%' }}"
                                        class="border-0 inv-desc-mbl btlr">@lang('app.description')</td>
                                    @if ($invoiceSetting->hsn_sac_code_show)
                                        <td width="10%" class="border-0" align="right">@lang("app.hsnSac")</td>
                                    @endif
                                    <td width="10%" class="border-0" align="right" >
                                        @lang('modules.invoices.qty')
                                    </td>
                                    <td width="10%" class="border-0" align="right">
                                        @lang("modules.invoices.unitPrice")</td>
                                    <td width="13%" class="border-0" align="right">@lang('modules.invoices.tax')
                                    </td>
                                    <td width="17%" class="border-0 bblr-mbl" align="right">
                                        @lang('modules.invoices.amount')</td>
                                </tr>
                                <tr>
                                    <td class="border-bottom-0 btrr-mbl btlr">
                                        <input type="hidden" class="form-control f-14 border-0 w-100 item_name"
                                            name="item_name[]" placeholder="@lang('modules.expenses.itemName')"
                                            value="{{ $item->item_name }}">
                                            {{ $item->item_name }}
                                    </td>
                                    <td class="border-bottom-0 d-block d-lg-none d-md-none">
                                        <input type="hidden" class="form-control f-14 border-0 w-100 mobile-description"
                                            placeholder="@lang('placeholders.invoices.description')"
                                            name="item_summary[]" value="{{ $item->item_summary }}">
                                            {{ $item->item_summary }}
                                    </td>
                                    @if ($invoiceSetting->hsn_sac_code_show)
                                        <td class="border-bottom-0" align="right">
                                            <input type="hidden" class="f-14 border-0 w-100 text-right hsn_sac_code"
                                                value="{{ $item->hsn_sac_code }}" name="hsn_sac_code[]">
                                            {{ !is_null($item->hsn_sac_code) ? $item->hsn_sac_code : '--' }}
                                        </td>
                                    @endif
                                    <td class="border-bottom-0" align="right">
                                        <input type="hidden"
                                            class="form-control f-14 border-0 w-100 text-right quantity"
                                            value="{{ $item->quantity }}" name="quantity[]">
                                        {{ $item->quantity }}
                                        @if (!is_null($item->unit_id) && $item->unit_id != 0)
                                            <span class="text-dark-grey border-0 f-12">{{ $item->unit->unit_type }}</span>
                                            <input type="hidden" name="product_id[]" value="{{ $item->product_id }}">
                                            <input type="hidden" name="unit_id[]" value="{{ $item->unit_id }}">
                                        @endif
                                    </td>
                                    <td class="border-bottom-0" align="right">
                                        <input type="hidden"
                                            class="f-14 border-0 w-100 text-right cost_per_item" placeholder="0.00"
                                            value="{{ $item->unit_price }}" name="cost_per_item[]">
                                            <span>{{ $item->unit_price }}</span>
                                    </td>
                                    <td class="border-bottom-0">
                                        <div class="select-others height-35 rounded border-0">
                                            <select id="multiselect"
                                                multiple="multiple" class="select-picker type customSequence border-0"
                                                data-size="3" disabled>
                                                @foreach ($taxes as $tax)
                                                    <option data-rate="{{ $tax->rate_percent }}" data-tax-text="{{ strtoupper($tax->tax_name) .':'. $tax->rate_percent }}%" @if (isset($item->taxes) && array_search($tax->id, $item->taxes->pluck('id')->toArray()) !== false)
                                                    selected @endif
                                                    value="{{ $tax->id }}">
                                                    {{ strtoupper($tax->tax_name) }}:{{ $tax->rate_percent }}%
                                                    </option>
                                                @endforeach
                                            </select>
                                            @foreach ($taxes as $tax)
                                                @if (isset($item->taxes) && array_search($tax->id, $item->taxes->pluck('id')->toArray()) !== false)
                                                    <input type="hidden" name="taxes[{{ $key }}][]" value="{{ $tax->id }}">
                                                @endif
                                            @endforeach
                                        </div>
                                    </td>
                                    <td rowspan="2" align="right" valign="top" class="bg-amt-grey btrr-bbrr">
                                        <span class="amount-html">{{ number_format((float) $item->amount, 2, '.', '') }}</span>
                                        <input type="hidden" class="amount" name="amount[]" value="{{ $item->amount }}">
                                    </td>
                                </tr>
                                <tr class="d-none d-md-block d-lg-table-row">
                                    <td colspan="{{ $invoiceSetting->hsn_sac_code_show ? '4' : '3' }}" class="dash-border-top bblr">
                                        <textarea class="f-14 border-0 w-100 desktop-description" name="item_summary[]" readonly
                                            placeholder="@lang('placeholders.invoices.description')">{{ $item->item_summary }}</textarea>
                                    </td>
                                    <td class="border-left-0">
                                        <input type="file"
                                        class="dropify"
                                        name="invoice_item_image[]"
                                        data-allowed-file-extensions="png jpg jpeg"
                                        data-messages-default="test"
                                        data-height="70"
                                        data-id="{{ $item->id }}"
                                        id="{{ $item->id }}"
                                        data-default-file="{{ $item->purchaseItemImage ? $item->purchaseItemImage->file_url : '' }}"
                                        disabled="disabled"
                                        />
                                        <input type="hidden" name="invoice_item_image_url[]" value="{{ $item->purchaseItemImage ? (!empty($item->purchaseItemImage->external_link) ? $item->purchaseItemImage->external_link : $item->purchaseItemImage->file_url) : '' }}">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- DESKTOP DESCRIPTION TABLE END -->
            @endforeach
        </div>

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
                                        <td width="30%" class="border-top-0 sub-total">0.00</td>
                                        <input type="hidden" class="sub-total-field" name="sub_total" value="0">
                                    </tr>
                                    <tr>
                                        <td width="30%" class="text-dark-grey">@lang('modules.invoices.discount')
                                        </td>
                                        <td width="30%" style="padding: 5px;">
                                            <table width="100%">
                                                <tbody>
                                                    <tr>
                                                        <td width="50%" class="c-inv-sub-padding">
                                                            <input type="hidden" min="0" name="discount_value"
                                                                class="form-control f-14 border-0 w-100 text-right discount_value"
                                                                placeholder="0" value="{{ $order->discount }}">
                                                            <span>{{ $order->discount }}</span>
                                                        </td>
                                                        <td width="50%" align="left" class="c-inv-sub-padding">
                                                            <div class="select-others select-tax height-35 rounded border-0">
                                                                <input type="hidden" value="{{ $order->discount_type }}" name="discount_type"/>
                                                                <select class="form-control select-picker" id="discount_type"
                                                                    disabled>
                                                                    <option @if ($order->discount_type == 'percent') selected @endif value="percent">%</option>
                                                                    <option @if ($order->discount_type == 'fixed') selected @endif value="fixed">
                                                                        @lang('modules.invoices.amount')</option>
                                                                </select>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                        <td>
                                            <span id="discount_amount">
                                                {{ number_format((float) $order->discount, 2, '.', '') }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>@lang('modules.invoices.tax')</td>
                                        <td colspan="2" class="p-0 border-0">
                                            <table width="100%" id="invoice-taxes">
                                                <tr>
                                                    <td colspan="2"><span class="tax-percent">0.00</span></td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr class="bg-amt-grey f-16 f-w-500">
                                        <td colspan="2">@lang('modules.invoices.total')</td>
                                        <td><span class="total">0.00</span></td>
                                        <input type="hidden" class="total-field" name="total" value="0">
                                        <input type="hidden" id="total-field" value="0">
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
                <!-- NOTE AND TERMS AND CONDITIONS START -->
                <div class="d-flex flex-wrap px-lg-4 px-md-4 px-3 py-3">
                    <div class="col-md-6 col-sm-12 c-inv-note-terms p-0 mb-lg-0 mb-md-0 mb-3">
                        <label class="f-14 text-dark-grey mb-12  w-100"
                            for="usr">@lang('modules.invoices.note')</label>
                        <textarea class="form-control" name="note" id="lead_note" rows="4"
                            placeholder="@lang('placeholders.invoices.note')"></textarea>
                    </div>
                    <div class="col-md-6 col-sm-12 p-0 c-inv-note-terms">
                        <x-forms.label fieldId="" :fieldLabel="__('modules.invoiceSettings.invoiceTerms')">
                        </x-forms.label>
                        <p>
                            {!! nl2br($invoiceSetting->invoice_terms) !!}
                        </p>
                    </div>
                </div>
                <!-- NOTE AND TERMS AND CONDITIONS END -->

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
                                        <i class="fa fa-save f-w-500 mr-2 f-11"></i> @lang('app.save')
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item f-14 text-dark save-form" href="javascript:void(0);"
                                        data-type="send">
                                        <i class="fa fa-paper-plane f-w-500  mr-2 f-12"></i> @lang('app.saveSend')
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item f-14 text-dark save-form" href="javascript:void(0);"
                                        data-type="mark_as_send" data-toggle="tooltip" data-original-title="@lang('messages.markSentInfo')">
                                        <i class="fa fa-check-double f-w-500  mr-2 f-12"></i> @lang('app.saveMark')
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <x-forms.button-cancel :link="route('vendor-credits.index')" class="border-0">@lang('app.cancel')
                        </x-forms.button-cancel>

                    </div>


                </x-form-actions>
    </x-form>
    <!-- FORM END -->
</div>
<!-- CREATE INVOICE END -->
<script src="{{ asset('vendor/jquery/dropzone.min.js') }}"></script>
<script>
    $(document).ready(function() {
        let defaultImage = '';
        let lastIndex = 0;

        const hsn_status = {{ $invoiceSetting->hsn_sac_code_show }};
        $('.custom-date-picker').each(function(ind, el) {
            datepicker(el, {
                position: 'bl',
                ...datepickerConfig
            });
        });

        const dp1 = datepicker('#vendor_credit_date', {
            position: 'bl',
            ...datepickerConfig
        });


        $('#add-bills').prop('disabled', true);

        $('#currency_id').prop('disabled', true);

        function ucWord(str){
            str = str.toLowerCase().replace(/\b[a-z]/g, function(letter) {
                return letter.toUpperCase();
            });
            return str;
        }

     $('#saveInvoiceForm').on('click', '.remove-item', function() {
            $(this).closest('.item-row').fadeOut(300, function() {
                $(this).remove();
                $('select.customSequence').each(function(index) {
                    $(this).attr('name', 'taxes[' + index + '][]');
                    $(this).attr('id', 'multiselect' + index + '');
                });
                if($(document).find('#sortable .item-row').length == 0){
                    $('#alertMessage').show().fadeIn(500);
                }
                calculateTotal();
            });
        });
        $('.save-form').click(function() {
            var type = $(this).data('type');

            var opt = $('#vendor_id option:selected').map(function(i,v) {
                return this.value;
            }).get();


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
                url: "{{ route('vendor-credits.store') }}" + "?type=" + type,
                container: '#saveInvoiceForm',
                type: "POST",
                blockUI: true,
                redirect: true,
                file: true,  // Commented so that we dot get error of Input variables exceeded 1000
                data: $('#saveInvoiceForm').serialize()
            })
        });

        $('#saveInvoiceForm').on('keyup', '.quantity,.cost_per_item,.item_name, .discount_value', function() {
            var quantity = $(this).closest('.item-row').find('.quantity').val();
            var perItemCost = $(this).closest('.item-row').find('.cost_per_item').val();
            var amount = (quantity * perItemCost);

            $(this).closest('.item-row').find('.amount').val(decimalupto2(amount));
            $(this).closest('.item-row').find('.amount-html').html(decimalupto2(amount));

            calculateTotal();
        });

        $('#saveInvoiceForm').on('change', '.type, #discount_type, #calculate_tax', function() {
            var quantity = $(this).closest('.item-row').find('.quantity').val();
            var perItemCost = $(this).closest('.item-row').find('.cost_per_item').val();
            var amount = (quantity * perItemCost);

            $(this).closest('.item-row').find('.amount').val(decimalupto2(amount));
            $(this).closest('.item-row').find('.amount-html').html(decimalupto2(amount));

            calculateTotal();
        });

        $('#saveInvoiceForm').on('input', '.quantity', function() {
            var quantity = $(this).closest('.item-row').find('.quantity').val();
            var perItemCost = $(this).closest('.item-row').find('.cost_per_item').val();
            var amount = (quantity * perItemCost);

            $(this).closest('.item-row').find('.amount').val(decimalupto2(amount));
            $(this).closest('.item-row').find('.amount-html').html(decimalupto2(amount));

            calculateTotal();
        });


        calculateTotal();

        $('.vendors').on('change', function(){
            var vendorId = $(this).val();
            var url = "{{ route('vendor-credits.get_bills', ':id') }}"
            url = (vendorId) ? url.replace(':id', vendorId) : url.replace(':id', null);
            $.easyAjax({
                url : url,
                type : "GET",
                success : function(response){
                    if(response.status == 'success'){
                        var options = [];
                        var rData = [];
                        rData = response.data;
                        $.each(rData, function(index, value){
                            var selectData = '';
                            selectData = '<option value="' + value.id + '">'
                                + value.bill_number +'</options>'
                            options.push(selectData);
                        });
                        console.log(options);
                        $('#add-bills').html('<option value=""class="from-control">{{__('app.select') .' ' . __('app.bill')}}</option>' +
                        options);
                        $('#add-bills').selectpicker('refresh');
                    }
                }
            });

        });
        $('#add-bills').on('change', function(){
            var billId = $(this).val();
            if(billId)
            {
                $('#products').addClass('d-none');
                $("#discount").hide();

            }
            else
            {
                $("#alertMessage").show();
                $('#products').removeClass('d-none');
                $('.bill').replaceWith($("#alertMessage"));
            }
        });

        init(RIGHT_MODAL);

    });




</script>

