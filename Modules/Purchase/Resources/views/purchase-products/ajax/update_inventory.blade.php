<div class="modal-header">
    <h5 class="modal-title" id="modelHeading">@lang('purchase::app.adjustStock')</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
</div>

<div class="modal-body">
    <x-form id="adjustStockForm">
        <input type="hidden" id="product_id" name="product_id" value="{{ $productId }}">

        <div class="row">
            <div class="col-md-12">
                <x-forms.label class="my-3" fieldId="" :fieldLabel="__('purchase::modules.product.modeOfAdjustment')">
                </x-forms.label><sup class="text-red f-14 mr-1">*</sup>
                <div class="form-group">
                    <div class="d-flex">
                        <x-forms.radio class="quantity" fieldId="quantity" :fieldLabel="__('purchase::modules.product.quantityAdjustment')" fieldValue="quantity"
                            fieldName="type" :checked="(($adjustment && $adjustment->type == 'quantity') ? 'true' : 'true')"></x-forms.radio>

                        <x-forms.radio class="value" fieldId="value" :fieldLabel="__('purchase::modules.product.valueAdjustment')" fieldValue="value"
                            fieldName="type" :checked="(($adjustment && $adjustment->type == 'value') ? 'true' : '')"></x-forms.radio>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-6">
                <x-forms.text :fieldLabel="__('app.date')" fieldName="date" fieldId="date" :fieldPlaceholder="__('app.date')" :fieldValue=" ($adjustment && $adjustment->date) ? \Carbon\Carbon::parse($adjustment->date)->timezone(company()->timezone)->translatedFormat(company()->date_format) : now(company()->timezone)->translatedFormat(company()->date_format)"
                    fieldRequired />
            </div>
            <div class="col-md-6">
                <x-forms.text fieldId="reference_number" :fieldLabel="__('purchase::modules.product.referenceNumber')" fieldName="reference_number" fieldValue="{{ $adjustment && $adjustment->reference_number ? $adjustment->reference_number : '' }}">
                </x-forms.text>
            </div>
        </div>

        <div class="quantity_div {{ ($adjustment && $adjustment->type == 'value') ? 'd-none' : '' }}">
            <div class="row mt-3">
                <div class="col-md-6">
                    <x-forms.label class="my-3" fieldId="" :fieldLabel="__('purchase::modules.product.availableQuantity')">
                    </x-forms.label>
                </div>
                <div class="col-md-6 text-right">
                    <input type="text" name="available_quantity" id="available_quantity"
                        class="form-control height-35 f-15 readonly-background text-right" value="{{ ($adjustment && $adjustment->net_quantity) ? $adjustment->net_quantity : '' }}" readonly>
                    <span class="text-dark" id="">{{ $product->unit ? $product->unit->unit_type : $product->name }}</span>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <x-forms.label class="my-3" fieldId="quantity_on_hand" :fieldLabel="__('purchase::modules.product.newQuantityOnHand')">
                    </x-forms.label>
                </div>
                <div class="col-md-6 text-right">
                    <input type="number" name="quantity_on_hand" id="quantity_on_hand"
                        class="form-control height-35 f-15 readonly-background text-right"
                        placeholder="@lang('purchase::placeholders.changedValue')"
                        value="{{ ($adjustment && $adjustment->net_quantity) ? $adjustment->net_quantity : '' }}">
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <x-forms.label class="my-3" fieldId="quantity_adjusted" :fieldLabel="__('purchase::modules.product.quabtityAdjusted')" fieldRequired>
                    </x-forms.label>
                </div>
                <div class="col-md-6 text-right">
                    <input type="text" name="quantity_adjusted" id="quantity_adjusted"
                        class="form-control height-35 f-15 readonly-background text-right"
                        placeholder="@lang('purchase::placeholders.adjustedValue')" value="{{ ($adjustment && $adjustment->quantity_adjustment) ? $adjustment->quantity_adjustment : '' }}" readonly>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <x-forms.label class="my-3" fieldId="" :fieldLabel="__('purchase::modules.product.costPrice')">
                    </x-forms.label>
                </div>
                <div class="col-md-6 text-right">
                    <input type="text" name="cost_price" id="cost_price"
                        class="form-control height-35 f-15 readonly-background text-right"
                        value="{{ ($adjustment && $adjustment->changed_value) ? $adjustment->changed_value : $product->purchase_price }}">
                </div>
            </div>
        </div>

        <div class="value_div {{ ($adjustment && $adjustment->type == 'value') ? '' : 'd-none' }}">
            <div class="row mt-3">
                <div class="col-md-6">
                    <x-forms.label class="my-3" fieldId="current_value" :fieldLabel="__('purchase::modules.product.currentValue')">
                    </x-forms.label>
                </div>
                <div class="col-md-6 text-right">
                    <input type="number" name="current_value" id="current_value"
                        class="form-control height-35 f-15 readonly-background text-right" readonly
                        value="{{ $product->purchase_price }}">
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <x-forms.label class="my-3" fieldId="changed_value" :fieldLabel="__('purchase::modules.product.changedValue')">
                    </x-forms.label>
                </div>
                <div class="col-md-6 text-right">
                    <input type="number" name="changed_value" id="changed_value"
                        class="form-control height-35 f-15 readonly-background text-right"
                        placeholder="@lang('purchase::placeholders.changedValue')" value="{{ ($adjustment && $adjustment->changed_value) ? $adjustment->changed_value : $product->price }}" fieldRequired>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <x-forms.label class="my-3" fieldId="adjusted_value" :fieldLabel="__('purchase::modules.product.adjustedValue')">
                    </x-forms.label>
                </div>
                <div class="col-md-6 text-right">
                    <input placeholder="@lang('purchase::placeholders.adjustedValue')" type="text" name="adjusted_value" id="adjusted_value"
                        class="form-control height-35 f-15 readonly-background text-right" value="{{ ($adjustment && $adjustment->adjusted_value) ? $adjustment->adjusted_value : '' }}" readonly>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-12">
                <x-forms.label class="my-3" fieldId="reason" :fieldLabel="__('purchase::modules.product.reason')" fieldRequired>
                </x-forms.label>
                <x-forms.input-group>
                    <select class="form-control select-picker" name="reason_id" id="adjustment_reason_id"
                        data-live-search="true">
                        <option value="">--</option>
                        @foreach ($reasons as $reason)
                            <option value="{{ $reason->id }}" @if($adjustment && ($adjustment->reason_id == $reason->id)) selected @endif>
                                {{ mb_ucwords($reason->name) }}
                            </option>
                        @endforeach
                    </select>

                    <x-slot name="append">
                        <button id="addReason" type="button" class="btn btn-outline-secondary border-grey"
                            data-toggle="tooltip"
                            data-original-title="{{ __('purchase::modules.inventory.addReason') }}">@lang('app.add')</button>
                    </x-slot>
                </x-forms.input-group>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-12">
                <div class="form-group">
                    <x-forms.label class="my-3" fieldId="description-text" :fieldLabel="__('app.description')">
                    </x-forms.label>
                    <textarea name="description" id="description-text" rows="4" class="form-control">@if($adjustment) {!! $adjustment->description !!} @endif</textarea>
                </div>
            </div>
        </div>
    </x-form>
