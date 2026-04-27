<div class="d-flex c-inv-desc item-row">

    <div class="c-inv-desc-table w-100 d-lg-flex d-md-flex d-block">
        <table width="100%">
            <tbody>
                <tr class="text-dark-grey font-weight-bold f-14">
                    <td width="{{ $invoiceSetting->hsn_sac_code_show ? '40%' : '50%' }}"
                        class="border-0 inv-desc-mbl btlr">@lang('app.description')
                    </td>
                    @if ($invoiceSetting->hsn_sac_code_show)
                        <td width="10%" class="border-0" align="right">@lang('app.hsnSac')
                        </td>
                    @endif
                    <td width="10%" class="border-0" align="right">
                        @lang('purchase::modules.product.availableQuantity')
                    </td>
                    <td width="10%" class="border-0" align="right">
                        @lang('purchase::modules.product.quantityOnHand')
                    </td>
                    <td width="13%" class="border-0" align="right">
                        @lang('purchase::modules.product.quabtityAdjusted')
                    </td>
                </tr>
                <tr>
                    <td class="border-bottom-0 btrr-mbl btlr">
                        <input type="text" class="f-14 border-0 w-100 item_name bg-additional-grey" readonly
                            name="" placeholder="@lang('modules.expenses.itemName')" value="{{ $item->name }}">
                    </td>
                    @if ($invoiceSetting->hsn_sac_code_show)
                        <td class="border-bottom-0">
                            <span>{{ $item->hsn_sac_code }}</span>
                            <input type="hidden" class="form-control f-14 border-0 w-100 text-right hsn_sac_code"
                                value="{{ $item->hsn_sac_code }}" name="">
                        </td>
                    @endif
                    <td class="border-bottom-0">
                        <input type="number" min="1" class="f-14 border-0 w-100 text-right quantity mt-3 bg-additional-grey"
                            value="{{ $stockAdjustment ? $stockAdjustment->net_quantity : $item->opening_stock }}" name="available_quantity[]" id="available_quantity{{ $item->id }}" readonly>
                        <span class="text-dark-grey float-right border-0 f-12">{{ $item->unit ? $item->unit->unit_type : '' }}</span>
                        <input type="hidden" name="product_id[]" value="{{ $item->id }}" class="product_id">
                    </td>
                    <td class="border-bottom-0">
                        <input type="number" name="quantity_on_hand[]" data-item-id="{{ $item->id }}" id="quantity_on_hand" class="cost_per_item f-14 w-100 text-right quantity_on_hand">
                    </td>
                    <td class="border-bottom-0">
                        <input type="text" class="form-control height-35 f-14 border-0 w-100 text-right bg-additional-grey" name="quantity_adjusted[]"
                            id="quantity_adjusted{{ $item->id }}" readonly placeholder="@lang('purchase::placeholders.adjustedValue')" value="0">
                    </td>
                </tr>
                <tr class="d-md-block d-lg-table-row">
                    <td colspan="{{ $invoiceSetting->hsn_sac_code_show ? '4' : '3' }}" class="dash-border-top bblr">
                        <textarea type="text" readonly class="f-14 border-0 w-100 desktop-description" name=""
                            placeholder="">{{ strip_tags($item->description) }}</textarea>
                    </td>
                    <td class="border-left-0">
                        @if ($item->image_url != '')
                            <input type="file" class="dropify" disabled name=""
                                data-allowed-file-extensions="png jpg jpeg pdf xls" data-messages-default="test"
                                data-height="70" data-default-file="{{ $item->image_url }}" data-show-remove="false" />
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>

        <a href="javascript:;" class="d-flex align-items-center justify-content-center ml-3 remove-item"
            data-item-id="{{ $item->id }}"><i class="fa fa-times-circle f-20 text-lightest"></i></a>
    </div>

    <script>
        $(function() {

            $(document).find('.dropify').dropify({
                messages: dropifyMessages
            });

            var quantity = $('#sortable').find('.quantity[data-item-id="{{ $item->id }}"]').val();
            var perItemCost = $('#sortable').find('.cost_per_item[data-item-id="{{ $item->id }}"]').val();
            var amount = (quantity * perItemCost);
            $('#sortable').find('.amount[data-item-id="{{ $item->id }}"]').val(amount);
            $('#sortable').find('.amount-html[data-item-id="{{ $item->id }}"]').html(amount);

            $('.quantity_on_hand').keyup(function() {
                let id = $(this).data('item-id');
                var availQuantity = parseInt($('#available_quantity'+id).val());
                var onHandQuantity = parseInt($(this).val());
                let adjustedQuantity = 0;

                if (onHandQuantity > availQuantity && onHandQuantity != 0) {
                    let adjustedQuantity = onHandQuantity - availQuantity;
                    $('#quantity_adjusted'+id).val('+' + adjustedQuantity);
                } else if (onHandQuantity < availQuantity && onHandQuantity != 0) {
                    let adjustedQuantity = availQuantity - onHandQuantity;
                    $('#quantity_adjusted'+id).val('-' + adjustedQuantity);
                } else {
                    $('#quantity_adjusted'+id).val(adjustedQuantity);
                }
            });

        });
    </script>
</div>
