<div class="modal-header">
    <h5 class="modal-title" id="modelHeading">@lang('recruit::modules.jobApplication.applicantNotes')</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">×</span></button>
</div>
<div class="modal-body">

    <x-form id="edit-comment-data-form" method="PUT">
        <input type="hidden" name="applicationId" value="{{ $note->recruit_job_application_id }}">
        <div class="row">
            <div class="col-md-12 p-20 ">
                <div class="media">
                    <div class="media-body bg-white">
                        <div class="form-group">
                            <x-forms.textarea class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('app.add'). ' ' . __('app.note')"
                                    fieldName="note" fieldId="note-text" fieldPlaceholder="" fieldValue="{{$note->note_text}}">
                                </x-forms.textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-form>
</div>
<div class="modal-footer">
    <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.cancel')</x-forms.button-cancel>
    <x-forms.button-primary id="save-edit-comment" icon="check">@lang('app.save')</x-forms.button-primary>
</div>

<script>
    $(document).ready(function () {

        $('body').off('click', "#save-edit-comment").on('click', '#save-edit-comment', function () {

            const url = "{{ route('applicant-note.update', $note->id) }}";

            $.easyAjax({
                url: url,
                container: '#edit-comment-data-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                buttonSelector: "#save-edit-comment",
                data: $('#edit-comment-data-form').serialize(),
                success: function (response) {
                    if (response.status == "success") {
                        document.getElementById('comment-list').innerHTML = response.view;
                        $(MODAL_LG).modal('hide');

                        $(".edit-comment").click(function() {
                            var id = $(this).data('row-id');
                            var url = "{{ route('applicant-note.edit', ':id') }}";
                            url = url.replace(':id', id);
                            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
                            $.ajaxModal(MODAL_LG, url);
                        });
                    }

                }
            });
        });
    });

</script>
