<div class="modal-header">
    <h5 class="modal-title" id="modelHeading">@lang('recruit::modules.jobApplication.remarks')</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">×</span></button>
</div>
<div class="modal-body">
    <x-form id="addRejectionRemark">
        <input type="hidden" name="applicationID" value="{{ $applicationID }}">
        <input type="hidden" name="board" value="{{ $board }}">
        <div class="row">
            <div class="col-sm-12">
                <x-forms.text fieldId="remark" :fieldLabel="__('recruit::modules.jobApplication.rejectReason')"
                              fieldName="remark"
                              fieldRequired="true"
                              :fieldPlaceholder="__('app.add'). ' ' . __('recruit::modules.jobApplication.rejectReason')">
                </x-forms.text>
            </div>
        </div>
    </x-form>
</div>
<div class="modal-footer">
    <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.close')</x-forms.button-cancel>
    <x-forms.button-primary id="save-rejection-remark" icon="check">@lang('app.save')</x-forms.button-primary>
</div>
<script>

    $('body').on('click', '#save-rejection-remark', function () {
        var url = "{{ route('job-appboard.rejected_remark_store', $applicationID) }}";
        $.easyAjax({
            url: url,
            container: '#addRejectionRemark',
            type: "POST",
            data: $('#addRejectionRemark').serialize(),
            disableButton: true,
            blockUI: true,
            buttonSelector: "#save-rejection-remark",
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
