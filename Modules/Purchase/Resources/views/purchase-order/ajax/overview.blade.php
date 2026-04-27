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
@if($order->billed_status != 'billed')
    <div class="d-flex align-content-center flex-lg-row-reverse mt-4">
        <x-forms.link-primary link="{{route('bills.create')}}?order={{$order->id}}" class="mr-3 float-left" icon="money-bill">
            @lang('purchase::modules.purchaseOrder.convertToBill')
        </x-forms.link-primary>
    </div>
@endif
<!-- ORDER CARD START -->
<div class="card border-0 invoice">
    <!-- CARD BODY START -->
    <div class="card-body">
        <div class="invoice-table-wrapper">
            <table width="100%">
                <tr class="inv-logo-heading">
                    <td><img src="{{ invoice_setting()->logo_url }}" alt="{{ mb_ucwords(company()->company_name) }}"
                            id="logo" /></td>
                    <td align="right" class="font-weight-bold f-21 text-dark text-uppercase mt-4 mt-lg-0 mt-md-0">
                        @lang('purchase::app.menu.purchaseOrder')</td>
                </tr>
                <tr class="inv-num">
                    <td class="f-14 text-dark">
                        <p class="mt-3 mb-0">
                            {{ mb_ucwords(company()->company_name) }}<br>
                            @if (!is_null($settings) && $order->address)
                                {!! nl2br($order->address->address) !!}<br>
                            @endif
                            {{ company()->company_phone }}
                            @if ($invoiceSetting->show_gst == 'yes' && $order->address)
                                <br>{{ strtoupper($order->address->tax_name) }}: {{ $order->address->tax_number }}
                            @endif
                        </p>
                        <br>
                    </td>
                    <td align="right">
                        <table class="inv-num-date text-dark f-13 mt-3">
                            <tr>
                                <td class="bg-light-grey border-right-0 f-w-500">
                                    @lang('app.orderNumber')</td>
                                <td class="border-left-0">{{ $order->purchase_order_number }}</td>
                            </tr>
                            <tr>
                                <td class="bg-light-grey border-right-0 f-w-500">
                                    @lang('modules.orders.orderDate')</td>
                                <td class="border-left-0">{{ $order->purchase_date->translatedFormat(company()->date_format) }}
                                </td>
                            </tr>
                            <tr>
                                <td class="bg-light-grey border-right-0 f-w-500">
                                    @lang('app.status')</td>
                                <td class="border-left-0">{{ $order->purchase_status }}
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
                            @if ($order->vendor && $order->vendor->primary_name)
                                {{$order->vendor->primary_name}}<br>
                            @endif
                            @if ($order->vendor && $order->vendor->email)
                                {{$order->vendor->email}}<br>
                            @endif
                            @if ($order->vendor && $order->vendor->phone)
                                {{$order->vendor->phone}}<br>
                            @endif
                            @if ($order->vendor && $order->vendor->company_name)
                                {{mb_ucwords($order->vendor->company_name)}}<br>
                            @endif
                            @if ($order->vendor && $order->vendor->billing_address)
                                {{$order->vendor->billing_address}}<br>
                            @endif
                        </p>
                    </td>
                    <td align="right" class="mt-2 mt-lg-0 mt-md-0">
                        <span class="unpaid {{ $order->billed_status == 'billed' ? 'text-success border-success' : '' }} {{ $order->billed_status == 'unbilled' ? 'text-danger border-danger' : '' }} rounded f-15 ">@lang('purchase::modules.purchaseOrder.' . $order->billed_status)</span>
                    </td>
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
                                @if ($invoiceSetting->hsn_sac_code_show)
                                    <td class="border-right-0 border-left-0" align="right">@lang('app.hsnSac')</td>
                                @endif
                                <td class="border-right-0 border-left-0" align="right">
                                    @lang('modules.invoices.qty')
                                </td>
                                <td class="border-right-0 border-left-0" align="right">
                                    @lang('modules.invoices.unitPrice') ({{ $order->currency->currency_code }})
                                </td>
                                <td class="border-right-0 border-left-0" align="right">@lang('modules.invoices.tax')</td>
                                <td class="border-left-0" align="right"
                                    width="{{ $invoiceSetting->hsn_sac_code_show ? '17%' : '20%' }}">
                                    @lang('modules.invoices.amount')
                                    ({{ $order->currency->currency_code }})</td>
                            </tr>
                            @foreach ($order->items as $item)
                                @if ($item->type == 'item')
                                    <tr class="text-dark font-weight-semibold f-13">
                                        <td>{{ ($item->item_name) }}</td>
                                        @if ($invoiceSetting->hsn_sac_code_show)
                                            <td align="right">{{ $item->hsn_sac_code }}</td>
                                        @endif
                                        <td align="right">{{ $item->quantity }} @if($item->unit)<br><span class="f-11 text-dark-grey">{{ $item->unit->unit_type }}</span>@endif</td>
                                        <td align="right">
                                            {{ currency_format($item->unit_price, $order->currency_id, false) }}</td>
                                        <td align="right">{{ strtoupper($item->tax_list) }}</td>
                                        <td align="right">
                                            {{ currency_format($item->amount, $order->currency_id, false) }}
                                        </td>
                                    </tr>
                                    @if ($item->item_summary || $item->purchaseItemImage)
                                        <tr class="text-dark f-12">
                                            <td colspan="{{ $invoiceSetting->hsn_sac_code_show ? '6' : '5' }}"
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

                            <tr>
                                <td colspan="{{ $invoiceSetting->hsn_sac_code_show ? '4' : '3' }}"
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
                                        <tr class=" text-dark-grey font-weight-bold" align="right">
                                            <td class="w-50 border-bottom-0 border-left-0">
                                                @lang('modules.invoices.total')</td>
                                        </tr>
                                    </table>
                                </td>
                                <td class="p-0 border-left-0" align="right">
                                    <table width="100%">
                                        <tr class="text-dark-grey" align="right">
                                            <td class="border-top-0 border-right-0">
                                                {{ currency_format($order->sub_total, $order->currency_id, false) }}
                                            </td>
                                        </tr>
                                        @if ($discount != 0 && $discount != '')
                                            <tr class="text-dark-grey" align="right">
                                                <td class="border-top-0 border-right-0">
                                                    {{ currency_format($discount, $order->currency_id, false) }}</td>
                                            </tr>
                                        @endif
                                        @foreach ($taxes as $key => $tax)
                                            <tr class="text-dark-grey" align="right">
                                                <td class="border-top-0 border-right-0">
                                                    {{ currency_format($tax, $order->currency_id, false) }}</td>
                                            </tr>
                                        @endforeach
                                        <tr class=" text-dark-grey font-weight-bold" align="right">
                                            <td class="border-bottom-0 border-right-0">
                                                {{ currency_format($order->total, $order->currency_id, false) }}
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>

                </tr>
            </table>
            <table width="100%" class="inv-desc-mob d-block d-lg-none d-md-none">

                @foreach ($order->items as $item)
                    @if ($item->type == 'item')
                        <tr>
                            <th width="50%" class="bg-light-grey text-dark-grey font-weight-bold">
                                @lang('app.description')</th>
                            <td class="p-0 ">
                                <table>
                                    <tr width="100%" class="font-weight-semibold f-13">
                                        <td class="border-left-0 border-right-0 border-top-0">
                                            {{ ($item->item_name) }}</td>
                                    </tr>
                                    @if ($item->item_summary != '' || $item->purchaseItemImage)
                                        <tr>
                                            <td class="border-left-0 border-right-0 border-bottom-0 f-12">
                                                {!! nl2br(strip_tags($item->item_summary, ['p', 'b', 'strong', 'a'])) !!}
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
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <th width="50%" class="bg-light-grey text-dark-grey font-weight-bold">
                                @lang('modules.invoices.qty')</th>
                            <td width="50%">{{ $item->quantity }}</td>
                        </tr>
                        <tr>
                            <th width="50%" class="bg-light-grey text-dark-grey font-weight-bold">
                                @lang('modules.invoices.unitPrice')
                                ({{ $order->currency->currency_code }})</th>
                            <td width="50%">{{ currency_format($item->unit_price, $order->currency_id, false) }}
                            </td>
                        </tr>
                        <tr>
                            <th width="50%" class="bg-light-grey text-dark-grey font-weight-bold">
                                @lang('modules.invoices.amount')
                                ({{ $order->currency->currency_code }})</th>
                            <td width="50%">{{ currency_format($item->amount, $order->currency_id, false) }}</td>
                        </tr>
                        <tr>
                            <td height="3" class="p-0 " colspan="2"></td>
                        </tr>
                    @endif
                @endforeach

                <tr>
                    <th width="50%" class="text-dark-grey font-weight-normal">@lang('modules.invoices.subTotal')
                    </th>
                    <td width="50%" class="text-dark-grey font-weight-normal">
                        {{ currency_format($order->sub_total, $order->currency_id, false) }}</td>
                </tr>
                @if ($discount != 0 && $discount != '')
                    <tr>
                        <th width="50%" class="text-dark-grey font-weight-normal">@lang('modules.invoices.discount')
                        </th>
                        <td width="50%" class="text-dark-grey font-weight-normal">
                            {{ currency_format($discount, $order->currency_id, false) }}</td>
                    </tr>
                @endif

                @foreach ($taxes as $key => $tax)
                    <tr>
                        <th width="50%" class="text-dark-grey font-weight-normal">{{ mb_strtoupper($key) }}</th>
                        <td width="50%" class="text-dark-grey font-weight-normal">
                            {{ currency_format($tax, $order->currency_id, false) }}</td>
                    </tr>
                @endforeach
                <tr>
                    <th width="50%" class="text-dark-grey font-weight-bold">@lang('modules.invoices.total')</th>
                    <td width="50%" class="text-dark-grey font-weight-bold">
                        {{ currency_format($order->total, $order->currency_id, false) }}</td>
                </tr>
            </table>
            <table class="inv-note">
                <tr>
                    <td height="30" colspan="2"></td>
                </tr>
                <tr>
                    <td>@lang('app.note')</td>
                    <td style="text-align: right;">@lang('modules.invoiceSettings.invoiceTerms')</td>
                </tr>
                <tr>
                    <td style="vertical-align: text-top">
                        <p class="text-dark-grey">{!! !empty($order->note) ? $order->note : '--' !!}</p>
                    </td>
                    <td style="text-align: right;">
                        <p class="text-dark-grey">{!! nl2br($purchaseSetting->purchase_terms) !!}</p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table>
                            <tr>
                                @if (isset($taxes) && invoice_setting()->tax_calculation_msg == 1)
                                    <p class="text-dark-grey">
                                        @if ($order->calculate_tax == 'after_discount')
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
            </table>
        </div>
    </div>
    <!-- CARD BODY END -->
