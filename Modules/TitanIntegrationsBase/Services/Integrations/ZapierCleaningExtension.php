<?php

namespace Modules\TitanIntegrations\Services\Integrations;

use Modules\TitanIntegrations\Entities\Integration;

/**
 * ZapierCleaningExtension — additional cleaning-specific Zapier triggers and actions.
 * Extends ZapierIntegration with domain-specific definitions.
 */
class ZapierCleaningExtension extends ZapierIntegration
{
    public function getProvider(): string { return 'zapier'; }

    /**
     * Returns the cleaning-specific trigger definitions for Zapier.
     * Each trigger fires when the corresponding event occurs in the cleaning SaaS.
     */
    public function getCleaningTriggers(): array
    {
        return [
            [
                'key'         => 'job_created',
                'event'       => 'job.created',
                'label'       => 'New Cleaning Job Created',
                'description' => 'Fires when a new cleaning job is created.',
                'sample'      => [
                    'id'             => 1,
                    'reference'      => 'JOB-0001',
                    'address'        => '123 Main St, Sydney NSW',
                    'scheduled_date' => '2026-05-01',
                    'status'         => 'scheduled',
                ],
            ],
            [
                'key'         => 'job_completed',
                'event'       => 'job.completed',
                'label'       => 'Cleaning Job Completed',
                'description' => 'Fires when a cleaning job is marked as completed.',
                'sample'      => [
                    'id'           => 1,
                    'reference'    => 'JOB-0001',
                    'completed_at' => '2026-05-01T14:30:00Z',
                    'cleaner_id'   => 5,
                ],
            ],
            [
                'key'         => 'invoice_paid',
                'event'       => 'invoice.paid',
                'label'       => 'Invoice Paid',
                'description' => 'Fires when a cleaning invoice is marked as paid.',
                'sample'      => [
                    'id'             => 42,
                    'invoice_number' => 'INV-0042',
                    'amount'         => 195.00,
                    'paid_at'        => '2026-05-01T15:00:00Z',
                ],
            ],
            [
                'key'         => 'new_booking',
                'event'       => 'new_booking',
                'label'       => 'New Booking Received',
                'description' => 'Fires when a new booking is submitted (e.g. from the client portal).',
                'sample'      => [
                    'id'             => 7,
                    'client_name'    => 'Jane Smith',
                    'client_email'   => 'jane@example.com',
                    'service_type'   => 'Regular Clean',
                    'requested_date' => '2026-05-03',
                ],
            ],
        ];
    }

    /**
     * Returns the cleaning-specific action definitions for Zapier.
     * Each action lets Zapier create or modify data in the cleaning SaaS.
     */
    public function getCleaningActions(): array
    {
        return [
            [
                'key'         => 'create_job',
                'label'       => 'Create Cleaning Job',
                'description' => 'Creates a new cleaning job in the system.',
                'input_fields' => [
                    ['key' => 'address',        'label' => 'Job Address',      'required' => true,  'type' => 'string'],
                    ['key' => 'scheduled_date', 'label' => 'Scheduled Date',   'required' => true,  'type' => 'string'],
                    ['key' => 'service_type',   'label' => 'Service Type',     'required' => false, 'type' => 'string'],
                    ['key' => 'client_email',   'label' => 'Client Email',     'required' => false, 'type' => 'string'],
                    ['key' => 'notes',          'label' => 'Notes',            'required' => false, 'type' => 'string'],
                ],
            ],
            [
                'key'         => 'assign_staff',
                'label'       => 'Assign Staff to Job',
                'description' => 'Assigns a staff member to an existing cleaning job.',
                'input_fields' => [
                    ['key' => 'job_id',    'label' => 'Job ID',         'required' => true, 'type' => 'integer'],
                    ['key' => 'staff_id',  'label' => 'Staff Member ID','required' => true, 'type' => 'integer'],
                    ['key' => 'role',      'label' => 'Role',           'required' => false,'type' => 'string'],
                ],
            ],
        ];
    }

    /**
     * Handle an incoming Zapier action, returning a result array.
     */
    public function handleCleaningAction(string $action, array $payload, Integration $integration): array
    {
        return match ($action) {
            'create_job'   => $this->handleCreateJob($payload),
            'assign_staff' => $this->handleAssignStaff($payload),
            default        => ['error' => "Unknown action: {$action}"],
        };
    }

    // -------------------------------------------------------------------------
    // Action handlers (thin dispatch — actual logic lives in domain services)
    // -------------------------------------------------------------------------

    private function handleCreateJob(array $payload): array
    {
        // Validate minimum required fields and return a stub response.
        // Real implementation should delegate to the booking/job service.
        if (empty($payload['address']) || empty($payload['scheduled_date'])) {
            return ['error' => 'address and scheduled_date are required'];
        }

        return [
            'status'  => 'queued',
            'message' => 'Job creation queued for processing.',
            'payload' => $payload,
        ];
    }

    private function handleAssignStaff(array $payload): array
    {
        if (empty($payload['job_id']) || empty($payload['staff_id'])) {
            return ['error' => 'job_id and staff_id are required'];
        }

        return [
            'status'  => 'queued',
            'message' => 'Staff assignment queued for processing.',
            'payload' => $payload,
        ];
    }
}
