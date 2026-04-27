@php($pageTitle = $pageTitle ?? __('accountings::app.menu.accounting'))
<div class="table-responsive p-20">
    <x-table class="table-bordered">
        <x-slot name="thead">
            <th>#</th>
            <th>@lang('accountings::app.menu.seq')</th>
            <th>@lang('accountings::app.menu.bsName')</th>
            <th>@lang('accountings::app.menu.bsType')</th>
            <th>@lang('accountings::app.menu.bsGroup')</th>
            <th class="text-right">@lang('app.action')</th>
        </x-slot>

        @forelse($bs as $key => $balancesheet)
            <tr id="cat-{{ $balancesheet->id }}">
                <td>{{ $key + 1 }}</td>
                <td data-row-id="{{ $balancesheet->id }}" contenteditable="true" class="edit-seq">
                    {{ mb_ucwords($balancesheet->seq) }}</td>

                <td data-row-id="{{ $balancesheet->id }}" contenteditable="true" class="edit-name">
                    {{ mb_ucwords($balancesheet->bs_name) }}</td>
                <td>
                    <select class="form-control select-picker height-35 f-14" name="bsType" id="edit_type"
                        data-live-search="true" data-row-id="{{ $balancesheet->id }}">
                        <option value="{{ $balancesheet->bs_type }}"> {{ $balancesheet->bs_type }}</option>
                        <option value="aktiva"> --</option>
                        <option value="aktiva"> Aktiva</option>
                        <option value="passiva"> Passiva</option>
                        <option value="capital"> Capital</option>
                    </select>
                </td>
                <td>
                    <select class="form-control select-picker height-35 f-14" name="bsType" id="edit_group"
                        data-live-search="true" data-row-id="{{ $balancesheet->id }}">
                        <option value="{{ $balancesheet->bs_group }}"> {{ $balancesheet->bs_group }}</option>
                        <option value="aktiva"> --</option>
                        <option value="current-assets"> Current Asset</option>
                        <option value="fixed-assets"> Fixed Asset</option>
                        <option value="intangible-assets"> Intangible Asset</option>
                        <option value="current-liabilities"> Current Liabilities</option>
                        <option value="long-term-liabilities"> Long Term Liabilities</option>
                        <option value="equity"> Equity</option>
                    </select>
                </td>
                <td class="text-right">
                    <x-forms.button-secondary data-cat-id="{{ $balancesheet->id }}" icon="trash"
                        class="delete-category">
                        @lang('app.delete')
                    </x-forms.button-secondary>
                </td>
            </tr>
        @empty
            <x-cards.no-record-found-list colspan="5" />
        @endforelse
    </x-table>
</div>

<script>
    $('.delete-category').click(function() {
        const id = $(this).data('cat-id');
        let url = "{{ route('balance-sheet.destroy', ':id') }}";
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
                    success: function(response) {
                        if (response.status == "success") {
                            $('#cat-' + id).fadeOut();
                        }
                    }
                });
            }
        });

    });

    $('.edit-seq').focus(function() {
        $(this).data("initialText", $(this).html());
    }).blur(function() {
        if ($(this).data("initialText") !== $(this).html()) {
            let id = $(this).data('row-id');
            let value = $(this).html();
            let url = "{{ route('balance-sheet.update', ':id') }}";
            url = url.replace(':id', id);

            const token = "{{ csrf_token() }}";
            $.easyAjax({
                url: url,
                container: '#row-' + id,
                type: "POST",
                data: {
                    'seq': value,
                    '_token': token,
                    '_method': 'PUT'
                },
                blockUI: true,
            })
        }
    });

    $('.edit-name').focus(function() {
        $(this).data("initialText", $(this).html());
    }).blur(function() {
        if ($(this).data("initialText") !== $(this).html()) {
            let id = $(this).data('row-id');
            let value = $(this).html();
            let url = "{{ route('balance-sheet.update', ':id') }}";
            url = url.replace(':id', id);

            const token = "{{ csrf_token() }}";
            $.easyAjax({
                url: url,
                container: '#row-' + id,
                type: "POST",
                data: {
                    'bs_name': value,
                    '_token': token,
                    '_method': 'PUT'
                },
                blockUI: true,
            })
        }
    });

    $(document).on('change', '#edit_type', function() {
        const id = $(this).data('row-id');
        const categoryId = $(this).val();
        if (id == undefined) {
            return false;
        }
        let url = "{{ route('balance-sheet.update', ':id') }}";
        url = url.replace(':id', id);
        const token = "{{ csrf_token() }}";
        $.easyAjax({
            url: url,
            type: "POST",
            data: {
                'bs_type': categoryId,
                '_token': token,
                '_method': 'PUT'
            },
            blockUI: true,
        })
    });

    $(document).on('change', '#edit_group', function() {
        const id = $(this).data('row-id');
        const categoryId = $(this).val();
        if (id == undefined) {
            return false;
        }
        let url = "{{ route('balance-sheet.update', ':id') }}";
        url = url.replace(':id', id);
        const token = "{{ csrf_token() }}";
        $.easyAjax({
            url: url,
            type: "POST",
            data: {
                'bs_group': categoryId,
                '_token': token,
                '_method': 'PUT'
            },
            blockUI: true,
        })
    });
</script>
