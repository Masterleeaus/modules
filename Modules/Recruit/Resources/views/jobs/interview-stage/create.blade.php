<div class="modal-header">
    <h5 class="modal-title"
        id="modelHeading">@lang('recruit::app.menu.add') @lang('recruit::app.interviewSchedule.stages') </h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">×</span></button>
</div>
<div class="modal-body" id="stage-table">
    <x-table class="table-bordered" headType="thead-light">
        <x-slot name="thead">
            <th>#</th>
            <th class="w-75">@lang('recruit::app.interviewSchedule.stages')</th>
            <th class="text-right">@lang('app.action')</th>
        </x-slot>
        @forelse($stages as $key=>$item)
            <tr id="row-{{ $item->id }}">
                <td>{{ $key + 1 }}</td>
                <td data-row-id="{{ $item->id }}" contenteditable="true">{{ $item->name }}</td>
                <td class="text-right">
                    <x-forms.button-secondary data-row-id="{{ $item->id }}" icon="trash" class="delete-row">
                        @lang('app.delete')</x-forms.button-secondary>
            </tr>
        @empty
            <x-cards.no-record-found-list />
        @endforelse
    </x-table>

    <x-form id="createStages">
        <div class="row border-top-grey ">
            <div class="col-sm-12">
                <input type="hidden" value="{{ $selectedStages }}" name="selectedStages">

                <x-forms.text fieldId="name" :fieldLabel="__('recruit::app.interviewSchedule.stages')" fieldName="name"
                              fieldRequired="true"
                              :fieldPlaceholder="__('recruit::app.interviewSchedule.stages') . ' ' . __('app.name') ">
                </x-forms.text>
            </div>
        </div>
    </x-form>
</div>
<div class="modal-footer">
    <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.close')</x-forms.button-cancel>
    <x-forms.button-primary id="save-stage" icon="check">@lang('app.save')</x-forms.button-primary>
</div>

<script>

    $('body').on('click', '.delete-row', function () {

        const id = $(this).data('row-id');
        let url = "{{ route('interview-stages.destroy', ':id') }}";
        url = url.replace(':id', id);

        const token = "{{ csrf_token() }}";

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
                            $('#selectStages').html(response.data);
                            $('#selectStages').selectpicker('refresh');
                        }
                    }
                });
            }
        });
    });

    $('body').off('click', "#save-stage").on('click', '#save-stage', function () {

        const url = "{{ route('interview-stages.store') }}";
        $.easyAjax({
            url: url,
            container: '#createStages',
            type: "POST",
            data: $('#createStages').serialize(),
            disableButton: true,
            blockUI: true,
            buttonSelector: "#save-stage",
            success: function (response) {
                if (response.status == 'success') {
                    $('#selectStages').html(response.data);
                    $('#selectStages').selectpicker('refresh');
                    $(MODAL_LG).modal('hide');
                }
            }
        })
    });

    $('#stage-table [contenteditable=true]').focus(function () {
        $(this).data("initialText", $(this).html());
    }).blur(function () {
        // ...if content is different...
        if ($(this).data("initialText") !== $(this).html()) {
            let id = $(this).data('row-id');
            let value = $(this).html();
            let url = "{{ route('interview-stages.update', ':id') }}";
            url = url.replace(':id', id);
            const token = "{{ csrf_token() }}";
            const selectedStages = "{{ $selectedStages }}";
            $.easyAjax({
                url: url,
                container: '#row-' + id,
                type: "PUT",
                data: {
                    'name': value,
                    '_token': token,
                    'selectedStages': selectedStages
                },
                blockUI: true,
                success: function (response) {
                    if (response.status === 'success') {
                        $('#selectStages').html(response.data);
                        $('#selectStages').selectpicker('refresh');
                    }
                }
            })
        }
    });

</script>