</div>

<div class="modal-footer">
    <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.cancel')</x-forms.button-cancel>
    <x-forms.button-primary id="save-adjustment" icon="check">@lang('app.save')</x-forms.button-primary>
</div>

<script>
    $(document).ready(function() {
        const dp1 = datepicker('#date', {
            position: 'bl',
            ...datepickerConfig
        });

        $("#reason").selectpicker();

        $('input[type=radio][name=type]').change(function() {
            if (this.value == 'value') {
                $('.quantity_div').addClass('d-none');
                $('.value_div').removeClass('d-none');

                $('#quantity_on_hand, #quantity_adjusted').val('');
                $('#cost_price').val('{{ $product->purchase_price }}');
            } else {
                $('.quantity_div').removeClass('d-none');
                $('.value_div').addClass('d-none');
            }
        });

        $('#quantity_on_hand').keyup(function() {
            var availQuantity = parseInt($('#available_quantity').val());
            var onHandQuantity = parseInt($(this).val());
            let adjustedQuantity = 0;

            if (onHandQuantity > availQuantity && onHandQuantity != 0) {
                let adjustedQuantity = onHandQuantity - availQuantity;
                $('#quantity_adjusted').val('+' + adjustedQuantity);
            } else if (onHandQuantity < availQuantity && onHandQuantity != 0) {
                let adjustedQuantity = availQuantity - onHandQuantity;
                $('#quantity_adjusted').val('-' + adjustedQuantity);
            } else {
                $('#quantity_adjusted').val(adjustedQuantity);
            }
        });

        $('#changed_value').keyup(function() {
            var currentVal = parseInt($('#current_value').val());
            var changedVal = parseInt($(this).val());
            let adjustedVal = 0;

            if (currentVal > changedVal && changedVal != 0) {
                let adjustedVal = currentVal - changedVal;
                $('#adjusted_value').val('-' + adjustedVal);

            } else if (currentVal < changedVal && changedVal != 0) {
                let adjustedVal = changedVal - currentVal;
                $('#adjusted_value').val('+' + adjustedVal);

            } else {
                $('#adjusted_value').val(adjustedVal);
            }
        });

        $('#addReason').click(function() {
            const url = "{{ route('adjustment-reasons.create') }}";
            $(MODAL_DEFAULT + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_DEFAULT, url);
        });

        $('#save-adjustment').click(function() {
            var url = "{{ route('purchase_products.update_inventory') }}";
            $.easyAjax({
                url: url,
                container: '#adjustStockForm',
                type: "POST",
                data: $('#adjustStockForm').serialize(),
                success: function(response) {
                    if (response.status == 'success') {
                        $(MODAL_LG).modal('hide');
                        window.location.reload();
                    }
                }
            })
        });
    });
</script>
