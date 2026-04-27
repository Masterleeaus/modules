<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>@lang('purchase::app.menu.bill') - {{ $purchaseBill->bill_number }}</title>
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{ $company->favicon_url }}">
    <meta name="theme-color" content="#ffffff">
    @includeIf('invoices.pdf.invoice_pdf_css')
    <style>
        .bg-grey {
            background-color: #F2F4F7;
        }

        .bg-white {
            background-color: #fff;
        }

        .border-radius-25 {
            border-radius: 0.25rem;
        }

        .p-25 {
            padding: 1.25rem;
        }

        .f-11 {
            font-size: 11px;
        }

        .f-12 {
            font-size: 12px;
        }

        .f-13 {
            font-size: 13px;
        }

        .f-14 {
            font-size: 13px;
        }

        .f-15 {
            font-size: 13px;
        }

        .f-21 {
            font-size: 17px;
        }

        .text-black {
            color: #28313c;
        }

        .text-grey {
            color: #616e80;
        }

        .font-weight-700 {
            font-weight: 700;
        }

        .text-uppercase {
            text-transform: uppercase;
        }

        . {
            text-transform: capitalize;
        }

        .line-height {
            line-height: 15px;
        }

        .mt-1 {
            margin-top: 1rem;
        }

        .mb-0 {
            margin-bottom: 0px;
        }

        .b-collapse {
            border-collapse: collapse;
        }

        .heading-table-left {
            padding: 6px;
            border: 1px solid #DBDBDB;
            font-weight: bold;
            background-color: #f1f1f3;
            border-right: 0;
        }

        .heading-table-right {
            padding: 6px;
            border: 1px solid #DBDBDB;
            border-left: 0;
        }

        .unpaid {
            color: #d30000;
            border: 1px solid #d30000;
            position: relative;
            padding: 5px 10px;
            font-size: 14px;
            border-radius: 0.25rem;
            width: 100px;
            text-align: center;
            margin-top: 50px;
        }

        .other {
            color: #000000;
            border: 1px solid #000000;
            position: relative;
            padding: 5px 10px;
            font-size: 14px;
            border-radius: 0.25rem;
            width: 120px;
            text-align: center;
            margin-top: 50px;
        }

        .paid {
            color: #28a745 !important;
            border: 1px solid #28a745;
            position: relative;
            padding: 6px 12px;
            font-size: 14px;
            border-radius: 0.25rem;
            width: 100px;
            text-align: center;
            margin-top: 50px;
        }

        .main-table-heading {
            border: 1px solid #DBDBDB;
            background-color: #f1f1f3;
            font-weight: 700;
        }

        .main-table-heading td {
            padding: 5px 8px;
            border: 1px solid #DBDBDB;
            font-size: 13px;
        }

        .main-table-items td {
            padding: 5px 8px;
            border: 1px solid #e7e9eb;
        }

        .total-box {
            border: 1px solid #e7e9eb;
            padding: 0px;
            border-bottom: 0px;
        }

        .subtotal {
            padding: 5px 8px;
            border: 1px solid #e7e9eb;
            border-top: 0;
            border-left: 0;
            border-right: 0;
        }

        .subtotal-amt {
            padding: 5px 8px;
            border: 1px solid #e7e9eb;
            border-top: 0;
            border-left: 0;
            border-right: 0;
        }

        .total {
            padding: 5px 8px;
            border: 1px solid #e7e9eb;
            border-top: 0;
            font-weight: 700;
            border-left: 0;
            border-right: 0;
        }

        .total-amt {
            padding: 5px 8px;
            border: 1px solid #e7e9eb;
            border-top: 0;
            border-left: 0;
            border-right: 0;
            font-weight: 700;
        }

        .balance {
            font-size: 14px;
            font-weight: bold;
            background-color: #f1f1f3;
        }

        .balance-left {
            padding: 5px 8px;
            border: 1px solid #e7e9eb;
            border-top: 0;
            border-left: 0;
            border-right: 0;
        }

        .balance-right {
            padding: 5px 8px;
            border: 1px solid #e7e9eb;
            border-top: 0;
            border-left: 0;
            border-right: 0;
        }

        .centered {
            margin: 0 auto;
        }

        .rightaligned {
            margin-right: 0;
            margin-left: auto;
        }

        .leftaligned {
            margin-left: 0;
            margin-right: auto;
        }

        .page_break {
            page-break-before: always;
        }

        #logo {
            height: 50px;
        }

        .word-break {
            max-width: 175px;
            word-wrap: break-word;
        }

        .summary {
            padding: 11px 10px;
            border: 1px solid #e7e9eb;
            font-size: 11px;
        }

        .border-left-0 {
            border-left: 0 !important;
        }

        .border-right-0 {
            border-right: 0 !important;
        }

        .border-top-0 {
            border-top: 0 !important;
        }

        .border-bottom-0 {
            border-bottom: 0 !important;
        }
        .h3-border {
            border-bottom: 1px solid #AAAAAA;
        }
</style>
    @if($invoiceSetting->locale == 'th')
    <style>

            table td {
            font-weight: bold !important;
            font-size: 20px !important;
        }

        .description {
            font-weight: bold !important;
            font-size: 16px !important;
        }


    </style>
