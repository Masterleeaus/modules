<style>
    #logo {
        height: 50px;
    }
    #signatory img {
        height:95px;
        margin-bottom: -40px;
        margin-top: 5px;
        margin-right: 15px;
    }
</style>

<div class="card border-0 invoice">
    <!-- CARD BODY START -->
    <div class="card-body">

        @if ($message = Session::get('success'))
            <div class="alert alert-success alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                <i class="fa fa-check"></i> {!! $message !!}
            </div>
            <?php Session::forget('success'); ?>
        @endif

        @if ($message = Session::get('error'))
            <div class="custom-alerts alert alert-danger fade in">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                {!! $message !!}
            </div>
            <?php Session::forget('error'); ?>
        @endif

        <div class="invoice-table-wrapper">
            <table width="100%">
                <tr class="inv-logo-heading">
                    <td><img src="{{ invoice_setting()->logo_url }}" alt="{{ mb_ucwords(company()->company_name) }}"
                            id="logo" /></td>
                    <td align="right" class="font-weight-bold f-21 text-dark text-uppercase mt-4 mt-lg-0 mt-md-0">
                        @lang('purchase::app.menu.bill')</td>
                </tr>
                <tr class="inv-num">
                    <td class="f-14 text-dark">
                        <p class="mt-3 mb-0">
                            {{ mb_ucwords(company()->company_name) }}<br>
                            @if (!is_null($purchaseBill) && $purchaseBill->order->address)
                                {!! nl2br($purchaseBill->order->address->address) !!}<br>
                            @endif
                            {{ company()->company_phone }}
                        </p><br>
                    </td>
                    <td align="right">
                        <table class="inv-num-date text-dark f-13 mt-3">
                            <tr>
                                <td class="bg-light-grey border-right-0 f-w-500">
                                    @lang('purchase::app.menu.billNumber')</td>
                                <td class="border-left-0">{{ $purchaseBill->bill_number }}</td>
                            </tr>
                            <tr>
                                <td class="bg-light-grey border-right-0 f-w-500">
                                    @lang('purchase::app.menu.billDate')</td>
                                <td class="border-left-0">{{ $purchaseBill->bill_date->translatedFormat(company()->date_format) }}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td height="20"></td>
                </tr>
            </table>
            <table width="100%">
                <tr class="inv-unpaid">
                    <td class="f-14 text-dark">
                        <p class="mb-0 text-left">
                                <span class="text-dark-grey ">@lang('modules.invoices.billedTo')</span><br>

                                @if ($purchaseBill->purchaseVendor && $purchaseBill->purchaseVendor->primary_name)
                                    {{ mb_ucwords($purchaseBill->purchaseVendor->primary_name) }}<br>
                                @endif

                                @if ($purchaseBill->purchaseVendor && $purchaseBill->purchaseVendor->email)
                                    {{ $purchaseBill->purchaseVendor->email }}<br>
                                @endif

                                @if ($purchaseBill->purchaseVendor && $purchaseBill->purchaseVendor->phone)
                                    {{ $purchaseBill->purchaseVendor->phone }}<br>
                                @endif

                                @if ($purchaseBill->purchaseVendor && $purchaseBill->purchaseVendor->company_name)
                                    {{ mb_ucwords($purchaseBill->purchaseVendor->company_name) }}<br>
                                @endif

                                @if (
                                    $purchaseBill->purchaseVendor && $purchaseBill->purchaseVendor->billing_address)
                                    {!! nl2br($purchaseBill->purchaseVendor->billing_address) !!}
                                @endif
                        </p>
                    </td>
                    @if ($purchaseBill->purchaseVendor && $purchaseBill->purchaseVendor->shipping_address)
                        <td align="right" class="f-14 text-black">
                            <p>
                            <span
                                    class="text-dark-grey ">@lang('app.shippingAddress')</span><br>
                                {!! nl2br($purchaseBill->purchaseVendor->shipping_address) !!}</p>
                        </td>
                    @endif
                </tr>
                <tr>
                    <td height="30" colspan="2"></td>
                </tr>
            </table>
            <table width="100%" class="inv-desc d-none d-lg-table d-md-table">
                <tr>
                    <td colspan="2">
                        <table class="inv-detail f-14 table-responsive-sm" width="100%">
                            <tr class="i-d-heading bg-light-grey text-dark-grey font-weight-bold">
                                <td class="border-right-0" width="35%">@lang('app.description')</td>
                                    <td class="border-right-0 border-left-0" align="right">@lang('app.hsnSac')</td>
                                <td class="border-right-0 border-left-0" align="right">
                                    @lang('modules.invoices.qty')
                                </td>
                                <td class="border-right-0 border-left-0" align="right">
                                    @lang('modules.invoices.unitPrice') ({{ $orderCurrency->currency->currency_code }})
                                </td>
                                <td class="border-right-0 border-left-0" align="right">@lang('modules.invoices.tax')</td>
                                <td class="border-left-0" align="right"
                                    width="20%">
                                    @lang('modules.invoices.amount')
                                    </td>
                            </tr>
                            @foreach ($purchaseOrder->items as $item)
                                @if ($item->type == 'item')
                                    <tr class="text-dark font-weight-semibold f-13">
                                        <td>{{ ($item->item_name) }}</td>
                                            <td align="right">{{ $item->hsn_sac_code }}</td>
                                        <td align="right">{{ $item->quantity }} </td>
                                        <td align="right">
                                            {{ $item->unit_price}}</td>
                                        <td align="right">
                                            {{ strtoupper($item->tax_list) }}
                                        </td>
                                        <td align="right">
                                            {{ $item->amount}}
                                        </td>
                                    </tr>
                                    @if ($item->item_summary || $item->purchaseItemImage)
                                        <tr class="text-dark f-12">
                                            <td colspan="{{ invoice_setting()->hsn_sac_code_show ? '6' : '5' }}"
                                                class="border-bottom-0">
                                                {!! nl2br($item->item_summary) !!}
                                                @if ($item->purchaseItemImage)
                                                    <p class="mt-2">
                                                        <a href="javascript:;" class="img-lightbox"
                                                            data-image-url="{{ $item->purchaseItemImage->file_url }}">
                                                            <img src="{{ $item->purchaseItemImage->file_url }}"
                                                                width="80" height="80" class="img-thumbnail">
                                                        </a>
                                                    </p>
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                @endif
                            @endforeach

                                <td colspan="4"
                                    class="blank-td border-bottom-0 border-left-0 border-right-0"></td>
                                <td class="p-0 border-right-0" align="right">
                                    <table width="100%">
                                        <tr class="text-dark-grey" align="right">
                                            <td class="w-50 border-top-0 border-left-0">
                                                @lang('modules.invoices.subTotal')</td>
                                        </tr>
                                        @if ($purchaseBill->discount != 0 && $purchaseBill->discount != '')
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
                                        <tr class=" text-dark-grey font-weight-bold" align="right">
                                            <td class="w-50 border-bottom-0 border-left-0">
                                                @lang('modules.invoices.total')</td>
                                        </tr>
                                        <tr class="bg-light-grey text-dark f-w-500 f-16" align="right">
                                            <td class="w-50 border-bottom-0 border-left-0">
                                                @lang('modules.invoices.total')
                                                @lang('modules.invoices.due')</td>
                                        </tr>
                                    </table>
                                </td>
                                <td class="p-0 border-left-0" align="right">
                                    <table width="100%">
                                        <tr class="text-dark-grey" align="right">
                                            <td class="border-top-0 border-right-0">
                                                {{$purchaseBill->sub_total}}
                                            </td>
                                        </tr>
                                        @if ($discount != 0 && $discount != '')
                                            <tr class="text-dark-grey" align="right">
                                                <td class="border-top-0 border-right-0">
                                                    {{ $discount}}</td>
                                            </tr>
                                        @endif
                                        @foreach ($taxes as $key => $tax)
                                            <tr class="text-dark-grey" align="right">
                                                <td class="border-top-0 border-right-0">
                                                    {{ $tax }}</td>
                                            </tr>
                                        @endforeach
                                        <tr class=" text-dark-grey font-weight-bold" align="right">
                                            <td class="border-bottom-0 border-right-0">
                                                {{ $purchaseBill->total}}
                                            </td>
                                        </tr>
                                        <tr class="bg-light-grey text-dark f-w-500 f-16" align="right">
                                            <td class="border-bottom-0 border-right-0">
                                                {{ $purchaseBill->amountDue($purchaseBill->purchaseVendor->id)}}
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>

                </tr>
            </table>
            <table class="inv-note">
                <tr>
                    <td height="30" colspan="2"></td>
                </tr>
                <tr>
                    <td>@lang('app.note')</td>
                </tr>
                <tr>
                    <td style="vertical-align: text-top">
                        <p class="text-dark-grey">{!! !empty($purchaseBill->note) ? $purchaseBill->note : '--' !!}</p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table>
                            <tr>
                                @if (isset($taxes))
                                    <p class="text-dark-grey">
                                        @if ($purchaseOrder->calculate_tax == 'after_discount')
                                            @lang('messages.calculateTaxAfterDiscount')
                                        @else
                                            @lang('messages.calculateTaxBeforeDiscount')
                                        @endif
                                    </p>
                                @endif
                            </tr>
                        </table>
                    </td>
                </tr>
            <table>

        </div>
    </div>
    <!-- CARD BODY END -->
    <!-- CARD FOOTER START -->

        <div class="card-footer bg-white border-0 d-flex justify-content-start py-0 py-lg-4 py-md-4 mb-4 mb-lg-3 mb-md-3 px-0">
            <div class="d-flex">
                <div class="inv-action mr-3 mr-lg-3 mr-md-3 dropup">
                    <button class="dropdown-toggle btn-primary" type="button" id="dropdownMenuButton"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">@lang('app.action')
                        <span><i class="fa fa-chevron-up f-15"></i></span>
                    </button>
                    {{-- <!-- DROPDOWN - INFORMATION --> --}}
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton" tabindex="0">

                        @if (($editBillPermission =='all' || $editBillPermission =='added' && user()->id == $purchaseBill->added_by)
                            && $purchaseBill->status != 'paid')
                            <li>
                                <a class="dropdown-item f-14 text-dark"
                                    href="{{ route('bills.edit', [$purchaseBill->id]) }}">
                                    <i class="fa fa-edit f-w-500 mr-2 f-11"></i> @lang('app.edit')
                                </a>
                            </li>
                        @endif

                        {{-- @if ($deleteBillPermission =='all' || $deleteBillPermission =='added' && user()->id == $purchaseBill->added_by)
                            <li>
                                <a class="dropdown-item f-14 text-dark delete-bill" href="javascript:;"
                                    data-bill-id="{{ $purchaseBill->id }}">
                                    <i class="fa fa-trash f-w-500 mr-2 f-11"></i> @lang('app.delete')
                                </a>
                            </li>
                        @endif --}}

                        <li>
                            <a class="dropdown-item f-14 text-dark"
                                href="{{ route('bills.download', [$purchaseBill->id]) }}">
                                <i class="fa fa-download f-w-500 mr-2 f-11"></i> @lang('app.download')
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item" target="_blank" href="{{ route('bills.download', [$purchaseBill->id, 'view' => true]) }}">
                                <i class="fa fa-eye mr-2"></i>
                                @lang('app.viewPdf')
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item sendButton" href="javascript:;" data-toggle="tooltip"  data-bill-id="{{ $purchaseBill->id }}">
                                <i class="fa fa-paper-plane mr-2"></i>
                                @lang('app.send')
                            </a>
                        </li>
                        @if ($purchaseBill->status != 'paid' && $purchaseBill->status != 'draft')
                            <li>
                                <a class="dropdown-item sendButton" href="{{route('vendor-payments.create')}}?bill={{$purchaseBill->id}}" data-toggle="tooltip"  data-bill-id="{{ $purchaseBill->id }}">
                                    <i class="fa fa-plus mr-2"></i>
                                    @lang('purchase::app.menu.addPayment')
                                </a>
                            </li>
                        @endif
            </div>


        </div>

    <!-- CARD FOOTER END -->

