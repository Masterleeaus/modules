<link rel="stylesheet" href="{{ asset('vendor/css/dropzone.min.css') }}">

<!-- CREATE BILL
     START -->
<div class="bg-white rounded b-shadow-4 create-inv">


    <!-- HEADING START -->
    <div class="px-lg-4 px-md-4 px-3 py-3">
        <h4 class="mb-0 f-21 font-weight-normal ">@lang('purchase::app.menu.bill')</h4>
    </div>
    <!-- HEADING END -->
    <hr class="m-0 border-top-grey">
    <!-- FORM START -->
    <x-form class="c-inv-form" id="save-bill-data-form">
        @method('PUT')
        <!-- BILL NUMBER, DATE, FREQUENCY START -->
        <div class="row px-lg-4 px-md-4 px-3 pt-3">
            <!-- BILL NUMBER START -->
            <div class="col-md-4">
                <div class="form-group mb-lg-0 mb-md-0 mb-4">
                    <x-forms.label class="mb-12" fieldId="bill_number"
                        :fieldLabel="__('purchase::app.menu.billNumber')" fieldRequired="true">
                    </x-forms.label>
                    <x-forms.input-group>
                        <x-slot name="prepend">
                            <span
                                class="input-group-text">{{ $purchaseSetting->bill_prefix }}{{ $purchaseSetting->bill_number_separator }}{{ $zero }}
                            </span>
                        </x-slot>
                        <input readOnly type="number" name="bill_number" id="bill_number" class="form-control height-35 f-15"
                            value="{{ $purchaseBill->original_bill_number }}">
                    </x-forms.input-group>
                </div>
            </div>

        <!-- BILL NUMBER END -->
            <!-- SELECT VENDOR -->
            <div class="col-md-4">
                <x-forms.label fieldId="vendor-id" :fieldLabel="__('purchase::app.selectVendor')">
                </x-forms.label>
                <input type="hidden" value="{{ $purchaseBill->purchaseVendor->id }}" name="vendor_id">
                <input type="text" readOnly name="vendor_name" id="vendor_id" class="form-control height-35 f-15"
                            value="{{ $purchaseBill->purchaseVendor->primary_name }}">
            </div>
            <!-- SELECT VENDOR -->

            <!-- BILL DATE START -->
            <div class="col-md-4">
                <div class="form-group mb-lg-0 mb-md-0 mb-4">
                    <x-forms.label fieldId="due_date" :fieldLabel="__('purchase::app.menu.billDate')">
                    </x-forms.label>
                    <div class="input-group">
                        <input type="text" id="bill_date" name="issue_date"
                            class="px-6 position-relative text-dark font-weight-normal form-control height-35 rounded p-0 text-left f-15"
                            placeholder="@lang('placeholders.date')"
                            value="{{ $purchaseBill->bill_date->translatedFormat(company()->date_format) }}">
                    </div>
                </div>
            </div>
            <!-- BILL DATE END -->




        </div>
        <!-- INVOICE NUMBER, DATE, DUE DATE, FREQUENCY END -->

        <div class="row px-lg-4 px-md-4 px-3 py-3">
            <div class="col-md-3">
                <x-forms.label fieldId="purchase_order" :fieldLabel="__('purchase::app.purchaseOrder.purchaseOrder')">
                </x-forms.label>
                <div class="form-group c-inv-select mb-2">
                    <input readOnly type="text" id="purchase_order" name="purchase_order"
                            class="px-6 positioissue_daten-relative text-dark font-weight-normal form-control height-35 rounded p-0 text-left f-15"
                            value="{{ $purchaseBill->order->purchase_order_number}}">
                </div>
            </div>
        </div>

        <div id="sortable">
            <!-- DESKTOP DESCRIPTION TABLE START -->
        @foreach($purchaseItems as $item)
            <div class="d-flex px-4 py-3 c-inv-desc item-row" id="bill-desc">

                <div class="c-inv-desc-table w-100 d-lg-flex d-md-flex d-block ">
                    <table width="100%">
                        <tbody>
                            <tr class="text-dark-grey font-weight-bold f-14">
                                <td width="{{ $item->hsn_sac_code_show ? '40%' : '50%' }}"
                                    class="border-0 inv-desc-mbl btlr">@lang('app.description')</td>
                                @if ($item->hsn_sac_code_show)
                                    <td width="10%" class="border-0" align="right">@lang('app.hsnSac')</td>
                                @endif
                                <td width="10%" class="border-0" align="right">@lang('modules.invoices.qty')</td>
                                <td width="10%" class="border-0" align="right">@lang('modules.invoices.unitPrice')</td>
                                <td width="13%" class="border-0" align="right">@lang('modules.invoices.tax')</td>
                                <td width="17%" class="border-0 bblr-mbl" align="right">@lang('modules.invoices.amount')</td>
                            </tr>
                            <tr>
                                <td class="border-bottom-0 btrr-mbl btlr">
                                    <input type="text" class="form-control f-14 border-0 w-100 item_name" name="item_name[]"
                                        placeholder="@lang('modules.expenses.itemName')" value="{{ $item->item_name }}" readonly>
                                </td>
                                <td class="border-bottom-0 d-block d-lg-none d-md-none">
                                    <textarea class="form-control f-14 border-0 w-100 mobile-description" name="item_summary[]"
                                        placeholder="@lang('placeholders.invoices.description')">{{ strip_tags($item->item_summary) }}</textarea>
                                </td>
                                @if ($item->hsn_sac_code_show)
                                    <td class="border-bottom-0">
                                        <input type="text" min="1"
                                            class="form-control f-14 border-0 w-100 text-right hsn_sac_code"
                                            data-item-id="{{ $item->id }}" value="{{ $item->hsn_sac_code }}"
                                            name="hsn_sac_code[]" readonly>
                                    </td>
                                @endif
                                <td class="border-bottom-0">
                                    <input type="number" min="1"
                                        class="form-control f-14 border-0 w-100 text-right quantity mt-3"
                                        data-item-id="{{ $item->id }}" value="{{$item->quantity}}" name="quantity[]" readonly>
                                    <span class="text-dark-grey float-right border-0 f-12">{{ $item->unit->unit_type }}</span>
                                </td>
                                <td class="border-bottom-0">
                                    <input type="number" min="1"
                                        class="f-14 border-0 w-100 text-right cost_per_item form-control"
                                        data-item-id="{{ $item->id }}" placeholder="{{ $item->unit_price }}"
                                        value="{{ $item->unit_price }}" name="cost_per_item[]" readonly>
                                </td>
                                <td class="border-bottom-0">
                                                    @foreach ($item->taxes as $tax)
                                                    <span data-rate="{{ $tax->rate_percent }}"
                                                        data-tax-text="{{ strtoupper($tax->tax_name) .':'. $tax->rate_percent }}%"
                                                        >{{$tax->tax_name.': '.$tax->rate_percent.'%'}}</span><br>
                                                    @endforeach
                                </td>
                                <td rowspan="2" align="right" valign="top" class="bg-amt-grey btrr-bbrr">
                                    <span class="amount-html" data-item-id="{{ $item->id }}">0.00</span>
                                    <input type="hidden" class="amount" name="amount[]" data-item-id="{{ $item->id }}"
                                        value="0">
                                </td>
                            </tr>
                            <tr class="d-none d-md-table-row d-lg-table-row">
                                <td colspan="{{ $item->hsn_sac_code_show ? '4' : '3' }}" class="dash-border-top bblr">
                                    <textarea class="form-control f-14 border-0 w-100 desktop-description" name="item_summary[]"
                                        placeholder="@lang('placeholders.invoices.description')" readonly>{{ strip_tags($item->item_summary) }}</textarea>
                                </td>
                                @if(isset($item->purchaseItemImage->file_url))
                                    <td class="border-left-0">
                                        <input type="file" class="dropify" id="dropify" name="order_item_image[]"
                                            data-allowed-file-extensions="png jpg jpeg" data-messages-default="test" data-height="70"
                                            data-default-file="{{ $item->purchaseItemImage->file_url }}" />
                                        <input type="hidden" name="order_item_image_url[]" value="{{ $item->purchaseItemImage->file_url }}">
                                    </td>
                                @endif
                            </tr>
                        </tbody>
                    </table>

                </div>

            </div>
        @endforeach
    <!-- DESKTOP DESCRIPTION TABLE END -->

    <hr class="m-0 border-top-grey mt-2">
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
                                    <td width="20%" class="text-dark-grey">@lang('modules.invoices.discount')
                                    </td>
                                    <td width="40%" style="padding: 5px;">
                                        <table width="100%">
                                            <tbody>
                                                <tr>
                                                    <td width="70%" class="c-inv-sub-padding">
                                                        <input readonly type="number" min="0" name="discount_value"
                                                            class="form-control f-14 border-0 w-100 text-right discount_value"
                                                            placeholder="0"
                                                            value="{{ isset($purchaseBill) ? $purchaseBill->discount : '0' }}">
                                                    </td>
                                                    <td width="30%" align="left" class="c-inv-sub-padding">
                                                        <input type="text" class="form-control f-14 border-0 w-100 text-right discount_value"
                                                        value="{{ $item->purchaseOrder->discount_type == 'percent' ? '%' : 'Amt.'}}" readonly>
                                                        <input type="hidden" id = "discount_type" name="discount_type" value="{{ isset($item->purchaseOrder) ? $item->purchaseOrder->discount_type : ''}}">
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                    <td><span
                                            id="discount_amount">{{ isset($purchaseBill) ? number_format((float) $purchaseBill->discount, 2, '.', '') : '0.00' }}</span>
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
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

            <!-- DESKTOP DESCRIPTION TABLE END -->

        </div>

        <hr class="m-0 border-top-grey">


        <!-- NOTE AND TERMS AND CONDITIONS START -->
        <div class="d-flex flex-wrap px-lg-4 px-md-4 px-3 py-3">
            <div class="col-md-6 col-sm-12 c-inv-note-terms p-0 mb-lg-0 mb-md-0 mb-3">
                <x-forms.label fieldId="" class="" :fieldLabel="__('modules.invoices.note')">
                </x-forms.label>
                <textarea class="form-control" name="note" id="note" rows="4"
                    placeholder="@lang('placeholders.invoices.note')">{{$purchaseBill->note}}</textarea>
            </div>
        </div>

        <!-- CANCEL SAVE SEND START -->
        <x-form-actions class="c-inv-btns d-block d-lg-flex d-md-flex">

            <div class="d-flex mb-3 mb-lg-0 mb-md-0">

                <div class="inv-action dropup mr-3">
                    <button class="btn-primary dropdown-toggle" type="button" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                    @lang('app.save')
                    <span><i class="fa fa-chevron-up f-15 text-white"></i></span>
                </button>
                <!-- DROPDOWN - INFORMATION -->
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuBtn" tabindex="0">
                        @if($purchaseBill->status == 'paid' || $purchaseBill->status == 'partially_paid')
                            <li>
                                <a class="dropdown-item f-14 text-dark save-form" href="javascript:;">
                                    <i class="fa fa-save f-w-500 mr-2 f-11"></i> @lang('app.save')
                                </a>
                            </li>
                        @else
                            <li>
                                <a class="dropdown-item f-14 text-dark save-form" href="javascript:;" data-type="draft">
                                    <i class="fa fa-save f-w-500 mr-2 f-11"></i> @lang('app.saveDraft')
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item f-14 text-dark save-form" href="javascript:void(0);"
                                    data-type="open">
                                    <i class="fa fa-paper-plane f-w-500  mr-2 f-12"></i> @lang('purchase::modules.purchaseBill.saveAsOpen')
                                </a>
                            </li>
                        @endif
                        </ul>
                </div>
            </div>

            <x-forms.button-cancel :link="route('bills.index')" class="border-0 ">@lang('app.cancel')
            </x-forms.button-cancel>

        </x-form-actions>
        <!-- CANCEL SAVE SEND END -->

    </x-form>
    <!-- FORM END -->
