<div class="col-lg-12 col-md-12 ntfcn-tab-content-left w-100 p-4 ">
    @method('POST')
    <div class="row">
            <div class="col-lg-3">
                <x-forms.text class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('purchase::modules.purchaseSettings.purchaseOrderPrefix')" :fieldPlaceholder="__('purchase::modules.purchaseSettings.purchaseOrderPrefix')" fieldName="purchase_order_prefix"
                    fieldId="purchase_order_prefix" :fieldValue="$purchaseSetting->purchase_order_prefix" fieldRequired="true" />
            </div>

            <div class="col-lg-3">
                <x-forms.text class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('purchase::modules.purchaseSettings.purchaseOrderNumberSeprator')" :fieldPlaceholder="__('purchase::modules.purchaseSettings.purchaseOrderNumberSeprator')"
                    fieldName="purchase_order_number_seprator" fieldId="purchase_order_number_seprator" :fieldValue="$purchaseSetting->purchase_order_number_separator" />
            </div>

            <div class="col-lg-3">
                <x-forms.number class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('purchase::modules.purchaseSettings.purchaseOrderDigits')" fieldName="purchase_order_digit"
                    fieldId="purchase_order_digit" :fieldValue="$purchaseSetting->purchase_order_number_digit" minValue="2" />
            </div>

            <div class="col-lg-3">
                <x-forms.text class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('purchase::modules.purchaseSettings.purchaseOrderExample')" fieldId="purchase_order_look_like"
                    fieldName="purchase_order_look_like" fieldReadOnly="true" />
            </div>

            <div class="col-lg-3">
                <x-forms.text class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('purchase::modules.purchaseSettings.billOrderPrefix')" :fieldPlaceholder="__('purchase::modules.purchaseSettings.billOrderPrefix')"
                    fieldName="bill_prefix" fieldRequired="true" fieldId="bill_prefix" :fieldValue="$purchaseSetting->bill_prefix" />
            </div>

            <div class="col-lg-3">
                <x-forms.text class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('purchase::modules.purchaseSettings.billOrderNumberSeprator')" :fieldPlaceholder="__('purchase::modules.purchaseSettings.billOrderNumberSeprator')"
                    fieldName="bill_number_seprator" fieldId="bill_number_seprator" :fieldValue="$purchaseSetting->bill_number_separator" />
            </div>

            <div class="col-lg-3">
                <x-forms.number class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('purchase::modules.purchaseSettings.billOrderDigits')" fieldName="bill_digit"
                    fieldId="bill_digit" :fieldValue="$purchaseSetting->bill_number_digit" minValue="2" />
            </div>

            <div class="col-lg-3">
                <x-forms.text class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('purchase::modules.purchaseSettings.billOrderExample')" fieldName="bill_look_like"
                    fieldId="bill_look_like" fieldValue="" fieldReadOnly="true" />
            </div>

            <div class="col-lg-3">
                <x-forms.text class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('purchase::modules.purchaseSettings.venodrCreditPrefix')" :fieldPlaceholder="__('purchase::modules.purchaseSettings.venodrCreditPrefix')"
                    fieldName="vendor_credit_prefix" fieldRequired="true" fieldId="vendor_credit_prefix" :fieldValue="$purchaseSetting->vendor_credit_prefix" />
            </div>
            <div class="col-lg-3">
                <x-forms.text class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('purchase::modules.purchaseSettings.venodrCreditNumberSeprator')" :fieldPlaceholder="__('purchase::modules.purchaseSettings.venodrCreditNumberSeprator')"
                    fieldName="vendor_credit_number_seprator" fieldId="vendor_credit_number_seprator" :fieldValue="$purchaseSetting->vendor_credit_number_seprator" />
            </div>
            <div class="col-lg-3">
                <x-forms.number class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('purchase::modules.purchaseSettings.vendorCreditDigits')" fieldName="vendor_credit_digit"
                    fieldId="vendor_credit_digit" :fieldValue="$purchaseSetting->vendor_credit_number_digit" minValue="2" />
            </div>
            <div class="col-lg-3">
                <x-forms.text class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('purchase::modules.purchaseSettings.vendorCreditExample')" fieldId="vendor_credit_look_like"
                    fieldName="vendor_credit_look_like" fieldReadOnly="true" />
            </div>
            <div class="col-lg-12 mt-3">
                <x-forms.textarea class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('purchase::modules.purchaseSettings.termsAndCondition')" fieldId="purchase_terms"
                    fieldName="purchase_terms" :fieldValue="$purchaseSetting->purchase_terms"/>   
            </div>
    </div>
</div>

<!-- Buttons Start -->
<div class="w-100 border-top-grey">
    <x-setting-form-actions>
        <x-forms.button-primary id="save-prefix-form" class="mr-3" icon="check">@lang('app.save')
        </x-forms.button-primary>
    </x-setting-form-actions>
</div>
<!-- Buttons End -->

<script>
    // save prefix setting
    $('#save-prefix-form').click(function() {
        $.easyAjax({
            url: "{{ route('purchase_settings.update_prefix', $purchaseSetting->id) }}",
            container: '#editSettings',
            type: "POST",
            redirect: true,
            file: true,
            data: $('#editSettings').serialize(),
            disableButton: true,
            blockUI: true,
            buttonSelector: "#save-prefix-form",
        })
    });

    $('#purchase_order_prefix, #purchase_order_number_seprator, #purchase_order_digit, #bill_prefix, #bill_number_seprator, #bill_digit, #vendor_credit_prefix, #vendor_credit_number_seprator, #vendor_credit_digit').on('keyup', function() {
        genrateInvoiceNumber();
    });

    genrateInvoiceNumber();

    function genrateInvoiceNumber() {
        var purchaseOrderPrefix = $('#purchase_order_prefix').val();
        var purchaseOrderNumberSeparator = $('#purchase_order_number_seprator').val();
        var purchaseOrderDigit = $('#purchase_order_digit').val();
        var purchaseOrderZero = '';
        for ($i = 0; $i < purchaseOrderDigit - 1; $i++) {
            purchaseOrderZero = purchaseOrderZero + '0';
        }
        purchaseOrderZero = purchaseOrderZero + '1';
        var purchase_order_no = purchaseOrderPrefix + purchaseOrderNumberSeparator + purchaseOrderZero;
        $('#purchase_order_look_like').val(purchase_order_no);

        var billOrderPrefix = $('#bill_prefix').val();
        var billOrderNumberSeparator = $('#bill_number_seprator').val();
        var billOrderDigit = $('#bill_digit').val();
        var billOrderZero = '';
        for ($i = 0; $i < billOrderDigit - 1; $i++) {
            billOrderZero = billOrderZero + '0';
        }
        billOrderZero = billOrderZero + '1';
        var bill_order_no = billOrderPrefix + billOrderNumberSeparator + billOrderZero;
        $('#bill_look_like').val(bill_order_no);

        var vendorCreditPrefix = $('#vendor_credit_prefix').val();
        var purchase_terms = $('#purchase_terms').val();

        var vendorCreditNumberSeprator = $('#vendor_credit_number_seprator').val();
        var vendorCreditDigit = $('#vendor_credit_digit').val();
        var vendorCreditZero = '';
        for ($i = 0; $i < vendorCreditDigit - 1; $i++) {
            vendorCreditZero = vendorCreditZero + '0';
        }

        vendorCreditZero = vendorCreditZero + '1';
        var vendor_credit_no = vendorCreditPrefix + vendorCreditNumberSeprator + vendorCreditZero;
        $('#vendor_credit_look_like').val(vendor_credit_no);
    }
</script>
