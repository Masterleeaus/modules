@extends('quality_control::layouts.master')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">File Preview</h4>
    <a class="btn btn-primary" href="{{ $filepath }}" target="_blank" rel="noopener">Open File</a>
</div>

<div class="card">
    <div class="card-body" style="min-height:70vh;">
        <iframe src="{{ $filepath }}" style="width:100%;height:68vh;border:0;"></iframe>
    </div>
</div>
@endsection
