@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <h4 class="mb-3">{{ __('Shared Ad Creative') }}</h4>

                @if(!empty($record->generated_images))
                    @foreach($record->generated_images as $imgUrl)
                    <div class="mb-3">
                        <img src="{{ $imgUrl }}" class="img-fluid rounded shadow-sm"
                            alt="{{ $record->prompt ?? 'Ad Creative' }}"
                            style="max-height:600px;">
                    </div>
                    @endforeach
                @else
                    <p class="text-muted">{{ __('No image available.') }}</p>
                @endif

                <p class="text-muted mt-3">
                    <em>"{{ $record->prompt }}"</em>
                </p>

                <div class="d-flex justify-content-center gap-3 mt-3">
                    @if(!empty($record->generated_images[0]))
                    <a href="{{ $record->generated_images[0] }}" download class="btn btn-outline-secondary">
                        {{ __('Download') }}
                    </a>
                    @endif
                    <a href="{{ route('instant-ads.index') }}" class="btn btn-primary">
                        {{ __('Create Your Own') }}
                    </a>
                </div>

                <div class="mt-4 text-muted small">
                    <span>{{ __('Model:') }} {{ $record->model ?? 'AI' }}</span>
                    &bull;
                    <span>{{ __('Views:') }} {{ $record->views_count }}</span>
                    &bull;
                    <span>{{ __('Likes:') }} {{ $record->likes_count }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
