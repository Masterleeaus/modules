<!doctype html>
<html lang="en">

    <head>
    <!-- Required meta tags -->
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>@lang('purchase::app.menu.report')</title>
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage" content="{{ $company->favicon_url }}">
        <meta name="theme-color" content="#ffffff">
        @includeIf('vendor-payments.pdf.payment_pdf_css')
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
                font-size: 20px;
            }

            .f-12 {
                font-size: 18px;
            }

            .f-13 {
                font-size: 20px;
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
                margin-top: 3rem;
            }
            .mt-5 {
                margin-top: 9rem;
            }

            .mb-0 {
                margin-bottom: 0px;
            }

            .b-collapse {
                border-collapse: collapse;
            }

            .heading-table-left {
                padding: 5px;
                border: 1px solid #DBDBDB;
                font-weight: bold;
                background-color: #f1f1f3;
                border-right: 0;
            }

            .heading-table-right {
                padding: 4px;
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

            .word-break {
                word-wrap: break-word;
                word-break: break-all;
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
        <table class="bg-white" width="100%" role="presentation">
            @forelse ($bills as $bill)
            <!-- Table Row Start -->
                <tr>
                    <td align="right" class="f-21 text-black font-weight-700 text-uppercase">@lang('purchase::app.menu.paymentReceipt')<br>
                        <table class="text-black mt-1 f-11 b-collapse rightaligned">
                            <tr>
                                <td class="heading-table-left">@lang('purchase::app.menu.purchaseOrder')</td>
                                <td class="heading-table-right">{{ $bill->bill->order->purchase_order_number }}</td>
                            </tr>
                            <tr>
                                <td class="heading-table-left">@lang('app.date')</td>
                                <td class="heading-table-right">{{ $vendorPayment->payment_date->translatedFormat(company()->date_format) ?? '--' }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            @empty
            @endforelse
<!-- Table Row End -->
            <tr>
                <table width="100%" class="text-black mt-5 f-11 b-collapse center" border="1" cellspacing="0" cellpadding="0" >
                    <tr class="f-13 main-table-items text-black">
                        @php
                        $bankName = isset($vendorPayment->bank_account_id) && $vendorPayment->bankAccount->bank_name ? $vendorPayment->bankAccount->bank_name.' |' : '';
                        $currencyId = (isset($vendorPayment->vendor->currency)) ? $vendorPayment->vendor->currency->id : '';
                        @endphp
                        <td width="20%" style="font-weight: bold;">@lang('purchase::app.menu.vendorName')</td>
                        <td width="20%">{{ ucwords($vendorPayment->vendor->primary_name) }}</td>
                        <td width="20%" style="font-weight: bold;">@lang('purchase::app.menu.bankAccount')</td>
                        <td width="20%">{{ ($vendorPayment->bank_account_id ? $bankName.' '.mb_ucwords($vendorPayment->bankAccount->account_name) : '--') }}</td>
                    </tr>
                    <tr class="f-13 main-table-items text-black">
                        <td width="20%" style="font-weight: bold;">@lang('purchase::app.menu.paymentMade')</td>
                        <td width="20%">{{currency_format($vendorPayment->received_payment, $currencyId ,false) ?? '--' }}
                            ({{ $vendorPayment->vendor->currency->currency_code }})
                        </td>
                        <td width="20%" style="font-weight: bold;">@lang('purchase::app.menu.internalNote')</td>
                        <td width="20%">{{$vendorPayment->internal_note ?? '--'}}</td>
                    </tr>
                    <tr class="f-13 main-table-items text-black">
                        <td width="20%" style="font-weight: bold;">@lang('purchase::app.menu.billNumber')</td>
                        <td width="20%">{{ $bill->bill->bill_number }}</td>
                        <td width="20%" style="font-weight: bold;">@lang('purchase::modules.vendorPayment.billAmount')</td>
                        <td width="20%">{{ currency_format($bill->bill->total, $currencyId, false) }}
                            ({{ $vendorPayment->vendor->currency->currency_code }})
                        </td>
                    </tr>
                    <tr class="f-13 main-table-items text-black">
                        @php
                            $due = ($bill->bill->total) - ($billArray[$bill->id]);
                        @endphp
                        <td width="20%" style="font-weight: bold;">@lang('purchase::modules.vendorPayment.amountDue')</td>
                        <td width="20%">{{currency_format(($due),$currencyId, false)}}
                            ({{ $vendorPayment->vendor->currency->currency_code }})
                        </td>
                        <td width="20%" style="font-weight: bold;">@lang('app.payment')</td>
                        <td width="20%">{{currency_format($bill->total_paid, $currencyId, false)}}
                            ({{ $vendorPayment->vendor->currency->currency_code }})
                        </td>
                    </tr>
                </table>
            </tr>
    </body>

</html>
