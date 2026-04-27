@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="row mb-3 align-items-center">
        <div class="col">
            <h4 class="mb-0">Compliance Documents</h4>
        </div>
        <div class="col-auto">
            <a href="{{ route('titan-vault.compliance.dashboard') }}" class="btn btn-outline-secondary btn-sm mr-2">
                <i class="fa fa-tachometer-alt mr-1"></i> Dashboard
            </a>
            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addComplianceModal">
                <i class="fa fa-plus mr-1"></i> Add Compliance Doc
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    {{-- Expired --}}
    @if($expired->isNotEmpty())
    <div class="card mb-4 border-danger">
        <div class="card-header bg-danger text-white d-flex align-items-center">
            <i class="fa fa-times-circle mr-2"></i>
            <strong>Expired ({{ $expired->count() }})</strong>
        </div>
        <div class="card-body p-0">
            @include('titan_vault::compliance._table', ['items' => $expired, 'rowClass' => 'table-danger'])
        </div>
    </div>
    @endif

    {{-- Expiring Soon --}}
    @if($expiringSoon->isNotEmpty())
    <div class="card mb-4 border-warning">
        <div class="card-header bg-warning d-flex align-items-center">
            <i class="fa fa-exclamation-triangle mr-2"></i>
            <strong>Expiring within 30 days ({{ $expiringSoon->count() }})</strong>
        </div>
        <div class="card-body p-0">
            @include('titan_vault::compliance._table', ['items' => $expiringSoon, 'rowClass' => 'table-warning'])
        </div>
    </div>
    @endif

    {{-- Active --}}
    @if($active->isNotEmpty())
    <div class="card mb-4">
        <div class="card-header bg-success text-white d-flex align-items-center">
            <i class="fa fa-check-circle mr-2"></i>
            <strong>Current ({{ $active->count() }})</strong>
        </div>
        <div class="card-body p-0">
            @include('titan_vault::compliance._table', ['items' => $active, 'rowClass' => ''])
        </div>
    </div>
    @endif

    {{-- No Expiry --}}
    @if($noExpiry->isNotEmpty())
    <div class="card mb-4">
        <div class="card-header d-flex align-items-center">
            <i class="fa fa-infinity mr-2"></i>
            <strong>No Expiry Set ({{ $noExpiry->count() }})</strong>
        </div>
        <div class="card-body p-0">
            @include('titan_vault::compliance._table', ['items' => $noExpiry, 'rowClass' => ''])
        </div>
    </div>
    @endif

    @if($expired->isEmpty() && $expiringSoon->isEmpty() && $active->isEmpty() && $noExpiry->isEmpty())
        <div class="alert alert-info">No compliance documents found. Add one to get started.</div>
    @endif
</div>

{{-- Inline partial for the table --}}
@push('scripts')
<script>
// No additional JS required
</script>
@endpush

{{-- Add Compliance Modal --}}
<div class="modal fade" id="addComplianceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Compliance Document</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form method="POST" action="{{ route('titan-vault.compliance.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Type <span class="text-danger">*</span></label>
                        <select name="compliance_type" class="form-control" required>
                            @foreach($complianceTypes as $type)
                                <option value="{{ $type }}">{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Linked Document ID</label>
                        <input type="number" name="document_id" class="form-control" placeholder="Vault Document ID (optional)">
                    </div>
                    <div class="form-group">
                        <label>Staff Member ID</label>
                        <input type="number" name="staff_id" class="form-control" placeholder="User ID (optional)">
                    </div>
                    <div class="form-group">
                        <label>Chemical Name</label>
                        <input type="text" name="chemical_name" class="form-control" placeholder="For SDS documents">
                    </div>
                    <div class="form-group">
                        <label>Expiry Date</label>
                        <input type="date" name="expiry_date" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