</div>
<!-- BILL CARD END -->


<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script src="{{ asset('vendor/jquery/clipboard.min.js') }}"></script>

<script>


    $('body').on('click', '.delete-bill', function() {
            var id = $(this).data('bill-id');
            Swal.fire({
                title: "@lang('messages.sweetAlertTitle')",
                text: "@lang('messages.recoverRecord')",
                icon: 'warning',
                showCancelButton: true,
                focusConfirm: false,
                confirmButtonText: "@lang('messages.confirmDelete')",
                cancelButtonText: "@lang('app.cancel')",
                customClass: {
                    confirmButton: 'btn btn-primary mr-3',
                    cancelButton: 'btn btn-secondary'
                },
                showClass: {
                    popup: 'swal2-noanimation',
                    backdrop: 'swal2-noanimation'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    var url = "{{ route('bills.destroy', ':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        blockUI: true,
                        data: {
                            '_token': token,
                            '_method': 'DELETE'
                        },
                        success: function(response) {
                            if (response.status == "success") {
                                window.location.href="{{route('bills.index')}}";
                            }
                        }
                    });
                }
            });
        });

        $('body').on('click', '.sendButton', function() {
            var id = $(this).data('bill-id');
            var url = "{{ route('bills.send_invoice', ':id') }}";
            url = url.replace(':id', id);

            var token = "{{ csrf_token() }}";

            $.easyAjax({
                type: 'POST',
                url: url,
                container: '#purchasebills-table',
                blockUI: true,
                data: {
                    '_token': token,
                },
                success: function(response) {
                    if (response.status == "success") {
                        showTable();
                    }
                }
            });
        });
</script>
