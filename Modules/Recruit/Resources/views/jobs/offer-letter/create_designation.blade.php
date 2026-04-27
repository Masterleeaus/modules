<div class="modal-header">
    <h5 class="modal-title" id="modelHeading">@lang('app.menu.addDesignation')</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
    </button>
</div>
<div class="modal-body">
    <x-form id="save-designation-data-form">
        <div class="add-client bg-white rounded">
             
            <div class="row p-20">
                <div class="col-md-6">
                    <x-forms.text fieldId="designation_name" :fieldLabel="__('app.name')" fieldName="name" fieldRequired="true"
                        :fieldPlaceholder="__('placeholders.designation')">
                    </x-forms.text>
                </div>
                <div class="col-md-6">
                    <x-forms.label class="mt-3" fieldId="parent_label" :fieldLabel="__('app.menu.parent_id')" fieldName="parent_label">
                    </x-forms.label>
                    <x-forms.input-group>
                        <select class="form-control select-picker" name="parent_id" id="parent_id"
                            data-live-search="true">
                            <option value="">--</option>
                            @foreach ($designations as $designation)
                                <option value="{{ $designation->id }}">{{ $designation->name }}</option>
                            @endforeach
                        </select>
                    </x-forms.input-group>
                </div>
            </div>


        </div>
    </x-form>
</div>
<div class="modal-footer">
    <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.cancel')</x-forms.button-cancel>
    <x-forms.button-primary id="save-designation-form" icon="check">@lang('app.save')</x-forms.button-primary>
</div>

<script>
    $(document).ready(function() {

        $('#save-designation-form').click(function() {

            const url = "{{ route('job-offer-letter.store-designation') }}";

            $.easyAjax({
                url: url,
                container: '#save-designation-data-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                buttonSelector: "#save-designation-form",
                data: $('#save-designation-data-form').serialize(),
                success: function(response) {
                    $('#employee_designation').html(response.data);
                    $('#employee_designation').selectpicker('refresh');
                    var selected = response.selected;
                    if (selected) {
                        $('#employee_designation').selectpicker('val', selected);
                    }

                    $(MODAL_LG).modal('hide');

                }
            });
        });

        $(".select-picker").selectpicker();

        init(RIGHT_MODAL);
    });
</script>
