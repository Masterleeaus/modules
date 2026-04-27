@php($pageTitle = $pageTitle ?? __('accountings::app.menu.accounting'))
<div class="row">
    <div class="col-sm-12">
        <x-form id="save-lead-data-form" method="put">
            <div class="add-client bg-white rounded">
                <h4 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-bottom-grey">
                    @lang('accountings::app.acc.editAcc')</h4>
                <div class="row p-20">
                    <div class="col-md-4">
                        <x-forms.label class="mt-3" fieldId="parent_label" :fieldLabel="__('accountings::modules.bs.balanceSheet')" fieldName="parent_label">
                        </x-forms.label>
                        <x-forms.input-group>
                            <select class="form-control select-picker" name="bs_id" id="bs_id"
                                data-live-search="true">
                                <option value="">--</option>
                                @foreach ($bs as $item)
                                    <option @if ($item->id == $acc->bs_id) selected @endif
                                        value="{{ $item->id }}">
                                        {{ $item->bs_name }}
                                    </option>
                                @endforeach
                            </select>
                        </x-forms.input-group>
                    </div>
                    <div class="col-md-4">
                        <x-forms.label class="mt-3" fieldId="parent_label" :fieldLabel="__('accountings::modules.pnl.pnl')" fieldName="parent_label">
                        </x-forms.label>
                        <x-forms.input-group>
                            <select class="form-control select-picker" name="pnl_id" id="pnl_id"
                                data-live-search="true">
                                <option value="">--</option>
                                @foreach ($pnl as $items)
                                <option @if ($items->id == $acc->pnl_id) selected @endif
                                    value="{{ $items->id }}">
                                    {{ $items->pnl_name }}
                                </option>
                                @endforeach
                            </select>
                        </x-forms.input-group>
                    </div>
                    <div class="col-md-4">
                        <x-forms.text fieldId="coa" :fieldLabel="__('accountings::app.menu.coa')" fieldName="coa" fieldRequired="true"
                        :fieldValue="$acc->coa">
                        </x-forms.text>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <x-forms.label class="my-3" fieldId="description-text" :fieldLabel="__('accountings::app.menu.coaDesc')"
                                fieldRequired="true">
                            </x-forms.label>
                            <textarea name="coa_desc" id="description-text" rows="4" class="form-control">{{ $acc->coa_desc }}</textarea>
                        </div>
                    </div>
                </div>

                <x-form-actions>
                    <x-forms.button-primary id="save-leave-form" class="mr-3" icon="check">@lang('app.save')
                    </x-forms.button-primary>
                    <x-forms.button-cancel :link="route('accountings.index')" class="border-0">@lang('app.cancel')
                    </x-forms.button-cancel>
                </x-form-actions>

            </div>
        </x-form>

    </div>
</div>


<script>
    $(document).ready(function() {

        $('#save-leave-form').click(function() {
            const url = "{{ route('accountings.update', $acc->id) }}";
            $.easyAjax({
                url: url,
                container: '#save-lead-data-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                buttonSelector: "#save-leave-form",
                data: $('#save-lead-data-form').serialize(),
                success: function(response) {
                    window.location.href = response.redirectUrl;
                }
            });
        });

        init(RIGHT_MODAL);
    });
</script>
