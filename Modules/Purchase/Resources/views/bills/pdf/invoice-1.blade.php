<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>@lang('purchase::app.menu.bill') - {{ $purchaseBill->bill_number }}</title>
    @includeIf('invoices.pdf.invoice_pdf_css')
    <style>
        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }

        a {

            text-decoration: none;
        }

        body {
            position: relative;
            width: 100%;
            height: auto;
            margin: 0 auto;
            color: #555555;
            background: #FFFFFF;
            font-size: 13px;
            /* font-family: Verdana, Arial, Helvetica, sans-serif; */
        }

        h2 {
            font-weight: normal;
        }

        header {
            padding: 10px 0;
        }

        #logo img {
            height: 50px;
            margin-bottom: 15px;
        }

        #details {
            margin-bottom: 25px;
        }

        #client {
            padding-left: 6px;
            float: left;
        }

        #client .to {
            color: #777777;
        }

        h2.name {
            font-size: 1.2em;
            font-weight: normal;
            margin: 0;
        }

        #invoice h1 {
            color: #0087C3;
            line-height: 2em;
            font-weight: normal;
            margin: 0 0 10px 0;
            font-size: 20px;
        }

        #invoice .date {
            font-size: 1.1em;
            color: #777777;
        }

        table {
            width: 100%;
            border-spacing: 0;
            /* margin-bottom: 20px; */
        }

        table th,
        table td {
            padding: 5px 8px;
            text-align: center;
        }

        table th {
            background: #EEEEEE;
        }

        table th {
            white-space: nowrap;
            font-weight: normal;
        }

        table td {
            text-align: right;
        }

        table td.desc h3,
        table td.qty h3 {
            font-size: 0.9em;
            font-weight: normal;
            margin: 0 0 0 0;
        }

        table .no {
            font-size: 1.2em;
            width: 10%;
            text-align: center;
            border-left: 1px solid #e7e9eb;
        }

        table .desc,
        table .item-summary {
            text-align: left;
        }

        table .unit {
            /* background: #DDDDDD; */
            border: 1px solid #e7e9eb;
        }


        table .total {
            background: #57B223;
            color: #FFFFFF;
        }

        table td.unit,
        table td.qty,
        table td.total {
            font-size: 1.2em;
            text-align: center;
        }

        table td.unit {
            width: 35%;
        }

        table td.desc {
            width: 45%;
        }

        table td.qty {
            width: 5%;
        }

        .status {
            margin-top: 15px;
            padding: 1px 8px 5px;
            font-size: 1.3em;
            width: 80px;
            float: right;
            text-align: center;
            display: inline-block;
        }

        .status.unpaid {
            background-color: #E7505A;
        }

        .status.paid {
            background-color: #26C281;
        }

        .status.cancelled {
            background-color: #95A5A6;
        }

        .status.error {
            background-color: #F4D03F;
        }

        table tr.tax .desc {
            text-align: right;
        }

        table tr.discount .desc {
            text-align: right;
            color: #E43A45;
        }

        table tr.subtotal .desc {
            text-align: right;
        }


        table tfoot td {
            padding: 10px;
            font-size: 1.2em;
            white-space: nowrap;
            border-bottom: 1px solid #e7e9eb;
            font-weight: 700;
        }

        table tfoot tr:first-child td {
            border-top: none;
        }

        table tfoot tr td:first-child {
            /* border: none; */
        }


        #notices {
            padding-left: 6px;
            border-left: 6px solid #0087C3;
        }

        #notices .notice {
            font-size: 1.2em;
        }

        footer {
            color: #777777;
            width: 100%;
            height: 30px;
            position: absolute;
            bottom: 0;
            border-top: 1px solid #e7e9eb;
            padding: 8px 0;
            text-align: center;
        }

        table.billing td {
            background-color: #fff;
        }

        table td#ordered_to {
            text-align: left;
            padding-left: 0;
        }

        #notes {
            color: #767676;
            font-size: 11px;
        }

        .item-summary {
            font-size: 11px;
            padding-left: 0;
        }


        .page_break {
            page-break-before: always;
        }


        table td.text-center {
            text-align: center;
        }

        .word-break {
            word-wrap: break-word;
        }

        #invoice-table td {
            border-bottom: 1px solid #FFFFFF;
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

        .background-green{
            background-color: #57B223;
            color: #FFFFFF;
        }

        .text-green{
            background-color: #e7e9eb;
            color: #57B223;
        }

        .text-dark-grey{
            background-color: #ced0d2;
        }

        #signatory img {
            height:95px;
            margin-bottom: -50px;
            margin-top: 5px;
            margin-right: 20;
        }

        @if($invoiceSetting->locale == 'th')

            table td {
            font-weight: bold !important;
            font-size: 20px !important;
            }

            .description
            {
                font-weight: bold !important;
                font-size: 16px !important;
            }
        @endif

        .client-logo {
            height:50px;
            margin-bottom:20px;
        }

    </style>