</div>
    <script>

        @foreach($purchaseItems as $item)
        $(function()
        {
            var quantity = $('#sortable').find('.quantity[data-item-id="{{ $item->id }}"]').val();
            var perItemCost = $('#sortable').find('.cost_per_item[data-item-id="{{ $item->id }}"]').val();
            var amount = (quantity * perItemCost);
            $('#sortable').find('.amount[data-item-id="{{ $item->id }}"]').val(amount);
            $('#sortable').find('.amount-html[data-item-id="{{ $item->id }}"]').html(amount);
            calculateTotal();
        });
        @endforeach

        $(document).ready(function() {
            let defaultImage = '';
            let lastIndex = 0;

            $('.custom-date-picker').each(function(ind, el) {
                datepicker(el, {
                    position: 'bl',
                    ...datepickerConfig
                });
            });

            const dp1 = datepicker('#bill_date', {
                position: 'bl',
                ...datepickerConfig
            });

            calculateTotal();

            $('.save-form').click(function() {
                var type = $(this).data('type');
                if(type != undefined)
                {
                    var url = "{{ route('bills.update', $purchaseBill->id) }}"+ "?type=" + type;
                }
                else {
                    var url = "{{ route('bills.update', $purchaseBill->id) }}";
                }

                    if (KTUtil.isMobileDevice()) {
                        $('.desktop-description').remove();
                    } else {
                        $('.mobile-description').remove();
                    }

                    calculateTotal();

                    $.easyAjax({
                        url: url,
                        container: '#save-bill-data-form',
                        type: "POST",
                        blockUI: true,
                        redirect: true,
                        file: true,  // Commented so that we dot get error of Input variables exceeded 1000
                        data: $('#save-bill-data-form').serialize(),
                        success: function(response) {
                            window.location.href="{{route('bills.index')}}";
                        }
                    });
            });

            function calculateTotal() {
                var subtotal = 0;
                var discount = 0;
                var tax = "";
                var taxList = new Object();
                var taxTotal = 0;
                var discountAmount = 0;
                var discountType = $("#discount_type").val();
                var discountValue = $(".discount_value").val();
                var calculateTax = $("#calculate_tax").val();
                var adjustmentAmount = $("#adjustment_amount").val();

                $(".quantity").each(function (index, element) {
                    var discountedAmount = 0;
                    var amount = parseFloat(
                        $(this).closest(".item-row").find(".amount").val()
                    );

                    if (isNaN(amount)) {
                        amount = 0;
                    }

                    subtotal = (parseFloat(subtotal) + parseFloat(amount)).toFixed(2);
                });

                if (discountType == "percent" && discountValue != "") {
                    discountAmount =
                        (parseFloat(subtotal) / 100) * parseFloat(discountValue);
                    discountedAmount = parseFloat(subtotal - discountAmount);
                } else {
                    discountAmount = parseFloat(discountValue);
                    discountedAmount = parseFloat(subtotal - parseFloat(discountValue));
                }

                $(".quantity").each(function (index, element) {
                    var itemTax = [];
                    var itemTaxName = [];
                    subtotal = parseFloat(subtotal);

                    $(this)
                        .closest(".item-row")
                        .find("span")
                        .each(function (index) {
                            itemTax[index] = $(this).data("rate");
                            itemTaxName[index] = $(this).data('tax-text');
                        });
                    var itemTaxId = $(this).closest(".item-row").find("select.type").val();

                    var amount = parseFloat(
                        $(this).closest(".item-row").find(".amount").val()
                    );

                    if (isNaN(amount)) {
                        amount = 0;
                    }

                    if (itemTaxId != "") {
                        for (var i = 0; i <= itemTaxName.length; i++) {
                            if (typeof taxList[itemTaxName[i]] === "undefined") {
                                if (
                                    calculateTax == "after_discount" &&
                                    discountAmount > 0
                                ) {
                                    var taxValue =
                                        (amount - (amount / subtotal) * discountAmount) *
                                        (parseFloat(itemTax[i]) / 100);

                                    if (!isNaN(taxValue)) {
                                        taxList[itemTaxName[i]] = parseFloat(taxValue);
                                    }
                                } else {
                                    var taxValue = amount * (parseFloat(itemTax[i]) / 100);

                                    if (!isNaN(taxValue)) {
                                        taxList[itemTaxName[i]] = parseFloat(taxValue);
                                    }
                                }
                            } else {
                                if (
                                    calculateTax == "after_discount" &&
                                    discountAmount > 0
                                ) {
                                    var taxValue =
                                        parseFloat(taxList[itemTaxName[i]]) +
                                        (amount - (amount / subtotal) * discountAmount) *
                                            (parseFloat(itemTax[i]) / 100);

                                    if (!isNaN(taxValue)) {
                                        taxList[itemTaxName[i]] = parseFloat(taxValue);
                                    }
                                } else {
                                    var taxValue =
                                        parseFloat(taxList[itemTaxName[i]]) +
                                        amount * (parseFloat(itemTax[i]) / 100);

                                    if (!isNaN(taxValue)) {
                                        taxList[itemTaxName[i]] = parseFloat(taxValue);
                                    }
                                }
                            }
                        }
                    }
                });

                $.each(taxList, function (key, value) {
                    if (!isNaN(value)) {
                        tax =
                            tax +
                            '<tr><td class="text-dark-grey">' +
                            key +
                            '</td><td><span class="tax-percent">' +
                            decimalupto2(value).toFixed(2) +
                            "</span></td></tr>";
                        taxTotal = taxTotal + decimalupto2(value);
                    }
                });

                if (isNaN(subtotal)) {
                    subtotal = 0;
                }

                $(".sub-total").html(decimalupto2(subtotal).toFixed(2));
                $(".sub-total-field").val(decimalupto2(subtotal));

                if (discountValue != "") {
                    if (discountType == "percent") {
                        discount = (parseFloat(subtotal) / 100) * parseFloat(discountValue);
                    } else {
                        discount = parseFloat(discountValue);
                    }
                }

                if (tax != "") {
                    $("#invoice-taxes").html(tax);
                } else {
                    $("#invoice-taxes").html(
                        '<tr><td colspan="2"><span class="tax-percent">0.00</span></td></tr>'
                    );
                }

                if (adjustmentAmount && adjustmentAmount != 0 && adjustmentAmount != '') {
                    subtotal = subtotal + parseFloat(adjustmentAmount);
                }

                $("#discount_amount").html(decimalupto2(discount).toFixed(2));

                var totalAfterDiscount = decimalupto2(subtotal - discount);

                totalAfterDiscount = totalAfterDiscount < 0 ? 0 : totalAfterDiscount;

                var total = decimalupto2(totalAfterDiscount + taxTotal);

                $(".total").html(total.toFixed(2));
                $(".total-field").val(total.toFixed(2));
            }

            init(RIGHT_MODAL);
        });
    </script>
