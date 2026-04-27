<?php

namespace Modules\ProShots\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\ProShots\Entities\JobBatch;
use Modules\ProShots\Services\ProShotsCleaningService;

class JobBatchController extends Controller
{
    public function __construct(
        protected ProShotsCleaningService $cleaningService
    ) {}

    public function index()
    {
        $batches = JobBatch::where('created_by', Auth::id())
            ->orderByDesc('created_at')
            ->get();

        return view('proshots::batch.index', compact('batches'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'job_ref' => 'required|string|max:100',
            'title'   => 'nullable|string|max:255',
        ]);

        $user      = Auth::user();
        $companyId = $user->company_id ?? 1;

        $batch = $this->cleaningService->createJobBatch(
            $data['job_ref'],
            $companyId,
            $user->id
        );

        if ($data['title'] ?? null) {
            $batch->update(['title' => $data['title']]);
        }

        return redirect()->route('proshots.batches.show', $batch->id)->with([
            'message' => __('Batch created successfully.'),
            'type'    => 'success',
        ]);
    }

    public function show(JobBatch $batch)
    {
        $photos = $batch->photos()->latest()->get();

        return view('proshots::batch.show', compact('batch', 'photos'));
    }
}
