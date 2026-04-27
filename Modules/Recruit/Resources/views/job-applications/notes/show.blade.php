@php
    $addApplicationNotePermission = user()->permission('add_notes');
    $editApplicationNotePermission = user()->permission('edit_notes');
    $deleteApplicationNotePermission = user()->permission('delete_notes');
@endphp

@forelse ($comments as $comment)
    <div class="card w-100 rounded-0 border-0 comment">
        <div class="card-horizontal">

            <div class="card-body border-0 pl-0 py-1">
                <div class="d-flex flex-grow-1">
                    <h4 class="card-title f-15 f-w-500 text-dark mr-3">{{ $comment->user->name }}</h4>
                    <p class="card-date f-11 text-lightest mb-0">
                        {{ ($comment->created_at->diffForHumans()) }}
                    </p>
                    <div class="dropdown ml-auto comment-action">
                        <button class="btn btn-lg f-14 p-0 text-lightest  rounded  dropdown-toggle"
                                type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-ellipsis-h"></i>
                        </button>

                        <div class="dropdown-menu dropdown-menu-right border-grey rounded b-shadow-4 p-0"
                             aria-labelledby="dropdownMenuLink" tabindex="0">
                            @if ($editApplicationNotePermission == 'all' || ($editApplicationNotePermission == 'added' && $comment->user_id == user()->id))
                                <a class="cursor-pointer d-block text-dark-grey f-13 py-3 px-3 edit-comment"
                                   href="javascript:;" data-row-id="{{ $comment->id }}">@lang('app.edit')</a>
                            @endif
                            @if ($deleteApplicationNotePermission == 'all' || ($deleteApplicationNotePermission == 'added' && $comment->user_id == user()->id))
                                <a class="cursor-pointer d-block text-dark-grey f-13 pb-3 px-3 delete-comment"
                                   data-row-id="{{ $comment->id }}" href="javascript:;">@lang('app.delete')</a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-text f-14 text-dark-grey text-justify ql-editor">{!! $comment->note_text !!}
                </div>
            </div>
        </div>
    </div>
@empty
    <x-cards.no-record :message="__('messages.noCommentFound')" icon="comment-alt"/>
@endforelse

<script>
    $(".edit-comment").click(function() {
        var id = $(this).data('row-id');
        var url = "{{ route('applicant-note.edit', ':id') }}";
        url = url.replace(':id', id);
        $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
        $.ajaxModal(MODAL_LG, url);
    });
</script>
