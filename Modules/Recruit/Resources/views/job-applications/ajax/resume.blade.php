@php
    $addPermission = user()->permission('add_job_application');
    $viewPermission = user()->permission('view_job_application');
    $deletePermission = user()->permission('delete_job_application');
    $interviewViewPermission = user()->permission('view_interview_schedule');
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
<div class="tab-pane fade show active show-resume" role="tabpanel" aria-labelledby="nav-email-tab">

    @if ($application->files->count() == 0)
    <div class="p-20">

        <div class="row">
            <div class="col-md-12">
                @if ($addPermission == 'all' || $addPermission == 'added')
                    <a class="f-15 f-w-500" href="javascript:;" id="add-application-file"><i
                            class="icons icon-plus font-weight-bold mr-1"></i>@lang('modules.projects.uploadFile')</a>
                @endif
            </div>
        </div>

        <x-form id="save-applicationfile-data-form" class="d-none">
            <input type="hidden" name="applicationID" value="{{ $application->id }}">
            <div class="row">
                <div class="col-md-12">
                    <x-forms.file-multiple fieldLabel="" fieldName="file[]" fieldId="application-file-upload-dropzone"/>
                </div>
                <div class="col-md-12">
                    <div class="w-100 justify-content-end d-flex mt-2">
                        <x-forms.button-cancel id="cancel-applicationfile" class="border-0">@lang('app.cancel')
                        </x-forms.button-cancel>
                    </div>
                </div>
            </div>
        </x-form>
    </div>
    @endif

    <div class="d-flex flex-wrap p-20" id="application-file-list">
        @forelse($application->files as $file)
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
                            @if ($viewPermission == 'all' || ($viewPermission == 'added' || $interviewViewPermission == 'owned' || $file->added_by == user()->id))
                                    <a class="cursor-pointer d-block text-dark-grey f-13 pt-3 px-3 " target="_blank"
                                       href="{{ $file->file_url }}">@lang('app.view')</a>
                                <a class="cursor-pointer d-block text-dark-grey f-13 py-3 px-3 "
                                   href="{{ route('application-file.download', md5($file->id)) }}">@lang('app.download')</a>
                            @endif
                            @if ($deletePermission == 'all' || ($deletePermission == 'added' && $file->added_by == user()->id))
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
        taskDropzone = new Dropzone("div#application-file-upload-dropzone", {
            dictDefaultMessage: "{{ __('app.dragDrop') }}",
            url: "{{ route('application-file.store') }}",
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
            var ids = "{{ $application->id }}";
            formData.append('applicationID', ids);
            $.easyBlockUI();
        });
        taskDropzone.on('uploadprogress', function () {
            $.easyBlockUI();
        });
        taskDropzone.on('completemultiple', function (file) {
            var taskView = JSON.parse(file[0].xhr.response).view;
            taskDropzone.removeAllFiles();
            $.easyUnblockUI();
            $('#application-file-list').html(taskView);
        });


        $('body').on('click', '#add-application-file', function () {
            $(this).closest('.row').addClass('d-none');
            $('#save-applicationfile-data-form').removeClass('d-none');
        });

        $('body').on('click', '#cancel-applicationfile', function () {
            $('#save-applicationfile-data-form').addClass('d-none');
            $('#add-application-file').closest('.row').removeClass('d-none');
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
                var url = "{{ route('application-file.destroy', ':id') }}";
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
                            $('#application-file-list').html(response.view);
                        }
                    }
                });
            }
        });
    });
</script>

