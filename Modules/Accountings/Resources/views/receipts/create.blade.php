@extends('layouts.app')
<?php $pageTitle = $pageTitle ?? 'New Receipt'; ?>
@section('content')

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">New Receipt / Evidence</h4>
        <a href="{{ route('receipts.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="post" action="{{ route('receipts.store') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Attachable Type (optional)</label>
                        <input type="text" name="attachable_type" class="form-control" placeholder="Modules\\Accountings\\Entities\\Bill" />
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Attachable ID</label>
                        <input type="number" name="attachable_id" class="form-control" />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">File Path / URL</label>
                        <input type="text" name="file_path" class="form-control" placeholder="storage/receipts/....pdf" />
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-md-4">
                        <label class="form-label">File Name</label>
                        <input type="text" name="file_name" class="form-control" />
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Mime</label>
                        <input type="text" name="mime" class="form-control" placeholder="application/pdf" />
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">File Size (bytes)</label>
                        <input type="number" name="file_size" class="form-control" />
                    </div>
                </div>

                <div class="mt-3">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="3"></textarea>
                </div>

                <div class="mt-3">
                    <button class="btn btn-primary">Save Receipt</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
