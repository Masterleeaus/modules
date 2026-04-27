@php($pageTitle = $pageTitle ?? __('accountings::app.menu.accounting'))
<div class="card border-0 invoice">
    <!-- CARD BODY START -->
    <div class="card-body">
        <div class="invoice-table-wrapper">
            <table width="100%">
                <tr class="inv-logo-heading">
                    <td class="font-weight-bold f-21 text-dark text-uppercase mt-4 mt-lg-0 mt-md-0">
                        @lang('accountings::app.menu.journal')</td>
                </tr>
                <tr class="inv-num">
                    <td>
                        <table class="inv-num-date text-dark f-13 mt-3">
                            <tr>
                                <td class="bg-light-grey border-right-0 f-w-500">
                                    @lang('accountings::app.menu.journalNo')</td>
                                <td class="border-left-0">#{{ $invoice->no_journal }}</td>
                            </tr>
                            <tr>
                                <td class="bg-light-grey border-right-0 f-w-500">Date</td>
                                <td class="border-left-0">{{ $invoice->journal_date }}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td height="20"></td>
                </tr>
            </table>
            <table width="100%" class="inv-desc d-none d-lg-table d-md-table">
                <tr>
                    <td colspan="2">
                        <table class="inv-detail f-14 table-responsive-sm" width="100%">
                            <tr class="i-d-heading bg-light-grey text-dark-grey font-weight-bold">
                                <td width="50%" class="border-right-0">
                                    @lang('accountings::app.menu.coa')</td>
                                <td width="25%" class="border-right-0 border-left-0" align="right" id="type">
                                    @lang('accountings::app.menu.debit')</td>
                                <td width="25%" class="border-right-0 border-left-0" align="right">
                                    @lang('accountings::app.menu.credit')
                                </td>
                            </tr>
                            @foreach ($invoice->items as $item)
                                <tr class="text-dark font-weight-semibold f-13 border">
                                    <td>{{ $item->coa->coa }}</td>
                                    <td align="right">{{ $item->debit }}</td>
                                    <td align="right">{{ $item->credit }}</td>
                                </tr>
                                <tr class="text-dark f-12">
                                    <td colspan="3"
                                        class="border-bottom">
                                        <p class="mt-2">
                                            {{ $item->notes }}
                                        </p>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <!-- CARD BODY END -->
    <!-- CARD FOOTER START -->
    <div class="card-footer bg-white border-0 d-flex justify-content-start py-0 py-lg-4 py-md-4 mb-4 mb-lg-3 mb-md-3 ">
        <div class="d-flex">
            <div class="inv-action mr-3 mr-lg-3 mr-md-3 dropup">
                <button class="dropdown-toggle btn-primary" type="button" id="dropdownMenuButton"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">@lang('app.action')
                    <span><i class="fa fa-chevron-up f-15"></i></span>
                </button>
                <!-- DROPDOWN - INFORMATION -->
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton" tabindex="0">
                    <li>
                        <a class="dropdown-item f-14 text-dark" href="{{ route('journal.edit', [$invoice->id]) }}">
                            <i class="fa fa-edit f-w-500 mr-2 f-11"></i> @lang('app.edit')
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item f-14 text-dark delete-invoice" href="javascript:;"
                            data-invoice-id="{{ $invoice->id }}">
                            <i class="fa fa-trash f-w-500 mr-2 f-11"></i> @lang('app.delete')
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item f-14 text-dark"
                            href="{{ route('journal.download', [$invoice->id]) }}">
                            <i class="fa fa-download f-w-500 mr-2 f-11"></i> @lang('app.download')
                        </a>
                    </li>

                </ul>
            </div>
            <x-forms.button-cancel :link="route('journal.index')" class="border-0 mr-3">@lang('app.cancel')
            </x-forms.button-cancel>
        </div>


    </div>
    <!-- CARD FOOTER END -->
</div>
<script>
        $('body').on('click', '.delete-invoice', function() {
        var id = $(this).data('invoice-id');
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
                var token = "{{ csrf_token() }}";

                var url = "{{ route('journal.destroy', ':id') }}";
                url = url.replace(':id', id);

                $.easyAjax({
                    type: 'POST',
                    url: url,
                    blockUI: true,
                    data: {
                        '_token': token,
                        '_method': 'DELETE'
                    },
                    success: function(response) {
                        if (response.status == "success") {
                            window.location.href = "{{ route('journal.index') }}";
                        }
                    }
                });
            }
        });
    });
</script>
