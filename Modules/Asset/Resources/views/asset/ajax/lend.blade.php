<div class="modal-header">
    <h5 class="modal-title" id="modelHeading">@lang('asset::app.lendAsset')</h5>
    <button type="button" onclick="removeOpenModal()" class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">×</span></button>
</div>
<div class="modal-body">
    <div class="portlet-body">
        <x-form id="lendTo" method="POST" class="ajax-form">
            <div class="form-body">
                <div class="row">
                    <div class="col-lg-4">
                        <x-forms.select fieldId="employee_id" :fieldLabel="__('asset::app.employee')" search="true"
                                        data-live-search="true" data-size="8"
                                        fieldName="employee_id" fieldRequired="true">


                            @foreach ($employees as $employee)
                                <x-user-option :user="$employee" :pill="true" />
                            @endforeach
                        </x-forms.select>
                    </div>
                    <div class="col-md-4">
                        <x-forms.datepicker fieldId="date_given" :fieldLabel="__('asset::app.dateGiven')"
                                            fieldRequired="true" fieldName="date_given"
                                            :fieldPlaceholder="__('placeholders.date')"/>
                    </div>
                    <div class="col-md-4">
                        <x-forms.datepicker fieldId="return_date" :fieldLabel="__('asset::app.returnDate')"
                                            fieldName="return_date" :fieldPlaceholder="__('placeholders.date')"/>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group my-3">
                            <x-forms.textarea class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('asset::app.notes')"
                                              fieldName="notes" fieldId="notes"
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
    <x-forms.button-primary id="save-land-to" icon="check">@lang('app.save')</x-forms.button-primary>
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

        $(".select-picker").selectpicker();
        // save land
        $('#save-land-to').click(function () {
            $.easyAjax({
                url: "{{ route('history.store', $asset->id) }}",
                container: '#lendTo',
                type: "POST",
                blockUI: true,
                disableButton: true,
                buttonSelector: "#save-land-to",
                data: $('#lendTo').serialize(),
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
