@extends('onboardingpro::layouts.master')

@section('onboardingpro-content')
<div class="content-wrapper">
    <div class="row content-header">
        <div class="col-sm-8">
            <h1 class="content-header-title">{{ __('onboardingpro::onboardingpro.banners') }}</h1>
        </div>
        <div class="col-sm-4">
            <a href="{{ route('onboardingpro.admin.banners.create') }}" class="btn btn-primary float-right">
                <i class="fa fa-plus"></i> {{ __('onboardingpro::onboardingpro.add_banner') }}
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
                                <th>Order</th>
                                <th>Active</th>
                                <th>{{ __('app.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($banners as $banner)
                            <tr>
                                <td>{{ $banner->id }}</td>
                                <td>{{ $banner->title }}</td>
                                <td><span class="badge badge-secondary">{{ $banner->role }}</span></td>
                                <td>{{ $banner->order }}</td>
                                <td>
                                    @if($banner->active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('onboardingpro.admin.banners.edit', $banner->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-danger"
                                            onclick="deleteBanner({{ $banner->id }})">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No banners yet.</td>
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
function deleteBanner(id) {
    if (!confirm('Delete this banner?')) return;
    fetch(`/account/onboarding/admin/banners/${id}`, {
        method: 'DELETE',
        headers: {'X-CSRF-TOKEN': OBP_CSRF, 'Accept': 'application/json'},
    }).then(() => window.location.reload());
}
</script>
@endsection
