@extends('onboardingpro::layouts.master')

@section('onboardingpro-content')
<div class="content-wrapper">
    <div class="row content-header">
        <div class="col-12">
            <h1 class="content-header-title">{{ __('onboardingpro::onboardingpro.introduction_styles') }}</h1>
        </div>
    </div>

    <div class="row">
        @foreach($styles as $style)
        <div class="col-md-3 mb-4">
            <div class="card {{ $style->active ? 'border-primary' : '' }}">
                <div class="card-body text-center">
                    <h5>{{ __('onboardingpro::onboardingpro.style_' . $style->style) }}</h5>
                    @if($style->position)
                    <p class="text-muted small">Position: {{ $style->position }}</p>
                    @endif
                    @if($style->active)
                    <span class="badge badge-primary">Active</span>
                    @else
                    <form action="{{ route('onboardingpro.admin.styles.activate', $style->id) }}" method="POST" class="mt-2">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-primary">Activate</button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
        @if($styles->isEmpty())
        <div class="col-12 text-center py-5 text-muted">No introduction styles configured.</div>
        @endif
    </div>
</div>
@endsection
