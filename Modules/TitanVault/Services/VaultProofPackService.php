<?php

namespace Modules\TitanVault\Services;

use Illuminate\Support\Collection;
use Modules\TitanVault\Entities\VaultComplianceDocument;
use Modules\TitanVault\Entities\VaultDocument;
use Modules\TitanVault\Entities\VaultProofPack;

class VaultProofPackService
{
    /**
     * Create a new proof pack for a cleaning job, attaching the given documents.
     */
    public function createProofPack(
        string $jobRef,
        array $documentIds,
        string $clientEmail,
        string $clientName
    ): VaultProofPack {
        $pack = VaultProofPack::create([
            'job_ref'      => $jobRef,
            'title'        => "Proof Pack — {$jobRef}",
            'status'       => VaultProofPack::STATUS_DRAFT,
            'created_by'   => auth()->id(),
            'company_id'   => auth()->user()->company_id ?? null,
            'client_email' => $clientEmail,
            'client_name'  => $clientName,
        ]);

        // Attach documents with sort order.
        $pivot = [];
        foreach (array_values($documentIds) as $i => $docId) {
            $pivot[$docId] = ['sort_order' => $i];
        }

        $pack->documents()->sync($pivot);

        return $pack->load('documents');
    }

    /**
     * Mark the proof pack as sent and record the sent timestamp.
     */
    public function sendProofPack(VaultProofPack $pack): void
    {
        if (!$pack->approval_token) {
            $pack->generateApprovalToken();
        }

        $pack->update([
            'status'  => VaultProofPack::STATUS_SENT,
            'sent_at' => now(),
        ]);
    }

    /**
     * Fetch all compliance documents expiring within $days days for a company.
     */
    public function getExpiringCompliance(int $companyId, int $days = 30): Collection
    {
        return VaultComplianceDocument::with('document', 'staff')
            ->where('company_id', $companyId)
            ->expiringSoon($days)
            ->orderBy('expiry_date')
            ->get();
    }

    /**
     * Automatically assemble a proof pack from all vault documents linked to a job ref.
     */
    public function assembleJobEvidencePack(string $jobRef, int $companyId): VaultProofPack
    {
        $documentIds = VaultDocument::where('company_id', $companyId)
            ->where('job_ref', $jobRef)
            ->pluck('id')
            ->toArray();

        return $this->createProofPack(
            $jobRef,
            $documentIds,
            clientEmail: '',
            clientName: ''
        );
    }
}
