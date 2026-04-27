<div class="modal-header">
    <h5 class="modal-title" id="modelHeading">@lang('recruit::modules.jobApplication.remarks')</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">×</span></button>
</div>
<div class="modal-body">
    <x-form id="addRemark">
        <input type="hidden" name="applicationID" value="{{ $applicationID }}">
        <input type="hidden" name="board" value="{{ $board }}">
        <div class="row">
            <div class="col-sm-12">
                <x-forms.text fieldId="remark" :fieldLabel="__('recruit::modules.jobApplication.remarks')"
                              fieldName="remark"
                              fieldRequired="true"
                              :fieldPlaceholder="__('app.add'). ' ' . __('recruit::modules.jobApplication.remarks')">
                </x-forms.text>
            </div>
        </div>
    </x-form>
</div>
<div class="modal-footer">
    <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.close')</x-forms.button-cancel>
    <x-forms.button-primary id="save-category" icon="check">@lang('app.save')</x-forms.button-primary>
</div>
<script>

    $('body').on('click', '#save-category', function () {
        const board = {{ $board }};
        var url = "{{ route('job-appboard.application_remark_store', $applicationID) }}";
        $.easyAjax({
            url: url,
            container: '#addRemark',
            type: "POST",
            data: $('#addRemark').serialize(),
            disableButton: true,
            blockUI: true,
            buttonSelector: "#save-category",
            success: function (response) {
                if (response.status == 'success') {
                    $(MODAL_DEFAULT).modal('hide');
                    if(response.board == 0){
                        showTable();
                    } else{
                        loadData();
                    }
                }
            }
        })
    });

</script>
