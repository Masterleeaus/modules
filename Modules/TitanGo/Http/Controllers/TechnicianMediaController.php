<?php

namespace Modules\TitanGo\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\Job;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class TechnicianMediaController extends Controller
{
    /**
     * Upload a photo (with optional before/after tag) to a job.
     */
    public function uploadPhoto(Request $request, Job $job): JsonResponse
    {
        abort_unless($job->assigned_to === $request->user()->id, 403);

        $request->validate([
            'photo' => ['required', 'file', 'image', 'max:10240'],
            'tag'   => ['nullable', Rule::in(['before', 'after'])],
            'area'  => ['nullable', 'string', 'max:255'],
        ]);

        $disk = config('filesystems.attachment_disk', 'public');
        $file = $request->file('photo');
        $path = $file->store("jobs/{$job->id}/photos", $disk);

        $attachment = $job->attachments()->create([
            'organization_id' => $job->organization_id,
            'uploaded_by'     => $request->user()->id,
            'filename'        => $file->getClientOriginalName(),
            'disk'            => $disk,
            'path'            => $path,
            'mime_type'       => $file->getMimeType(),
            'size'            => $file->getSize(),
            'tag'             => $request->input('tag'),
            'meta'            => $request->filled('area') ? ['area' => $request->input('area')] : null,
        ]);

        return response()->json(['status' => 'ok', 'data' => $attachment], 201);
    }

    /**
     * Delete a photo from a job.
     */
    public function deletePhoto(Request $request, Job $job, Attachment $attachment): JsonResponse
    {
        abort_unless($job->assigned_to === $request->user()->id, 403);
        abort_unless($attachment->attachable_type === Job::class && $attachment->attachable_id === $job->id, 404);

        Storage::disk($attachment->disk)->delete($attachment->path);
        $attachment->delete();

        return response()->json(['status' => 'ok']);
    }

    /**
     * Save a customer signature (base64 PNG) to a job.
     *
     * Payload: { "signature": "data:image/png;base64,..." }
     *
     * The signature is decoded and stored as a PNG file in the same attachment
     * bucket, tagged with 'client_signature'.
     */
    public function storeSignature(Request $request, Job $job): JsonResponse
    {
        abort_unless($job->assigned_to === $request->user()->id, 403);

        $request->validate([
            'signature' => ['required', 'string'],
        ]);

        $dataUri = $request->input('signature');

        // Accept "data:image/png;base64,<data>" or just the raw base64 string.
        if (str_contains($dataUri, ',')) {
            [, $base64] = explode(',', $dataUri, 2);
        } else {
            $base64 = $dataUri;
        }

        $decoded = base64_decode($base64, strict: true);
        if ($decoded === false) {
            return response()->json(['status' => 'error', 'message' => 'Invalid base64 data.'], 422);
        }

        $disk     = config('filesystems.attachment_disk', 'public');
        $filename = "signature_{$job->id}_" . now()->format('YmdHis') . '.png';
        $path     = "jobs/{$job->id}/signatures/{$filename}";

        Storage::disk($disk)->put($path, $decoded);

        $attachment = $job->attachments()->create([
            'organization_id' => $job->organization_id,
            'uploaded_by'     => $request->user()->id,
            'filename'        => $filename,
            'disk'            => $disk,
            'path'            => $path,
            'mime_type'       => 'image/png',
            'size'            => strlen($decoded),
            'tag'             => 'client_signature',
        ]);

        return response()->json(['status' => 'ok', 'data' => $attachment], 201);
    }
}
