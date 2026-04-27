
<div class="modal-header">
    <h5 class="modal-title">@lang('app.edit') @lang('recruit::modules.setting.question')</h5>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
<div class="modal-body">
    <div class="portlet-body">
        <x-form id="editQuestion" method="POST" class="ajax-form">
            <div class="row">

                <div class="col-lg-6">
                    <div class="form-group my-3">
                        <label class="control-label required" for="display_name">@lang('modules.tasks.category')</label>
                        <select disabled class="form-control select-picker" data-size="8">
                        <option @if($question->status == 'job_application') selected @endif value="job_application"
                                data-content="@lang('recruit::app.report.jobapplication')"></option>
                        <option @if($question->status == 'job_offer') selected @endif value="job_offer"
                                data-content="@lang('recruit::app.menu.joboffer')"></option>
                    </select>
                    </div>
                </div>

                <div class="col-lg-6">
                    <input type="hidden" name="id" value='{{ $question->id }}'/>
                    <x-forms.text fieldId="question" :fieldLabel="__('recruit::modules.setting.question')"
                                  fieldName="question" :fieldValue="$question->question" fieldRequired="true"
                                  :fieldPlaceholder="__('recruit::modules.setting.question')">
                    </x-forms.text>
                </div>

                <div class="col-lg-6">
                    <x-forms.select fieldId="status" :fieldLabel="__('app.status')" fieldName="status"
                                    search="true">
                        <option value="">--</option>
                        <option @if($question->status == 'enable') selected @endif value="enable"
                                data-content="@lang('app.enable')"></option>
                        <option @if($question->status == 'disable') selected @endif value="disable"
                                data-content="@lang('app.disable')"></option>
                    </x-forms.select>
                </div>

                <div class="col-lg-6">
                    <div class="form-group my-3">
                        <label class="f-14 text-dark-grey mb-12 w-100" for="usr">@lang('app.required')</label>
                        <div class="d-flex">
                            <x-forms.radio fieldId="optionsRadios1" :fieldLabel="__('app.yes')" fieldName="required"
                                fieldValue="yes" :checked="$question->required == 'yes'">
                            </x-forms.radio>
                            <x-forms.radio fieldId="optionsRadios2" :fieldLabel="__('app.no')" fieldValue="no"
                                fieldName="required" :checked="$question->required == 'no'"></x-forms.radio>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12">

                    <div class="form-group mt-repeater" @if($question->type != 'radio' && $question->type != 'select' && $question->type != 'checkbox') style="display: none;" @endif>

                        @foreach ($question->values as $item)
                        <div id="addMoreBox{{$loop->iteration}}" class="row mt-2">
                            <div class="col-md-10">
                                <div class="form-group">
                                    <label class="control-label">@lang('app.value')</label>
                                    <input class="form-control height-35 f-14" name="value[]" type="text" value="{{ $item }}" placeholder=""/>
                                </div>
                            </div>
                            @if($loop->iteration !== 1)
                                <div class="col-md-1">
                                    <div class="task_view mt-4"> <a href="javascript:;" class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle" onclick="removeBox({{$loop->iteration}})"> <i class="fa fa-trash icons mr-2"></i> @lang('app.delete')</a> </div>
                                </div>
                            @endif
                        </div>
                        @endforeach
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
    <x-forms.button-primary id="save-question" icon="check">@lang('app.save')</x-forms.button-primary>
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

    $('body').off('click', "#save-question").on('click', '#save-question', function () {

        $.easyAjax({
            container: '#editQuestion',
            type: "PUT",
            disableButton: true,
            blockUI: true,
            buttonSelector: "#save-question",
            url: "{{ route('custom-question-settings.update', $question->id) }}",
            data: $('#editQuestion').serialize(),
            success: function (response) {
                if (response.status == 'success') {
                    window.location.reload();
                }
            }
        })
    });

</script>
