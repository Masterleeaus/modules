<link rel="stylesheet" href="{{ asset('vendor/css/dropzone.min.css') }}">
<!-- CREATE BILL
     START -->
<div class="bg-white rounded b-shadow-4 create-inv">


    <!-- HEADING START -->
    <div class="px-3 py-3 px-lg-4 px-md-4">
        <h4 class="mb-0 f-21 font-weight-normal ">@lang('purchase::app.menu.bill')</h4>
    </div>
    <!-- HEADING END -->
    <hr class="m-0 border-top-grey">
    <!-- FORM START -->
    <x-form class="c-inv-form" id="save-bill-data-form">

        <!-- BILL NUMBER, DATE, FREQUENCY START -->
        <div class="px-3 pt-3 row px-lg-4 px-md-4">
            <!-- BILL NUMBER START -->
            <div class="col-md-4">
                <div class="mb-4 form-group mb-lg-0 mb-md-0">
                    <x-forms.label class="mb-12" fieldId="bill_number"
                        :fieldLabel="__('purchase::app.menu.billNumber')" fieldRequired="true">
                    </x-forms.label>
                    <x-forms.input-group>
                        <x-slot name="prepend">
                            <span
                                class="input-group-text">{{ $purchaseSetting->bill_prefix }}{{ $purchaseSetting->bill_number_separator }}{{ $zero }}</span>
                        </x-slot>
                        <input type="number" name="bill_number" id="bill_number" class="form-control height-35 f-15"
                            value="{{ is_null($lastBill) ? 1 : $lastBill }}">
                    </x-forms.input-group>
                </div>
            </div>

        <!-- BILL NUMBER END -->
            <!-- SELECT VENDOR -->
            <div class="col-md-4">
                <x-forms.label fieldId="vendor-id" :fieldLabel="__('purchase::app.selectVendor')">
                </x-forms.label>
                <div class="rounded select-others height-35">
                    @if($vendorID) <input type="hidden" name="vendor_id" value="{{$vendorID}}"> @endif
                        <select @if($vendorID) disabled @endif class="form-control select-picker" id="vendor-id" name ="vendor_id" data-live-search="true"
                            data-size="8">
                            @if(isset($type) && $type == "order")
                                <option value="{{$purchaseOrder->vendor->id}}" selected>{{$purchaseOrder->vendor->primary_name}}</option>
                            @elseif($purchaseVendors->isNotEmpty())
                                <option value="">--</option>
                                @foreach ($purchaseVendors as $purchaseVendor)
                                    <option @if($vendorID == $purchaseVendor->id) selected @endif value="{{$purchaseVendor->id}}">{{$purchaseVendor->primary_name}}</option>
                                @endforeach
                            @endif
                        </select>
                </div>

            </div>
            <!-- SELECT VENDOR -->

            <!-- BILL DATE START -->
            <div class="col-md-4">
                <div class="mb-4 form-group mb-lg-0 mb-md-0">
                    <x-forms.label fieldId="due_date" :fieldLabel="__('purchase::app.menu.billDate')">
                    </x-forms.label>
                    <div class="input-group">
                        <input type="text" id="bill_date" name="issue_date"
                            class="p-0 px-6 text-left rounded position-relative text-dark font-weight-normal form-control height-35 f-15"
                            placeholder="@lang('placeholders.date')"
                            value="{{ now(company()->timezone)->translatedFormat(company()->date_format) }}">
                    </div>
                </div>
            </div>
            <!-- BILL DATE END -->
        </div>
        <!-- INVOICE NUMBER, DATE, DUE DATE, FREQUENCY END -->

        <div class="px-3 py-3 row px-lg-4 px-md-4">
            <div class="col-md-3">
                <div class="mb-2 form-group c-inv-select">
                    <x-forms.select fieldId="purchase-order"
                                :fieldLabel="__('purchase::app.purchaseOrder.purchaseOrder')"
                                fieldName="purchase_order_id"
                                search="true">
                                @if(isset($type) && $type == 'order')
                                    <option   option value="{{$purchaseOrder->id}}" selected>{{$purchaseOrder->purchase_order_number}}</option>
                                @else
                                    <option value="">--</option>
                                @endif
                    </x-forms.select>
                </div>
            </div>
        </div>

        <div id="sortable">
            <!-- DESKTOP DESCRIPTION TABLE START -->

            <!-- DESKTOP DESCRIPTION TABLE END -->

        </div>


        <hr class="m-0 border-top-grey">


        <!-- NOTE AND TERMS AND CONDITIONS START -->
        <div class="flex-wrap px-3 py-3 d-flex px-lg-4 px-md-4">
            <div class="p-0 mb-3 col-md-6 col-sm-12 c-inv-note-terms mb-lg-0 mb-md-0">
                <x-forms.label fieldId="" class="" :fieldLabel="__('modules.invoices.note')">
                </x-forms.label>
                <textarea class="form-control" name="note" id="note" rows="4"
                    placeholder="@lang('placeholders.invoices.note')"></textarea>
            </div>
        </div>

        <!-- CANCEL SAVE SEND START -->
        <x-form-actions class="c-inv-btns d-block d-lg-flex d-md-flex">
            <div class="mb-3 d-flex mb-lg-0 mb-md-0">

                <div class="mr-3 inv-action dropup">
                    <button class="btn-primary dropdown-toggle" type="button" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        @lang('app.save')
                        <span><i class="text-white fa fa-chevron-up f-15"></i></span>
                    </button>
                    <!-- DROPDOWN - INFORMATION -->
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuBtn" tabindex="0">
                        <li>
                            <a class="dropdown-item f-14 text-dark save-form" href="javascript:;" data-type="draft">
                                <i class="mr-2 fa fa-save f-w-500 f-11"></i> @lang('app.saveDraft')
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item f-14 text-dark save-form" href="javascript:void(0);"
                                data-type="open">
                                <i class="mr-2 fa fa-paper-plane f-w-500 f-12"></i> @lang('purchase::modules.purchaseBill.saveAsOpen')
                            </a>
                        </li>
                    </ul>
                </div>
            </div>


            <x-forms.button-cancel :link="isset($vendorID) ? route('vendors.show', $vendorID).'?tab=bills' : route('bills.index')" class="border-0">@lang('app.cancel')
            </x-forms.button-cancel>

        </x-form-actions>
        <!-- CANCEL SAVE SEND END -->

    </x-form>
    <!-- FORM END -->