</head>

<body>
    <header class="clearfix"  class="description">

        <table cellpadding="0" cellspacing="0" class="billing">
            <tr>
                <td colspan="2">
                    <h1>@lang('app.bill')</h1>
                </td>
            </tr>
            <tr>
                <td id="ordered_to">
                    <div  class="description">
                            <small>@lang('modules.invoices.billedTo'):</small><br>

                            {{ mb_ucwords(company()->company_name) }}<br>
                            @if (company()->address)
                                {!! nl2br(company()->address) !!}<br>
                            @endif
                            {{ company()->company_phone }}
                            @if ($invoiceSetting->show_gst == 'yes' && $purchaseOrder->address)
                                <br>{{ strtoupper($purchaseOrder->address->tax_name) }}: {{ $purchaseOrder->address->tax_number }}
                            @endif
                    </div>
                </td>
                <td>
                    <div id="company"  class="description">
                        <div id="logo">
                            <img src="{{ $invoiceSetting->logo_url }}" alt="home" class="dark-logo" />
                        </div>
                        <small>@lang('modules.invoices.billedFrom'):</small>
                        @if ($purchaseBill->vendor && $purchaseBill->vendor->primary_name)
                            {{$purchaseBill->vendor->primary_name}}<br>
                        @endif
                        @if ($purchaseBill->vendor && $purchaseBill->vendor->email)
                            {{$purchaseBill->vendor->email}}<br>
                        @endif
                        @if ($purchaseBill->vendor && $purchaseBill->vendor->billing_address)
                            {{$purchaseBill->vendor->billing_address}}<br>
                        @endif
                        @if ($purchaseBill->vendor && $purchaseBill->vendor->phone)
                            {{$purchaseBill->vendor->phone}}<br>
                        @endif
                    </div>
                </td>
            </tr>
        </table>
    </header>
    <main>
        <div id="details">
            <div id="order"  class="description">
                <h1>{{ $purchaseBill->bill_number }}</h1>

                <div class="date">@lang('purchase::app.menu.billDate'):
                    {{ $purchaseBill->bill_date->translatedFormat(company()->date_format) }}</div>

                <div class="">@lang('app.status'): {{ $purchaseBill->status }}</div>
            </div>
        </div>
        <table cellspacing="0" cellpadding="0" id="invoice-table">
            <thead>
                <tr style="border-bottom: 1px solid #FFFFFF;">
                    <th class="no description background-green">#</th>
                    <th class="desc description">@lang('modules.invoices.item')</th>
                    @if ($invoiceSetting->hsn_sac_code_show)
                        <th class="qty description">@lang('app.hsnSac')</th>
                    @endif
                    <th class="qty description">@lang('modules.invoices.qty')</th>
                    <th class="qty description">@lang('modules.invoices.unitPrice')</th>
                    <th class="qty description">@lang('modules.invoices.tax')</th>
                    <th class="unit description text-dark-grey">@lang('modules.invoices.price') ({!! htmlentities($purchaseOrder->currency->currency_code) !!})</th>
                </tr>
            </thead>
            <tbody>
                <?php $count = 0; ?>
                @foreach ($purchaseBill->order->items as $item)
                    @if ($item->type == 'item')
                        <tr style="page-break-inside: avoid;">
                            <td class="no background-green">{{ ++$count }}</td>
                            <td class="desc text-green">
                                <h3  class="description">{{ ($item->item_name) }}</h3>
                                @if (!is_null($item->item_summary))
                                    <table>
                                        <tr>
                                            <td
                                                class="item-summary  description word-break border-top-0 border-right-0 border-left-0 border-bottom-0" style="color:#555555;">
                                                {!! nl2br(strip_tags($item->item_summary, ['p', 'b', 'strong', 'a'])) !!}</td>
                                        </tr>
                                    </table>
                                @endif
                                @if ($item->purchaseItemImage)
                                    <p class="mt-2">
                                        <img src="{{ $item->purchaseItemImage->file_url }}" width="60" height="60"
                                            class="img-thumbnail">
                                    </p>
                                @endif
                            </td>
                            @if ($invoiceSetting->hsn_sac_code_show)
                                <td class="qty text-green">
                                    <h3>{{ $item->hsn_sac_code ? $item->hsn_sac_code : '--' }}</h3>
                                </td>
                            @endif
                            <td class="qty text-green">
                                <h3>{{ $item->quantity }}<br><span class="item-summary" style="color:#555555;">{{ $item->unit->unit_type }}</h3>
                            </td>
                            <td class="qty text-green">
                                <h3>{{ currency_format($item->unit_price, $purchaseOrder->currency_id, false) }}</h3>
                            </td>
                            <td class="text-green">{{ strtoupper($item->tax_list) }}</td>
                            <td class="unit text-dark-grey">{{ currency_format($item->amount, $purchaseOrder->currency_id, false) }}</td>
                        </tr>
                    @endif
                @endforeach
                <tr style="page-break-inside: avoid;" class="subtotal">
                    <td class="no background-green">&nbsp;</td>
                    <td class="qty text-green">&nbsp;</td>
                    <td class="qty text-green">&nbsp;</td>
                    <td class="qty text-green">&nbsp;</td>
                    @if ($invoiceSetting->hsn_sac_code_show)
                        <td class="qty text-green">&nbsp;</td>
                    @endif
                    <td class="desc" style="background-color:#e7e9eb;">@lang('modules.invoices.subTotal')</td>
                    <td class="unit text-dark-grey">{{ currency_format($purchaseOrder->sub_total, $purchaseOrder->currency_id, false) }}</td>
                </tr>
                @if ($discount != 0 && $discount != '')
                    <tr style="page-break-inside: avoid;" class="discount">
                        <td class="no">&nbsp;</td>
                        <td class="qty">&nbsp;</td>
                        <td class="qty">&nbsp;</td>
                        @if ($invoiceSetting->hsn_sac_code_show)
                            <td class="qty">&nbsp;</td>
                        @endif
                        <td class="qty">&nbsp;</td>
                        <td class="desc">@lang('modules.invoices.discount')</td>
                        <td class="unit border-left-0 border-right-0" style="border-bottom: 1px solid #e7e9eb;">{{ currency_format($discount, $purchaseBill->order->currency_id, false) }}</td>
                    </tr>
                @endif
                @foreach ($taxes as $key => $tax)
                    <tr style="page-break-inside: avoid;" class="tax">
                        <td class="no">&nbsp;</td>
                        <td class="qty">&nbsp;</td>
                        <td class="qty">&nbsp;</td>
                        @if ($invoiceSetting->hsn_sac_code_show)
                            <td class="qty">&nbsp;</td>
                        @endif
                        <td class="qty">&nbsp;</td>
                        <td class="desc">{{ mb_strtoupper($key) }}</td>
                        <td class="unit border-left-0 border-right-0" style="border-bottom: 1px solid #e7e9eb;">{{ currency_format($tax, $purchaseBill->order->currency_id, false) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr dontbreak="true">
                    <td colspan="{{ $invoiceSetting->hsn_sac_code_show ? '6' : '5' }}">
                        @lang('modules.invoices.total')</td>
                    <td style="text-align: center; border-bottom: 1px solid #e7e9eb;">{{ currency_format($purchaseBill->total, $purchaseBill->order->currency_id, false) }}</td>
                </tr>
            </tfoot>
        </table>

        <p id="notes" class="word-break description">
            <div>
                @if (!is_null($purchaseOrder->note))
                    <b>@lang('app.note')</b><br>{!! nl2br($purchaseOrder->note) !!}<br>
                @endif
            </div>
            <div style="margin-top: 10px;">
                <b>@lang('modules.invoiceSettings.invoiceTerms')</b><br>{!! nl2br($invoiceSetting->invoice_terms) !!}
            </div>
        </p>

        @if (isset($taxes) && $invoiceSetting->tax_calculation_msg == 1)
            <p class="description">
                @if ($order->calculate_tax == 'after_discount')
                    @lang('messages.calculateTaxAfterDiscount')
                @else
                    @lang('messages.calculateTaxBeforeDiscount')
                @endif
            </p>
        @endif

    </main>
</body>

</html>
