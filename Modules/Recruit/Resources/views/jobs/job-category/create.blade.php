<div class="modal-header">
    <h5 class="modal-title"
        id="modelHeading">@lang('recruit::app.menu.add') @lang('recruit::modules.job.job') @lang('app.category') </h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">×</span></button>
</div>
<div class="modal-body" id="category-table">
    <x-table class="table-bordered" headType="thead-light">
        <x-slot name="thead">
            <th>#</th>
            <th class="w-75">@lang('app.category') @lang('app.name')</th>
            <th class="text-right">@lang('app.action')</th>
        </x-slot>
        @forelse($categories as $key=>$item)
            <tr id="row-{{ $item->id }}">
                <td>{{ $key + 1 }}</td>
                <td data-row-id="{{ $item->id }}" contenteditable="true">{{ $item->category_name }}</td>
                <td class="text-right">
                    @if ($deletePermission == 'all' || $deletePermission == 'added')
                        <x-forms.button-secondary data-row-id="{{ $item->id }}" icon="trash" class="delete-category">
                            @lang('app.delete')</x-forms.button-secondary>
                @endif
            </tr>
        @empty
            <x-cards.no-record-found-list />
        @endforelse
    </x-table>

    <x-form id="jobCategoryForm">
        <div class="row border-top-grey ">
            <div class="col-sm-12">

                <x-forms.text fieldId="category_name" :fieldLabel="__('app.category')" fieldName="category_name"
                              fieldRequired="true"
                              :fieldPlaceholder="__('app.category') . ' ' . __('app.name') ">
                </x-forms.text>
            </div>
        </div>
    </x-form>
</div>
<div class="modal-footer">
    <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.close')</x-forms.button-cancel>
    <x-forms.button-primary id="save-form" icon="check">@lang('app.save')</x-forms.button-primary>
</div>

<script>

    $('.delete-category').click(function () {

        var id = $(this).data('row-id');
        var url = "{{ route('job-category.destroy', ':id') }}";
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
                            var options = [];
                            var rData = [];
                            rData = response.data;
                            $.each(rData, function (index, value) {
                                var selectData = '';
                                selectData = '<option value="' + value.id + '">' +
                                    value
                                        .category_name + '</option>';
                                options.push(selectData);
                            });

                            $('#category_id').html('<option value="">--</option>' +
                                options);
                            $('#category_id').selectpicker('refresh');

                            $('#sub_category_id').html('<option value="">--</option>');
                            $('#sub_category_id').selectpicker('refresh');
                        }
                    }
                });
            }
        });
    });

    $('#save-form').click(function () {
        var url = "{{ route('job-category.store') }}";
        $.easyAjax({
            url: url,
            container: '#jobCategoryForm',
            type: "POST",
            data: $('#jobCategoryForm').serialize(),
            success: function (response) {
                if (response.status == 'success') {
                    var options = [];
                    var rData = [];
                    rData = response.data;
                    $.each(rData, function (index, value) {
                        var selectData = '';
                        selectData = '<option value="' + value.id + '">' + value
                            .category_name + '</option>';
                        options.push(selectData);
                    });

                    $('#category_id').html('<option value="">--</option>' + options);
                    $('#category_id').selectpicker('refresh');

                    $('#sub_category_id').html('<option value="">--</option>');
                    $('#sub_category_id').selectpicker('refresh');
                    $(MODAL_LG).modal('hide');
                }
            }
        })
    });

    $('#category-table [contenteditable=true]').focus(function () {
        $(this).data("initialText", $(this).html());
        let rowId = $(this).data('row-id');
    }).blur(function () {
        // ...if content is different...
        if ($(this).data("initialText") !== $(this).html()) {
            let id = $(this).data('row-id');
            let value = $(this).html();

            var url = "{{ route('job-category.update', ':id') }}";
            url = url.replace(':id', id);

            var token = "{{ csrf_token() }}";

            $.easyAjax({
                url: url,
                container: '#row-' + id,
                type: "POST",
                data: {
                    'category_name': value,
                    '_token': token,
                    '_method': 'PUT'
                },
                blockUI: true,
                success: function (response) {
                    if (response.status == 'success') {
                        var options = [];
                        var rData = [];
                        rData = response.data;
                        $.each(rData, function (index, value) {
                            var selectData = '';
                            selectData = '<option value="' + value.id + '">' + value
                                .category_name + '</option>';
                            options.push(selectData);
                        });

                        $('#category_id').html('<option value="">--</option>' + options);
                        $('#category_id').selectpicker('refresh');

                        $('#sub_category_id').html('<option value="">--</option>');
                        $('#sub_category_id').selectpicker('refresh');
                    }
                }
            })
        }
    });

</script>