</div>
    <script>
        $(document).ready(function() {

            var type = "{{isset($type) ? $type : '' }}";

            if(type == 'order')
            {
                let url = "{{route('purchase_order_products')}}";
                let purchaseOrderId = "{{$purchaseOrder ? $purchaseOrder->id : ''}}";
                addProduct(purchaseOrderId, url);
            }

            let defaultImage = '';
            let lastIndex = 0;

            $('.custom-date-picker').each(function(ind, el) {
                datepicker(el, {
                    position: 'bl',
                    ...datepickerConfig
                });
            });

            const dp1 = datepicker('#bill_date', {
                position: 'bl',
                ...datepickerConfig
            });


            calculateTotal();
            var vendorId = "{{ $purchaseOrder ? $purchaseOrder->vendor->id : '' }}";

            function vendorOrders(vendorId) {
                let url = "{{route('purchase_orders')}}";
                let prefix = "{{ $purchaseSetting->purchase_order_prefix }}{{ $purchaseSetting->purchase_order_number_seprator }}{{ $zero }}"
                $.easyAjax({
                    url : url,
                    type : "GET",
                    data:{vendor_id:vendorId},
                    success: function (response) {
                        var options = [];
                        var rData = [];
                        rData = response.data;
                        var options = [];
                        var purchaseOrderId = $('#purchase-order').val();

                        $.each(rData, function(index, value) {
                            let selected = "";

                            if (purchaseOrderId == value.id) {
                                selected = "selected";
                            }

                            let purchaseOrderNumber = value.purchase_order_number ? value.purchase_order_number : 'N/A';
                            let selectData = '<option ' + selected + ' value="' + value.id + '">' + purchaseOrderNumber + '</option>';

                            options.push(selectData);
                        });

                        $('#purchase-order').html('<option value="">--</option>' + options.join(''));
                        $('#purchase-order').selectpicker('refresh');

                    }

                });
            }

            $('#vendor-id').change(function(){
                let vendorId = $(this).val();
                vendorOrders(vendorId);
            });

            let vendor_id = $('#vendor-id').val();
            if (vendor_id) {
                vendorOrders(vendor_id);
            }


            $('#purchase-order').change(function(){
                let purchaseOrderId = $(this).val();
                let url = "{{route('purchase_order_products')}}";
                $('#bill-desc').remove();
                $('.c-inv-total').remove();
                addProduct(purchaseOrderId, url);
                $('#bill-desc').remove();
                $('.c-inv-total').remove();
            });

            function addProduct(purchaseOrderId, url) {
            $.easyAjax({
                url: url,
                type: "GET",
                data:{purchase_order_id: purchaseOrderId},
                blockUI: true,
                success: function(response) {
                    $(response.view).hide().appendTo("#sortable").fadeIn(500);
                    calculateTotal();
                }
            });
            }


            $('.save-form').click(function() {
                var type = $(this).data('type');
                var url = "{{ route('bills.store') }}";

                    if (KTUtil.isMobileDevice()) {
                        $('.desktop-description').remove();
                    } else {
                        $('.mobile-description').remove();
                    }

                    calculateTotal();

                    var discount = $('#discount_amount').html();
                    var total = $('.sub-total-field').val();

                    if (parseFloat(discount) > parseFloat(total)) {
                        Swal.fire({
                            icon: 'error',
                            text: "{{ __('messages.discountExceed') }}",

                            customClass: {
                                confirmButton: 'btn btn-primary',
                            },
                            showClass: {
                                popup: 'swal2-noanimation',
                                backdrop: 'swal2-noanimation'
                            },
                            buttonsStyling: false
                        });
                        return false;
                    }

                    $.easyAjax({
                        url: "{{ route('bills.store') }}" + "?type=" + type,
                        container: '#save-bill-data-form',
                        type: "POST",
                        blockUI: true,
                        redirect: true,
                        file: true,  // Commented so that we dot get error of Input variables exceeded 1000
                        data: $('#save-bill-data-form').serialize(),
                        success: function(response) {
                            window.location.href="{{route('bills.index')}}";
                        }
                    });
            });
        });
    </script>
