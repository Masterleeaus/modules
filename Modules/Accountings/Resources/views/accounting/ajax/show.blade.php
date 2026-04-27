@php($pageTitle = $pageTitle ?? __('accountings::app.menu.accounting'))
@php
    $editUnitPermission = user()->permission('edit_acc');
    $deleteUnitPermission = user()->permission('delete_acc');
@endphp
<div id="leave-detail-section">
    <div class="row">
        <div class="col-sm-12">
            <div class="card bg-white border-0 b-shadow-4">
                <div class="card-header bg-white  border-bottom-grey text-capitalize justify-content-between p-20">
                    <div class="row">
                        <div class="col-md-10 col-10">
                            <h3 class="heading-h1">@lang('accountings::app.acc.showAcc') @lang('app.details')</h3>
                        </div>
                        <div class="col-md-2 col-2 text-right">
                            <div class="dropdown">

                                <button
                                    class="btn btn-lg f-14 px-2 py-1 text-dark-grey text-capitalize rounded  dropdown-toggle"
                                    type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-ellipsis-h"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right border-grey rounded b-shadow-4 p-0"
                                    aria-labelledby="dropdownMenuLink" tabindex="0">
                                    @if ($editUnitPermission == 'all')
                                        <a class="dropdown-item openRightModal"
                                            data-redirect-url="{{ url()->previous() }}"
                                            href="{{ route('accountings.edit', $acc->id) }}">@lang('app.edit')</a>
                                    @endif
                                    @if ($deleteUnitPermission == 'all')
                                        <a class="dropdown-item delete-unit">@lang('app.delete')</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <x-cards.data-row :label="__('accountings::app.menu.bs')" :value="$acc->bs->bs_name" html="true" />
                    <x-cards.data-row :label="__('accountings::app.menu.bsType')" :value="$acc->bs->bs_type" html="true" />
                    <x-cards.data-row :label="__('accountings::app.menu.bsGroup')" :value="$acc->bs->bs_group" html="true" />
                    <x-cards.data-row :label="__('accountings::app.menu.pnl')" :value="$acc->pnl->pnl_name" html="true" />
                    <x-cards.data-row :label="__('accountings::app.menu.pnlType')" :value="$acc->pnl->pnl_type" html="true" />
                    <x-cards.data-row :label="__('accountings::app.menu.pnlGroup')" :value="$acc->pnl->pnl_group" html="true" />
                    <x-cards.data-row :label="__('accountings::app.menu.coa')" :value="$acc->coa" html="true" />
                    <x-cards.data-row :label="__('accountings::app.menu.coaDesc')" :value="$acc->coa_desc" html="true" />
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $('body').on('click', '.delete-unit', function() {
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
                var url = "{{ route('accountings.destroy', $acc->id) }}";

                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                    url: url,
                    data: {
                        '_token': token,
                        '_method': 'DELETE'
                    },
                    success: function(response) {
                        if (response.status == "success") {
                            window.location.href = response.redirectUrl;
                        }
                    }
                });
            }
        });
    });
</script>
