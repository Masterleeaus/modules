<link rel="stylesheet" href="{{ asset('vendor/css/dropzone.min.css') }}">

<div class="modal-header">
    <h5 class="modal-title" id="modelHeading">@lang('purchase::modules.inventory.addFiles')</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">×</span></button>
</div>
<div class="modal-body">
    <div class="col-lg-12">
        <x-forms.file-multiple class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('purchase::modules.inventory.addFiles')"
            fieldName="file" fieldId="file-upload-dropzone" :fieldRequired="true" />
        <input type="hidden" name="image_url" id="image_url">
    </div>
</div>
<div class="modal-footer">
    <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.cancel')</x-forms.button-cancel>
    <x-forms.button-primary id="save-files" disabled icon="check">@lang('app.save')</x-forms.button-primary>
</div>

<script src="{{ asset('vendor/jquery/dropzone.min.js') }}"></script>

<script>
    $(document).ready(function() {

        let inventoryId = '{{ $inventoryId }}';
        let url = "{{ route('inventory-files.store') }}?inventory_id=" + inventoryId;

        Dropzone.autoDiscover = false;
        //Dropzone class
        inventoryDropzone = new Dropzone("div#file-upload-dropzone", {
            dictDefaultMessage: "{{ __('app.dragDrop') }}",
            url: url,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            paramName: "file",
            maxFilesize: DROPZONE_MAX_FILESIZE,
            maxFiles: 10,
            autoProcessQueue: false,
            uploadMultiple: true,
            addRemoveLinks: true,
            parallelUploads: 10,
            acceptedFiles: DROPZONE_FILE_ALLOW,
            init: function() {
                inventoryDropzone = this;
            }
        });
        inventoryDropzone.on('sending', function(file, xhr, formData) {
            formData.append('lead_id', $('#add-files').data('lead-id'));
            $.easyBlockUI();
        });
        inventoryDropzone.on('uploadprogress', function() {
            $.easyBlockUI();
        });
        inventoryDropzone.on('completemultiple', function() {
            var msgs = "@lang('messages.recordSaved')";
            inventoryFilesView(fileLayout);
            $(MODAL_LG).modal('hide');
        });
        inventoryDropzone.on('addedfile', function() {
            $('#save-files').prop("disabled", false);
        });
    });

    $('#save-files').click(function() {
        inventoryDropzone.processQueue();
    });

</script>
