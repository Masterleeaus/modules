@extends('layouts.app')

@push('datatable-styles')
    @include('sections.datatable_css')
@endpush


@push('styles')
    <script src="{{ asset('vendor/jquery/frappe-charts.min.iife.js') }}"></script>
    <script src="{{ asset('vendor/jquery/Chart.min.js') }}"></script>
@endpush

@section('content')

    <div class="content-wrapper">
    @include($view)
    </div>

@endsection

@push('scripts')

    <script>
        const activeTab = "{{ $activeTab }}";
        $('.project-menu .' + activeTab).addClass('active');
    </script>
@endpush