<!-- INVOICE CARD END -->
 <!-- CARD FOOTER START -->
 <div class="card-footer bg-white border-0 d-flex justify-content-start py-0 py-lg-4 py-md-4 mb-4 mb-lg-3 mb-md-3">
    <div class="d-flex">
        <div class="inv-action mr-3 mr-lg-3 mr-md-3 dropup">
            <button class="dropdown-toggle btn-primary" type="button" id="dropdownMenuButton"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">@lang('app.action')
                <span><i class="fa fa-chevron-up f-15"></i></span>
            </button>
            <!-- DROPDOWN - INFORMATION -->
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton" tabindex="0">

                @if($order->delivery_status!='delivered')
                    <li>
                        <a class="dropdown-item f-14 text-dark"
                            href="{{ route('purchase-order.edit', [$order->id]) }}">
                            <i class="fa fa-edit f-w-500 mr-2 f-11"></i> @lang('app.edit')
                        </a>
                    </li>
                @endif
                @if($order->delivery_status!='delivered')
                    <li>
                        <a class="dropdown-item f-14 text-dark delete-order" href="javascript:;"
                            data-order-id="{{ $order->id }}">
                            <i class="fa fa-trash f-w-500 mr-2 f-11"></i> @lang('app.delete')
                        </a>
                    </li>
                @endif
                <li>
                    <a class="dropdown-item f-14 text-dark"
                        href="{{ route('purchase_order.download', [$order->id]) }}">
                        <i class="fa fa-download f-w-500 mr-2 f-11"></i> @lang('app.download')
                    </a>
                </li>

                @if ($order->purchase_status != 'canceled')
                    <li>
                        <a class="dropdown-item f-14 text-dark sendButton" href="javascript:;"
                            data-invoice-id="{{ $order->id }}"  data-type="send">
                            <i class="fa fa-paper-plane f-w-500 mr-2 f-11"></i> @lang('app.send')
                        </a>
                    </li>
                    @if ($order->send_status == 0)
                        <li>
                            <a class="dropdown-item f-14 text-dark sendButton" href="javascript:;" data-toggle="tooltip" data-original-title="@lang('messages.markSentInfo')"
                                data-invoice-id="{{ $order->id }}" data-type="mark_as_send">
                                <i class="fa fa-paper-plane f-w-500 mr-2 f-11"></i> @lang('app.markSent')
                            </a>
                        </li>
                    @endif
                @endif
            </ul>
        </div>

        <x-forms.button-cancel :link="route('purchase-order.index')" class="border-0 mr-3">@lang('app.cancel')
        </x-forms.button-cancel>
    </div>
