<div class="row">
    <div class="col-sm-12">
        <x-form id="save-skill-data-form">
            <div class="add-client bg-white rounded">
                <h4 class="mb-0 p-20 f-21 font-weight-normal  border-bottom-grey">
                    @lang('recruit::modules.skill.createnew')</h4>
                <div class="row pl-20 pr-20 pt-20">
                    <div class="col-lg-5">
                        <div class="form-group my-3">
                            <x-forms.text :fieldLabel="__('recruit::app.menu.skill')" fieldName="names[]"
                                          fieldId="names0" :fieldPlaceholder="__('recruit::modules.skill.skillname')"
                                          fieldValue=""
                                          fieldRequired="true"/>
                        </div>
                    </div>
                </div>
                <div id="insertBefore"></div>

                <!--  ADD ITEM START-->
                <div class="row px-lg-4 px-md-4 px-3 pb-3 pt-0 mb-3  mt-2">
                    <div class="col-md-12">
                        <a class="f-15 f-w-500" href="javascript:;" id="add-item"><i
                                class="icons icon-plus font-weight-bold mr-1 "></i> @lang('app.add') @lang('app.more')
                        </a>
                    </div>
                </div>
                <!--  ADD ITEM END-->

                <x-form-actions>
                    <x-forms.button-primary class="mr-3" id="save-skill-form" icon="check">@lang('app.save')
                    </x-forms.button-primary>
                    <x-forms.button-cancel :link="route('job-skills.index')" class="border-0">@lang('app.cancel')
                    </x-forms.button-cancel>
                </x-form-actions>
            </div>
        </x-form>
    </div>
</div>

<script>

    $(document).ready(function () {
        var $insertBefore = $('#insertBefore');
        var i = 0;

        // Add More Inputs
        $('body').on('click', '#add-item', function () {

            i += 1;

            $(`<div id="addMoreBox${i}" class="row pl-20 pr-20 skill-name"><div class="col-md-5"> <div id="nameBox${i}" class="form-group"><input class="form-control name_new height-35 f-14" name="names[]" id="names${i}" placeholder="@lang('recruit::modules.skill.skillname')" value="" required="true" /></div></div><div class="col-md-1"><div class="task_view mt-1"><a href="javascript:;" class="task_view_more d-flex align-items-center justify-content-center remove-item" data-item-id="${i}"><i class="fa fa-trash icons mr-2 text-lightest"></i>@lang('recruit::app.menu.delete')</a></div> </div></div>`).insertBefore($insertBefore);

        });

        // Remove fields
        $('body').on('click', '.remove-item', function () {
            var index = $(this).data('item-id');
            $('#addMoreBox' + index).remove();
        });

        $('body').on('click', '#save-skill-form', function () {
            $.easyAjax({
                url: '{{ route('job-skills.store') }}',
                container: '#save-skill-data-form',
                type: "POST",
                redirect: true,
                disableButton: true,
                blockUI: true,
                buttonSelector: "#save-skill-form",
                data: $('#save-skill-data-form').serialize(),
                success: function (response) {
                    if (response.status == 'success') {
                        window.location.href = response.redirectUrl;
                    }
                },
                error: function (response) {
                    if (response.status == '422') {
                        $('.invalid-feedback').html('');

                        $.each(response.responseJSON.errors, function (key, value) {
                            var result = key.split('.');
                            var message = response.responseJSON.errors[key][0];
                            var taken_message = 'already';
                            var already_message = "@lang('recruit::modules.message.alreadyExist')"

                            if (message.includes(taken_message) == true) {
                                $('<div class="invalid-feedback">' + already_message + '</div>').insertAfter('#names' + result[1]);
                            } else {
                                $('<div class="invalid-feedback">' + response.responseJSON.errors[key][0] + '</div>').insertAfter('#names' + result[1]);
                            }

                            $('#names' + result[1]).addClass('is-invalid');

                        });
                    }
                }
            })
        });
        init(RIGHT_MODAL);
    });
</script>
