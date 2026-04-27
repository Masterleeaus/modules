@extends('layouts.app')
@push('datatable-styles')
    @include('sections.datatable_css')
@endpush


@section('filter-section')
    <!-- FILTER START -->
    <!-- PROJECT HEADER STARTmplete -->
    <div class="d-flex filter-box project-header bg-white">

        <div class="mobile-close-overlay w-100 h-100" id="close-client-overlay"></div>
        <div class="project-menu d-lg-flex" id="mob-client-detail">

            <a class="d-none close-it" href="javascript:;" id="close-client-detail">
                <i class="fa fa-times"></i>
            </a>
            {{-- <nav class="tabs">
                <ul class="-primary"> --}}
            <x-tab :href="route('vendors.show', $vendor->id)" :text="__('purchase::modules.vendorPayment.overView')" class="overview" />

                <x-tab :href="route('vendors.show', $vendor->id).'?tab=notes'" ajax="false" :text="__('purchase::modules.vendorPayment.notes')"
                    class="notes" />

               <x-tab :href="route('vendors.show', $vendor->id).'?tab=contacts'" ajax="false" :text="__('purchase::modules.vendorPayment.contacts')"
                    class="contacts" />

                <x-tab :href="route('vendors.show', $vendor->id).'?tab=purchaseOrders'" ajax="false" :text="__('purchase::modules.vendor.purchaseOrders')"
                    class="purchaseOrders" />

                <x-tab :href="route('vendors.show', $vendor->id).'?tab=bills'" ajax="false" :text="__('purchase::app.menu.bills')"
                    class="bills" />

                <x-tab :href="route('vendors.show', $vendor->id).'?tab=payments'" ajax="false" :text="__('purchase::modules.vendor.payments')"
                    class="payments" />

                <x-tab :href="route('vendors.show', $vendor->id). '?tab=history'" ajax="false" :text="__('purchase::modules.vendor.history')"
                    class="history" />

                {{-- </ul>
            <nav> --}}
        </div>

        <a class="mb-0 d-block d-lg-none text-dark-grey ml-auto mr-2 border-left-grey"
            onclick="openClientDetailSidebar()"><i class="fa fa-ellipsis-v "></i></a>

    </div>
    <!-- FILTER END -->
    <!-- PROJECT HEADER END -->

@endsection

@push('styles')
<script src="{{ asset('vendor/jquery/Chart.min.js') }}"></script>
@endpush

@section('content')

    <div class="content-wrapper border-top-0 client-detail-wrapper">
        @include($view)
    </div>

@endsection

@push('scripts')
    <script>
        $("body").on("click", ".ajax-tab", function(event) {
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

    </script>
    <script>
        const activeTab = "{{ $activeTab }}";
        $('.project-menu .' + activeTab).addClass('active');

    </script>
@endpush
