@extends('onboardingpro::layouts.master')

@section('onboardingpro-content')
<div class="content-wrapper">

    <div class="row content-header">
        <div class="col-sm-8">
            <h1 class="content-header-title">Onboarding Flows</h1>
        </div>
        <div class="col-sm-4">
            <a href="{{ route('onboardingpro.admin.flows.create') }}"
               class="btn btn-primary float-right">
                <i class="fa fa-plus"></i> New Flow
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Job Type</th>
                                <th>Steps</th>
                                <th>Completions</th>
                                <th>Status</th>
                                <th>{{ __('app.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($flows as $flow)
                            <tr>
                                <td>{{ $flow->id }}</td>
                                <td>{{ $flow->name }}</td>
                                <td>
                                    <span class="badge badge-secondary">{{ ucfirst($flow->type) }}</span>
                                </td>
                                <td>{{ $flow->job_type ?? '—' }}</td>
                                <td>{{ $flow->steps_count ?? $flow->steps->count() }}</td>
                                <td>{{ $flow->completions_count ?? 0 }}</td>
                                <td>
                                    @if($flow->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-danger"
                                            onclick="obpDeleteFlow({{ $flow->id }})">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    No flows yet.
                                    <a href="{{ route('onboardingpro.admin.flows.create') }}">Create the first one.</a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
const OBP_CSRF = '{{ csrf_token() }}';

function obpDeleteFlow(id) {
    if (! confirm('Delete this flow and all its steps?')) return;

    fetch('/account/admin/onboarding/flows/' + id, {
        method : 'DELETE',
        headers: {'X-CSRF-TOKEN': OBP_CSRF, 'Accept': 'application/json'},
    }).then(function () { window.location.reload(); });
}
</script>
@endsection
