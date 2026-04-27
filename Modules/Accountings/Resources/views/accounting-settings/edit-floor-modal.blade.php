@php($pageTitle = $pageTitle ?? __('accountings::app.menu.accounting'))

<div class="modal-header">
    <h5 class="modal-title" id="modelHeading">@lang('app.edit')</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
</div>

<div class="modal-body">
    <x-form id="editFloorForm" method="POST" class="ajax-form">
        @csrf
        @method('PUT')

        {{-- Balance Sheet --}}
        @if(isset($floor) && isset($floor->bs_name))
            <div class="row">
                <div class="col-lg-6">
                    <x-forms.number fieldId="seq" :fieldLabel="__('accountings::app.menu.seq')" fieldName="seq"
                        :fieldValue="$floor->seq" fieldRequired="true" fieldPlaceholder="e.g. 01, 02, etc." />
                </div>
                <div class="col-md-6">
                    <x-forms.text fieldId="bs_name" :fieldLabel="__('accountings::app.menu.bsName')" fieldName="bs_name"
                        :fieldValue="$floor->bs_name" fieldRequired="true" />
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <x-forms.label class="mt-3" fieldId="bs_type_label" :fieldLabel="__('accountings::app.menu.bsType')" fieldName="bs_type_label" />
                    <x-forms.input-group>
                        <select class="form-control select-picker height-35 f-14" name="bs_type" id="bs_type">
                            <option value="aktiva" @selected($floor->bs_type === 'aktiva')>Aktiva</option>
                            <option value="passiva" @selected($floor->bs_type === 'passiva')>Passiva</option>
                            <option value="capital" @selected($floor->bs_type === 'capital')>Capital</option>
                        </select>
                    </x-forms.input-group>
                </div>
                <div class="col-md-6">
                    <x-forms.label class="mt-3" fieldId="bs_group_label" :fieldLabel="__('accountings::app.menu.bsGroup')" fieldName="bs_group_label" />
                    <x-forms.input-group>
                        <select class="form-control select-picker height-35 f-14" name="bs_group" id="bs_group">
                            <option value="current-assets" @selected($floor->bs_group === 'current-assets')>Current Asset</option>
                            <option value="fixed-assets" @selected($floor->bs_group === 'fixed-assets')>Fixed Asset</option>
                            <option value="intangible-assets" @selected($floor->bs_group === 'intangible-assets')>Intangible Asset</option>
                            <option value="current-liabilities" @selected($floor->bs_group === 'current-liabilities')>Current Liabilities</option>
                            <option value="long-term-liabilities" @selected($floor->bs_group === 'long-term-liabilities')>Long Term Liabilities</option>
                            <option value="equity" @selected($floor->bs_group === 'equity')>Equity</option>
                        </select>
                    </x-forms.input-group>
                </div>
            </div>
        @endif

        {{-- P&L --}}
        @if(isset($floor) && isset($floor->pnl_name))
            <div class="row">
                <div class="col-lg-6">
                    <x-forms.number fieldId="seq" :fieldLabel="__('accountings::app.menu.seq')" fieldName="seq"
                        :fieldValue="$floor->seq" fieldRequired="true" fieldPlaceholder="e.g. 01, 02, etc." />
                </div>
                <div class="col-md-6">
                    <x-forms.text fieldId="pnl_name" :fieldLabel="__('accountings::app.menu.pnlName')" fieldName="pnl_name"
                        :fieldValue="$floor->pnl_name" fieldRequired="true" />
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <x-forms.label class="mt-3" fieldId="pnl_type_label" :fieldLabel="__('accountings::app.menu.pnlType')" fieldName="pnl_type_label" />
                    <x-forms.input-group>
                        <select class="form-control select-picker height-35 f-14" name="pnl_type" id="pnl_type">
                            <option value="income" @selected($floor->pnl_type === 'income')>Income</option>
                            <option value="expense" @selected($floor->pnl_type === 'expense')>Expense</option>
                        </select>
                    </x-forms.input-group>
                </div>
                <div class="col-md-6">
                    <x-forms.label class="mt-3" fieldId="pnl_group_label" :fieldLabel="__('accountings::app.menu.pnlGroup')" fieldName="pnl_group_label" />
                    <x-forms.input-group>
                        <select class="form-control select-picker height-35 f-14" name="pnl_group" id="pnl_group">
                            <option value="operating" @selected($floor->pnl_group === 'operating')>Operating</option>
                            <option value="non-operating" @selected($floor->pnl_group === 'non-operating')>Non-operating</option>
                        </select>
                    </x-forms.input-group>
                </div>
            </div>
        @endif

        {{-- Journal Type --}}
        @if(isset($floor) && isset($floor->type_journal))
            <div class="row">
                <div class="col-md-6">
                    <x-forms.text fieldId="type_journal_code" :fieldLabel="__('accountings::app.menu.journalCode')" fieldName="type_journal_code"
                        :fieldValue="$floor->type_journal_code" fieldRequired="true" />
                </div>
                <div class="col-md-6">
                    <x-forms.text fieldId="type_journal" :fieldLabel="__('accountings::app.menu.journalType')" fieldName="type_journal"
                        :fieldValue="$floor->type_journal" fieldRequired="true" />
                </div>
            </div>
        @endif

    </x-form>
</div>

<div class="modal-footer">
    <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.close')</x-forms.button-cancel>
    <x-forms.button-primary id="save-floor-edit" icon="check">@lang('app.save')</x-forms.button-primary>
</div>

<script>
    // Determine update endpoint based on the model fields present.
    let updateUrl = null;

    @if(isset($floor) && isset($floor->bs_name))
        updateUrl = "{{ route('balance-sheet.update', $floor->id) }}";
    @elseif(isset($floor) && isset($floor->pnl_name))
        updateUrl = "{{ route('pnl.update', $floor->id) }}";
    @elseif(isset($floor) && isset($floor->type_journal))
        updateUrl = "{{ route('journal-type.update', $floor->id) }}";
    @endif

    $('#save-floor-edit').click(function() {
        if (!updateUrl) {
            // Fail safe: nothing to update.
            $(MODAL_LG).modal('hide');
            return;
        }

        $.easyAjax({
            url: updateUrl,
            container: '#editFloorForm',
            type: 'POST',
            blockUI: true,
            data: $('#editFloorForm').serialize(),
            success: function(response) {
                if (response.status === 'success') {
                    window.location.reload();
                }
            }
        });
    });

    init(MODAL_LG);
</script>
