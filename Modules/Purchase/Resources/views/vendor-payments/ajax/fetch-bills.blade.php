@if (count($bills)>0)
    <div class="row">
        <input type="hidden" id="excess" name="excess" value="">
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
                        value="">
                </x-forms.input-group>
            </div>
        </div>
        <div class="col-md-4">
            <x-forms.datepicker fieldId="payment_date"
                                :fieldLabel="__('purchase::modules.vendorPayment.paymentDate')"
                                fieldName="payment_date"
                                :fieldValue="now($company->timezone)->format($company->date_format)"
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
                                <option value="{{ $bankDetail->id }}">@if($bankDetail->type == 'bank')
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
@endif
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

        <div class="text-right mt-3">
            <a class="f-12" id="clear_btn" onclick="clearAmount()" >@lang('purchase::modules.vendorPayment.clearAppliedAmount')</a>
        </div>

        @forelse ($bills as $bill)
            <tr class="row{{ $bill->id }}">
                <td>
                    {{ $bill->bill_date ? $bill->bill_date->timezone(company()->timezone)->format(company()->date_format) : '--' }}
                </td>
                <td>
                    {{ $bill->bill_number}}
                    <input type="hidden" name="bill_id[]" value="{{ $bill->id }}">
                </td>
                <td>
                    {{ $bill->order->purchase_order_number }}
                </td>
                <td>
                    {{ currency_format($bill->total, ($vendor->currency ? $vendor->currency->id : company()->currency->id )) }}
                </td>
                <td>
                    @php
                        if($bill->status == 'partially_paid'){
                            $due = ($bill->total) - ($billArray[$bill->id]);
                        }
                    @endphp
                    <input type="hidden" id="due{{ $bill->id }}" name="due[]" value="">
                    @if($bill->status == 'partially_paid')
                        <span id="due_balance{{ $bill->id }}">{{ currency_format($due, ($vendor->currency ? $vendor->currency->id : company()->currency->id)) }}</span>
                    @else
                        <span id="due_balance{{ $bill->id }}">{{currency_format($bill->total, ($vendor->currency ? $vendor->currency->id : company()->currency->id ))}}</span>
                    @endif
                </td>
                <td class="text-right col-md-2">
                    @if($bill->status == 'partially_paid')
                        <input type="number" class="form-control height-35 f-14" onkeyup="changeDue(this.value, {{ $bill->id }})" value="0" data-bill-total="{{ $due }}" id="payment{{ $bill->id }}" min="0">
                    @else
                        <input type="number" class="form-control height-35 f-14" onkeyup="changeDue(this.value, {{ $bill->id }})" value="0" data-bill-total="{{ $bill->total }}" id="payment{{ $bill->id }}" min="0">
                    @endif

                    <input type="hidden" id="amount_paid_per{{ $bill->id }}" name="amount_paid_per[]" value="">
                    @if($bill->status == 'partially_paid')
                        <a class="f-12" data-total-val="{{ $due }}" value="{{ $bill->total }}" id="pay-in-full{{ $bill->id }}" onclick="payFull({{ $bill->id }})">@lang('purchase::modules.vendorPayment.payInFull')</a>
                    @else
                        <a class="f-12" data-total-val="{{ $bill->total }}" value="{{ $bill->total }}" id="pay-in-full{{ $bill->id }}" onclick="payFull({{ $bill->id }})">@lang('purchase::modules.vendorPayment.payInFull')</a>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6">
                    <input type="hidden" value="0" name="noBill">
                    <x-cards.no-record icon="user" :message="__('purchase::modules.vendorPayment.noBills')"/>
                </td>
            </tr>
        @endforelse
    </x-table>
</div>
@if (count($bills)>0)
    <div class="d-flex px-lg-4 px-md-4 px-3 pb-3 c-inv-total">
        <table width="100%" class="text-right f-14 ">
            <tbody>
                <tr>
                    <td width="60%" class="border-0 d-lg-table d-md-table d-none"></td>
                    <td width="40%" class="p-0 border-0 c-inv-total-right">
                        <table width="100%" class="information-box">
                            <tbody>
                                <tr>
                                    <td colspan="2" class="border-top-0 border-bottom-0 text-warning">
                                        @lang('modules.invoices.amountPaid')</td>
                                    <td width="30%" class="border-top-0 border-bottom-0" id="amount_paid">0.00</td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="border-top-0 border-bottom-0 text-warning">
                                        @lang('purchase::modules.vendorPayment.amountUsed')</td>
                                    <td width="30%" class="border-top-0 border-bottom-0" id="amount_used">0.00</td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="border-top-0 border-bottom-0 text-warning">
                                        @lang('purchase::modules.vendorPayment.amountRefunded')</td>
                                    <td width="30%" class="border-top-0 border-bottom-0">0.00</td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="border-top-0 border-bottom-0 text-warning">
                                        @lang('purchase::modules.vendorPayment.amountInExcess')</td>
                                    <td width="30%" class="border-top-0 border-bottom-0" id="amount_excess">0.00</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="row ml-2">
        <div class="col-md-12">
            <div class="form-group">
                <x-forms.textarea class="mr-0 mr-lg-2 mr-md-2" fieldId="internal_note" :fieldLabel="__('purchase::modules.vendorPayment.internalNote')" :popover="__('purchase::modules.vendorPayment.internalUse')" fieldName="internal_note">
                </x-forms.textarea>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label class="f-14 text-dark-grey mb-12 w-100" for="notify_vendor"></label>
                <div class="d-flex">
                    <x-forms.checkbox fieldId="notify_vendor" :fieldLabel="__('purchase::modules.vendorPayment.sendNotification')" fieldValue="1" fieldName="notify_vendor"></x-forms.checkbox>
                </div>
            </div>
        </div>
    </div>
        <x-form-actions>
            <x-forms.button-primary id="save-vendor-payment" class="mr-3" icon="check">@lang('app.save')
            </x-forms.button-primary>
            <x-forms.button-cancel :link="route('vendor-payments.index')" class="border-0">@lang('app.cancel')
            </x-forms.button-cancel>
        </x-form-actions>
