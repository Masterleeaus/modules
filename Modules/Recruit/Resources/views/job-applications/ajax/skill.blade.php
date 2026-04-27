@php
    $skillPermission = user()->permission('manage_skill');
@endphp
<!-- TAB CONTENT START -->
<div class="tab-pane fade show active" role="tabpanel" aria-labelledby="nav-email-tab">
    <div class="p-20">
        <x-form id="save-skill-data-form">
            <input type="hidden" name="application_id" value="{{ $application->id }}">
            <div class="col-md-4">
                <x-forms.label class="my-3" fieldId="selectEmployeeData"
                               :fieldLabel="__('recruit::modules.jobApplication.candidateSkills')">
                </x-forms.label>
                <x-forms.input-group>
                    <select class="form-control multiple-users" multiple name="skill_id[]" id="selectEmployeeData"
                            data-live-search="true" data-size="8">
                        @forelse ($skills as $skill)
                            <option @if (in_array($skill->id, $selected_skills)) selected @endif
                            data-content="<span class='badge badge-pill badge-light border'><div class='d-inline-block mr-1'></div> {{ ($skill->name) }}</span>"
                                    value="{{ $skill->id }}">{{ $skill->name }}</option>
                        @empty
                            <option value=""> @lang('recruit::messages.noSkillAdded')</option>
                        @endforelse
                    </select>
                    @if ($skillPermission == 'all')
                        <x-slot name="append">
                            <button type="button"
                                    class="btn btn-outline-secondary border-grey skill-setting">@lang('app.add')</button>
                        </x-slot>
                    @endif
                </x-forms.input-group>

            </div>

            <div class="w-100 justify-content-end d-flex mt-2">
                <x-forms.button-cancel id="cancel-note" class="border-0 mr-3">@lang('app.cancel')
                </x-forms.button-cancel>
                <x-forms.button-primary id="submit-skill" disabled
                                        icon="location-arrow">@lang('app.submit')</x-forms.button-primary>
            </div>
        </x-form>
    </div>
</div>
<!-- TAB CONTENT END -->

<script>
    $(document).ready(function () {
        $("#selectEmployeeData").selectpicker({
            actionsBox: true,
            selectAllText: "{{ __('modules.permission.selectAll') }}",
            deselectAllText: "{{ __('modules.permission.deselectAll') }}",
            multipleSeparator: " ",
            selectedTextFormat: "count > 8",
            countSelectedText: function (selected, total) {
                return selected + " {{ __('app.membersSelected') }} ";
            }
        });

        $('#selectEmployeeData').on('change', function () {
            if (this.value != '') {
                $('#submit-skill').prop('disabled', false)
            } else {
                $('#submit-skill').prop('disabled', true)
            }
        });

        $('body').off('click', "#submit-skill").on('click', '#submit-skill', function () {
            const url = "{{ route('job-appboard.add-skills') }}";
            $.easyAjax({
                url: url,
                container: '#save-skill-data-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                buttonSelector: "#submit-skill",
                data: $('#save-skill-data-form').serialize(),
                success: function (response) {
                    if (response.status == "success") {

                    }
                }
            });
        });
    });

    $('body').on('click', '.skill-setting', function () {
        var selectedValue = $('#selectEmployeeData').val();
        var newVal = selectedValue.join(',');
        const url = "{{ route('job-skills.addSkill') }}?skill=" + newVal;
        $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
        $.ajaxModal(MODAL_LG, url);
    });
</script>
