@extends('onboardingpro::layouts.master')

@section('onboardingpro-content')
<div class="content-wrapper">

    <div class="row content-header">
        <div class="col-12">
            <h1 class="content-header-title">Create Onboarding Flow</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-10">
            <div class="card">
                <div class="card-body">

                    <form action="{{ route('onboarding.admin.flows.store') }}" method="POST" id="flowForm">
                        @csrf

                        <div class="row">
                            {{-- Flow details --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="f-14 text-dark-grey">Flow Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" required
                                           placeholder="e.g. New Cleaner Onboarding">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="f-14 text-dark-grey">Type <span class="text-danger">*</span></label>
                                    <select name="type" class="form-control" required>
                                        <option value="staff">Staff</option>
                                        <option value="client">Client</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="f-14 text-dark-grey">Job Type</label>
                                    <input type="text" name="job_type" class="form-control"
                                           placeholder="e.g. bond_clean (optional)">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="f-14 text-dark-grey">Sort Order</label>
                                    <input type="number" name="sort_order" class="form-control" value="0" min="0">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="f-14 text-dark-grey d-block">Active</label>
                                    <div class="custom-control custom-switch mt-2">
                                        <input type="checkbox" class="custom-control-input" id="isActive"
                                               name="is_active" value="1" checked>
                                        <label class="custom-control-label" for="isActive">Enabled</label>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="company_id" value="{{ auth()->user()->company_id ?? 1 }}">
                        </div>

                        {{-- Steps builder ─────────────────────────────────── --}}
                        <hr>
                        <h6 class="font-weight-bold mb-3">Steps</h6>

                        <div id="steps-container">
                            {{-- Rendered by JS --}}
                        </div>

                        <button type="button" class="btn btn-outline-secondary btn-sm mb-3" id="add-step-btn">
                            <i class="fa fa-plus mr-1"></i> Add Step
                        </button>

                        <hr>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save mr-1"></i> {{ __('app.save') }}
                        </button>
                        <a href="{{ route('onboarding.admin.flows.index') }}" class="btn btn-secondary ml-2">
                            {{ __('app.cancel') }}
                        </a>
                    </form>

                </div>
            </div>
        </div>
    </div>

</div>

<script>
let stepIndex = 0;

const STEP_TYPES = [
    {value: 'policy_accept',  label: 'Policy Accept'},
    {value: 'form',           label: 'Form'},
    {value: 'checklist',      label: 'Checklist'},
    {value: 'video',          label: 'Video'},
    {value: 'booking_wizard', label: 'Booking Wizard'},
];

function stepTypeOptions(selected) {
    return STEP_TYPES.map(function (t) {
        return '<option value="' + t.value + '"' + (t.value === selected ? ' selected' : '') + '>' + t.label + '</option>';
    }).join('');
}

function addStep(data) {
    data = data || {};
    const i    = stepIndex++;
    const html = `
        <div class="card mb-2 border step-card" id="step-card-${i}">
            <div class="card-body py-2 px-3">
                <div class="row align-items-center">
                    <div class="col-md-2">
                        <label class="f-12 text-muted mb-1">Type</label>
                        <select name="steps[${i}][step_type]" class="form-control form-control-sm" required>
                            ${stepTypeOptions(data.step_type || '')}
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="f-12 text-muted mb-1">Title <span class="text-danger">*</span></label>
                        <input type="text" name="steps[${i}][title]" class="form-control form-control-sm"
                               value="${data.title || ''}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="f-12 text-muted mb-1">Description</label>
                        <input type="text" name="steps[${i}][description]" class="form-control form-control-sm"
                               value="${data.description || ''}">
                    </div>
                    <div class="col-md-1">
                        <label class="f-12 text-muted mb-1">Order</label>
                        <input type="number" name="steps[${i}][sort_order]" class="form-control form-control-sm"
                               value="${data.sort_order || i}" min="0">
                    </div>
                    <div class="col-md-1 text-right">
                        <label class="f-12 text-muted mb-1 d-block">&nbsp;</label>
                        <button type="button" class="btn btn-sm btn-outline-danger"
                                onclick="removeStep(${i})">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-11">
                        <label class="f-12 text-muted mb-1">Content</label>
                        <textarea name="steps[${i}][content]" class="form-control form-control-sm" rows="2"
                                  placeholder="Optional step content / instructions">${data.content || ''}</textarea>
                    </div>
                    <div class="col-md-1 d-flex align-items-end pb-1">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input"
                                   id="req-${i}" name="steps[${i}][is_required]" value="1" checked>
                            <label class="custom-control-label f-11" for="req-${i}">Req.</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;

    document.getElementById('steps-container').insertAdjacentHTML('beforeend', html);
}

function removeStep(i) {
    const el = document.getElementById('step-card-' + i);
    if (el) el.remove();
}

document.getElementById('add-step-btn').addEventListener('click', function () {
    addStep();
});

// Start with one blank step
addStep();
</script>
@endsection