@endif
<script>
    $(document).ready(function(){
        function validate(data, url){
            $.easyAjax({
                url: url,
                container: '#save-vendor-payment-data-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                buttonSelector: "#save-vendor-payment",
                data: data,
                success: function (response) {
                    if (response.status === 'success') {
                        if (response.add_more == true) {
                            $(RIGHT_MODAL_CONTENT).html(response.html.html);
                        } else if ($(MODAL_XL).hasClass('show')) {
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
        }

        $('#save-vendor-payment').click(function () {
            var payment = jQuery('input[name="payment_made"]').val();
            var excess = jQuery('input[name="excess"]').val();
            var amountPerPaid = jQuery('input[name="amount_paid_per[]"]').map(function() {
            return jQuery(this).val();
            }).get();

            const url = "{{ route('vendor-payments.store') }}";

            var data = $('#save-vendor-payment-data-form').serialize();
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

        var type = "{{isset($type) ? $type : '' }}";

        if(type == 'bill')
        {
            let vendorId = "{{isset($purchaseBill) ? $purchaseBill->vendor->id : ''}}";
            let billId = "{{isset($purchaseBill) ? $purchaseBill->id : ''}}";

            var url = "{{ route('vendor-payments-fetch.fetch_bill', ':id') }}?bill="+billId+"";
            url = url.replace(':id', vendorId);
            $.easyAjax({
                url: url,
                type: "GET",
                success: function (response) {
                    if (response.status == 'success') {
                        $('#all-bills').html(response.html);
                    }
                }
            });
        }

    });
    datepicker('#payment_date', {
        position: 'bl',
        ...datepickerConfig
    });

    $("#bank_account_id").selectpicker();

    function changeDue(vals, id) {

        var billTotal = $('#payment'+id).data('bill-total');
        var variablePayment = vals;
        var total = billTotal;
        var dueBalance = (total - variablePayment);
        $('#due_balance'+id).html(number_format(dueBalance));
        $('#due'+id).val(dueBalance);
        $('#amount_used').html(variablePayment);

        if (vals == '')
        {
            $('#payment'+id).val(0);
        };

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
        calculateTotal();
    }

    function payFull(id){

        var billTotal = $('#pay-in-full'+id).data('total-val');
        $('#due_balance'+id).html(0);
        $('#payment'+id).val(billTotal);

        calculateTotal();
    }

    function clearAmount()
    {
        var allBillsId = {{ $allBills }};
        allBillsId.forEach(function(item, index, arr){
            $('#payment' + arr[index]).val(0);
        });
        calculateTotal();
    }

    function updateAmountPaid(vals) {
        $('#amount_paid').html(number_format(vals));
        calculateTotal();
    }

    function calculateTotal(){
        var allBillsId = {{ $allBills }};
        var billTotal = 0;
        var allTotal = 0;

        $.each(allBillsId, function(index, value) {
            billTotal = parseFloat($('#payment'+value).val());
            allTotal = parseFloat((allTotal + billTotal));
            $('#amount_paid_per'+value).val(billTotal);
        });
        $('#amount_used').html();
        var paid = $('#payment_made').val();
        var excess = (paid - allTotal)
        $('#amount_excess').html(number_format(parseFloat(excess)));
        $('#excess').val(parseFloat(excess));

        //text-danger
        if(excess < 0) {
            $("#amount_excess").addClass("text-danger");
        }
        else{
            $("#amount_excess").removeClass("text-danger");
        }

    }

    function number_format(number) {
        let decimals = '{{ currency_format_setting()->no_of_decimal }}';
        let thousands_sep = '{{ currency_format_setting()->thousand_separator }}';
        let currency_position = '{{ currency_format_setting()->currency_position }}';
        let dec_point = '{{ currency_format_setting()->decimal_separator }}';
        // Strip all characters but numerical ones.
        number = (number + '').replace(/[^0-9+\-Ee.]/g, '');

        var currency_symbol = '{{ ($vendor->currency->currency_symbol ? $vendor->currency->currency_symbol : company()->currency->currency_symbol ) }}';
        var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function (n, prec) {
                var k = Math.pow(10, prec);
                return '' + Math.round(n * k) / k;
            };
        // Fix for IE parseFloat(0.55).toFixed(0) = 0;
        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || '').length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1).join('0');
        }

        // number = dec_point == '' ? s[0] : s.join(dec);

        number = s.join(dec);

        switch (currency_position) {
            case 'left':
                number = currency_symbol + number;
                break;
            case 'right':
                number = number + currency_symbol;
                break;
            case 'left_with_space':
                number = currency_symbol + ' ' + number;
                break;
            case 'right_with_space':
                number = number + ' ' + currency_symbol;
                break;
            default:
                number = currency_symbol + number;
                break;
        }
        return number;
    }

    </script>
