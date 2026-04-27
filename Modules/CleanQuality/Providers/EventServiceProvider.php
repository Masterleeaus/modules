<?php

namespace Modules\CleanQuality\Providers;

use App\Events\NewCompanyCreatedEvent;
use Modules\CleanQuality\Events\InspectionCompleted;
use Modules\CleanQuality\Events\QualityScoreUpdated;
use Modules\CleanQuality\Events\RecleanAuthorised;
use Modules\CleanQuality\Entities\Schedule;
use Modules\CleanQuality\Listeners\CompanyCreatedListener;
use Modules\CleanQuality\Listeners\InspectionCompletedListener;
use Modules\CleanQuality\Listeners\QualityScoreUpdatedListener;
use Modules\CleanQuality\Listeners\RecleanAuthorisedListener;
use Modules\CleanQuality\Entities\RecurringSchedule;
use Modules\CleanQuality\Observers\ScheduleObserver;
use Modules\CleanQuality\Observers\ScheduleRecurringObserver;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;



class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        NewCompanyCreatedEvent::class => [CompanyCreatedListener::class],
        InspectionCompleted::class    => [InspectionCompletedListener::class],
        QualityScoreUpdated::class    => [QualityScoreUpdatedListener::class],
        RecleanAuthorised::class      => [RecleanAuthorisedListener::class],
    ];

    protected $observers = [
        RecurringSchedule::class => [ScheduleRecurringObserver::class],
        Schedule::class          => [ScheduleObserver::class],
    ];
}

