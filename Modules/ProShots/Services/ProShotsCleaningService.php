<?php

namespace Modules\ProShots\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Modules\ProShots\Entities\BackgroundPreset;
use Modules\ProShots\Entities\JobBatch;
use Modules\ProShots\Entities\UserPebblely;

class ProShotsCleaningService
{
    public function getCleaningPresets(): Collection
    {
        return BackgroundPreset::whereIn('category', ['residential', 'commercial'])
            ->orderBy('category')
            ->orderBy('name')
            ->get();
    }

    public function createJobBatch(string $jobRef, int $companyId, int $userId): JobBatch
    {
        return JobBatch::create([
            'company_id' => $companyId,
            'job_ref'    => $jobRef,
            'title'      => 'Job ' . $jobRef,
            'status'     => 'pending',
            'created_by' => $userId,
        ]);
    }

    public function processJobBatch(JobBatch $batch, array $photoIds): void
    {
        $batch->update([
            'status'       => 'processing',
            'total_photos' => count($photoIds),
        ]);

        foreach ($photoIds as $photoId) {
            $photo = UserPebblely::find($photoId);
            if (! $photo) {
                continue;
            }

            $photo->update(['job_ref' => $batch->job_ref]);
            $batch->increment('completed_photos');
        }

        $batch->markAsCompleted();
    }

    public function publishBatchToVault(JobBatch $batch): void
    {
        if (! class_exists(\Modules\TitanVault\Services\VaultService::class)) {
            Log::info('ProShots: TitanVault module not available — skipping vault publish', [
                'batch_id' => $batch->id,
            ]);
            return;
        }

        try {
            $vault     = app(\Modules\TitanVault\Services\VaultService::class);
            $proofPack = $vault->createProofPack($batch->job_ref, $batch->photos->pluck('image')->toArray());
            $batch->update(['vault_proof_pack_id' => $proofPack->id ?? null]);
        } catch (\Throwable $e) {
            Log::error('ProShots: failed to publish batch to vault', [
                'batch_id' => $batch->id,
                'error'    => $e->getMessage(),
            ]);
        }
    }
}
