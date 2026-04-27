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
                            <input readonly type="text" class="form-control f-14 border-0 w-100 item_name" name="item_name[]"
                                placeholder="@lang('modules.expenses.itemName')" value="{{ $item->item_name }}">
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
                            <div class="select-others height-35 rounded border-0">
                                            @foreach ($item->taxes as $tax)
                                            <span data-rate="{{ $tax->rate_percent }}"
                                                data-tax-text="{{ strtoupper($tax->tax_name) .':'. $tax->rate_percent }}%"
                                                >{{$tax->tax_name.': '.$tax->rate_percent.'%'}}</span><br>
                                            @endforeach
                            </div>
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

        <script>
            $(function()
            {
                $(document).find('.dropify').dropify({
                    messages: dropifyMessages
                });
                    var quantity = $('#sortable').find('.quantity[data-item-id="{{ $item->id }}"]').val();
                    var perItemCost = $('#sortable').find('.cost_per_item[data-item-id="{{ $item->id }}"]').val();
                    var amount = (quantity * perItemCost);
                    $('#sortable').find('.amount[data-item-id="{{ $item->id }}"]').val(amount);
                    $('#sortable').find('.amount-html[data-item-id="{{ $item->id }}"]').html(amount);
                    calculateTotal();
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
                var calculateTax = "{{$purchaseOrder->calculate_tax}}";
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
        </script>

    </div>
@endforeach
    <!-- DESKTOP DESCRIPTION TABLE END -->

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
                                                        <input type="number" min="0" name="discount_value"
                                                            class="form-control f-14 border-0 w-100 text-right discount_value"
                                                            placeholder="0"
                                                            value="{{ isset($item->purchaseOrder) ? $item->purchaseOrder->discount : '0' }}" readonly>
                                                    </td>
                                                    <td width="30%" align="left" class="c-inv-sub-padding">
                                                            <input type="text" class="form-control f-14 border-0 w-100 text-right discount_value"
                                                             value="{{ isset($item) ? $item->purchaseOrder->discount_type == 'percent' ? '%' : 'Amt.' : '' }}" readonly>
                                                             <input type="hidden" id = "discount_type" name="discount_type" value="{{ isset($item->purchaseOrder) ? $item->purchaseOrder->discount_type : ''}}">
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                    <td>
                                        <span id="discount_amount">{{ isset($item->purchaseOrder) ? number_format((float) $item->purchaseOrder->discount, 2, '.', '') : '0.00' }}</span>
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
