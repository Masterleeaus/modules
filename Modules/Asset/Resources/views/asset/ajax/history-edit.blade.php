<div class="modal-header">
    <h5 class="modal-title" id="modelHeading">@lang('asset::app.lendAsset')</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">×</span></button>
</div>
<div class="modal-body">
    <div class="portlet-body">
        <x-form id="edit-history-form" method="PUT" class="ajax-form">
            <input type="hidden" name="show_page" value="1"/>
            <div class="form-body">
                <div class="row">
                    <div class="col-lg-6">

                        <x-forms.select fieldId="employee_id" :fieldLabel="__('asset::app.employee')" search="true"
                                        fieldName="employee_id" fieldRequired="true">
                            @foreach ($employees as $employee)
                                <x-user-option :user="$employee" :selected="($employee->id == $history->user_id)" :pill="true"/>
                            @endforeach
                        </x-forms.select>

                    </div>
                    <div class="col-md-6">
                        <x-forms.datepicker fieldId="date_given" :fieldLabel="__('asset::app.dateGiven')"
                                            fieldRequired="true" fieldName="date_given"
                                            :fieldValue="$history->date_given->format(company()->date_format)"
                                            :fieldPlaceholder="__('placeholders.date')"/>
                    </div>
                    <div class="col-md-6">
                        <x-forms.datepicker fieldId="return_date" :fieldLabel="__('asset::app.returnDate')"
                                            fieldName="return_date"
                                            :fieldValue="$history->return_date ? $history->return_date->format(company()->date_format) : ''"
                                            :fieldPlaceholder="__('placeholders.date')"/>
                    </div>
                    <div class="col-md-6">
                        <x-forms.datepicker fieldId="date_of_return" :fieldLabel="__('asset::app.dateOfReturn')"
                                            fieldName="date_of_return"
                                            :fieldValue="$history->date_of_return ? $history->date_of_return->format(company()->date_format) : ''"
                                            :fieldPlaceholder="__('placeholders.date')"/>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group my-3">
                            <x-forms.textarea class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('asset::app.notes')"
                                              fieldName="notes" :fieldValue="$history->notes ?? ''" fieldId="notes"
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
    <x-forms.button-primary id="update-history" icon="check">@lang('app.save')</x-forms.button-primary>
</div>

<script>
    $(document).ready(function () {

        datepicker('#date_given', {
            position: 'bl',
            ...datepickerConfig
        });

        datepicker('#return_date', {
            position: 'bl',
            ...datepickerConfig
        });

        datepicker('#date_of_return', {
            position: 'bl',
            ...datepickerConfig
        });

        $(".select-picker").selectpicker();
        // save land
        $('#update-history').click(function () {
            $.easyAjax({
                url: "{{ route('history.update', [$history->asset_id, $history->id]) }}",
                container: '#edit-history-form',
                type: "POST",
                blockUI: true,
                disableButton: true,
                buttonSelector: "#update-history",
                data: $('#edit-history-form').serialize(),
                success: function (response) {
                    if (response.status == "success") {
                        $('#history').html(response.view);
                        $(MODAL_LG).modal('hide');
                    }
                }
            })
        });
    });
</script>