</div>


</div>
<!-- CARD FOOTER END -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script src="{{ asset('vendor/jquery/clipboard.min.js') }}"></script>

<script>
    var clipboard = new ClipboardJS('.btn-copy');

    clipboard.on('success', function(e) {
        Swal.fire({
            icon: 'success',
            text: '@lang('app.copied')',
            toast: true,
            position: 'top-end',
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: false,
            customClass: {
                confirmButton: 'btn btn-primary',
            },
            showClass: {
                popup: 'swal2-noanimation',
                backdrop: 'swal2-noanimation'
            },
        })
    });

    $('body').on('click', '.sendButton', function() {
        var id = $(this).data('invoice-id');
        var token = "{{ csrf_token() }}";
        var type = $(this).data('type');

        var url = "{{ route('purchase_order.send_order', ':id') }}";
        url = url.replace(':id', id);

        $.easyAjax({
            type: 'POST',
            url: url,
            container: '.content-wrapper',
            blockUI: true,
            data: {
                '_token': token,
                'data_type': type,
                'type': 'send'
            },
            success: function(response) {
                if (response.status == "success") {
                    window.location.reload();
                }
            }
        });
    });

    $('body').on('click', '.reminderButton', function() {
        var id = $(this).data('invoice-id');
        var token = "{{ csrf_token() }}";

        var url = "{{ route('invoices.payment_reminder', ':id') }}";
        url = url.replace(':id', id);

        $.easyAjax({
            type: 'GET',
            container: '#invoices-table',
            blockUI: true,
            url: url,
            success: function(response) {
                if (response.status == "success") {
                    $.unblockUI();
                }
            }
        });
    });

    $('body').on('click', '.cancel-invoice', function() {
        var id = $(this).data('invoice-id');
        Swal.fire({
            title: "@lang('messages.sweetAlertTitle')",
            text: "@lang('messages.invoiceText')",
            icon: 'warning',
            showCancelButton: true,
            focusConfirm: false,
            confirmButtonText: "@lang('app.yes')",
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
                var token = "{{ csrf_token() }}";

                var url = "{{ route('invoices.update_status', ':id') }}";
                url = url.replace(':id', id);

                $.easyAjax({
                    type: 'GET',
                    url: url,
                    container: '#invoices-table',
                    blockUI: true,
                    success: function(response) {
                        if (response.status == "success") {
                            window.location.reload();
                        }
                    }
                });
            }
        });
    });

    $('body').on('click', '.delete-order', function() {
        var id = $(this).data('order-id');
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
                var token = "{{ csrf_token() }}";

                var url = "{{ route('purchase-order.destroy', ':id') }}";
                url = url.replace(':id', id);

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
                            window.location.href = "{{ route('purchase-order.index') }}";
                        }
                    }
                });
            }
        });
    });

    $('body').on('click', '.toggle-shipping-address', function() {
        let invoiceId = $(this).data('invoice-id');

        let url = "{{ route('invoices.toggle_shipping_address', ':id') }}";
        url = url.replace(':id', invoiceId);

        $.easyAjax({
            url: url,
            type: 'GET',
            container: '#invoices-table',
            blockUI: true,
            success: function(response) {
                if (response.status === 'success') {
                    window.location.reload();
                }
            }
        });
    });

    $('body').on('click', '.add-shipping-address', function() {
        let invoiceId = $(this).data('invoice-id');

        var url = "{{ route('invoices.shipping_address_modal', [':id']) }}";
        url = url.replace(':id', invoiceId);

        $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
        $.ajaxModal(MODAL_LG, url);
    });

    $('body').on('click', '.invoice-upload', function() {
        var invoiceId = $(this).data('invoice-id');
        const url = "{{ route('invoices.file_upload') }}?invoice_id=" + invoiceId;
        $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
        $.ajaxModal(MODAL_LG, url);
    });

    $('body').on('click', '.delete-file', function() {
        let id = $(this).data('row-id');
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
                var url = "{{ route('invoice-files.destroy', ':id') }}";
                url = url.replace(':id', id);

                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                    url: url,
                    data: {
                        '_token': token,
                        '_method': 'DELETE'
                    },
                    success: function(response) {
                        if (response.status == "success") {
                            $('#invoice-file-list').html(response.view);
                        }
                    }
                });
            }
        });
    });
</script>