@endif
</head>

<body class="content-wrapper">
    <table class="bg-white" border="0" cellpadding="0" cellspacing="0" width="100%" role="presentation">
        <tbody>
        <!-- Table Row Start -->
        <tr>
            <td><img src="{{ $invoiceSetting->logo_url }}" alt="{{ mb_ucwords($company->company_name) }}"
                    id="logo"/></td>
            <td align="right" class="f-21 text-black font-weight-700 text-uppercase">@lang('purchase::app.menu.bill')<br>
                <table class="text-black mt-1 f-11 b-collapse rightaligned">
                    <tr>
                        <td class="heading-table-left">@lang('purchase::app.menu.billNumber')</td>
                        <td class="heading-table-right">{{ $purchaseBill->bill_number }}</td>
                    </tr>
                    <tr>
                        <td class="heading-table-left">@lang('purchase::app.menu.billDate')</td>
                        <td class="heading-table-right">
                            {{ $purchaseBill->bill_date->translatedFormat(company()->date_format) }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <!-- Table Row End -->
        <!-- Table Row Start -->
        <tr>
            <td class="f-12 text-black">
                <p class="line-height mb-0 ">
                    <span class="text-grey ">@lang('modules.invoices.billedFrom')</span><br>
                    @if ($purchaseOrder->vendor && $purchaseOrder->vendor->primary_name)
                        {{$purchaseOrder->vendor->primary_name}}<br>
                    @endif
                    @if ($purchaseOrder->vendor && $purchaseOrder->vendor->email)
                        {{$purchaseOrder->vendor->email}}<br>
                    @endif
                    @if ($purchaseOrder->vendor && $purchaseOrder->vendor->billing_address)
                        {{$purchaseOrder->vendor->billing_address}}<br>
                    @endif
                    @if ($purchaseOrder->vendor && $purchaseOrder->vendor->phone)
                        {{$purchaseOrder->vendor->phone}}<br>
                    @endif
                </p>
            </td>
            <td class="f-12 text-black" align="right">
                <p class="line-height mb-0">
                            <span class="text-grey ">
                                @lang('modules.invoices.billedTo')</span><br>
                        {{ mb_ucwords(company()->company_name) }}<br>
                        @if ($purchaseOrder->address)
                            {!! nl2br($purchaseOrder->address->address) !!}<br>
                        @endif
                        {{ company()->company_phone }}
                        @if ($invoiceSetting->show_gst == 'yes' && $purchaseOrder->address)
                            <br>{{ strtoupper($purchaseOrder->address->tax_name) }}: {{ $purchaseOrder->address->tax_number }}
                        @endif
                </p>
            </td>
        </tr>
        <!-- Table Row End -->
        <!-- Table Row Start -->
        <tr>
            <td height="10"></td>
        </tr>
        <!-- Table Row End -->
        <!-- Table Row Start -->
        </tbody>
    </table>

    <table width="100%" class="f-14 b-collapse">
        <tr>
            <td height="10" colspan="2"></td>
        </tr>
        <!-- Table Row Start -->
        <tr class="main-table-heading text-grey">
            <td width="40%">@lang('app.description')</td>
            @if ($invoiceSetting->hsn_sac_code_show)
                <td align="right">@lang('app.hsnSac')</td>
            @endif
            <td align="right">@lang('modules.invoices.qty')</td>
            <td align="right">@lang('modules.invoices.unitPrice')</td>
            <td align="right">@lang('modules.invoices.tax')</td>
            <td align="right"
                width="{{ $invoiceSetting->hsn_sac_code_show ? '20%' : '23%' }}">@lang('modules.invoices.amount')
                ({{ $purchaseOrder->currency->currency_code }})
            </td>
        </tr>
        <!-- Table Row End -->
        @foreach ($purchaseOrder->items as $item)
            @if ($item->type == 'item')
            <!-- Table Row Start -->
                <tr class="f-12 main-table-items text-black">
                    <td width="40%" class="border-bottom-0">
                        {{ ($item->item_name) }}
                    </td>
                    @if ($invoiceSetting->hsn_sac_code_show)
                        <td align="right" width="10%" class="border-bottom-0">
                            {{ $item->hsn_sac_code ?  : '--' }}</td>
                    @endif
                    <td align="right" width="10%" class="border-bottom-0">{{ $item->quantity }} <br><span class="f-11 text-grey">{{ $item->unit->unit_type }}</td>
                    <td align="right"
                        class="border-bottom-0">{{ currency_format($item->unit_price, $purchaseOrder->currency_id, false) }}</td>
                    <td align="right" class="border-bottom-0">{{ strtoupper($item->tax_list) }}</td>
                    <td align="right" class="border-bottom-0"
                        width="{{ $invoiceSetting->hsn_sac_code_show ? '20%' : '23%' }}">
                        {{ currency_format($item->amount, $purchaseOrder->currency_id, false) }}</td>
                </tr>
                <!-- Table Row End -->
                @if ($item->item_summary != '' || $item->purchaseItemImage)
                    {{-- DOMPDF HACK FOR RENDER IN TABLE --}}
                    <tr>
                        <td colspan="{{ $invoiceSetting->hsn_sac_code_show ? '6' : '5' }}" class="f-13 summary text-black border-bottom-0 description">
                            {!! nl2br(strip_tags($item->item_summary, ['p', 'b', 'strong', 'a'])) !!}
                            @if ($item->purchaseItemImage)
                                <p class="mt-2">
                                    <img src="{{ $item->purchaseItemImage->file_url }}" width="60" height="60" class="img-thumbnail">
                                </p>
                            @endif
                        </td>
                    </tr>
                    {{-- DOMPDF HACK FOR RENDER IN TABLE --}}
                @endif
            @endif
        @endforeach
        <!-- Table Row Start -->
        <tr>
            <td class="total-box" align="right" colspan="{{ $invoiceSetting->hsn_sac_code_show ? '5' : '4' }}">
                <table width="100%" border="0" class="b-collapse">
                    <!-- Table Row Start -->
                    <tr align="right" class="text-grey">
                        <td width="50%" class="subtotal">@lang('modules.invoices.subTotal')</td>
                    </tr>
                    <!-- Table Row End -->
                    @if ($discount != 0 && $discount != '')
                        <!-- Table Row Start -->
                            <tr align="right" class="text-grey">
                                <td width="50%" class="subtotal">@lang('modules.invoices.discount')
                                </td>
                            </tr>
                            <!-- Table Row End -->
                    @endif
                    @foreach ($taxes as $key => $tax)
                        <!-- Table Row Start -->
                            <tr align="right" class="text-grey">
                                <td width="50%" class="subtotal">{{ mb_strtoupper($key) }}</td>
                            </tr>
                            <!-- Table Row End -->
                    @endforeach
                    <!-- Table Row Start -->
                    <tr align="right" class="text-grey">
                        <td width="50%" class="total">@lang('modules.invoices.total')</td>
                    </tr>
                    <!-- Table Row End -->
                </table>
            </td>
            <td class="total-box" align="right"
                width="{{ $invoiceSetting->hsn_sac_code_show ? '20%' : '23%' }}">
                <table width="100%" class="b-collapse">
                    <!-- Table Row Start -->
                    <tr align="right" class="text-grey">
                        <td class="subtotal-amt">
                            {{ currency_format($purchaseOrder->sub_total, $purchaseOrder->currency_id, false) }}</td>
                    </tr>
                    <!-- Table Row End -->
                @if ($discount != 0 && $discount != '')
                    <!-- Table Row Start -->
                        <tr align="right" class="text-grey">
                            <td class="subtotal-amt">
                                {{ currency_format($discount, $purchaseOrder->currency_id, false) }}</td>
                        </tr>
                        <!-- Table Row End -->
                @endif
                @foreach ($taxes as $key => $tax)
                    <!-- Table Row Start -->
                        <tr align="right" class="text-grey">
                            <td class="subtotal-amt">{{ currency_format($tax, $purchaseOrder->currency_id, false) }}
                            </td>
                        </tr>
                        <!-- Table Row End -->
                @endforeach
                <!-- Table Row Start -->
                    <tr align="right" class="text-grey">
                        <td class="total-amt f-15">
                            {{ currency_format($purchaseOrder->total, $purchaseOrder->currency_id, false) }}</td>
                    </tr>
                    <!-- Table Row End -->
                </table>
            </td>
        </tr>
    </table>

    <table class="bg-white" border="0" cellpadding="0" cellspacing="0" width="100%" role="presentation">
        <tbody>
        <!-- Table Row Start -->
        @if ($purchaseOrder->note != '')
            <tr>
                <td height="10"></td>
            </tr>
            <tr>
                <td class="f-11">@lang('app.note')</td>
            </tr>
            <!-- Table Row End -->
            <!-- Table Row Start -->
            <tr class="text-grey">
                <td class="f-11 line-height word-break">{!! $purchaseOrder->note ? nl2br($purchaseOrder->note) : '--' !!}</td>
            </tr>
        @endif
        <tr>
            <td height="10"></td>
        </tr>
        {{-- <tr>
            <td class="f-11">
                @lang('modules.invoiceSettings.invoiceTerms')</td>
        </tr> --}}
        <!-- Table Row End -->

        @if (isset($taxes) && $invoiceSetting->tax_calculation_msg == 1)
            <!-- Table Row Start -->
            <tr class="text-grey">
                <td width="100%" class="f-11 line-height">
                    <p class="text-dark-grey">
                        @if ($purchaseOrder->calculate_tax == 'after_discount')
                            @lang('messages.calculateTaxAfterDiscount')
                        @else
                            @lang('messages.calculateTaxBeforeDiscount')
                        @endif
                    </p>
                </td>
            </tr>
            <!-- Table Row End -->
        @endif
        <!-- Table Row End -->
        </tbody>
    </table>

    <p>
        <div style="margin-top: 10px;" class="f-11 line-height text-grey">
            <b>@lang('modules.invoiceSettings.invoiceTerms')</b><br>{!! nl2br($purchaseSetting->purchase_terms) !!}
        </div>
    </p>

</body>

</html>
