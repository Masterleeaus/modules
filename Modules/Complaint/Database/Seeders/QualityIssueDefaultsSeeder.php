<?php

namespace Modules\Complaint\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Complaint\Entities\ComplaintType;
use Modules\Complaint\Entities\ComplaintChannel;
use Modules\Complaint\Entities\ComplaintGroup;
use Modules\Complaint\Entities\ComplaintReplyTemplate;

/**
 * Cleaning-aligned defaults for the "Quality Issues" (Complaint) module.
 * All inserts are idempotent.
 */
class QualityIssueDefaultsSeeder extends Seeder
{
    public static function seedForCompany(?int $companyId): void
    {
        (new self())->runForCompany($companyId);
    }

    public function run(): void
    {
        $companyId = company()?->id ?? null;
        $this->runForCompany($companyId);
    }

    private function runForCompany(?int $companyId): void
    {

        $types = [
            'Missed area',
            'Re-clean required',
            'Damage reported',
            'Access problem',
            'Supplies missing',
            'Schedule missed',
            'Customer complaint',
        ];

        foreach ($types as $type) {
            ComplaintType::firstOrCreate(
                ['company_id' => $companyId, 'type' => $type],
                ['company_id' => $companyId, 'type' => $type]
            );
        }

        $channels = [
            'Client portal',
            'Staff app',
            'Manual',
            'Phone call',
            'SMS/WhatsApp',
            'Email',
        ];

        foreach ($channels as $channel) {
            ComplaintChannel::firstOrCreate(
                ['company_id' => $companyId, 'channel_name' => $channel],
                ['company_id' => $companyId, 'channel_name' => $channel]
            );
        }

        $groups = [
            'Cleaning Team',
            'Supervisors',
            'Managers',
            'Customer Care',
        ];

        foreach ($groups as $group) {
            ComplaintGroup::firstOrCreate(
                ['company_id' => $companyId, 'group_name' => $group],
                ['company_id' => $companyId, 'group_name' => $group]
            );
        }

        // Basic reply templates to standardise comms.
        $templates = [
            [
                'heading' => 'Re-clean scheduled',
                'text' => "Thanks for letting us know. We've scheduled a re-clean and will confirm once it's complete.",
            ],
            [
                'heading' => 'Apology and update',
                'text' => "Sorry about that. We're investigating and will update you shortly.",
            ],
        ];

        foreach ($templates as $t) {
            ComplaintReplyTemplate::firstOrCreate(
                ['company_id' => $companyId, 'reply_heading' => $t['heading']],
                ['company_id' => $companyId, 'reply_heading' => $t['heading'], 'reply_text' => $t['text']]
            );
        }
    }
}
