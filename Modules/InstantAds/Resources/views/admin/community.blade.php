@extends('layouts.app')

@section('filter-section')
<h4 class="mb-0">{{ __('InstantAds — Community Images') }}</h4>
@endsection

@section('content')
<div class="row g-3">
    @forelse($images as $record)
        @if(!empty($record->generated_images))
            @foreach($record->generated_images as $imgUrl)
            <div class="col-md-2 col-sm-4 col-6">
                <div class="card h-100 overflow-hidden">
                    <img src="{{ $imgUrl }}" class="card-img-top"
                        style="height:140px;object-fit:cover;" loading="lazy"
                        alt="{{ $record->prompt ?? '' }}">
                    <div class="card-body p-2">
                        <p class="small text-muted mb-1">{{ Str::limit($record->prompt ?? '', 30) }}</p>
                        <span class="badge bg-secondary">♥ {{ $record->likes_count }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        @endif
    @empty
        <div class="col-12 text-center text-muted py-5">
            {{ __('No published images yet.') }}
        </div>
    @endforelse
</div>

<div class="mt-4">{{ $images->links() }}</div>
@endsection
