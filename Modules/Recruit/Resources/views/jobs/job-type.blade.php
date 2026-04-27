<div class="modal-header">
    <h5 class="modal-title" id="modelHeading">@lang('recruit::app.menu.jobType')</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">×</span></button>
</div>
<div class="modal-body" id="job-type-table">
    <x-table class="table-bordered" headType="thead-light">
        <x-slot name="thead">
            <th>#</th>
            <th class="w-75">@lang('recruit::app.menu.jobType')</th>
            <th class="text-right">@lang('app.action')</th>
        </x-slot>

        @forelse($jobTypes as $key=>$jobType)
            <tr id="row-{{ $jobType->id }}">
                <td>{{ $key + 1 }}</td>
                <td data-row-id="{{ $jobType->id }}" contenteditable="true">{{ $jobType->job_type }}</td>
                <td class="text-right">
                    <x-forms.button-secondary data-row-id="{{ $jobType->id }}" icon="trash" class="delete-row">
                        @lang('app.delete')</x-forms.button-secondary>
            </tr>
        @empty
            <x-cards.no-record-found-list />
        @endforelse
    </x-table>

    <x-form id="createProjectCategory">
        <div class="row border-top-grey ">
            <div class="col-sm-12">
                <x-forms.text fieldId="department_name" :fieldLabel="__('app.name')" fieldName="job_type"
                              fieldRequired="true" :fieldPlaceholder="__('recruit::modules.message.fullTime')">
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
    $('body').on('click', '.delete-row', function () {

        var id = $(this).data('row-id');
        var url = "{{ route('job-type.destroy', ':id') }}";
        url = url.replace(':id', id);

        var token = "{{ csrf_token() }}";

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
                $.easyAjax({
                    type: 'POST',
                    url: url,
                    data: {
                        '_token': token,
                        '_method': 'DELETE'
                    },
                    success: function (response) {
                        if (response.status == "success") {
                            $('#row-' + id).fadeOut();
                            $('#job-type').html(response.data);
                            $('#job-type').selectpicker('refresh');
                        }
                    }
                });
            }
        });

    });

    $('body').off('click', "#save-category").on('click', '#save-category', function () {
        var url = "{{ route('job-type.store') }}";
        $.easyAjax({
            url: url,
            container: '#createProjectCategory',
            type: "POST",
            data: $('#createProjectCategory').serialize(),
            disableButton: true,
            blockUI: true,
            buttonSelector: "#save-category",
            success: function (response) {
                if (response.status == 'success') {
                    if (response.status == 'success') {
                        $('#job-type').html(response.data);
                        $('#job-type').selectpicker('refresh');
                        $(MODAL_LG).modal('hide');
                    }
                }
            }
        })
    });
    $('#job-type-table [contenteditable=true]').focus(function () {
        $(this).data("initialText", $(this).html());
        let rowId = $(this).data('row-id');
    }).blur(function () {
        // ...if content is different...
        if ($(this).data("initialText") !== $(this).html()) {
            let id = $(this).data('row-id');
            let value = $(this).html();

            var url = "{{ route('job-type.update', ':id') }}";
            url = url.replace(':id', id);

            var token = "{{ csrf_token() }}";

            $.easyAjax({
                url: url,
                container: '#row-' + id,
                type: "POST",
                data: {
                    'job_type': value,
                    '_token': token,
                    '_method': 'PUT'
                },
                blockUI: true,
                success: function (response) {
                    if (response.status == 'success') {
                        $('#job-type').html(response.data);
                        $('#job-type').selectpicker('refresh');
                    }
                }
            })
        }
    });


</script>
