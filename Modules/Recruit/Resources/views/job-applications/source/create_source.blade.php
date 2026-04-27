<div class="modal-header">
    <h5 class="modal-title" id="modelHeading">@lang('recruit::modules.sourceSetting.addSource')</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">×</span></button>
</div>
<div class="modal-body">
    <x-form id="createJobBoardColumn">
        <div class="row">
           
            <div class="col-md-6">
                <x-forms.text class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('recruit::modules.sourceSetting.source')"
                              fieldName="source" fieldId="source"
                              :placeholder="__('')"
                              fieldRequired="true"/>
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
        var url = "{{ route('source-setting.store') }}";
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
</script>
