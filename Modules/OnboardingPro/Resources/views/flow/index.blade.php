@extends('onboardingpro::layouts.master')

@section('onboardingpro-content')
<div class="content-wrapper">

    <div class="row content-header">
        <div class="col-12">
            <h1 class="content-header-title">Onboarding</h1>
        </div>
    </div>

    @if($flowData->isEmpty())
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fa fa-check-circle f-40 text-success mb-3 d-block"></i>
                        <h4 class="text-muted">All caught up! No active onboarding steps.</h4>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @foreach($flowData as $entry)
        @php
            /** @var \Modules\OnboardingPro\Entities\OnboardingFlow $flow */
            $flow        = $entry['flow'];
            $steps       = $entry['steps'];
            $currentStep = $entry['current_step'];
            $progressPct = $entry['progress_pct'];
            $isComplete  = $entry['is_complete'];
        @endphp

        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ $flow->name }}</h5>
                        @if($isComplete)
                            <span class="badge badge-success">Complete</span>
                        @else
                            <span class="badge badge-primary">In Progress</span>
                        @endif
                    </div>

                    {{-- Progress bar --}}
                    <div class="card-body pb-2">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-muted">Progress</small>
                            <small class="text-muted">{{ $progressPct }}%</small>
                        </div>
                        <div class="progress mb-3" style="height:8px;">
                            <div class="progress-bar bg-success"
                                 role="progressbar"
                                 style="width: {{ $progressPct }}%"
                                 aria-valuenow="{{ $progressPct }}"
                                 aria-valuemin="0"
                                 aria-valuemax="100"></div>
                        </div>

                        {{-- Step wizard ────────────────────────────────────── --}}
                        <div class="d-flex align-items-center mb-4 overflow-auto pb-2">
                            @foreach($steps as $i => $step)
                                @php
                                    $isStepDone    = ! $entry['incomplete_steps']->contains('id', $step->id);
                                    $isCurrentStep = $currentStep && $currentStep->id === $step->id;
                                @endphp
                                <div class="d-flex align-items-center">
                                    <div class="text-center" style="min-width:80px;">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-1
                                             {{ $isStepDone ? 'bg-success text-white' : ($isCurrentStep ? 'bg-primary text-white' : 'bg-light text-muted') }}"
                                             style="width:36px; height:36px; font-weight:600;">
                                            @if($isStepDone)
                                                <i class="fa fa-check"></i>
                                            @else
                                                {{ $i + 1 }}
                                            @endif
                                        </div>
                                        <p class="f-11 mb-0 text-truncate" style="max-width:72px;" title="{{ $step->title }}">
                                            {{ $step->title }}
                                        </p>
                                    </div>
                                    @if(! $loop->last)
                                        <div class="flex-grow-1 border-top mx-1" style="min-width:20px; margin-top:-16px;"></div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        {{-- Current step content ────────────────────────────── --}}
                        @if($currentStep && ! $isComplete)
                            <div class="border rounded p-3 bg-light"
                                 id="step-content-{{ $currentStep->id }}">
                                <h6 class="font-weight-bold mb-1">
                                    {{ $currentStep->title }}
                                    @if($currentStep->is_required)
                                        <span class="badge badge-danger ml-1" style="font-size:10px;">Required</span>
                                    @endif
                                </h6>

                                @if($currentStep->description)
                                    <p class="text-muted f-13 mb-2">{{ $currentStep->description }}</p>
                                @endif

                                @if($currentStep->content)
                                    <div class="mb-3 f-14">{!! nl2br(e($currentStep->content)) !!}</div>
                                @endif

                                <button class="btn btn-primary btn-sm obp-complete-step"
                                        data-step-id="{{ $currentStep->id }}"
                                        data-url="{{ route('onboarding.flow.complete', $currentStep->id) }}">
                                    <i class="fa fa-check mr-1"></i> Mark Complete
                                </button>
                            </div>
                        @elseif($isComplete)
                            <div class="text-center py-3">
                                <i class="fa fa-check-circle text-success f-30 mb-2 d-block"></i>
                                <p class="text-success font-weight-bold mb-0">Flow complete — great work!</p>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    @endforeach

</div>

<script>
const OBP_FLOW_CSRF = '{{ csrf_token() }}';

document.querySelectorAll('.obp-complete-step').forEach(function (btn) {
    btn.addEventListener('click', function () {
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin mr-1"></i> Saving…';

        fetch(btn.dataset.url, {
            method : 'POST',
            headers: {
                'X-CSRF-TOKEN': OBP_FLOW_CSRF,
                'Accept'      : 'application/json',
                'Content-Type': 'application/json',
            },
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.status === 'success' || data.step_id) {
                window.location.reload();
            } else {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa fa-check mr-1"></i> Mark Complete';
                alert('Could not save — please try again.');
            }
        })
        .catch(function () {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-check mr-1"></i> Mark Complete';
        });
    });
});
</script>
@endsection
