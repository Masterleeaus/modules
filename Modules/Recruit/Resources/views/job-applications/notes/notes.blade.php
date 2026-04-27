@php
    $addApplicationNotePermission = user()->permission('add_notes');
    $editApplicationNotePermission = user()->permission('edit_notes');
    $deleteApplicationNotePermission = user()->permission('delete_notes');
@endphp
<!-- TAB CONTENT START -->
<div class="tab-pane fade show active" role="tabpanel" aria-labelledby="nav-email-tab">
    <div class="row p-20">
        <div class="col-md-12">
            @if ($addApplicationNotePermission == 'all' || ($addApplicationNotePermission == 'owned' && user()->id == $application->job->recruiter_id))
                <a class="f-15 f-w-500" href="javascript:;" id="add-notes"><i
                        class="icons icon-plus font-weight-bold mr-1"></i>@lang('app.add')
                    @lang('app.note')</a>
            @endif
            <x-form id="save-note-data-form">
                <input type="hidden" name="applicationId" value="{{ $application->id }}">
                <div class="col-md-12">
                    <div class="media">
                        <div class="media-body bg-white">
                            <div class="form-group">
                                <x-forms.textarea class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('app.add'). ' ' . __('app.note')"
                                    fieldName="note" fieldId="note-text" fieldPlaceholder="">
                                </x-forms.textarea>
                            </div>
                        </div>
                    </div>
                    <div class="w-100 justify-content-end d-flex mt-2">
                        <x-forms.button-cancel id="cancel-note" class="border-0 mr-3">@lang('app.cancel')
                        </x-forms.button-cancel>
                        <x-forms.button-primary id="submit-note" icon="location-arrow">@lang('app.submit')
                            </x-button-primary>
                    </div>
                </div>
            </x-form>
        </div>
    </div>

    <div class="d-flex flex-wrap justify-content-between p-20" id="comment-list">
        @forelse ($application->comments as $comment)
            <div class="card w-100 rounded-0 border-0 comment">
                <div class="card-horizontal">
                    <div class="card-body border-0 pl-0 py-1">
                        <div class="d-flex flex-grow-1">
                            <h4 class="card-title f-15 f-w-500 text-dark mr-3">{{$comment->user->name}}</h4>
                            <p class="card-date f-11 text-lightest mb-0">
                                {{ ($comment->created_at->diffForHumans()) }}
                            </p>
                            <div class="dropdown ml-auto comment-action">
                                <button
                                    class="btn btn-lg f-14 p-0 text-lightest  rounded  dropdown-toggle"
                                    type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-ellipsis-h"></i>
                                </button>

                                <div class="dropdown-menu dropdown-menu-right border-grey rounded b-shadow-4 p-0"
                                     aria-labelledby="dropdownMenuLink" tabindex="0">
                                    @if  ($editApplicationNotePermission == 'all'
                                    || ($editApplicationNotePermission == 'added' && $comment->user_id == user()->id)
                                    || ($editApplicationNotePermission == 'owned' && user()->id ==  $application->job->recruiter_id)
                                    || ($editApplicationNotePermission == 'both' && user()->id ==  $application->job->recruiter_id)
                                    || $comment->user_id == user()->id)
                                        <a class="cursor-pointer d-block text-dark-grey f-13 py-3 px-3 edit-comment"
                                           href="javascript:;" data-row-id="{{ $comment->id }}">@lang('app.edit')</a>
                                    @endif

                                    @if  ($deleteApplicationNotePermission == 'all'
                                    || ($deleteApplicationNotePermission == 'added' && $comment->user_id == user()->id)
                                    || ($deleteApplicationNotePermission == 'owned' && user()->id ==  $application->job->recruiter_id)
                                    || ($deleteApplicationNotePermission == 'both' && user()->id ==  $application->job->recruiter_id)
                                    || $comment->user_id == user()->id)
                                        <a class="cursor-pointer d-block text-dark-grey f-13 pb-3 px-3 delete-comment"
                                           data-row-id="{{ $comment->id }}" href="javascript:;">@lang('app.delete')</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div
                            class="card-text f-14 text-dark-grey text-justify ql-editor">{!! $comment->note_text !!}</div>
                    </div>
                </div>
            </div>
        @empty
            <div class="align-items-center d-flex flex-column text-lightest p-20 w-100">
                <i class="fa fa-comment-alt f-21 w-100"></i>

                <div class="f-15 mt-4">
                    - @lang('recruit::modules.jobApplication.noNotesFound') -
                </div>
            </div>
        @endforelse
    </div>
</div>
<!-- TAB CONTENT END -->

<script>

    $("#save-note-data-form").hide();
    $('body').on('click', '#add-notes', function () {
        $("#save-note-data-form").show();
        $("#add-notes").hide();
    });

    $('body').on('click', '#cancel-note', function () {
        $("#save-note-data-form").hide();
        $("#add-notes").show();
    });

    $(document).ready(function () {

        $("#submit-note").click(function() {

            // var token = '{{ csrf_token() }}';

            const url = "{{ route('applicant-note.store') }}";

            $.easyAjax({
                url: url,
                container: '#save-note-data-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                buttonSelector: "#submit-note",
                data: $('#save-note-data-form').serialize(),
                success: function (response) {
                    if (response.status == "success") {
                        $('#comment-list').html(response.view);
                        $('#note-text').val('');
                    }
                }
            });
        });

    });

    $(".edit-comment").click(function() {
        var id = $(this).data('row-id');
        var url = "{{ route('applicant-note.edit', ':id') }}";
        url = url.replace(':id', id);
        $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
        $.ajaxModal(MODAL_LG, url);
    });

    $('body').on('click', '.delete-comment', function () {
        var id = $(this).data('row-id');
        Swal.fire({
            title: "@lang('messages.sweetAlertTitle')",
            text: "@lang('messages.recoverRecord')",
            icon: 'warning',
            showCancelButton: true,
            focusConfirm: false,
            confirmButtonText: "@lang('messages.confirmDelete')",
            cancelButtonText: "@lang('app.cancel')",
            customClass: {
                confirmButton: 'btn btn-primary mr-3',
                cancelButton: 'btn btn-secondary'
            },
            showClass: {
                popup: 'swal2-noanimation',
                backdrop: 'swal2-noanimation'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                var url = "{{ route('applicant-note.destroy', ':id') }}";
                url = url.replace(':id', id);

                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                    url: url,
                    data: {
                        '_token': token,
                        '_method': 'DELETE'
                    },
                    success: function (response) {
                        if (response.status == "success") {
                            $('#comment-list').html(response.view);
                        }
                    }
                });
            }
        });
    });

</script>
