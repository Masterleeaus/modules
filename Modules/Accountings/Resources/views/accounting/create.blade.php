@php($pageTitle = $pageTitle ?? __('accountings::app.menu.accounting'))
@extends('layouts.app')
@section('content')
    <div class="content-wrapper">
        @include($view)
    </div>
@endsection
