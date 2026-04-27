<?php

namespace Modules\CleanQuality\Tests\Feature;

use Tests\TestCase;

class CleanQualityMergeStructureTest extends TestCase
{
    /** @test */
    public function clean_quality_contains_required_merge_artifacts(): void
    {
        $requiredFiles = [
            // Actions
            'Modules/CleanQuality/Actions/CompleteInspection.php',
            'Modules/CleanQuality/Actions/AuthoriseReclean.php',
            'Modules/CleanQuality/Actions/ScoreQualityCheck.php',
            // Events
            'Modules/CleanQuality/Events/InspectionCompleted.php',
            'Modules/CleanQuality/Events/QualityCheckScheduled.php',
            'Modules/CleanQuality/Events/RecleanAuthorised.php',
            'Modules/CleanQuality/Events/QualityScoreUpdated.php',
            // Jobs
            'Modules/CleanQuality/Jobs/ScheduleQualityCheckJob.php',
            'Modules/CleanQuality/Jobs/SendInspectionReminderJob.php',
            'Modules/CleanQuality/Jobs/GenerateQualityReportJob.php',
            // Listeners
            'Modules/CleanQuality/Listeners/InspectionCompletedListener.php',
            'Modules/CleanQuality/Listeners/RecleanAuthorisedListener.php',
            'Modules/CleanQuality/Listeners/QualityScoreUpdatedListener.php',
            // Policies
            'Modules/CleanQuality/Policies/InspectionPolicy.php',
            'Modules/CleanQuality/Policies/QcRecordPolicy.php',
            // Manifests
            'Modules/CleanQuality/manifests/ai_tools.json',
            'Modules/CleanQuality/manifests/signals_manifest.json',
            'Modules/CleanQuality/manifests/lifecycle_manifest.json',
            'Modules/CleanQuality/manifests/api_manifest.json',
            // Filament
            'Modules/CleanQuality/Filament/Plugin/CleanQualityPlugin.php',
            'Modules/CleanQuality/Filament/Resources/InspectionResource.php',
            'Modules/CleanQuality/Filament/Resources/InspectionResource/Pages/ListInspections.php',
            'Modules/CleanQuality/Filament/Resources/InspectionResource/Pages/ViewInspection.php',
            'Modules/CleanQuality/Filament/Resources/QualityCheckResource.php',
            'Modules/CleanQuality/Filament/Resources/QualityCheckResource/Pages/ListQualityChecks.php',
            'Modules/CleanQuality/Filament/Resources/QualityCheckResource/Pages/ViewQualityCheck.php',
            'Modules/CleanQuality/Filament/Widgets/QualityScoreboard.php',
            'Modules/CleanQuality/Filament/Pages/CleanQualityDashboard.php',
        ];

        foreach ($requiredFiles as $file) {
            $this->assertFileExists(base_path($file), sprintf('Expected merged file "%s" to exist.', $file));
        }

        // Core actions
        $this->assertTrue(class_exists(\Modules\CleanQuality\Actions\CompleteInspection::class));
        $this->assertTrue(class_exists(\Modules\CleanQuality\Events\QualityScoreUpdated::class));
        $this->assertTrue(class_exists(\Modules\CleanQuality\Jobs\GenerateQualityReportJob::class));
        $this->assertTrue(method_exists(\Modules\CleanQuality\Actions\CompleteInspection::class, 'execute'));

        // Listeners
        $this->assertTrue(method_exists(\Modules\CleanQuality\Listeners\QualityScoreUpdatedListener::class, 'handle'));
        $this->assertTrue(method_exists(\Modules\CleanQuality\Listeners\InspectionCompletedListener::class, 'handle'));
        $this->assertTrue(method_exists(\Modules\CleanQuality\Listeners\RecleanAuthorisedListener::class, 'handle'));

        // Filament resource surface
        $this->assertTrue(method_exists(\Modules\CleanQuality\Filament\Resources\InspectionResource::class, 'table'));
        $this->assertTrue(method_exists(\Modules\CleanQuality\Filament\Resources\InspectionResource::class, 'infolist'));
        $this->assertTrue(method_exists(\Modules\CleanQuality\Filament\Resources\InspectionResource::class, 'getPages'));
        $this->assertTrue(method_exists(\Modules\CleanQuality\Filament\Resources\QualityCheckResource::class, 'table'));
        $this->assertTrue(method_exists(\Modules\CleanQuality\Filament\Resources\QualityCheckResource::class, 'infolist'));
        $this->assertTrue(method_exists(\Modules\CleanQuality\Filament\Resources\QualityCheckResource::class, 'getPages'));

        // Policies
        $this->assertTrue(method_exists(\Modules\CleanQuality\Policies\InspectionPolicy::class, 'viewAny'));
        $this->assertTrue(method_exists(\Modules\CleanQuality\Policies\InspectionPolicy::class, 'view'));
        $this->assertTrue(method_exists(\Modules\CleanQuality\Policies\InspectionPolicy::class, 'update'));
        $this->assertTrue(method_exists(\Modules\CleanQuality\Policies\InspectionPolicy::class, 'delete'));
        $this->assertTrue(class_exists(\Modules\CleanQuality\Policies\QcRecordPolicy::class));
        $this->assertTrue(method_exists(\Modules\CleanQuality\Policies\QcRecordPolicy::class, 'viewAny'));
        $this->assertTrue(method_exists(\Modules\CleanQuality\Policies\QcRecordPolicy::class, 'update'));

        // Plugin
        $plugin = new \Modules\CleanQuality\Filament\Plugin\CleanQualityPlugin();
        $this->assertSame('clean-quality', $plugin->getId());
    }
}

