<style>
    .information-box {
        border-style: dotted;
        border-color: orange;
        margin-bottom: 30px;
        margin-top:10px;
        padding-top: 10px;
        border-radius: 4px;
    }
</style>
<div class="row">
    <div class="col-sm-12">
        <x-form id="update-vendor-payment-data-form" method="PUT">
            <div class="add-client bg-white rounded">
                <h4 class="mb-0 p-20 f-21 font-weight-normal  border-bottom-grey">
                    @lang('purchase::modules.vendor.accountDetails')</h4>
                <div class="row p-20">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group c-inv-select mb-lg-0 mb-md-0 mb-4">
                                    <x-forms.label class="mt-3" fieldId="vendor_id" :fieldLabel="__('purchase::app.menu.vendor')" fieldRequired="true">
                                    </x-forms.label>

                                    <div class="select-others height-35 rounded">
                                        <input type="hidden" name="purchase_vendor_id" value="{{$payment->purchase_vendor_id}}"/>
                                        <select class="form-control select-picker" name="vendor_id" id="vendor_id" disabled>
                                            @foreach ($vendors as $vendor)
                                                <option @if ($payment->purchase_vendor_id == $vendor->id) selected @endif value="@if($payment->purchase_vendor_id)" {{$payment->purchase_vendor_id}}  @else {{$vendor->vendor_id}} @endif>{{ $vendor->primary_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div  id="all-bills">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-lg-0 mb-md-0 mb-4 mt-3">
                                        <x-forms.label class="mb-12" fieldId="payment_made"
                                            :fieldLabel="__('purchase::modules.vendorPayment.paymentMade')" fieldRequired="true">
                                        </x-forms.label>
                                        <x-forms.input-group>
                                            <x-slot name="prepend">
                                                <span
                                                    class="input-group-text">{{ $vendor->currency->currency_code }}</span>
                                            </x-slot>
                                            <input type="number" name="payment_made" id="payment_made" onkeyup="updateAmountPaid(this.value)" class="form-control height-35 f-15"
                                                value="{{ $payment->received_payment }}" readonly>
                                        </x-forms.input-group>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <x-forms.datepicker fieldId="payment_date"
                                                        :fieldLabel="__('purchase::modules.vendorPayment.paymentDate')"
                                                        fieldName="payment_date"
                                                        :fieldValue="($payment->payment_date ? $payment->payment_date->timezone(company()->timezone)->format(company()->date_format) : '')"
                                                        :fieldPlaceholder="__('placeholders.date')"/>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group c-inv-select mt-3">
                                        <x-forms.label fieldId="bank_account_id" :fieldLabel="__('app.bankaccount')">
                                        </x-forms.label>
                                        <div class="select-others height-35 rounded">
                                            <select class="form-control select-picker" data-live-search="true" data-size="8"
                                                    name="bank_account_id" id="bank_account_id">
                                                <option value="">--</option>
                                                @if($viewBankAccountPermission != 'none')
                                                    @foreach ($bankDetails as $bankDetail)
                                                        <option value="{{ $bankDetail->id }}" @if($bankDetail->id == $payment->bank_account_id) selected @endif>@if($bankDetail->type == 'bank')
                                                            {{ $bankDetail->bank_name }} | @endif
                                                            {{ mb_ucwords($bankDetail->account_name) }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
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
                                                {{ $bill->bill->bill_date ? $bill->bill->bill_date->timezone(company()->timezone)->format(company()->date_format) : '--' }}
                                            </td>
                                            <td>
                                                {{ $bill->bill->id }}
                                                <input type="hidden" name="bill_id[]" value="{{ $bill->bill->id }}">
                                            </td>
                                            <td>
                                                {{ $bill->bill->order->purchase_order_number }}
                                            </td>
                                            <td>
                                                {{ currency_format($bill->bill->total, ($payment->vendor->currency ? $payment->vendor->currency->id : company()->currency->id )) }}
                                            </td>
                                            @php
                                                $due = ($bill->bill->total) - ($billArray[$bill->id]);
                                            @endphp
                                            <td>
                                                <input type="hidden" id="due{{ $bill->id }}" name="due[]" value="">
                                                <span id="due_balance{{ $bill->bill->id }}">{{ currency_format($due, ($payment->vendor->currency ? $payment->vendor->currency->id : company()->currency->id )) }}</span>
                                            </td>
                                            <td class="text-right col-md-2">
                                                <input type="number" class="form-control height-35 f-14" onkeyup="changeDue(this.value, {{ $bill->bill->id }})"
                                                value="{{$bill->total_paid}}" data-bill-total="{{ $bill->bill->total }}" id="payment{{ $bill->bill->id }}" readonly>

                                                <input type="hidden" id="amount_paid_per{{ $bill->bill->id }}" name="amount_paid_per[]" value="{{$bill->total_paid}}">

                                                <a class="f-12" data-total-val="{{ $bill->bill->total }}" value="{{ $bill->total_paid }}" id="pay-in-full{{ $bill->bill->id }}" onclick="payFull({{ $bill->bill->id }})">@lang('purchase::modules.vendorPayment.payInFull')</a>
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
                            <div class="d-flex px-lg-4 px-md-4 px-3 pb-3 c-inv-total">
                                <table width="100%" class="text-right f-14 ">
                                    <tbody>
                                        <tr>
                                            <td width="60%" class="border-0 d-lg-table d-md-table d-none"></td>
                                            <td width="40%" class="p-0 border-0 c-inv-total-right">
                                                <table width="100%" class="information-box">
                                                    <tbody>
                                                        @php
                                                            $currencyId = (isset($payment->vendor->currency)) ? $payment->vendor->currency->id : '';
                                                        @endphp
                                                        <tr>
                                                            <td colspan="2" class="border-top-0 border-bottom-0 text-warning">
                                                                @lang('modules.invoices.amountPaid')</td>
                                                            <td width="30%" class="border-top-0 border-bottom-0" id="amount_paid">{{ currency_format($payment->received_payment,$currencyId) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2" class="border-top-0 border-bottom-0 text-warning">
                                                                @lang('purchase::modules.vendorPayment.amountUsed')</td>
                                                            <td width="30%" class="border-top-0 border-bottom-0" id="amount_used">{{ currency_format($amountUsedSum,$currencyId) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2" class="border-top-0 border-bottom-0 text-warning">
                                                                @lang('purchase::modules.vendorPayment.amountRefunded')</td>
                                                            <td width="30%" class="border-top-0 border-bottom-0">0.00</td>
                                                        </tr>
                                                        <tr>
                                                            <input type="hidden" id="excess" name="excess" value="{{ ($payment->received_payment ?? 0) - ($amountUsedSum ?? 0) }}">

                                                            <td colspan="2" class="border-top-0 border-bottom-0 text-warning">
                                                                @lang('purchase::modules.vendorPayment.amountInExcess')</td>
                                                            <td width="30%" class="border-top-0 border-bottom-0" id="amount_excess">{{ currency_format(($payment->received_payment) - ($amountUsedSum),$currencyId) }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <x-forms.textarea class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('purchase::modules.vendorPayment.internalNote')" :fieldValue="$payment->internal_note"
                                            fieldName="internal_note" fieldId="internal_note" fieldPlaceholder="">
                                        </x-forms.textarea>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="f-14 text-dark-grey mb-12 w-100" for="notify_vendor"></label>
                                        <div class="d-flex">
                                            <x-forms.checkbox fieldId="notify_vendor" :fieldLabel="__('purchase::modules.vendorPayment.sendNotification')" fieldValue="1" fieldName="notify_vendor" :checked="$payment->notify_vendor == '1'"></x-forms.checkbox>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <x-form-actions>
                    <x-forms.button-primary id="update-vendor-payment" class="mr-3" icon="check">@lang('app.save')
                    </x-forms.button-primary>
                    <x-forms.button-cancel :link="route('vendor-payments.index')" class="border-0">@lang('app.cancel')
                    </x-forms.button-cancel>
                </x-form-actions>
            </div>
        </x-form>
    </div>
</div>

<script>
    function changeDue(vals, id) {
        var variablePayment = vals;
        var billTotal = $('#payment'+id).data('bill-total');

        var total = billTotal;
        var dueBalance = (total - variablePayment);
        $('#due_balance'+id).html(dueBalance);
        $('#due'+id).val(dueBalance);
        $('#amount_used').html(variablePayment);

        //text-danger
        if(dueBalance < 0) {
            $("#due_balance"+id).addClass("text-danger");
        }
        else{
            $("#due_balance"+id).removeClass("text-danger");
        }
        if (dueBalance < 0) {
            $('#due'+id).val("1");
        } else {
            $('#due'+id).val("0");
        }

        calculate();
    }

    function payFull(id){
        var billTotal = $('#pay-in-full'+id).data('total-val');
        $('#due_balance'+id).html(0);
        $('#payment'+id).val(billTotal);
        calculate();
    }

    function updateAmountPaid(vals) {
        $('#amount_paid').html(vals);
        calculate();
    }

    function calculate(){
        var allBillsId = {{ $allBills }};
        var billTotal = 0;
        var allTotal = 0;

        $.each(allBillsId, function(index, value) {
            billTotal = parseFloat($('#payment'+value).val());
            allTotal = parseFloat((allTotal + billTotal));
            $('#amount_paid_per'+value).val(billTotal);
        });
        $('#amount_used').html(allTotal);
        var paid = $('#payment_made').val();
        var excess = (paid - allTotal)
        $('#amount_excess').html(excess);
        $('#excess').val(excess);

        //text-danger
        if(excess < 0) {
            $("#amount_excess").addClass("text-danger");
        }
        else{
            $("#amount_excess").removeClass("text-danger");
        }

    }

    $(document).ready(function () {
        datepicker('#payment_date', {
            position: 'bl',
            ...datepickerConfig
        });

        $("#bank_account_id").selectpicker();

        $('#update-vendor-payment').click(function () {

            var payment = jQuery('input[name="payment_made"]').val();
            var excess = jQuery('input[name="excess"]').val();
            const url = "{{ route('vendor-payments.update', $payment->id) }}";

            var data = $('#update-vendor-payment-data-form').serialize();
            if(payment == '' || payment == null || payment == undefined){
                validate(data, url);
                return 0;
            }
            if(excess<=0){
                validate(data, url);
                return 0;
            }
            Swal.fire({
                title: "@lang('messages.sweetAlertTitle')",
                text: "@lang('purchase::modules.vendorPayment.excessPaymentMsg')"+  excess + "@lang('purchase::modules.vendorPayment.excessMsg')",
                icon: 'warning',
                showCancelButton: true,
                focusConfirm: false,
                confirmButtonText: "@lang('purchase::modules.vendorPayment.continueToSave')",
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
                    validate(data, url);
                }
            });
        });

        function validate(data, url){

            $.easyAjax({
                url: url,
                container: '#update-vendor-payment-data-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                buttonSelector: "#update-vendor-payment",
                file: true,
                data: $('#update-vendor-data-form').serialize(),
                success: function (response) {
                    if (response.status == 'success') {
                        if ($(MODAL_XL).hasClass('show')) {
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
                }
            });
        };

        $('#vendor_id').change(function (e) {
            let vendorId = $(this).val();

            var url = "{{ route('vendor-payments-fetch.fetch_bill', ':id') }}";
            url = url.replace(':id', vendorId);

            $.easyAjax({
                url: url,
                type: "GET",
                success: function (response) {
                    if (response.status == 'success') {
                        $('#all-bills').html(response.html);
                    }
                }
            })

        });

        init(RIGHT_MODAL);
    });
</script>
