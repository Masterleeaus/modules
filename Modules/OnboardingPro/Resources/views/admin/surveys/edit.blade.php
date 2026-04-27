@extends('onboardingpro::layouts.master')

@section('onboardingpro-content')
<div class="content-wrapper">
    <div class="row content-header">
        <div class="col-12">
            <h1 class="content-header-title">{{ __('onboardingpro::onboardingpro.edit_survey') }}</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-10">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('onboardingpro.admin.surveys.update', $survey->id) }}" method="POST" id="surveyForm">
                        @csrf
                        @method('PUT')
                        @include('onboardingpro::admin.surveys._form')
                        <button type="submit" class="btn btn-primary">{{ __('app.save') }}</button>
                        <a href="{{ route('onboardingpro.admin.surveys.index') }}" class="btn btn-secondary">{{ __('app.cancel') }}</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
