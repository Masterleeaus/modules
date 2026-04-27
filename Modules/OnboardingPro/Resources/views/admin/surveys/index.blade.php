@extends('onboardingpro::layouts.master')

@section('onboardingpro-content')
<div class="content-wrapper">
    <div class="row content-header">
        <div class="col-sm-8">
            <h1 class="content-header-title">{{ __('onboardingpro::onboardingpro.surveys') }}</h1>
        </div>
        <div class="col-sm-4">
            <a href="{{ route('onboardingpro.admin.surveys.create') }}" class="btn btn-primary float-right">
                <i class="fa fa-plus"></i> {{ __('onboardingpro::onboardingpro.add_survey') }}
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
                                <th>{{ __('app.title') }}</th>
                                <th>Role</th>
                                <th>Trigger</th>
                                <th>Questions</th>
                                <th>Active</th>
                                <th>{{ __('app.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($surveys as $survey)
                            <tr>
                                <td>{{ $survey->id }}</td>
                                <td>{{ $survey->title }}</td>
                                <td><span class="badge badge-secondary">{{ $survey->role }}</span></td>
                                <td><span class="badge badge-info">{{ $survey->trigger }}</span></td>
                                <td>{{ count($survey->questions ?? []) }}</td>
                                <td>
                                    @if($survey->active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('onboardingpro.admin.surveys.edit', $survey->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-danger"
                                            onclick="deleteSurvey({{ $survey->id }})">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No surveys yet.</td>
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
function deleteSurvey(id) {
    if (!confirm('Delete this survey?')) return;
    fetch(`/account/onboarding/admin/surveys/${id}`, {
        method: 'DELETE',
        headers: {'X-CSRF-TOKEN': OBP_CSRF, 'Accept': 'application/json'},
    }).then(() => window.location.reload());
}
</script>
@endsection
