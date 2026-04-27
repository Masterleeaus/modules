<?php

namespace Modules\SupplyChain\Tests\Unit\Jobs;

use PHPUnit\Framework\TestCase;

class ReorderAlertNotificationTest extends TestCase
{
    public function test_notification_file_exists(): void
    {
        $path = dirname(__DIR__, 3) . '/Notifications/ReorderAlertNotification.php';
        $this->assertFileExists($path);
    }

    public function test_notification_via_mail(): void
    {
        $path    = dirname(__DIR__, 3) . '/Notifications/ReorderAlertNotification.php';
        $content = file_get_contents($path);

        $this->assertStringContainsString("'mail'", $content);
    }

    public function test_notification_subject_contains_item_name_placeholder(): void
    {
        $path    = dirname(__DIR__, 3) . '/Notifications/ReorderAlertNotification.php';
        $content = file_get_contents($path);

        $this->assertStringContainsString('item_name', $content);
        $this->assertStringContainsString('->subject(', $content);
    }

    public function test_notification_mail_contains_qty_and_minimum_fields(): void
    {
        $path    = dirname(__DIR__, 3) . '/Notifications/ReorderAlertNotification.php';
        $content = file_get_contents($path);

        $this->assertStringContainsString('qty_available', $content);
        $this->assertStringContainsString('min_qty', $content);
    }

    public function test_notification_references_send_reorder_alert_job(): void
    {
        $jobPath = dirname(__DIR__, 3) . '/Jobs/SendReorderAlertJob.php';
        $content = file_get_contents($jobPath);

        $this->assertStringContainsString('ReorderAlertNotification', $content);
        $this->assertStringContainsString('notify', $content);
    }
}
