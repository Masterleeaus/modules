<div class="row">
    <div class="col-sm-12">
        <x-form id="save-vendor-data-form" method="PUT">
            <div class="add-client bg-white rounded">
                <div class="row p-20">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-md-3">
                                <x-forms.text fieldId="primary_name" :fieldLabel="__('purchase::modules.vendor.primaryContactName')"
                                    fieldName="primary_name" fieldRequired="true"
                                    :fieldValue="$vendor->primary_name"
                                    :fieldPlaceholder="__('placeholders.name')">
                                </x-forms.text>
                            </div>

                            <div class="col-md-3">
                                <x-forms.label class="mt-3" fieldId="category"
                                    :fieldLabel="__('app.category')">
                                </x-forms.label>
                                <x-forms.input-group>
                                    <select class="form-control select-picker" name="category_id" id="category_id"
                                        data-live-search="true">
                                        <option value="">--</option>
                                        @foreach ($categories as $category)
                                            <option @selected($vendor->category_id == $category->id) value="{{ $category->id }}">
                                        {{ $category->category_name }}</option>
                                        @endforeach
                                    </select>

                                    
                                        <x-slot name="append">
                                            <button id="addClientCategory" type="button"
                                                class="btn btn-outline-secondary border-grey"
                                                data-toggle="tooltip" data-original-title="{{ __('app.category') }}">
                                                @lang('app.add')</button>
                                        </x-slot>
                                   
                                </x-forms.input-group>
                            </div>

                            <div class="col-md-3">
                                <x-forms.text fieldId="company_name" :fieldLabel="__('purchase::modules.vendor.companyName')"
                                            fieldName="company_name" :fieldValue="$vendor->company_name"
                                            :fieldPlaceholder="__('placeholders.company')">
                                </x-forms.text>
                            </div>

                            <div class="col-md-3">
                                <x-forms.text fieldId="email" :fieldLabel="__('app.email')"
                                              fieldName="email" :fieldValue="$vendor->email"
                                              :fieldPlaceholder="__('placeholders.email')">
                                </x-forms.text>
                            </div>

                            <div class="col-md-3">
                                <x-forms.tel fieldId="phone" :fieldLabel="__('app.phone')" fieldName="phone"
                                :fieldPlaceholder="__('placeholders.mobile')" :fieldValue="$vendor->phone"></x-forms.tel>
                            </div>
                        </div>
                    </div>
                </div>

                <h4 class="mb-0 p-20 f-21 font-weight-normal  border-top-grey">
                    @lang('purchase::modules.vendor.otherDetails')</h4>
                <div class="row p-20">
                    <div class="col-md-3">
                        <x-forms.text fieldId="website" :fieldLabel="__('modules.client.website')"
                                    fieldName="website" :fieldValue="$vendor->website"
                                    :fieldPlaceholder="__('placeholders.website')">
                        </x-forms.text>
                    </div>

                    <div class="col-md-3">
                        <x-forms.number fieldId="opening_balance" :fieldLabel="__('purchase::modules.vendor.openingsBalance')"      fieldName="opening_balance" :fieldValue="$vendor->opening_balance"
                            :fieldPlaceholder="__('placeholders.price')" :popover="__('purchase::app.availableBalance')"/>
                    </div>
                    <!-- CURRENCY START -->
                    <div class="col-md-3 col-lg-3 mt-3">
                        <div class="form-group c-inv-select mb-lg-0 mb-md-0 mb-4">
                            <x-forms.label fieldId="currency_id" :fieldLabel="__('modules.invoices.currency')">
                            </x-forms.label>

                            <div class="select-others height-35 rounded">
                                <select class="form-control select-picker" name="currency_id" id="currency_id">
                                    @foreach ($currencies as $currency)
                                    <option
                                        @if ($currency->id == $vendor->currency_id) selected @endif
                                        value="{{ $currency->id }}">
                                        {{ $currency->currency_code . ' (' . $currency->currency_symbol . ')' }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- CURRENCY END -->
                </div>
                <h4 class="mb-0 p-20 f-21 font-weight-normal  border-top-grey">
                    @lang('app.address')</h4>
                <div class="row p-20">
                    <div class="col-md-6">
                        <x-forms.textarea class="mr-0 mr-lg-2 mr-md-2"
                                        :fieldLabel="__('modules.invoices.billingAddress')"
                                        fieldName="billing_address"
                                        fieldId="billing_address"
                                        :fieldValue="$vendor->billing_address"
                                        :fieldPlaceholder="__('placeholders.address')">
                        </x-forms.textarea>
                    </div>
                    <div class="col-md-6">
                        <x-forms.textarea class="mr-0 mr-lg-2 mr-md-2"
                                        :fieldLabel="__('modules.invoices.shippingAddress')"
                                        fieldName="shipping_address"
                                        fieldId="shipping_address"
                                        :fieldValue="$vendor->shipping_address"
                                        :fieldPlaceholder="__('placeholders.address')">
                        </x-forms.textarea>
                    </div>
                </div>

                <x-form-actions>
                    <x-forms.button-primary id="save-vendor" class="mr-3" icon="check">@lang('app.save')
                    </x-forms.button-primary>
                    <x-forms.button-cancel :link="route('vendors.index')" class="border-0">@lang('app.cancel')
                    </x-forms.button-cancel>
                </x-form-actions>
            </div>
        </x-form>
    </div>
</div>

<script>
    $(document).ready(function () {
        $("#currency_id").selectpicker();

        $('#save-vendor').click(function () {
            const url = "{{ route('vendors.update', $vendor->id) }}";

            $.easyAjax({
                url: url,
                container: '#save-vendor-data-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                buttonSelector: "#save-vendor",
                file: true,
                data: $('#save-vendor-data-form').serialize(),
                success: function (response) {
                    if (response.status == 'success') {
                        if ($(MODAL_XL).hasClass('show')) {
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
                }
            });
        });

        init(RIGHT_MODAL);
    });

    $('#addClientCategory').click(function() {
            const url = "{{ route('vendor-cateogory.create') }}";
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        })
</script>
