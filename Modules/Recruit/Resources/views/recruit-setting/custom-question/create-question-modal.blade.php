<div class="modal-header">
    <h5 class="modal-title">@lang('app.add') @lang('recruit::modules.setting.question')</h5>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
<div class="modal-body">
    <div class="portlet-body">
        <x-form id="createJobQuestions" method="POST" class="ajax-form">
            <div class="row">

                <div class="col-lg-6">
                    <x-forms.select fieldId="category" :fieldLabel="__('modules.tasks.category')" fieldName="category"
                                    search="true">
                        <option value="job_application"
                                data-content="@lang('recruit::app.report.jobapplication')"></option>
                        <option value="job_offer"
                                data-content="@lang('recruit::app.menu.joboffer')"></option>
                    </x-forms.select>
                </div>

                <div class="col-lg-6">
                    <x-forms.text fieldId="question" :fieldLabel="__('recruit::modules.setting.question')"
                                  fieldName="question" fieldRequired="true"
                                  :fieldPlaceholder="__('recruit::modules.setting.question')">
                    </x-forms.text>
                </div>

                <div class="col-lg-6">
                    <x-forms.select fieldId="status" :fieldLabel="__('app.status')" fieldName="status"
                                    search="true">
                        <option value="enable"
                                data-content="@lang('app.enable')"></option>
                        <option value="disable"
                                data-content="@lang('app.disable')"></option>
                    </x-forms.select>
                </div>

                <div class="col-lg-6">
                    <div class="form-group my-3">
                        <label class="f-14 text-dark-grey mb-12 w-100" for="usr">@lang('app.required')</label>
                        <div class="d-flex">
                            <x-forms.radio fieldId="optionsRadios1" :fieldLabel="__('app.yes')" fieldName="required"
                                           fieldValue="yes" checked="true">
                            </x-forms.radio>
                            <x-forms.radio fieldId="optionsRadios2" :fieldLabel="__('app.no')" fieldValue="no"
                                           fieldName="required"></x-forms.radio>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12">
                    <x-forms.select fieldId="type" :fieldLabel="__('modules.invoices.type')" fieldName="type"
                                    search="true">
                        @foreach ($types as $type)
                            <option value="{{ $type }}">{{ __('recruit::modules.type.'.$type) }}</option>
                        @endforeach
                    </x-forms.select>
                </div>
                <div class="col-lg-12">
                <div class="form-group mt-repeater d-none">
                    <div id="addMoreBox1" class="row my-3">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">@lang('app.value')</label>
                                <input class="form-control height-35 f-14" name="value[]" type="text" value=""
                                       placeholder=""/>
                            </div>
                        </div>
                    </div>
                    <div id="insertBefore"></div>
                    <div class="row">
                        <div class="col-md-12">
    
                            <a class="f-15 f-w-500" href="javascript:;" data-repeater-create id="plusButton"><i
                                    class="icons icon-plus font-weight-bold mr-1"></i>@lang('modules.invoices.addItem')</a>
                        </div>
                    </div>
                </div>
                </div>

            </div>
        </x-form>
    </div>
</div>
<div class="modal-footer">
    <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.cancel')</x-forms.button-cancel>
    <x-forms.button-primary id="save-job-question" icon="check">@lang('app.save')</x-forms.button-primary>
</div>

<script>
     $(".select-picker").selectpicker();

    var $insertBefore = $('#insertBefore');
    var $i = 1;

    // Add More Inputs
    $('#plusButton').click(function () {
        $i = $i + 1;
        var indexs = $i + 1;
        $('<div id="addMoreBox' + indexs + '" class="row my-3"> <div class="col-md-10">  <label class="control-label">@lang('app.value')</label> <input class="form-control height-35 f-14" name="value[]" type="text" value="" placeholder=""/>  </div> <div class="col-md-1"> <div class="task_view mt-4"> <a href="javascript:;" class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle" onclick="removeBox(' + indexs + ')"> <i class="fa fa-trash icons mr-2"></i> @lang('app.delete')</a> </div> </div></div>').insertBefore($insertBefore);
    });

    // Remove fields
    function removeBox(index) {
        $('#addMoreBox' + index).remove();
    }

    $('#type').on('change', function () {
        (this.value === 'select' || this.value === 'radio' || this.value === 'checkbox') ? $('.mt-repeater').removeClass('d-none') : $('.mt-repeater').addClass('d-none');
    });

    function convertToSlug(Text) {
        return Text.toLowerCase().replace(/[^\w ]+/g, '').replace(/ +/g, '-');
    }

    $('#label').keyup(function () {
        $('#name').val(convertToSlug($(this).val()));
    });

    // save recruiter
    $('body').off('click', "#save-job-question").on('click', '#save-job-question', function () {

        $.easyAjax({
            url: "{{ route('custom-question-settings.store') }}",
            container: '#createJobQuestions',
            type: "POST",
            blockUI: true,
            data: $('#createJobQuestions').serialize(),
            success: function (response) {
                if (response.status == "success") {
                    window.location.reload();
                }
            }
        })
        return false;
    });

</script>
