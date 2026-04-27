@php
    $editPermission = user()->permission('edit_vendor_payment');
    $deletePermission = user()->permission('delete_vendor_payment');
@endphp

<div id="task-detail-section">
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 mb-4 mb-xl-0 mb-lg-4 mb-md-0">
            <div class="card bg-white border-0 b-shadow-4">
                <div class="card-header bg-white border-bottom-grey  justify-content-between p-20">
                    <div class="row">
                        <div class="col-lg-9 col-10">
                            <h1 class="heading-h1">
                                {{$vendorPayment->vendor->primary_name }}</h1>
                        </div>

                        <div class="col-lg-3 col-md-2 col-2 text-right">
                            @if ($editPermission == 'all'
                                || ($editPermission == 'added' && $vendorPayment->added_by == user()->id) ||
                                ($deletePermission == 'all'
                                || ($deletePermission == 'added' && $vendorPayment->added_by == user()->id)))
                                <div class="dropdown">
                                    <button
                                        class="btn btn-lg f-14 px-2 py-1 text-dark-grey  rounded  dropdown-toggle"
                                        type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-ellipsis-h"></i>
                                    </button>

                                    <div class="dropdown-menu dropdown-menu-right border-grey rounded b-shadow-4 p-0"
                                         aria-labelledby="dropdownMenuLink" tabindex="0">
                                        @if ($editPermission == 'all'
                                            || ($editPermission == 'added' && $vendorPayment->added_by == user()->id))
                                            <a class="dropdown-item openRightModal"
                                               href="{{ route('vendor-payments.edit',$vendorPayment->id) }}"><i class="fa fa-edit mr-2"></i>@lang('app.edit')</a>
                                        @endif
                                        @if (($deletePermission == 'all'
                                            || ($deletePermission == 'added' && $vendorPayment->added_by == user()->id)) && $vendorPayment->status != 'complete')
                                            <a class="dropdown-item delete-table-row"><i class="fa fa-trash mr-2"></i>@lang('app.delete')</a>
                                        @endif
                                        <a class="dropdown-item"
                                               href="{{ route('vendor-payments.download',$vendorPayment->id) }}"><i class="fa fa-download mr-2"></i>@lang('app.download')</a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-9 col-lg-8 col-md-12 mb-4 mb-xl-0 mb-lg-4 mb-md-0">
                            <x-cards.data-row :label="__('purchase::modules.vendorPayment.paymentDate')"
                            :value="$vendorPayment->payment_date->translatedFormat(company()->date_format) ?? '--'" />

                            @php
                                $bankName = isset($vendorPayment->bank_account_id) && $vendorPayment->bankAccount->bank_name ? $vendorPayment->bankAccount->bank_name.' |' : '';

                                $currencyId = (isset($vendorPayment->vendor->currency)) ? $vendorPayment->vendor->currency->id : '';

                            @endphp
                            <x-cards.data-row :label="__('app.menu.bankaccount')"
                            :value="($vendorPayment->bank_account_id ? $bankName.' '.mb_ucwords($vendorPayment->bankAccount->account_name) : '--')" />

                            <x-cards.data-row :label="__('purchase::modules.vendorPayment.paymentMade')"
                            :value="currency_format($vendorPayment->received_payment, $currencyId) ?? '--'" />

                            <x-cards.data-row :label="__('purchase::modules.vendorPayment.internalNote')"
                            :value="$vendorPayment->internal_note ?? '--'" />

                            <div class="col-12 px-0 pb-3 d-block d-lg-flex d-md-flex">
                                <p class="mb-0 text-lightest f-14 w-30 d-inline-block ">
                                    @lang('app.addedBy')
                                </p>

                                <x-employee :user="$vendorPayment->user"/>
                            </div>
                        </div>
                    </div>
                </div>
                <h4 class="mb-0 p-20 f-21 font-weight-normal  border-bottom-grey">
                    @lang('purchase::modules.vendor.billDetails')</h4>
                <div class="table-responsive p-20">
                    <x-table class="table-bordered">
                        <x-slot name="thead">
                            <th>@lang('app.date')</th>
                            <th>@lang('app.bill')</th>
                            <th>@lang('purchase::app.menu.purchaseOrder')</th>
                            <th>@lang('purchase::modules.vendorPayment.billAmount')</th>
                            <th>@lang('purchase::modules.vendorPayment.amountDue')</th>
                            <th class="text-right">@lang('app.payment')</th>
                        </x-slot>

                        @forelse ($bills as $bill)
                            <tr class="row{{ $bill->bill->id }}">
                                <td>
                                    {{ $bill->bill->bill_date->translatedFormat(company()->date_format) }}
                                </td>
                                <td>
                                    {{ $bill->bill->bill_number }}
                                </td>
                                <td>
                                    {{ $bill->bill->order->purchase_order_number }}
                                </td>
                                <td>
                                    {{ currency_format($bill->bill->total, $currencyId) }}
                                </td>
                                @php
                                    $due = ($bill->bill->total) - ($billArray[$bill->id]);
                                @endphp
                                <td>
                                    <span>{{currency_format(($due),$currencyId)}}</span>
                                </td>
                                <td class="text-right col-md-2">
                                    <span>{{currency_format($bill->total_paid, $currencyId)}}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <x-cards.no-record icon="user" :message="__('purchase::modules.vendorPayment.noBills')"/>
                                </td>
                            </tr>
                        @endforelse
                    </x-table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {

        $('body').on('click', '.delete-table-row', function () {
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
                    var url = "{{ route('vendor-payments.destroy',$vendorPayment->id) }}";
                    var token = "{{ csrf_token() }}";
                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        blockUI: true,
                        data: {
                            '_token': token,
                            '_method': 'DELETE'
                        },
                        success: function (response) {
                            if ($(RIGHT_MODAL).hasClass('in')) {
                                document.getElementById('close-task-detail').click();
                                if ($('#vendor-payments-table').length) {
                                    window.LaravelDataTables["vendor-payments-table"].draw(true);
                                } else {
                                    window.location.href = response.redirectUrl;
                                }
                            } else {
                                window.location.href = response.redirectUrl;
                            }
                        }
                    });
                }
            });
        });

        init(RIGHT_MODAL);

    });
</script>
