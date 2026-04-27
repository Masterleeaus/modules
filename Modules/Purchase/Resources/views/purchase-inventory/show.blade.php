@extends('layouts.app')

@push('datatable-styles')
    @include('sections.datatable_css')
@endpush

<style>
    #defaultImg {
        text-align: center;
        margin: auto;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
    }

    #stockAdjustBtn {
        margin-left: auto;
    }
</style>

@section('filter-section')
    <!-- INVENTORY HEADER START -->
    <div class="d-flex filter-box project-header bg-white">

        <div class="mobile-close-overlay w-100 h-100" id="close-client-overlay"></div>
        <div class="project-menu d-lg-flex" id="mob-client-detail">

            <a class="d-none close-it" href="javascript:;" id="close-client-detail">
                <i class="fa fa-times"></i>
            </a>

            <x-tab :href="route('purchase-inventory.show', $inventory->id)" :text="__('modules.projects.overview')" class="overview" />
            <x-tab :href="route('purchase-inventory.show', $inventory->id) . '?tab=files'" :text="__('purchase::modules.inventory.files')" class="files" />
            <x-tab :href="route('purchase-inventory.show', $inventory->id) . '?tab=history'" :text="__('purchase::modules.vendorPayment.history')" class="history" />
        </div>
    </div>
    <!-- INVENTORY HEADER END -->
@endsection

@section('content')
    <div class="content-wrapper border-top-0 client-detail-wrapper">
        @include($view)
    </div>
@endsection

@push('scripts')
    <script>
        $("body").on("click", ".project-menu .ajax-tab", function(event) {
            event.preventDefault();

            $('.project-menu .p-sub-menu').removeClass('active');
            $(this).addClass('active');

            const requestUrl = this.href;

            $.easyAjax({
                url: requestUrl,
                blockUI: true,
                container: ".content-wrapper",
                historyPush: true,
                success: function(response) {
                    if (response.status == "success") {
                        $('.content-wrapper').html(response.html);
                        init('.content-wrapper');
                    }
                }
            });
        });

        const activeTab = "{{ $activeTab }}";
        $('.project-menu .' + activeTab).addClass('active');

        $('body').on('click', '#add-files', function() {
            let inventoryId = '{{ $inventory->id }}';
            const url = "{{ route('purchase_inventory.add_files') }}?id=" + inventoryId;
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });

        $('body').on('click', '.change-status', function() {
            var id = $(this).data('id');
            var status = $(this).data('type');

            if (status == ' active ') {
                var confirmStatus = "@lang('purchase::messages.confirmActiveStatus')";
            } else {
                var confirmStatus = "@lang('purchase::messages.confirmInactiveStatus')";
            }

            Swal.fire({
                title: "@lang('messages.sweetAlertTitle')",
                text: confirmStatus,
                icon: 'warning',
                showCancelButton: true,
                focusConfirm: false,
                confirmButtonText: "@lang('purchase::messages.confirmStatus')",
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
                    let url = "{{ route('purchase_inventory.change_status') }}";
                    let token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        data: {
                            'id' : id,
                            'status' : status,
                            '_token' : token,
                            '_method' : 'POST'
                        },
                        success: function(response) {
                            if (response.status == "success") {
                                window.location.reload();
                            }
                        }
                    });
                }
            });
        });

        $('body').on('click', '.delete-table-row', function() {
            var id = $(this).data('id');
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
                    var url = "{{ route('purchase-inventory.destroy', ':id') }}";
                    url = url.replace(':id', id);

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
@endpush
