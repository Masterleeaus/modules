<!DOCTYPE html>

<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>@lang('purchase::app.menu.bill') - {{ $purchaseBill->bill_number }}</title>

    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description" content="Invoice">
    @includeIf('invoices.pdf.invoice_pdf_css')

    <style>
    @if($invoiceSetting->locale !== 'th')
        html,
        body,
        div,
        span,
        applet,
        object,
        iframe,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        p,
        blockquote,
        pre,
        a,
        abbr,
        acronym,
        address,
        big,
        cite,
        code,
        del,
        dfn,
        em,
        img,
        ins,
        kbd,
        q,
        s,
        samp,
        small,
        strike,
        strong,
        sub,
        sup,
        tt,
        var,
        b,
        u,
        i,
        center,
        dl,
        dt,
        dd,
        ol,
        ul,
        li,
        fieldset,
        form,
        label,
        legend,
        table,
        caption,
        tbody,
        tfoot,
        thead,
        tr,
        th,
        td,
        article,
        aside,
        canvas,
        details,
        embed,
        figure,
        figcaption,
        footer,
        header,
        hgroup,
        menu,
        nav,
        output,
        ruby,
        section,
        summary,
        time,
        mark,
        audio,
        video {
            margin: 0;
            padding: 0;
            border: 0;
            font: inherit;
            font-size: 12px;
            vertical-align: baseline;
            /* font-family: Verdana, Arial, Helvetica, sans-serif; */
        }

        html {
            line-height: 1;
        }

        @endif

        ol,
        ul {
            list-style: none;
        }

        table {
            border-collapse: collapse;
            border-spacing: 0;
        }

        caption,
        th,
        td {
            text-align: left;
            font-weight: normal;
            vertical-align: middle;
        }

        q,
        blockquote {
            quotes: none;
        }

        q:before,
        q:after,
        blockquote:before,
        blockquote:after {
            content: "";
            content: none;
        }

        a img {
            border: none;
        }

        article,
        aside,
        details,
        figcaption,
        figure,
        footer,
        header,
        hgroup,
        main,
        menu,
        nav,
        section,
        summary {
            display: block;
        }

        /* Invoice styles */
        /**
         * DON'T override any styles for the <html> and <body> tags, as this may break the layout.
         * Instead wrap everything in one main <div id="container"> element where you may change
         * something like the font or the background of the invoice
         */
        html,
        body {
            /* MOVE ALONG, NOTHING TO CHANGE HERE! */
        }

        /**
         * IMPORTANT NOTICE: DON'T USE '!important' otherwise this may lead to broken print layout.
         * Some browsers may require '!important' in oder to work properly but be careful with it.
         */
        .clearfix {
            display: block;
            clear: both;
        }

        .x-hidden {
            display: none !important;
        }

        .hidden {
            display: none;
        }

        b,
        strong,
        .bold {
            font-weight: bold;
        }

        #container {
            margin: 0 auto;
            position: relative;
        }

        .right-invoice {
            padding: 40px 30px;
        }

        #memo .company-info {
            float: left;
        }

        #memo .company-info {
            font-size: 12px;
            min-width: 20px;
            line-height: 15px;
        }

        #memo .company-info span {
            font-size: 12px;
            display: inline-block;
            min-width: 20px;
        }

        #memo .logo {
            float: right;
            margin-left: 15px;
        }

        #memo .logo img {
            height: 50px;
        }

        #memo:after {
            content: '';
            display: block;
            clear: both;
        }

        #invoice-title-number {
            margin: 20px 0 20px 0;
            display: inline-block;
            float: left;
        }

        #invoice-title-number .title-top {
            font-size: 15px;
            margin-bottom: 5px;
        }

        #invoice-title-number .title-top span {
            display: inline-block;
            min-width: 20px;
        }

        #invoice-title-number .title-top #number {
            text-align: right;
            float: right;
            color: #858585;
        }

        #invoice-title-number .title-top:after {
            content: '';
            display: block;
            clear: both;
        }

        #invoice-title-number #title {
            display: inline-block;
            background: #415472;
            color: white;
            font-size: 25px !important;
            padding: 8px 13px;
        }

        #client-info {
            text-align: right;
            min-width: 220px;
            line-height: 15px;
            font-size: 12px;
        }

        #client-info>div {
            margin-bottom: 3px;
            min-width: 20px;
        }

        #client-info span {
            display: block;
            min-width: 20px;
        }

        table {
            table-layout: fixed;
        }

        table th,
        table td {
            vertical-align: top;
            word-break: keep-all;
            word-wrap: break-word;
        }

        #invoice-info {
            float: left;
            margin-top: 10px;
            line-height: 18px;
        }

        #invoice-info div {
            margin-bottom: 3px;
        }

        #invoice-info div span {
            display: inline-block;
            min-width: 20px;
            min-height: 18px;
        }

        #invoice-info div span:first-child {
            font-weight: bold;
            margin-right: 10px;
        }

        .currency {
            margin-top: 20px;
            text-align: right;
            color: #858585;
            font-style: italic;
            font-size: 12px;
        }

        .currency span {
            display: inline-block;
            min-width: 20px;
        }

        #items {
            margin-top: 10px;
        }

        #items .first-cell,
        #items table th:first-child,
        #items table td:first-child {
            width: 18px;
            text-align: right;
        }

        #items table {
            border-collapse: separate;
            width: 100%;
        }

        #items table th {
            font-size: 12px;
            padding: 5px 3px;
            text-align: center;
            background: #b0b4b3;
            color: white;
        }

        #items table th:nth-child(2) {
            width: 30%;
            text-align: left;
        }

        #items table th:last-child {
            /*text-align: right;*/
        }

        #items table td {
            padding: 10px 3px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        #items table td:first-child {
            text-align: left;
        }

        #items table td:nth-child(2) {
            text-align: left;
        }

        #sums {
            margin: 25px 30px 0 0;
            width: 100%;
        }

        #sums table {
            width: 70%;
            float: right;
        }

        #sums table tr th,
        #sums table tr td {
            min-width: 100px;
            padding: 9px 8px;
            text-align: right;
        }

        #sums table tr th {
            width: 70%;
            font-weight: bold;
        }

        #sums table tr td.last {
            min-width: 0 !important;
            max-width: 0 !important;
            width: 0 !important;
            padding: 0 !important;
            border: none !important;
        }

        #sums table tr.amount-total td,
        #sums table tr.amount-total th {
            font-size: 20px !important;
        }

        #sums table tr.due-amount th,
        #sums table tr.due-amount td {
            font-weight: bold;
        }

        #sums:after {
            content: '';
            display: block;
            clear: both;
        }

        #terms {
            margin-top: 20px !important;
            font-size: 12px;
        }

        .calculate_tax {
            margin-top: 20px !important;
            font-size: 12px;
        }

        #terms>span {
            font-weight: bold;
            display: inline-block;
            min-width: 20px;
        }

        #terms>div {
            min-height: 50px;
            min-width: 50px;
        }

        #terms .notes {
            min-height: 30px;
            min-width: 50px;
        }

        .item-summary {
            font-size: 11px;
            margin-top: 10px;
            margin-bottom: 10px;
        }

        .mb-3 {
            margin-bottom: 1rem;
        }

        /**
         * If the printed invoice is not looking as expected you may tune up
         * the print styles (you can use !important to override styles)
         */
        @media print {
            /* Here goes your print styles */
        }

        .page_break {
            page-break-before: always;
        }

        .h3-border {
            border-bottom: 1px solid #AAAAAA;
        }

        table td.text-center {
            text-align: center;
        }

        #itemsPayment {
            margin-top: 10px;
        }

        #itemsPayment .first-cell,
        #itemsPayment table th:first-child,
        #itemsPayment table td:first-child {
            width: 18px;
            text-align: right;
        }

        #itemsPayment table {
            border-collapse: separate;
            width: 100%;
        }

        #itemsPayment table th {
            font-size: 12px;
            padding: 5px 3px;
            text-align: center;
            background: #b0b4b3;
            color: white;
        }

        #itemsPayment table th:nth-child(2) {
            width: 30%;
            text-align: left;
        }

        #itemsPayment table th:last-child {
            /*text-align: right;*/
        }

        #itemsPayment table td {
            padding: 10px 3px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        #itemsPayment table td:first-child {
            text-align: left;
        }

        #itemsPayment table td:nth-child(2) {
            text-align: left;
        }

        #itemsPayment,
        .box-title {
            margin: 25px 30px 0 30px;
        }

        .word-break {
            word-wrap: break-word;
        }

        .left-stripes {
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 100px;
            background: url("{{ asset("img/stripe-bg.jpg") }}") repeat;
        }
        .left-stripes .circle {
            -moz-border-radius: 50%;
            -webkit-border-radius: 50%;
            border-radius: 50%;
            background: #415472;
            width: 30px;
            height: 30px;
            position: absolute;
            left: 33%;
        }
        .left-stripes .circle.c-upper {
            top: 440px;
        }
        .left-stripes .circle.c-lower {
            top: 690px;
        }

        #notes {
            color: #767676;
            font-size: 11px;
            margin-top: 10px;
            margin-left: 40px;
        }

        #signatory img {
            height:95px;
            margin-bottom: -50px;
            margin-top: 5px;
            margin-right: 10;
        }

        #field-title {
            color: #504f4f;
            font-size: 13px;
            margin-top: 10px;
            margin-left: 40px;
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

        #memo .client-logo {
        float:left;
        display: flex !important;
        flex-direction: column !important;
        position: absolute;
        /* margin-bottom: 15px; */
        }

        #memo .client-logo img {
            height: 50px;
            margin-bottom: 10px;
        }

        .f-11 {
            font-size: 11px;
        }


    </style>

