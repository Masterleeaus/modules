<div class="modal-header">
    <h5 class="modal-title">@lang('app.addNew') @lang('recruit::modules.setting.recruiter')</h5>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
<div class="modal-body">
    <div class="portlet-body">
        <x-form id="createMethods" method="POST" class="ajax-form">
            <div class="row">
                <div class="col-md-12">
                    <x-forms.select fieldId="user_id" :fieldLabel="__('recruit::modules.setting.recruiter')"
                                    fieldName="user_id[]" search="true" fieldRequired="true" multiple="true">
                        @foreach ($employees as $emp)
                            @if(!in_array($emp->id, $selectedRecruiter))
                                <x-user-option :user="$emp" />
                            @endif
                        @endforeach
                    </x-forms.select>
                </div>

            </div>
        </x-form>
    </div>
</div>
<div class="modal-footer">
    <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.cancel')</x-forms.button-cancel>
    <x-forms.button-primary id="save-recruiter" icon="check">@lang('app.save')</x-forms.button-primary>
</div>

<script>
    $(".select-picker").selectpicker();

    // save recruiter
    $('body').off('click', "#save-recruiter").on('click', '#save-recruiter', function () {

        $.easyAjax({
            url: "{{ route('recruiter.store') }}",
            container: '#createMethods',
            type: "POST",
            blockUI: true,
            data: $('#createMethods').serialize(),
            success: function (response) {
                if (response.status == "success") {
                    window.location.reload();
                }
            }
        })
    });
</script>
