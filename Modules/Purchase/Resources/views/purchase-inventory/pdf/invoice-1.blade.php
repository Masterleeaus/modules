<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>@lang('app.invoice')</title>
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
                    <h1>@lang('purchase::app.menu.inventory')</h1>
                </td>
            </tr>
            <tr>
                <td id="ordered_to"></td>
                <td>
                    <div id="company" class="description">
                        <div id="logo">
                            <img src="{{ $invoiceSetting->logo_url }}" alt="home" class="dark-logo" />
                        </div>
                        @if ($company && $company->company_name)
                            {{ $company->company_name }}<br>
                        @endif
                        @if ($company && $company->company_email)
                            {{ $company->company_email }}<br>
                        @endif
                        @if ($company && $company->address)
                            {{ $company->address }}<br>
                        @endif
                        @if ($company && $company->company_phone)
                            {{ $company->company_phone }}<br>
                        @endif
                    </div>
                </td>
            </tr>
        </table>
    </header>
    <main>
        <div id="details">
            <div id="order"  class="description">
                <h1>@lang('purchase::app.menu.inventory') #{{ $inventory->id }}</h1>

                <div class="date">@lang('purchase::modules.inventory.inventoryDateInSmall'):
                    {{ $inventory->date->translatedFormat(company()->date_format) }}</div>

                <div class="">@lang('purchase::modules.inventory.inventoryStatus'): {{ $inventory->status }}</div>
            </div>
        </div>

        <table cellspacing="0" cellpadding="0" id="invoice-table">
            <thead>
                <tr style="border-bottom: 1px solid #FFFFFF;">
                    <th class="no description background-green">#</th>
                    <th class="desc description">@lang('purchase::app.itemName')</th>
                    @if ($invoiceSetting->hsn_sac_code_show)
                        <th class="qty description">@lang('app.hsnSac')</th>
                    @endif
                    @if ($inventory->type == 'quantity')
                        <th class="qty description">@lang('purchase::modules.product.quantityOnHand')</th>
                        <th class="qty description">@lang('purchase::modules.product.quabtityAdjusted')</th>
                    @else
                        <th class="qty description">@lang('purchase::modules.product.changedValue')</th>
                        <th class="qty description">@lang('purchase::modules.product.adjustedValue')</th>
                    @endif
                    <th class="unit description text-dark-grey">@lang('app.status')</th>
                </tr>
            </thead>
            <tbody>
                <?php $count = 0; ?>
                @foreach ($inventory->stocks as $item)
                    <tr style="page-break-inside: avoid;">
                        <td class="no background-green">{{ ++$count }}</td>
                        <td class="desc text-green">
                            <h3  class="description">{{ $item->product ? ($item->product->name) : '--' }}</h3>
                            @if (!is_null($item->description))
                                <table>
                                    <tr>
                                        <td class="item-summary  description word-break border-top-0 border-right-0 border-left-0 border-bottom-0" style="color:#555555;">
                                            {!! nl2br(strip_tags($item->description, ['p', 'b', 'strong', 'a'])) !!}</td>
                                    </tr>
                                </table>
                            @endif
                            @if ($item->files)
                                <p class="mt-2">
                                    <img src="{{ $item->file_url }}" width="60" height="60" class="img-thumbnail">
                                </p>
                            @endif
                        </td>
                        @if ($invoiceSetting->hsn_sac_code_show)
                            <td class="qty text-green">
                                <h3>{{ $item->hsn_sac_code ? $item->hsn_sac_code : '--' }}</h3>
                            </td>
                        @endif

                        @if ($inventory->type == 'quantity')
                            <td class="qty text-green">{{ $item->net_quantity }}</td>
                            <td class="qty text-green">{{ $item->quantity_adjustment }}</td>
                        @else
                            <td class="qty text-green">{{ $item->changed_value }}</td>
                            <td class="qty text-green">{{ $item->adjusted_value }}</td>
                        @endif

                        <td class="unit text-dark-grey"><span class="badge @if ($item->status == 'draft') badge-secondary @else badge-success @endif username-badge">{{ $item->status }}</span></td>

                    </tr>
                @endforeach
            </tbody>
        </table>

        <p id="notes" class="word-break description">
            <div style="margin-top: 10px;">
                <b>@lang('modules.invoiceSettings.invoiceTerms')</b><br>{!! nl2br($invoiceSetting->invoice_terms) !!}
            </div>
        </p>

    </main>
</body>

</html>
