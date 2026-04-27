<link rel="stylesheet" href="{{ asset('vendor/css/dropzone.min.css') }}">

<div class="bg-white rounded b-shadow-4 create-inv">
    <div class="px-lg-4 px-md-4 px-3 py-3">
        <h4 class="mb-0 f-21 font-weight-normal ">@lang('purchase::app.menu.vendorCredit') @lang('app.details')
        </h4>
    </div>
    <hr class="m-0 border-top-grey">
    <x-form class="c-inv-form" id="saveInvoiceForm">
        @method('PUT')
    <div class="row px-lg-4 px-md-4 px-3 pt-3">
        <div class="col-md-4 mb-4">
            <x-forms.label fieldId="vendor_id" :fieldLabel="__('purchase::app.menu.vendor')">
            </x-forms.label>
            <div class="select-others height-35 rounded">
                <select class="form-control select-picker" disabled data-live-search="true" data-size="8"
                    name="vendor_id" id="vendor_id" readonly>
                    <option value="">--</option>
                        @foreach ($vendor as $item)
                            <option @if ($item->id == $vendorCredit->vendor_id) selected @endif value="{{ $item->id }}" readonly>
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
                <input type="text" name="credit_note_no" id="credit_note_no" class="form-control height-35 f-15"
                    value="{{ $vendorCredit->vendor_credit_number }}" readonly>
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
            <x-forms.select :fieldLabel="__('modules.invoices.currency')" fieldName="currency_id"
            field_id="currency_id">
            @foreach ($currencies as $currency)
                <option @if ($currency->id == $vendorCredit->currency_id)
                            selected
                        @endif
                        value="{{ $currency->id }}">
                    {{ $currency->currency_code . ' (' . $currency->currency_symbol . ')' }}
                </option>
            @endforeach
        </x-forms.select>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group c-inv-select mb-5">
        <x-forms.select :fieldLabel="__('app.bill')" fieldName="bill_id"
            field_id="add-bills">
            <option value="">{{ __('app.select') . ' ' . __('app.bill') }}</option>
            @foreach ($bills as $item)
            <option @if (isset($item) && $vendorCredit->bill_id == $item->id)
                selected @endif value="{{ $item->id }}" readonly>
                {{ $item->bill_number }}</option>
            @endforeach
        </x-forms.select>
        </div>
    </div>
    </div>
    <input type="hidden" name="calculate_tax" id="calculate_tax" value="{{ $vendorCredit->calculate_tax }}">

    <hr class="m-0 border-top-grey">
    <div id="sortable">
        @if ($vendorCredit->bill_id)
            @foreach ($vendorCredit->items as $key => $item)
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
                                                    <option data-rate="{{ $tax->rate_percent }}" data-tax-text="{{ strtoupper($tax->tax_name) .':'. $tax->rate_percent }}%" @if (isset($item->taxes) && array_search($tax->id, json_decode($item->taxes)) !== false)
                                                    selected @endif
                                                    value="{{ $tax->id }}">
                                                    {{ strtoupper($tax->tax_name) }}:{{ $tax->rate_percent }}%
                                                    </option>
                                                @endforeach
                                            </select>
                                            @foreach ($taxes as $tax)
                                                @if (isset($item->taxes) && array_search($tax->id, json_decode($item->taxes)) !== false)
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
                                        data-default-file="{{ $item->purchaseVendorCreditItemImage ? $item->purchaseVendorCreditItemImage->file_url : '' }}"
                                        disabled="disabled"
                                        />
                                        <input type="hidden" name="invoice_item_image_url[]" value="{{ $item->purchaseVendorCreditItemImage ? (!empty($item->purchaseVendorCreditItemImage->external_link) ? $item->purchaseVendorCreditItemImage->external_link : $item->purchaseVendorCreditItemImage->file_url) : '' }}">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- DESKTOP DESCRIPTION TABLE END -->
            @endforeach
        @else
            <div class="d-flex px-4 py-3 c-inv-desc item-row">
                <div class="c-inv-desc-table w-100 d-lg-flex d-md-flex d-block">
                    <table width="100%">
                        <tbody>
                            <tr class="i-d-heading bg-light-grey text-dark-grey font-weight-bold">
                                <td width="{{ $invoiceSetting->hsn_sac_code_show ? '40%' : '50%' }}"
                                    class="border-0 inv-desc-mbl btlr">@lang('app.description')
                                    <input type="hidden" name="item_ids[]" value="{{ $item->id }}">
                                </td>
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
                                        item1
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
                                        value="{{ 1 }}" name="quantity[]">
                                    1
                                    @if (!is_null($item->unit_id) && $item->unit_id != 0)
                                        <span class="text-dark-grey border-0 f-12">{{ $item->unit->unit_type }}</span>
                                        <input type="hidden" name="product_id[]" value="{{ $item->product_id }}">
                                        <input type="hidden" name="unit_id[]" value="{{ $item->unit_id }}">
                                    @endif
                                </td>
                                <td class="border-bottom-0" align="right">
                                    <input type="hidden"
                                        class="f-14 border-0 w-100 text-right cost_per_item" placeholder="0.00"
                                        value="{{ currency_format($vendorCredit->total, $vendorCredit->currency_id, false) }}" name="cost_per_item[]">
                                        <span>{{ currency_format($vendorCredit->total, $vendorCredit->currency_id, false) }}</span>
                                </td>
                                <td class="border-bottom-0">
                                    <div class="select-others height-35 rounded border-0">
                                        <select id="multiselect"
                                            multiple="multiple" class="select-picker type customSequence border-0"
                                            data-size="3" disabled>
                                            @foreach ($taxes as $tax)
                                                <option data-rate="{{ $tax->rate_percent }}" data-tax-text="{{ strtoupper($tax->tax_name) .':'. $tax->rate_percent }}%" @if (isset($item->taxes) && array_search($tax->id, json_decode($item->taxes)) !== false)
                                                selected @endif
                                                value="{{ $tax->id }}">
                                                {{ strtoupper($tax->tax_name) }}:{{ $tax->rate_percent }}%
                                                </option>
                                            @endforeach
                                        </select>
                                        @foreach ($taxes as $tax)
                                            @if (isset($item->taxes) && array_search($tax->id, json_decode($item->taxes)) !== false)
                                                <input type="hidden" name="taxes[{{ $key }}][]" value="{{ $tax->id }}">
                                            @endif
                                        @endforeach
                                    </div>
                                </td>
                                {{-- <td align="right">{{ currency_format($vendorCredit->total, $vendorCredit->currency_id, false) }}</td> --}}

                                <td rowspan="2" align="right" valign="top" class="btrr-bbrr">
                                    <span class="amount-html">{{ currency_format($vendorCredit->total, $vendorCredit->currency_id, false) }}</span>
                                    <input type="hidden" class="amount" name="amount[]" value="{{ currency_format($vendorCredit->total, $vendorCredit->currency_id, false) }}">
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
                                    data-default-file="{{ $item->purchaseVendorCreditItemImage ? $item->purchaseVendorCreditItemImage->file_url : '' }}"
                                    disabled="disabled"
                                    />
                                    <input type="hidden" name="invoice_item_image_url[]" value="{{ $item->purchaseVendorCreditItemImage ? (!empty($item->purchaseVendorCreditItemImage->external_link) ? $item->purchaseVendorCreditItemImage->external_link : $item->purchaseVendorCreditItemImage->file_url) : '' }}">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
    <!--  ADD ITEM START-->
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
                                                            placeholder="0" value="{{ $vendorCredit->discount }}">
                                                        <span>{{ $vendorCredit->discount }}</span>
                                                    </td>
                                                    <td width="50%" align="left" class="c-inv-sub-padding">
                                                        <div class="select-others select-tax height-35 rounded border-0">
                                                            <input type="hidden" value="{{ $vendorCredit->discount_type }}" name="discount_type"/>
                                                            <select class="form-control select-picker" id="discount_type"
                                                                disabled>
                                                                <option @if ($vendorCredit->discount_type == 'percent') selected @endif value="percent">%</option>
                                                                <option @if ($vendorCredit->discount_type == 'fixed') selected @endif value="fixed">
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
                                            {{ number_format((float) $vendorCredit->discount, 2, '.', '') }}
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
    <div class="d-flex flex-wrap px-lg-4 px-md-4 px-3 py-3">
        <div class="col-md-6 col-sm-12 c-inv-note-terms p-0 mb-lg-0 mb-md-0 mb-3">
            <label class="f-14 text-dark-grey mb-12  w-100"
                for="usr">@lang('modules.invoices.note')</label>
            <textarea class="form-control" name="note" id="note" rows="4"
                placeholder="@lang('placeholders.invoices.note')">{{ $vendorCredit->note }}</textarea>
        </div>
        <div class="col-md-6 col-sm-12 p-0 c-inv-note-terms">
            <x-forms.label fieldId="" :fieldLabel="__('modules.invoiceSettings.invoiceTerms')">
            </x-forms.label>
            <p>
                {!! nl2br($invoiceSetting->invoice_terms) !!}
            </p>
        </div>
    </div>
    <x-form-actions class="c-inv-btns">
        <div class="c-inv-btns">

            <div class="d-flex">

                <div class="inv-action dropup mr-3">
                    <button class="btn-primary dropdown-toggle" type="button" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        @lang('app.save')
                        <span><i class="fa fa-chevron-down f-15 text-white"></i></span>
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
                    </ul>
                </div>

            </div>

            <x-forms.button-cancel :link="route('invoices.index')" class="border-0">@lang('app.cancel')
            </x-forms.button-cancel>

        </div>
    </x-form-actions>
    </x-form>

</div>
<script src="{{ asset('vendor/jquery/dropzone.min.js') }}"></script>
    <script>
        $(document).ready(function() {

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

            $('.save-form').click(function() {

                if (KTUtil.isMobileDevice()) {
                    $('.desktop-description').remove();
                } else {
                    $('.mobile-description').remove();
                }

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
                    url: "{{ route('vendor-credits.update', $vendorCredit->id) }}",
                    container: '#saveInvoiceForm',
                    type: "POST",
                    blockUI: true,
                    redirect: true,
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

            $('#saveInvoiceForm').on('change', '.type, #discount_type', function() {
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

            /* This is used for calculation purpose */
            $('#total-field').val($('.total-field').val());

            init(RIGHT_MODAL);
        });
    </script>



