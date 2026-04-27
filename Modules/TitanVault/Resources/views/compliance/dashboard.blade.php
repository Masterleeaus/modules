@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="row mb-3 align-items-center">
        <div class="col">
            <h4 class="mb-0">Compliance Dashboard</h4>
        </div>
        <div class="col-auto">
            <a href="{{ route('titan-vault.compliance.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fa fa-list mr-1"></i> View All
            </a>
        </div>
    </div>

    {{-- Summary cards --}}
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-danger text-center h-100">
                <div class="card-body">
                    <div class="display-4 font-weight-bold text-danger">{{ $expiredCount }}</div>
                    <div class="text-muted mt-1">Expired</div>
                </div>
                <div class="card-footer p-1">
                    <a href="{{ route('titan-vault.compliance.index') }}" class="small text-danger">View expired →</a>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-warning text-center h-100">
                <div class="card-body">
                    <div class="display-4 font-weight-bold text-warning">{{ $expiringSoonCount }}</div>
                    <div class="text-muted mt-1">Expiring within 30 days</div>
                </div>
                <div class="card-footer p-1">
                    <a href="{{ route('titan-vault.compliance.index') }}" class="small text-warning">Review now →</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-12 mb-3">
            <div class="card h-100">
                <div class="card-header"><strong>By Document Type</strong></div>
                <div class="card-body">
                    @forelse($byType as $type => $total)
                        <div class="d-flex justify-content-between mb-1">
                            <span class="badge badge-secondary">{{ ucfirst(str_replace('_', ' ', $type)) }}</span>
                            <strong>{{ $total }}</strong>
                        </div>
                    @empty
                        <span class="text-muted">No records.</span>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Recent uploads --}}
    <div class="card">
        <div class="card-header"><strong>Recent Uploads</strong></div>
        <div class="card-body p-0">
            <table class="table table-sm table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>Type</th>
                        <th>Document</th>
                        <th>Staff</th>
                        <th>Expiry</th>
                        <th>Added</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentUploads as $item)
                    <tr>
                        <td>
                            <span class="badge badge-secondary">
                                {{ ucfirst(str_replace('_', ' ', $item->compliance_type)) }}
                            </span>
                        </td>
                        <td>
                            @if($item->document)
                                <a href="{{ route('titan-vault.documents.show', $item->document_id) }}">
                                    {{ $item->document->title }}
                                </a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>{{ $item->staff?->name ?? '—' }}</td>
                        <td>
                            @if($item->expiry_date)
                                @if($item->expiry_date->isPast())
                                    <span class="text-danger">{{ $item->expiry_date->format('d M Y') }}</span>
                                @elseif($item->expiry_date->diffInDays(now()) <= 30)
                                    <span class="text-warning">{{ $item->expiry_date->format('d M Y') }}</span>
                                @else
                                    <span class="text-success">{{ $item->expiry_date->format('d M Y') }}</span>
                                @endif
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>{{ $item->created_at->diffForHumans() }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-3">No recent uploads.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
