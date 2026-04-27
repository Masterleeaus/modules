@extends('layouts.app')
<?php $pageTitle = $pageTitle ?? 'Receipts'; ?>
@section('content')
@include('accountings::partials.nav')


<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Receipts & Evidence</h4>
        <a href="{{ route('receipts.create') }}" class="btn btn-primary btn-sm">New Receipt</a>
    </div>

    @if(session('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Attached To</th>
                            <th>File</th>
                            <th>Notes</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($receipts as $r)
                            <tr>
                                <td>{{ $r->id }}</td>
                                <td>{{ $r->attachable_type ? ($r->attachable_type.' #'.$r->attachable_id) : '—' }}</td>
                                <td>
                                    @if($r->file_path)
                                        <div class="fw-semibold">{{ $r->file_name ?: 'Attachment' }}</div>
                                        <div class="text-muted small">{{ $r->file_path }}</div>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>{{ $r->notes }}</td>
                                <td class="text-end">
                                    <form method="post" action="{{ route('receipts.destroy', $r->id) }}" onsubmit="return confirm('Delete receipt?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-muted">No receipts yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $receipts->links() }}
        </div>
    </div>
</div>

@endsection
