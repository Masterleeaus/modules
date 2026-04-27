<div class="col-xl-6 col-lg-12 col-md-12 ntfcn-tab-content-left border-left-grey" style="padding-top:20px;">
    <h4 class="f-16  f-w-500 text-dark-grey">@lang("modules.emailSettings.notificationTitle")</h4>
    <div class="mb-3 mt-3 d-flex">

        <x-forms.checkbox :  :checked="$checkedAll==true"
                          :fieldLabel="__('modules.permission.selectAll')"
                          fieldName="select_all_checkbox" fieldId="select_all"
                          fieldValue="all"/>
    </div>
    @foreach ($emailSettings as $emailSetting)
        <div class="mb-3 d-flex">
            <x-forms.checkbox :checked="$emailSetting->send_email == 'yes'"
                              :fieldLabel="__('purchase::app.menu.'.str_slug($emailSetting->setting_name))"
                              fieldName="send_email[]" :fieldId="'send_email_'.$emailSetting->id"
                              :fieldValue="$emailSetting->id"/>
        </div>
    @endforeach
</div>

<!-- Buttons Start -->
<div class="w-100 border-top-grey set-btns">
    <x-setting-form-actions>
        <x-forms.button-primary id="save-email-form" class="mr-3" icon="check">@lang('app.save')
        </x-forms.button-primary>

    </x-setting-form-actions>
</div>
<!-- Buttons End -->

<script>

    function submitForm() {
        CHANGE_DETECTED = false;
        const url = "{{ route('purchase-smtp-settings.update', $smtpSetting->id) }}";

        $.easyAjax({
            url: url,
            type: "POST",
            container: '#editSettings',
            blockUI: true,
            data: $('#editSettings').serialize(),
            success: function (response) {
                if (response.status === 'error') {
                    $('#alert').prepend(
                        '<div class="alert alert-danger">{{ __('messages.smtpError') }}</div>'
                    )
                } else {
                    $('#alert').show();
                }
            }
        })
    }

    var checkboxes = document.querySelectorAll("input[type = 'checkbox']");

    $('#select_all').on('click', function(){
        var selectAll = $('#select_all').is(':checked');

        if(selectAll == true){
            checkboxes.forEach(function(checkbox){
                checkbox.checked = true;
            })
        }
        else{
            checkboxes.forEach(function(checkbox){
                checkbox.checked = false;
            })
        }
    });

    $('body').on('click', '#save-email-form', function () {
        submitForm()
    });

</script>

