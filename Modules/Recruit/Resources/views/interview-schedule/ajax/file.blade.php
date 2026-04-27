@php
    $addFilePermission = user()->permission('add_interview_schedule');
    $viewFilePermission = user()->permission('view_interview_schedule');
    $deleteFilePermission = user()->permission('delete_interview_schedule');
@endphp
<link rel="stylesheet" href="{{ asset('vendor/css/dropzone.min.css') }}">
<style>
    .file-action {
        visibility: hidden;
    }

    .file-card:hover .file-action {
        visibility: visible;
    }

</style>

<!-- TAB CONTENT START -->
<div class="tab-pane fade show active" role="tabpanel" aria-labelledby="nav-email-tab">
    <div class="p-20">
        <div class="row">
            <div class="col-md-12">
                @if ($addFilePermission == 'all' || $addFilePermission == 'added')
                    <a class="f-15 f-w-500" href="javascript:;" id="add-interview-file"><i
                            class="icons icon-plus font-weight-bold mr-1"></i>@lang('modules.projects.uploadFile')</a>
                @endif
            </div>
        </div>

        <x-form id="save-interviewfile-data-form" class="d-none">
            <input type="hidden" name="interview_id" value="{{ $interview->id }}">
            <div class="row">
                <div class="col-md-12">
                    <x-forms.file-multiple fieldLabel="" fieldName="file[]" fieldId="interview-file-upload-dropzone"/>
                </div>
                <div class="col-md-12">
                    <div class="w-100 justify-content-end d-flex mt-2">
                        <x-forms.button-cancel id="cancel-interviewfile" class="border-0">@lang('app.cancel')
                        </x-forms.button-cancel>
                    </div>
                </div>
            </div>
        </x-form>
    </div>
    <div class="d-flex flex-wrap p-20" id="interview-file-list">
        @forelse($interview->files as $file)
            <x-file-card :fileName="$file->filename" :dateAdded="$file->created_at->diffForHumans()">
                @if ($file->icon == 'images')
                    <img src="{{ $file->file_url }}">
                @else
                    <i class="fa {{ $file->icon }} text-lightest"></i>
                @endif

                <x-slot name="action">
                    <div class="dropdown ml-auto file-action">
                        <button class="btn btn-lg f-14 p-0 text-lightest  rounded  dropdown-toggle"
                                type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-ellipsis-h"></i>
                        </button>

                        <div class="dropdown-menu dropdown-menu-right border-grey rounded b-shadow-4 p-0"
                             aria-labelledby="dropdownMenuLink" tabindex="0">
                            @if ($viewFilePermission == 'all' || ($viewFilePermission == 'added' && $file->added_by == user()->id))
                                @if ($file->icon != 'images')
                                    <a class="cursor-pointer d-block text-dark-grey f-13 pt-3 px-3 " target="_blank"
                                       href="{{ $file->file_url }}">@lang('app.view')</a>
                                @endif
                                <a class="cursor-pointer d-block text-dark-grey f-13 py-3 px-3 "
                                   href="{{ route('interview_files.download', md5($file->id)) }}">@lang('app.download')</a>
                            @endif

                            @if ($deleteFilePermission == 'all' || ($deleteFilePermission == 'added' && $file->added_by == user()->id))
                                <a class="cursor-pointer d-block text-dark-grey f-13 pb-3 px-3 delete-file"
                                   data-row-id="{{ $file->id }}" href="javascript:;">@lang('app.delete')</a>
                            @endif
                        </div>
                    </div>
                </x-slot>

            </x-file-card>
        @empty
            <x-cards.no-record :message="__('messages.noFileUploaded')" icon="file"/>
        @endforelse

    </div>

</div>
<!-- TAB CONTENT END -->

<script src="{{ asset('vendor/jquery/dropzone.min.js') }}"></script>
<script>
    $(document).ready(function () {

        Dropzone.autoDiscover = false;
        taskDropzone = new Dropzone("div#interview-file-upload-dropzone", {
            dictDefaultMessage: "{{ __('app.dragDrop') }}",
            url: "{{ route('interview-files.store') }}",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            paramName: "file",
            maxFilesize: 10,
            maxFiles: 10,
            uploadMultiple: true,
            addRemoveLinks: true,
            parallelUploads: 10,
            init: function () {
                taskDropzone = this;
            }
        });
        taskDropzone.on('sending', function (file, xhr, formData) {
            var ids = "{{ $interview->id }}";
            formData.append('interview_id', ids);
            $.easyBlockUI();
        });
        taskDropzone.on('uploadprogress', function () {
            $.easyBlockUI();
        });
        taskDropzone.on('completemultiple', function (file) {
            var taskView = JSON.parse(file[0].xhr.response).view;
            taskDropzone.removeAllFiles();
            $.easyUnblockUI();
            $('#interview-file-list').html(taskView);
        });


        $('body').on('click', '#add-interview-file', function () {
            $(this).closest('.row').addClass('d-none');
            $('#save-interviewfile-data-form').removeClass('d-none');
        });

        $('body').on('click', '#cancel-interviewfile', function () {
            $('#save-interviewfile-data-form').addClass('d-none');
            $('#add-interview-file').closest('.row').removeClass('d-none');
            return false;
        });
    });

    $('body').on('click', '.delete-file', function () {
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
                var url = "{{ route('interview-files.destroy', ':id') }}";
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
                            $('#interview-file-list').html(response.view);
                        }
                    }
                });
            }
        });
    });
</script>