</head>

<body>
    <div id="container">
        <div class="right-invoice">
            <section id="memo" class="description">
                <div class="client-logo">
                    <div>
                        <img style="height:50px;" src="{{ $invoiceSetting->logo_url }}" />
                    </div>
                    <div class="company-info description">
                            <br>
                        <div class="description">
                            {{ mb_ucwords($company->company_name) }}
                        </div>
                        @if ($company->company_email)
                        <span class="description">{{ $company->company_email }}</span>
                            <br>
                        @endif
                        @if ($company->company_phone)
                        <span>{{ $company->company_phone }}</span>
                            <br>
                        @endif
                        @if ($purchaseOrder->address)
                            <span class="description">{!! nl2br($purchaseOrder->address->address) !!}</span>
                        @endif
                    </div>
                </div>

                <div class="logo" id="client-info" >

                    <section id="client-info"  class="description">
                        <span>@lang('modules.invoices.billedTo')</span>

                        {{ mb_ucwords(company()->company_name) }}<br>
                        @if ($purchaseOrder->address)
                            {!! nl2br($purchaseOrder->address->address) !!}<br>
                        @endif
                        {{ company()->company_phone }}
                        @if ($invoiceSetting->show_gst == 'yes' && $purchaseOrder->address)
                            <br>{{ strtoupper($purchaseOrder->address->tax_name) }}: {{ $purchaseOrder->address->tax_number }}
                        @endif

                    </section>

                </div>
            </section>

            <section id="invoice-title-number">

                <div class="title-top description">
                    <span>@lang('purchase::app.menu.billDate'):</span>
                    <span>{{ $purchaseBill->bill_date->translatedFormat(company()->date_format) }}</span>
                </div>

                <div id="title">{{ $purchaseBill->bill_number }}</div>

            </section>

            <div class="clearfix"></div>

            <section id="invoice-info">
                <div>
                    <span>@lang('app.status'):</span> <span>{{ mb_ucwords($purchaseBill->status) }}</span>
                </div>
                <div>
                    <span>@lang('purchase::app.menu.billNumber'):</span> <span>{{ $purchaseBill->bill_number }}</span>
                </div>
            </section>

            <div class="clearfix"></div>
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <p>&nbsp;</p>

            <section id="items">

                <table cellpadding="0" cellspacing="0" >

                    <tr>
                        <th>#</th> <!-- Dummy cell for the row number and row commands -->
                        <th class="description">@lang('modules.invoices.item')</th>
                        @if ($invoiceSetting->hsn_sac_code_show)
                            <th>@lang('app.hsnSac')</th>
                        @endif
                        <th class="description" style="text-align: right;">@lang('modules.invoices.qty')</th>
                        <th class="description">@lang('modules.invoices.unitPrice')</th>
                        <th class="description">@lang('modules.invoices.tax')</th>
                        <th class="description">@lang('modules.invoices.price') ({!! htmlentities($purchaseOrder->currency->currency_code) !!})</th>
                    </tr>

                    <?php $count = 0; ?>
                    @foreach ($purchaseOrder->items as $item)
                        @if ($item->type == 'item')
                            <tr data-iterate="item">
                                <td>{{ ++$count }}</td>
                                <!-- Don't remove this column as it's needed for the row commands -->
                                <td>
                                    {{ ($item->item_name) }}
                                    @if (!is_null($item->item_summary))
                                        <p class="item-summary mb-3">{!! nl2br(strip_tags($item->item_summary, ['p', 'b', 'strong', 'a'])) !!}</p>
                                    @endif
                                    @if ($item->purchaseItemImage)
                                        <p>
                                            <img src="{{ $item->purchaseItemImage->file_url }}" width="80" height="80"
                                                class="img-thumbnail">
                                        </p>
                                    @endif
                                </td>
                                @if ($invoiceSetting->hsn_sac_code_show)
                                    <td>{{ $item->hsn_sac_code ? $item->hsn_sac_code : '--' }}</td>
                                @endif
                                <td style="text-align: right;">{{ $item->quantity }} <br><span class="item-summary">{{ $item->unit->unit_type }}</td>
                                <td>{{ currency_format($item->unit_price, $purchaseOrder->currency_id, false) }}</td>
                                <td>{{ strtoupper($item->tax_list) }}</td>
                                <td>{{ currency_format($item->amount, $purchaseOrder->currency_id, false) }}</td>
                            </tr>
                        @endif
                    @endforeach

                </table>

            </section>

            <section id="sums">

                <table cellpadding="0" cellspacing="0">
                    <tr>
                        <th>@lang('modules.invoices.subTotal'):</th>
                        <td>{{ currency_format($purchaseOrder->sub_total, $purchaseOrder->currency_id, false) }}</td>
                    </tr>
                    @if ($discount != 0 && $discount != '')
                        <tr data-iterate="tax">
                            <th>@lang('modules.invoices.discount'):</th>
                            <td>{{ currency_format($discount, $purchaseOrder->currency_id, false) }}</td>
                        </tr>
                    @endif
                    @foreach ($taxes as $key => $tax)
                        <tr data-iterate="tax">
                            <th>{{ mb_strtoupper($key) }}:</th>
                            <td>{{ currency_format($tax, $purchaseOrder->currency_id, false) }}</td>
                        </tr>
                    @endforeach
                    <tr class="amount-total">
                        <th>@lang('modules.invoices.total'):</th>
                        <td>{{ currency_format($purchaseOrder->total, $purchaseOrder->currency_id, false) }}</td>
                    </tr>

                </table>

            </section>


            <div class="clearfix"></div>

            <section id="terms">

                <div class="notes word-break description">
                    @if (!is_null($purchaseOrder->note))
                        <b>@lang('app.note')</b> <br>{!! nl2br($purchaseOrder->note) !!}<br>
                    @endif
                    @if ($purchaseOrder->status == 'unpaid')
                        <br><br><b>@lang('modules.invoiceSettings.invoiceTerms')</b><br>{!! nl2br($invoiceSetting->invoice_terms) !!}
                    @endif
                </div>

            </section>
            @if (isset($taxes) && $invoiceSetting->tax_calculation_msg == 1)
                <div class="clearfix"></div>
                <section class="calculate_tax" >
                    <div class="description">
                        @if ($purchaseOrder->calculate_tax == 'after_discount')
                            @lang('messages.calculateTaxAfterDiscount')
                        @else
                            @lang('messages.calculateTaxBeforeDiscount')
                        @endif
                    </div>
                </section>
            @endif
        </div>
    </div>

</body>

</html>
