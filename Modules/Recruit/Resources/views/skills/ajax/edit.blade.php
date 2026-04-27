<div class="row">
    <div class="col-sm-12">
        <x-form id="save-skill-data-form" method="PUT">
            <div class="add-client bg-white rounded">
                <h4 class="mb-0 p-20 f-21 font-weight-normal  border-bottom-grey">
                    @lang('recruit::app.menu.edit')</h4>
                <div class="row p-20">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-5">
                                <x-forms.text fieldId="name" :fieldLabel="__('recruit::modules.skill.skillname')"
                                              fieldName="name" :fieldValue="$skills->name" fieldRequired="true"
                                              :fieldPlaceholder="__('placeholders.name')">
                                </x-forms.text>
                            </div>
                        </div>
                    </div>
                </div>

                <x-form-actions>
                    <x-forms.button-primary id="save-skill" class="mr-3" icon="check">@lang('app.save')
                    </x-forms.button-primary>
                    <x-forms.button-cancel :link="route('job-skills.index')" class="border-0">@lang('app.cancel')
                    </x-forms.button-cancel>
                </x-form-actions>

            </div>
        </x-form>

    </div>
</div>
<script src="{{ asset('vendor/jquery/tagify.min.js') }}"></script>

<script>
    $(document).ready(function () {

        var input = document.querySelector('input[name=tags]'),
            // init Tagify script on the above inputs
            tagify = new Tagify(input, {
                whitelist: {!! json_encode($skills) !!},
            });

        $('body').on('click', '#save-skill', function () {

            const url = "{{ route('job-skills.update', [$skills->id]) }}";
            $.easyAjax({
                url: url,
                container: '#save-skill-data-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                buttonSelector: "#save-skill",
                file: true,
                data: $('#save-skill-data-form').serialize(),
                success: function (response) {
                    if (response.status == 'success') {
                        window.location.href = response.redirectUrl;
                    }
                }
            });
        });
        init(RIGHT_MODAL);
    });
</script>
