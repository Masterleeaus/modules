@extends('layouts.layoutMaster')

@section('title', __('Job Checklist') . ' — ' . $project->name)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- Page header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                <a href="{{ route('pmcore.projects.show', $project) }}" class="text-muted me-1">
                    {{ $project->name }}
                </a>
                / {{ __('Checklists') }}
            </h4>
            <p class="text-muted mb-0">
                {{ $project->job_type?->label() ?? __('General') }}
                @if($project->address)
                    &mdash; {{ $project->address }}, {{ $project->suburb }} {{ $project->state }} {{ $project->postcode }}
                @endif
            </p>
        </div>
        <button
            class="btn btn-primary"
            data-bs-toggle="modal"
            data-bs-target="#addChecklistModal"
        >
            <i class="bx bx-plus me-1"></i> {{ __('Add Checklist') }}
        </button>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Checklists --}}
    @forelse($checklists as $checklist)
        @php
            $totalItems    = $checklist->items->count();
            $completedCount = $checklist->items->filter(fn($item) =>
                $item->completions->isNotEmpty()
            )->count();
            $pct = $totalItems > 0 ? round(($completedCount / $totalItems) * 100) : 0;
        @endphp

        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">{{ $checklist->name }}</h5>
                <span class="badge bg-label-primary">
                    {{ $completedCount }}/{{ $totalItems }} {{ __('complete') }}
                </span>
            </div>

            {{-- Progress bar --}}
            <div class="px-4 pt-2">
                <div class="progress" style="height: 6px;">
                    <div
                        class="progress-bar {{ $pct === 100 ? 'bg-success' : 'bg-primary' }}"
                        role="progressbar"
                        style="width: {{ $pct }}%"
                        aria-valuenow="{{ $pct }}"
                        aria-valuemin="0"
                        aria-valuemax="100">
                    </div>
                </div>
            </div>

            <div class="card-body">
                <form
                    method="POST"
                    action="{{ route('pmcore.checklists.complete', $project) }}"
                >
                    @csrf

                    <ul class="list-group list-group-flush">
                        @forelse($checklist->items as $item)
                            @php
                                $done = $item->completions->isNotEmpty();
                                $completion = $item->completions->first();
                            @endphp
                            <li class="list-group-item d-flex align-items-start gap-3 px-0">
                                <div class="form-check mt-1">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        name="item_ids[]"
                                        value="{{ $item->id }}"
                                        id="item-{{ $item->id }}"
                                        {{ $done ? 'checked disabled' : '' }}
                                    >
                                </div>
                                <label
                                    class="form-check-label flex-grow-1 {{ $done ? 'text-decoration-line-through text-muted' : '' }}"
                                    for="item-{{ $item->id }}"
                                >
                                    {{ $item->description }}
                                    @if($item->is_required)
                                        <span class="badge bg-label-danger ms-1">{{ __('Required') }}</span>
                                    @endif
                                </label>
                                @if($done && $completion)
                                    <small class="text-muted text-nowrap">
                                        {{ $completion->completedBy?->name ?? __('Unknown') }}
                                        <br>{{ $completion->completed_at?->diffForHumans() }}
                                    </small>
                                @endif
                            </li>
                        @empty
                            <li class="list-group-item text-muted px-0">{{ __('No items in this checklist.') }}</li>
                        @endforelse
                    </ul>

                    @if($checklist->items->where('completions', fn($c) => $c->isEmpty())->isNotEmpty() ?? true)
                        <div class="mt-3">
                            <button type="submit" class="btn btn-sm btn-success">
                                <i class="bx bx-check me-1"></i> {{ __('Mark Selected Complete') }}
                            </button>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    @empty
        <div class="alert alert-info">
            {{ __('No checklists yet for this job. Click "Add Checklist" to get started.') }}
        </div>
    @endforelse

</div>

{{-- Add Checklist Modal --}}
<div class="modal fade" id="addChecklistModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ route('pmcore.checklists.store', $project) }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Add Checklist') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('Checklist Name') }} <span class="text-danger">*</span></label>
                        <input
                            type="text"
                            name="name"
                            class="form-control"
                            placeholder="{{ __('e.g. Bond Clean — Bathroom') }}"
                            required
                        >
                    </div>

                    <label class="form-label">{{ __('Items') }} <span class="text-danger">*</span></label>
                    <div id="checklist-items">
                        <div class="input-group mb-2">
                            <input
                                type="text"
                                name="items[0][description]"
                                class="form-control"
                                placeholder="{{ __('Item description') }}"
                                required
                            >
                            <div class="input-group-text">
                                <input
                                    class="form-check-input mt-0"
                                    type="checkbox"
                                    name="items[0][is_required]"
                                    value="1"
                                    title="{{ __('Required') }}"
                                >
                                <span class="ms-1 small">{{ __('Required') }}</span>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="add-item-btn">
                        <i class="bx bx-plus me-1"></i> {{ __('Add Item') }}
                    </button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary">
                        {{ __('Create Checklist') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('page-script')
<script>
(function () {
    let itemIndex = 1;
    const container = document.getElementById('checklist-items');

    document.getElementById('add-item-btn').addEventListener('click', function () {
        const row = document.createElement('div');
        row.className = 'input-group mb-2';
        row.innerHTML = `
            <input type="text" name="items[${itemIndex}][description]"
                   class="form-control" placeholder="{{ __('Item description') }}" required>
            <div class="input-group-text">
                <input class="form-check-input mt-0" type="checkbox"
                       name="items[${itemIndex}][is_required]" value="1"
                       title="{{ __('Required') }}">
                <span class="ms-1 small">{{ __('Required') }}</span>
            </div>
            <button type="button" class="btn btn-outline-danger remove-item">
                <i class="bx bx-trash"></i>
            </button>
        `;
        row.querySelector('.remove-item').addEventListener('click', () => row.remove());
        container.appendChild(row);
        itemIndex++;
    });
})();
</script>
@endpush
