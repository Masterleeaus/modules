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
    <!-- PRODUCT HEADER START -->
    <div class="d-flex filter-box project-header bg-white">

        <div class="mobile-close-overlay w-100 h-100" id="close-client-overlay"></div>
        <div class="project-menu d-lg-flex" id="mob-client-detail">

            <a class="d-none close-it" href="javascript:;" id="close-client-detail">
                <i class="fa fa-times"></i>
            </a>

            <x-tab :href="route('purchase-products.show', $product->id)" :text="__('modules.projects.overview')" class="overview" />

            <x-tab :href="route('purchase-products.show', $product->id) . '?tab=files'" :text="__('purchase::modules.product.images')" class="files" />

            <!-- BELOW TAB IS PENDING TO WORK DON'T REMOVE IT -->
            {{-- <x-tab :href="route('purchase-products.show', $product->id) . '?tab=transactions'" :text="__('purchase::modules.product.transactions')" class="transactions" ajax="false" /> --}}

            <x-tab :href="route('purchase-products.show', $product->id) . '?tab=history'" :text="__('purchase::modules.vendorPayment.history')" class="history" />
        </div>
    </div>
    <!-- PRODUCT HEADER END -->
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
            let productId = '{{ $product->id }}';
            const url = "{{ route('purchase_products.add_images') }}?id=" + productId;
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });

        $('body').on('click', '#stock-adjustment', function() {
            let productId = '{{ $product->id }}';
            const url = "{{ route('purchase_products.adjust_inventory') }}?id=" + productId;
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
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
                    var url = "{{ route('products.destroy', ':id') }}";
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
