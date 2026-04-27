<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\JobReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Inertia\ResponseFactory;

class JobReviewController extends Controller
{
    /**
     * Show the review form for a completed job (linked from post-payment redirect).
     */
    public function show(string $token): Response|ResponseFactory
    {
        $job = Job::where('id', base64_decode($token))
            ->where('status', Job::STATUS_COMPLETED)
            ->whereDoesntHave('review')
            ->with(['customer', 'assignedTechnician', 'jobType'])
            ->firstOrFail();

        return inertia('Client/Review', [
            'job' => [
                'id'         => $job->id,
                'token'      => $token,
                'title'      => $job->title,
                'technician' => $job->assignedTechnician?->name,
                'job_type'   => $job->jobType?->name,
                'completed_at' => $job->completed_at?->toDateString(),
            ],
        ]);
    }

    /**
     * Store the review.
     */
    public function store(Request $request, string $token): RedirectResponse
    {
        $jobId = base64_decode($token);

        $job = Job::where('id', $jobId)
            ->where('status', Job::STATUS_COMPLETED)
            ->whereDoesntHave('review')
            ->firstOrFail();

        $request->validate([
            'rating'     => ['required', 'integer', 'min:1', 'max:5'],
            'comment'    => ['nullable', 'string', 'max:1000'],
            'tip_amount' => ['nullable', 'numeric', 'min:0', 'max:500'],
        ]);

        JobReview::create([
            'job_id'       => $job->id,
            'customer_id'  => $job->customer_id,
            'technician_id' => $job->assigned_to,
            'rating'       => $request->rating,
            'comment'      => $request->comment,
            'tip_amount'   => $request->tip_amount ?? 0,
        ]);

        return redirect()->route('portal.dashboard')
            ->with('success', 'Thank you for your feedback!');
    }
}
