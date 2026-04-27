@php($pageTitle = $pageTitle ?? __('accountings::app.menu.accounting'))
@extends('layouts.app')

@section('content')
    <!-- CONTENT WRAPPER START -->
    <div class="content-wrapper">
        @include('accountings::journal.ajax.show')
    </div>
    <!-- CONTENT WRAPPER END -->

@endsection
