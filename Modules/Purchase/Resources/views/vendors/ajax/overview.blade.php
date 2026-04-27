<script src="{{ asset('vendor/jquery/frappe-charts.min.iife.js') }}"></script>
@php
    $editPermission = user()->permission('edit_vendor');
    $deletePermission = user()->permission('delete_vendor');
@endphp

<div id="task-detail-section">
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 mb-4 mb-xl-0 mb-lg-4 mb-md-0">
            <div class="card bg-white border-0 b-shadow-4">
                <div class="card-header bg-white  border-bottom-grey  justify-content-between p-20">
                    <div class="row">
                        <div class="col-lg-9 col-10">
                            <h1 class="heading-h1">
                                {{ $vendor->primary_name }}</h1>
                        </div>

                        <div class="col-lg-3 col-md-2 col-2 text-right">
                            @if ($editPermission == 'all'
                                || ($editPermission == 'added' && $vendor->added_by == user()->id) ||
                                ($deletePermission == 'all'
                                || ($deletePermission == 'added' && $vendor->added_by == user()->id)))
                                <div class="dropdown">
                                    <button
                                        class="btn btn-lg f-14 px-2 py-1 text-dark-grey  rounded  dropdown-toggle"
                                        type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-ellipsis-h"></i>
                                    </button>

                                    <div class="dropdown-menu dropdown-menu-right border-grey rounded b-shadow-4 p-0"
                                         aria-labelledby="dropdownMenuLink" tabindex="0">
                                        @if ($editPermission == 'all'
                                            || ($editPermission == 'added' && $vendor->added_by == user()->id))
                                            <a class="dropdown-item openRightModal"
                                               href="{{ route('vendors.edit', $vendor->id) }}">@lang('app.edit')</a>
                                        @endif
                                        @if ($deletePermission == 'all'
                                            || ($deletePermission == 'added' && $vendor->added_by == user()->id))
                                            <a class="dropdown-item delete-table-row">@lang('app.delete')</a>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 col-lg-3 mt-3">
            <x-cards.widget :title="__('purchase::modules.vendorPayment.outstandingPayable')"
                :value="($totalBillAmount) - ($totalPaymentAmount)"
                icon="coins" />
        </div>
        {{-- to be updated according to the vendor credit --}}
        <div class="col-sm-12 col-lg-3 mt-3">
            <x-cards.widget :title="__('purchase::modules.vendorPayment.unusedCredit')"
                :value="($unusedAmt)"
                icon="coins" />
        </div>
        <div class="col-sm-12 col-lg-6 mt-3">
            <x-cards.data :title="__('purchase::modules.vendorPayment.paymentToVendor').' <i class=\'fa fa-question-circle\' data-toggle=\'popover\' data-placement=\'top\' data-content=\''.__('app.from').' '.$startDate->translatedFormat(company()->date_format).' '.__('app.to').' '.$endDate->translatedFormat(company()->date_format).'\\' data-trigger=\'hover\'></i>'">
                <x-bar-chart id="task-chart1" :chartData="$earningChartData" height="200"></x-bar-chart>
            </x-cards.data>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-xl-12 col-lg-12 col-md-12 mb-4 mb-xl-0 mb-lg-4 mb-md-0">
            <div class="card bg-white border-0 b-shadow-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-9 col-lg-8 col-md-12 mb-4 mb-xl-0 mb-lg-4 mb-md-0">
                            <x-cards.data-row :label="__('purchase::modules.vendor.companyName')"
                            :value="$vendor->company_name ?? '--'" />

                            <x-cards.data-row :label="__('app.email')" :value="$vendor->email ?? '--'" />

                            <x-cards.data-row :label="__('app.category')" :value="$vendor->category->category_name ?? '--'" />

                            <x-cards.data-row :label="__('app.phone')" :value="$vendor->phone ?? '--'" />

                            <x-cards.data-row :label="__('modules.client.website')" :value="$vendor->website ?? '--'" />

                            <x-cards.data-row :label="__('purchase::modules.vendor.openingsBalance')" :value="currency_format($vendor->opening_balance,  $currency->id, $currency->currency_code)" />

                            <x-cards.data-row :label="__('modules.invoices.currency')" :value="$currency->currency_name . '('.$currency->currency_code . ')' " />

                            <x-cards.data-row :label="__('modules.invoices.shippingAddress')" :value="$vendor->shipping_address ?? '--'" />

                            <x-cards.data-row :label="__('modules.invoices.billingAddress')" :value="$vendor->billing_address ?? '--'" />

                            <div class="col-12 px-0 pb-3 d-block d-lg-flex d-md-flex">
                                <p class="mb-0 text-lightest f-14 w-30 d-inline-block ">
                                    @lang('app.addedBy')
                                </p>
                                @if ($vendor->user)
                                    <x-employee :user="$vendor->user"/>
                                @else
                                    --
                                @endif
                            </div>
                        </div>
                    </div>
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
                    var url = "{{ route('vendors.destroy', $vendor->id) }}";
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
                                if ($('#vendors-table').length) {
                                    window.LaravelDataTables["vendors-table"].draw(true);
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
