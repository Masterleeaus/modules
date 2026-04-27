@extends('layouts.app')

@section('filter-section')
    <!-- OFFER HEADER START -->
    <div class="d-flex filter-box project-header bg-white">

        <div class="mobile-close-overlay w-100 h-100" id="close-client-overlay"></div>
        <div class="project-menu d-lg-flex" id="mob-client-detail">

            <a class="d-none close-it" href="javascript:;" id="close-client-detail">
                <i class="fa fa-times"></i>
            </a>

            <x-tab :href="route('job-offer-letter.show', $jobOffer->id)" :text="__('recruit::app.menu.details')"
                   class="letter"/>

            <x-tab :href="route('job-offer-letter.show', $jobOffer->id).'?tab=custom'"
                   :text="__('recruit::modules.jobApplication.additionalInfo')"
                   class="custom"/>

            <x-tab :href="route('job-offer-letter.show', $jobOffer->id).'?tab=history'"
                   :text="__('recruit::modules.job.history')"
                   class="history"/>

        </div>

        <a class="mb-0 d-block d-lg-none text-dark-grey ml-auto mr-2 border-left-grey"
           onclick="openClientDetailSidebar()"><i class="fa fa-ellipsis-v "></i></a>

    </div>
    <!-- OFFER HEADER END -->

@endsection

@push('styles')
    <script src="{{ asset('vendor/jquery/frappe-charts.min.iife.js') }}"></script>
@endpush

@section('content')

    <div class="content-wrapper pt-0 border-top-0 client-detail-wrapper mt-4">
        @include($view)
    </div>

@endsection

@push('scripts')
    <script>
        $("body").on("click", ".project-menu .ajax-tab", function (event) {
            event.preventDefault();

            $('.project-menu .p-sub-menu').removeClass('active');
            $(this).addClass('active');

            const requestUrl = this.href;

            $.easyAjax({
                url: requestUrl,
                blockUI: true,
                container: ".content-wrapper",
                historyPush: true,
                blockUI: true,
                success: function (response) {
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
