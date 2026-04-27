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
        <x-form id="save-vendor-payment-data-form">
            <div class="add-client bg-white rounded">
                <h4 class="mb-0 p-20 f-21 font-weight-normal  border-bottom-grey">
                    @lang('purchase::modules.vendorPayment.vendorPaymentDetails')</h4>
                <div class="row p-20">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group c-inv-select mb-lg-0 mb-md-0 mb-4">
                                    <x-forms.label class="mt-3" fieldId="vendor_id" :fieldLabel="__('purchase::app.menu.vendor')" fieldRequired="true">
                                    </x-forms.label>

                                    <div class="select-others height-35 rounded">
                                        @if($vendorID) <input type="hidden" name="vendor_id" value="{{$vendorID}}"> @endif
                                        <select @if($vendorID) id="vendor_id" disabled @endif class="form-control select-picker" name="vendor_id" id="vendor_id">
                                            @if(isset($type) && $type == 'bill' && isset($purchaseBill))
                                                <option value="{{$purchaseBill->vendor->id}}" selected>{{$purchaseBill->vendor->primary_name}}</option>
                                            @else
                                                <option value="all">--</option>
                                                @foreach ($vendors as $vendor)
                                                    <option @if($vendorID == $vendor->id) selected @endif value="{{ $vendor->id }}">{{ $vendor->primary_name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12" id="all-bills"></div>
                        </div>
                    </div>
                </div>
            </div>
        </x-form>
    </div>
</div>

<script>
    $(document).ready(function () {
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

        function changeVendor() {
            let vendorId = $('#vendor_id').val();
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
            });
        }

        $('#vendor_id').change(function (e) {
            changeVendor();
        });

        let vendorID = '{{$vendorID}}';

        if (vendorID) {
            changeVendor();
        }

        init(RIGHT_MODAL)
    });

</script>
