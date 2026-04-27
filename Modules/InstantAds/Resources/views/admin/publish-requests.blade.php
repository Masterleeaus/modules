@extends('layouts.app')

@section('filter-section')
<h4 class="mb-0">{{ __('InstantAds — Pending Publish Requests') }}</h4>
@endsection

@section('content')
<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>{{ __('Preview') }}</th>
                <th>{{ __('Prompt') }}</th>
                <th>{{ __('User') }}</th>
                <th>{{ __('Requested') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($requests as $record)
            <tr id="row-{{ $record->id }}">
                <td style="width:80px;">
                    @if(!empty($record->generated_images[0]))
                    <img src="{{ $record->generated_images[0] }}"
                        style="width:70px;height:70px;object-fit:cover;border-radius:6px;">
                    @endif
                </td>
                <td>{{ Str::limit($record->prompt, 80) }}</td>
                <td>{{ $record->user?->name ?? 'Guest' }}</td>
                <td>{{ $record->publish_requested_at?->diffForHumans() }}</td>
                <td>
                    <button class="btn btn-sm btn-success btn-approve" data-id="{{ $record->id }}">
                        {{ __('Approve') }}
                    </button>
                    <button class="btn btn-sm btn-danger btn-reject ms-1" data-id="{{ $record->id }}">
                        {{ __('Reject') }}
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center text-muted py-4">{{ __('No pending requests.') }}</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-3">{{ $requests->links() }}</div>
@endsection

@push('scripts')
<script>
document.addEventListener('click', function(e) {
    const approveBtn = e.target.closest('.btn-approve');
    const rejectBtn  = e.target.closest('.btn-reject');
    const btn        = approveBtn || rejectBtn;

    if (!btn) return;

    const id       = btn.dataset.id;
    const isApprove = !!approveBtn;
    const url      = isApprove
        ? `/account/instant-ads/admin/publish-requests/${id}/approve`
        : `/account/instant-ads/admin/publish-requests/${id}/reject`;

    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const row = document.getElementById('row-' + id);
            if (row) row.remove();
        }
    });
});
</script>
@endpush
