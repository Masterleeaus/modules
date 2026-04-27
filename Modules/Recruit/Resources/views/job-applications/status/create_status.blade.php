<div class="modal-header">
    <h5 class="modal-title" id="modelHeading">@lang('modules.tasks.addBoardColumn')</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">×</span></button>
</div>
<div class="modal-body">
    <x-form id="createJobBoardColumn">
        <div class="row">
            <div class="col-md-6">
                <x-forms.label class="mt-3" fieldId="category_id" :fieldLabel="__('app.category')"
                               fieldRequired="true">
                </x-forms.label>
                <x-forms.input-group>
                    <select class="form-control select-picker" name="category_id"
                            id="category_id" data-live-search="true">
                        <option value="">--</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ ($category->name) }}</option>
                        @endforeach
                    </select>
                </x-forms.input-group>
            </div>
            <div class="col-md-6">
                <x-forms.text class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('recruit::modules.jobApplication.status')"
                              fieldName="status" fieldId="status"
                              :placeholder="__('recruit::modules.jobApplication.status')"
                              fieldRequired="true"/>
            </div>

            <div class="col-md-6">
                <div class="form-group my-3">
                    <x-forms.label fieldId="colorselector" fieldRequired="true"
                                   :fieldLabel="__('recruit::modules.jobApplication.labelColor')">
                    </x-forms.label>
                    <x-forms.input-group id="colorpicker">
                        <input type="text" class="form-control height-35 f-14"
                               placeholder="{{ __('placeholders.colorPicker') }}" name="color" id="colorselector">

                        <x-slot name="append">
                            <span class="input-group-text height-35 colorpicker-input-addon"><i></i></span>
                        </x-slot>
                    </x-forms.input-group>

                </div>
            </div>
            <div class="col-md-6">
                <x-forms.select fieldId="position" fieldName="position"
                                :fieldLabel="__('recruit::modules.jobApplication.position')">
                    <option value="-1">{{'Before '.ucwords($firstStatus->status)}}</option>
                    @foreach ($statuses as $status)
                        <option value="{{$status->position}}">{{'After '.ucwords($status->status)}}</option>
                    @endforeach
                </x-forms.select>
            </div>

            <div class="col-lg-12 my-2 ml-1 py-20" id="fetch-label">
                <x-forms.checkbox :fieldLabel="__('recruit::modules.jobApplication.action')" fieldName="action"
                                  fieldId="action" fieldValue="yes" :checked="true"/>
            </div>
        </div>
    </x-form>
</div>
<div class="modal-footer">
    <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.cancel')</x-forms.button-cancel>
    <x-forms.button-primary id="save-board-status-column" icon="check">@lang('app.save')</x-forms.button-primary>
</div>

<script src="{{ asset('vendor/jquery/bootstrap-colorpicker.js') }}"></script>
<script>
    $('#colorpicker').colorpicker({
        "color": "#ff0000"
    });

    $('body').on('click', '#save-board-status-column', function () {
        var url = "{{ route('job-appboard.store-status') }}";
        $.easyAjax({
            url: url,
            container: '#createJobBoardColumn',
            disableButton: true,
            blockUI: true,
            buttonSelector: "#save-board-status-column",
            type: "POST",
            data: $('#createJobBoardColumn').serialize(),
            success: function (response) {
                if (response.status == 'success') {
                    window.location.reload();
                }
            }
        })
    });
    $(".select-picker").selectpicker();

    $('#category_id').change(function () {

        const categoryId = $(this).val();
        const url = "{{ route('job-appboard.fetch-status-model-label') }}";

        $.easyAjax({
            url: url,
            type: "GET",
            disableButton: true,
            blockUI: true,
            data: {
                category_id: categoryId
            },
            success: function (response) {
                if (response.status == 'success') {
                    $('#fetch-label').html(response.data);
                }
            }
        });
    });
</script>
