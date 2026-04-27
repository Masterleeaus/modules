<div class="card border-0 invoice">
    <div class="card-body">
        <div class="invoice-table-wrapper">
            <table width="100%">
                <tr class="inv-logo-heading">
                    <td><img src="{{ invoice_setting()->logo_url }}" alt="{{ mb_ucwords(company()->company_name) }}"
                            id="logo" height="50" width="50"/></td>
                    <td align="right" class="font-weight-bold f-21 text-dark text-uppercase mt-4 mt-lg-0 mt-md-0">
                        @lang('purchase::app.menu.vendorCredit')</td>
                </tr>
                <tr class="inv-num">
                    <td class="f-14 text-dark">
                        <p class="mt-3 mb-0">
                            @if ($vendorCredit->vendors && $vendorCredit->vendors->primary_name)
                                {{ $vendorCredit->vendors->primary_name }}<br>
                            @endif
                            @if ($vendorCredit->vendors && $vendorCredit->vendors->email)
                                {{ $vendorCredit->vendors->email }}<br>
                            @endif
                            @if ($vendorCredit->vendors && $vendorCredit->vendors->billing_address)
                                {{ $vendorCredit->vendors->billing_address }}<br>
                            @endif
                            @if ($vendorCredit->vendors && $vendorCredit->vendors->phone)
                                {{ $vendorCredit->vendors->phone }}<br>
                            @endif
                        </p>
                        <br>
                    </td>
                    <td align="right">
                        <table class="inv-num-date text-dark f-13 mt-3">
                            <tr>
                                <td class="bg-light-grey border-right-0 f-w-500">
                                    @lang('purchase::app.menu.creditNumber')</td>
                                <td class="border-left-0">{{ $vendorCredit->vendor_credit_number }}</td>
                            </tr>
                            <tr>
                                <td class="bg-light-grey border-right-0 f-w-500">
                                    @lang('purchase::modules.vendor.creditDate')</td>
                                <td class="border-left-0">{{ $vendorCredit->credit_date }}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td height="20"></td>
                </tr>
            </table>
            <div class="row">
                <div class="col-sm-12 ql-editor">
                </div>
            </div>
            <table width="100%" class="inv-desc d-none d-lg-table d-md-table">
                <tr>
                    <td colspan="2">
                        <table class="inv-detail f-14 table-responsive-sm" width="100%">
                            <tr class="i-d-heading bg-light-grey text-dark-grey font-weight-bold">
                                <td class="border-right-0" width="35%">@lang('app.description')</td>
                                @if ($vendorCreditSetting->hsn_sac_code_show == 1)
                                    <td class="border-right-0 border-left-0" align="right">@lang('app.hsnSac')</td>
                                @endif
                                <td class="border-right-0 border-left-0" align="right">@lang('modules.invoices.qty')</td>
                                <td class="border-right-0 border-left-0" align="right">
                                    @lang('modules.invoices.unitPrice') ({{ $vendorCredit->currency->currency_code }})
                                </td>
                                <td class="border-right-0 border-left-0" align="right">
                                    @lang('modules.invoices.tax')
                                </td>
                                <td class="border-left-0" align="right">
                                    @lang('modules.invoices.amount')
                                    ({{ $vendorCredit->currency->currency_code }})</td>
                            </tr>
                            @if ($vendorCredit->bill_id)
                                @foreach ($vendorCredit->items as $item)
                                    <tr class="text-dark font-weight-semibold f-13">
                                        <td>{{ ($item->item_name) }}</td>
                                        @if ($vendorCreditSetting->hsn_sac_code_show == 1)
                                            <td align="right">{{ $item->hsn_sac_code }}</td>
                                        @endif
                                        <td align="right">{{ $item->quantity }}@if ($item->unit)
                                                <br><span
                                                    class="f-11 text-dark-grey">{{ $item->unit->unit_type }}</span>
                                            @endif
                                        </td>
                                        <td align="right">
                                            {{ currency_format($item->unit_price, $vendorCredit->currency_id, false) }}
                                        </td>
                                        <td align="right">
                                            {{ $item->tax_list }}
                                        </td>
                                        <td align="right">
                                            {{ currency_format($item->amount, $vendorCredit->currency_id, false) }}
                                        </td>
                                    </tr>
                                    @if ($item->item_summary || $item->purchaseVendorCreditItemImage)
                                        <tr class="text-dark f-12">
                                            <td colspan="6" class="border-bottom-0">
                                                {!! nl2br(strip_tags($item->item_summary)) !!}
                                                @if ($item->purchaseVendorCreditItemImage)
                                                    <p class="mt-2">
                                                        <a href="javascript:;" class="img-lightbox"
                                                            data-image-url="{{ $item->purchaseVendorCreditItemImage->file_url }}">
                                                            <img src="{{ $item->purchaseVendorCreditItemImage->file_url }}"
                                                                width="80" height="80" class="img-thumbnail">
                                                        </a>
                                                    </p>
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            @else
                                <tr class="text-dark font-weight-semibold f-13">
                                    <td>item1</td>
                                    @if ($vendorCreditSetting->hsn_sac_code_show == 1)
                                        <td align="right">{{ $vendorCredit->hsn_sac_code }}</td>
                                    @endif
                                    <td align="right">1 Pcs</td>
                                    <td align="right">
                                        {{ currency_format($vendorCredit->total, $vendorCredit->currency_id, false) }}
                                    </td>
                                    <td align="right">
                                        {{ $vendorCredit->tax_list }}
                                    </td>
                                    <td align="right">
                                        {{ currency_format($vendorCredit->total, $vendorCredit->currency_id, false) }}
                                    </td>
                                </tr>

                            @endif
                            <tr>
                                <td colspan="{{ $vendorCreditSetting->hsn_sac_code_show ? '4' : '3' }}"
                                    class="blank-td border-bottom-0 border-left-0 border-right-0"></td>
                                <td class="p-0 border-right-0" align="right">
                                    <table width="100%">
                                        <tr class="text-dark-grey" align="right">
                                            <td class="w-50 border-top-0 border-left-0">
                                                @lang('modules.invoices.subTotal')</td>
                                        </tr>
                                        @if ($discount != 0 && $discount != '')
                                            <tr class="text-dark-grey" align="right">
                                                <td class="w-50 border-top-0 border-left-0">
                                                    @lang('modules.invoices.discount')</td>
                                            </tr>
                                        @endif
                                        @foreach ($taxes as $key => $tax)
                                            <tr class="text-dark-grey" align="right">
                                                <td class="w-50 border-top-0 border-left-0">
                                                    {{ mb_strtoupper($key) }}</td>
                                            </tr>
                                        @endforeach
                                        <tr class="text-dark f-w-500 f-16" align="right">
                                            <td class="w-50 border-bottom-0 border-left-0">
                                                @lang('modules.invoices.total')</td>
                                        </tr>
                                        <tr class=" text-dark-grey " align="right">
                                            <td class="w-50 border-bottom-0 border-left-0">
                                                @lang('modules.credit-notes.creditAmountUsed')</td>
                                        </tr>
                                        <tr class="bg-light-grey text-dark f-w-500 f-16" align="right">
                                            <td class="w-50 border-bottom-0 border-left-0">
                                                @lang('modules.credit-notes.creditAmountRemaining')</td>
                                        </tr>
                                    </table>
                                </td>
                                <td class="p-0 border-right-0" align="right">
                                    <table width="100%">
                                        @if ($vendorCredit->bill_id)
                                            <tr class="text-dark-grey" align="right">
                                                <td class="border-top-0 border-left-0">
                                                    {{ currency_format($vendorCredit->sub_total, $vendorCredit->currency_id, false) }}
                                                </td>
                                            </tr>
                                        @else
                                            <tr class="text-dark-grey" align="right">
                                                <td class="border-top-0 border-left-0">
                                                    {{ currency_format($vendorCredit->total, $vendorCredit->currency_id, false) }}
                                                </td>
                                            </tr>
                                        @endif
                                        @if ($discount != 0 && $discount != '')
                                            <tr class="text-dark-grey" align="right">
                                                <td class="border-top-0 border-left-0">
                                                    {{ currency_format($discount, $vendorCredit->currency_id, false) }}
                                                </td>
                                            </tr>
                                        @endif
                                        @foreach ($taxes as $key => $tax)
                                            <tr class="text-dark-grey" align="right">
                                                <td class="border-top-0 border-left-0">
                                                    {{ currency_format($tax, $vendorCredit->currency_id, false) }}</td>
                                            </tr>
                                        @endforeach
                                        <tr class=" text-dark-grey font-weight-bold" align="right">
                                            <td class="border-bottom-0 border-left-0">
                                                {{ currency_format($vendorCredit->total, $vendorCredit->currency_id, false) }}
                                            </td>
                                        </tr>
                                        <tr class=" text-dark-grey " align="right">
                                            <td class="border-bottom-0 border-left-0">
                                                {{ currency_format($vendorCredit->creditAmountUsed(), $vendorCredit->currency_id, false) }}
                                            </td>
                                        </tr>
                                        <tr class="bg-light-grey text-dark f-w-500 f-16" align="right">
                                            <td class="border-bottom-0 border-left-0">
                                                {{ currency_format($vendorCredit->creditAmountRemaining(), $vendorCredit->currency_id, false) }}
                                                {{ $vendorCredit->currency->currency_code }}</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>

                </tr>
            </table>
            <table width="100%" class="inv-desc-mob d-block d-lg-none d-md-none">

                @foreach ($vendorCredit->items as $item)
                    <tr>
                        <th width="50%" class="bg-light-grey text-dark-grey font-weight-bold">
                            @lang('app.description')</th>
                        <td class="p-0 ">
                            <table>
                                <tr width="100%" class="font-weight-semibold f-13">
                                    <td class="border-left-0 border-right-0 border-top-0">
                                        {{ ($item->item_name) }}</td>
                                </tr>
                                @if ($item->item_summary != '' || $item->purchaseVendorCreditItemImage)
                                    <tr>
                                        <td class="border-left-0 border-right-0 border-bottom-0 f-12">
                                            {!! nl2br(strip_tags($item->item_summary)) !!}
                                            @if ($item->purchaseVendorCreditItemImage)
                                                <p class="mt-2">
                                                    <a href="javascript:;" class="img-lightbox"
                                                        data-image-url="{{ $item->purchaseVendorCreditItemImage->file_url }}">
                                                        <img src="{{ $item->purchaseVendorCreditItemImage->file_url }}"
                                                            width="80" height="80" class="img-thumbnail">
                                                    </a>
                                                </p>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <th width="50%" class="bg-light-grey text-dark-grey font-weight-bold">
                            @lang('modules.invoices.qty')
                        </th>
                        <td width="50%">{{ $item->quantity }}</td>
                    </tr>
                    <tr>
                        <th width="50%" class="bg-light-grey text-dark-grey font-weight-bold">
                            @lang('modules.invoices.unitPrice')
                            ({{ $vendorCredit->currency->currency_code }})</th>
                        <td width="50%">{{ currency_format($item->unit_price, $vendorCredit->currency_id, false) }}
                        </td>
                    </tr>
                    <tr>
                        <th width="50%" class="bg-light-grey text-dark-grey font-weight-bold">
                            @lang('modules.invoices.amount')
                            ({{ $vendorCredit->currency->currency_code }})</th>
                        <td width="50%">{{ currency_format($item->amount, $vendorCredit->currency_id, false) }}
                        </td>
                    </tr>
                    <tr>
                        <td height="3" class="p-0 " colspan="2"></td>
                    </tr>
                @endforeach

                <tr>
                    <th width="50%" class="text-dark-grey font-weight-normal">@lang('modules.invoices.subTotal')
                    </th>
                    <td width="50%" class="text-dark-grey font-weight-normal">
                        {{ currency_format($vendorCredit->sub_total, $vendorCredit->currency_id, false) }}</td>
                </tr>
                @if ($discount != 0 && $discount != '')
                    <tr>
                        <th width="50%" class="text-dark-grey font-weight-normal">@lang('modules.invoices.discount')
                        </th>
                        <td width="50%" class="text-dark-grey font-weight-normal">
                            {{ currency_format($discount, $vendorCredit->currency_id, false) }}</td>
                    </tr>
                @endif

                @foreach ($taxes as $key => $tax)
                    <tr>
                        <th width="50%" class="text-dark-grey font-weight-normal">{{ mb_strtoupper($key) }}</th>
                        <td width="50%" class="text-dark-grey font-weight-normal">
                            {{ currency_format($tax, $vendorCredit->currency_id, false) }}</td>
                    </tr>
                @endforeach

                <tr>
                    <th width="50%" class="text-dark-grey font-weight-bold">@lang('modules.invoices.total')</th>
                    <td width="50%" class="text-dark-grey font-weight-bold">
                        {{ currency_format($vendorCredit->total, $vendorCredit->currency_id, false) }}</td>
                </tr>
                <tr>
                    <th width="50%" class="text-dark-grey font-weight-bold">
                        @lang('modules.credit-notes.creditAmountUsed')</th>
                    <td width="50%" class="text-dark-grey font-weight-bold">
                        {{ currency_format($vendorCredit->creditAmountUsed(), $vendorCredit->currency_id, false) }}
                    </td>
                </tr>
                <tr>
                    <th width="50%" class="f-16 bg-light-grey text-dark font-weight-bold">
                        @lang('modules.invoices.total')
                        @lang('modules.invoices.due')</th>
                    <td width="50%" class="f-16 bg-light-grey text-dark font-weight-bold">
                        {{ currency_format($vendorCredit->creditAmountRemaining(), $vendorCredit->currency_id, false) }}
                        {{ $vendorCredit->currency->currency_code }}</td>
                </tr>
            </table>
            <table class="inv-note">
                <tr>
                    <td height="30" colspan="2"></td>
                </tr>
                <tr>
                    <td>
                        <table>
                            <tr>@lang('app.note')</tr>
                            <tr>
                                <p class="text-dark-grey">{!! !empty($vendorCredit->note) ? $vendorCredit->note : '--' !!}</p>
                            </tr>
                        </table>
                    </td>
                    <td align="right">
                        <table>
                            <tr>@lang('modules.invoiceSettings.invoiceTerms')</tr>
                            <tr>
                                <p class="text-dark-grey">{!! nl2br($vendorCreditSetting->invoice_terms) !!}</p>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        @if (isset($taxes) && invoice_setting()->tax_calculation_msg == 1)
                            <p class="text-dark-grey">
                                @if ($vendorCredit->calculate_tax == 'after_discount')
                                    @lang('messages.calculateTaxAfterDiscount')
                                @else
                                    @lang('messages.calculateTaxBeforeDiscount')
                                @endif
                            </p>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white border-0 d-flex justify-content-start py-0 py-lg-4 py-md-4 mb-4 mb-lg-3 mb-md-3 ">

        <div class="d-flex">
            <div class="inv-action mr-3 mr-lg-3 mr-md-3 dropup">
                <button class="dropdown-toggle btn-secondary" type="button" id="dropdownMenuButton"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">@lang('app.action')
                    <span><i class="fa fa-chevron-down f-15 text-dark-grey"></i></span>
                </button>
                <!-- DROPDOWN - INFORMATION -->
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton" tabindex="0">
                    <li>
                        {{-- <a class="dropdown-item f-14 text-dark"
                            href="{{ route('front.proposal', $vendorCredit->hash) }}" target="_blank">
                            <i class="fa fa-link f-w-500 mr-2 f-11"></i> @lang('modules.proposal.publicLink')
                        </a> --}}
                        <a class="dropdown-item f-14 text-dark"
                            href="{{ route('vendor-credits.download', [$vendorCredit->id]) }}">
                            <i class="fa fa-download f-w-500 mr-2 f-11"></i> @lang('app.download')
                        </a>
                    </li>
                    @if ($vendorCredit->status == 'open')
                        <li>
                            <a class="dropdown-item f-14 text-dark openRightModal"
                                href="{{ route('vendor-credits.apply_to_bill', [$vendorCredit->id]) }}">
                                <i class="fa fa-receipt f-w-500 mr-2 f-11"></i> @lang('purchase::app.applyToBill')
                            </a>
                        </li>
                    @endif
                </ul>
            </div>

            <x-forms.button-cancel :link="route('vendor-credits.index')" class="border-0">
                @lang('app.cancel')
            </x-forms.button-cancel>
        </div>
    </div>
</div>
