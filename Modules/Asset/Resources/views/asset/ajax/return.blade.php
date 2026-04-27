<div class="modal-header">
    <h5 class="modal-title" id="modelHeading">@lang('asset::app.returnAsset')</h5>
    <button type="button" onclick="removeOpenModal()" class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">×</span></button>
</div>
<div class="modal-body">
    <div class="portlet-body">
        <x-form id="returnTo" method="PUT" class="ajax-form">
            <input type="hidden" name="show_page" value="0"/>
            <div class="form-body">
                <input type="hidden" name="type" value="return">
                <div class="row">
                    <div class="col-lg-6">
                        <x-employee :user="$history->user"/>
                    </div>
                    <div class="col-md-6">
                        <x-forms.label fieldId="asset_type_id" :fieldLabel="__('asset::app.dateGiven')"/>
                        <p class="simple-text">
                            {{ $history->date_given->setTimezone($global->timezone)->format('d F Y H:i A') .' ('. $history->date_given->setTimezone($global->timezone)->diffForHumans(now()->setTimezone($global->timezone)) .')' }}
                        </p>
                    </div>
                    <div class="col-md-6">
                        <x-forms.label fieldId="asset_type_id" class="mt-3" :fieldLabel="__('asset::app.returnDate')"/>
                        <p class="simple-text">
                            {{ !is_null($history->return_date) ?$history->return_date->setTimezone($global->timezone)->format('d F Y H:i A') . ' ('.$history->return_date->setTimezone($global->timezone)->diffForHumans(now()->setTimezone($global->timezone)) .')' : '-' }}
                        </p>
                    </div>
                    <div class="col-md-6">
                        <x-forms.datepicker fieldId="date_of_return" :fieldLabel="__('asset::app.dateOfReturn')"
                                            fieldRequired="true" fieldName="date_of_return"
                                            :fieldPlaceholder="__('placeholders.date')"/>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group my-3">
                            <x-forms.textarea class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('asset::app.notes')"
                                              fieldName="notes" :fieldValue="$history->notes" fieldId="notes"
                                              :fieldPlaceholder="__('asset::app.notes')">
                            </x-forms.textarea>
                        </div>
                    </div>
                </div>
            </div>
        </x-form>
    </div>
</div>
<div class="modal-footer">
    <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.close')</x-forms.button-cancel>
    <x-forms.button-primary id="update-return-to" icon="check">@lang('app.save')</x-forms.button-primary>
</div>

<script>
    $(document).ready(function () {

        datepicker('#date_of_return', {
            position: 'bl',
            ...datepickerConfig
        });
        // save source
        $('#update-return-to').click(function () {
            $.easyAjax({
                url: "{{ route('history.update', [$history->asset_id, $history->id]) }}",
                container: '#returnTo',
                type: "POST",
                blockUI: true,
                disableButton: true,
                buttonSelector: "#update-return-to",
                data: $('#returnTo').serialize(),
                success: function (response) {
                    if (response.status == "success") {
                        if ($('#assets-table').length > 0) {
                            window.LaravelDataTables["assets-table"].draw(true);
                            $(MODAL_LG).modal('hide');
                        } else {
                            window.location.reload();
                        }
                    }
                }
            })
        });
    });
</script>
