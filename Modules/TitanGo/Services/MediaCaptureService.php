<?php

namespace Modules\TitanGo\Services;

use App\Models\Attachment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MediaCaptureService
{
    /**
     * Persist a photo and return an array of attachment attributes.
     *
     * Client-side compression (Canvas API) is expected to produce images under
     * 800 KB before upload. Server-side we accept up to 10 MB as a safety net.
     *
     * @param  UploadedFile  $file
     * @param  int           $jobId
     * @param  int           $organizationId
     * @param  int           $uploadedBy
     * @param  string|null   $tag   'before' | 'after' | 'client_signature' | null
     * @param  string|null   $area  Free-text area label (e.g. "Kitchen floor")
     * @return array<string, mixed>
     */
    public function store(
        UploadedFile $file,
        int $jobId,
        int $organizationId,
        int $uploadedBy,
        ?string $tag = null,
        ?string $area = null,
    ): array {
        $disk = config('filesystems.attachment_disk', 'public');
        $path = $file->store("jobs/{$jobId}/photos", $disk);

        return [
            'organization_id' => $organizationId,
            'uploaded_by'     => $uploadedBy,
            'filename'        => $file->getClientOriginalName(),
            'disk'            => $disk,
            'path'            => $path,
            'mime_type'       => $file->getMimeType(),
            'size'            => $file->getSize(),
            'tag'             => $tag,
            'meta'            => $area !== null ? ['area' => $area] : null,
        ];
    }

    /**
     * Delete an attachment file from storage.
     */
    public function delete(Attachment $attachment): void
    {
        Storage::disk($attachment->disk)->delete($attachment->path);
    }
}
