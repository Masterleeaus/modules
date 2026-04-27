<div class="modal-header">
    <h5 class="modal-title" id="modelHeading">@lang('recruit::app.menu.add') @lang('recruit::app.menu.skill') </h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">×</span></button>
</div>
<div class="modal-body" id="skill-table">
    <x-table class="table-bordered" headType="thead-light">
        <x-slot name="thead">
            <th>#</th>
            <th class="w-75">@lang('recruit::app.menu.skill')</th>
            <th class="text-right">@lang('app.action')</th>
        </x-slot>
        @forelse($skills as $key=>$item)
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

    <x-form id="createSkill">
        <div class="row border-top-grey ">
            <div class="col-sm-12">
                <input type="hidden" value="{{ $selectedSkills }}" name="selectedSkills">

                <x-forms.text fieldId="names" :fieldLabel="__('recruit::app.menu.skills')" fieldName="names"
                              fieldRequired="true"
                              :fieldPlaceholder="__('recruit::modules.skill.skillname')">
                </x-forms.text>
            </div>
        </div>
    </x-form>
</div>
<div class="modal-footer">
    <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.close')</x-forms.button-cancel>
    <x-forms.button-primary id="save-skill" icon="check">@lang('app.save')</x-forms.button-primary>
</div>

<script>
    $('body').on('click', '.delete-row', function () {


        var id = $(this).data('row-id');
        var url = "{{ route('job-skills.destroy', ':id') }}";
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
                            $('#selectEmployeeData').html(response.data);
                            $('#selectEmployeeData').selectpicker('refresh');
                        }
                    }
                });
            }
        });

    });

    $('body').off('click', "#save-skill").on('click', '#save-skill', function () {
        var url = "{{ route('job-skills.storeSkill') }}";
        $.easyAjax({
            url: url,
            container: '#createSkill',
            type: "POST",
            data: $('#createSkill').serialize(),
            disableButton: true,
            blockUI: true,
            buttonSelector: "#save-skill",
            success: function (response) {
                if (response.status == 'success') {
                    if (response.status == 'success') {
                        $('#selectEmployeeData').html(response.data);
                        $('#selectEmployeeData').selectpicker('refresh');
                        $(MODAL_LG).modal('hide');
                    }
                }
            }
        })
    });


    $('#skill-table [contenteditable=true]').focus(function () {
        $(this).data("initialText", $(this).html());
        let rowId = $(this).data('row-id');
    }).blur(function () {
        // ...if content is different...
        if ($(this).data("initialText") !== $(this).html()) {
            let id = $(this).data('row-id');
            let value = $(this).html();
            var url = "{{ route('job-skills.updateSkill', ':id') }}";
            url = url.replace(':id', id);
            var token = "{{ csrf_token() }}";
            var selectedSkills = "{{ $selectedSkills }}";
            $.easyAjax({
                url: url,
                container: '#row-' + id,
                type: "POST",
                data: {
                    'name': value,
                    '_token': token,
                    'selectedSkills': selectedSkills
                },
                blockUI: true,
                success: function (response) {
                    if (response.status == 'success') {
                        $('#selectEmployeeData').html(response.data);
                        $('#selectEmployeeData').selectpicker('refresh');
                    }
                }
            })
        }
    });
</script>
