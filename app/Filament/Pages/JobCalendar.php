<?php

namespace App\Filament\Pages;

use App\Services\JobCalendarService;
use Filament\Pages\Page;

class JobCalendar extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar';

    protected static string|\UnitEnum|null $navigationGroup = 'Scheduling';

    protected static ?string $navigationLabel = 'Calendar';

    protected static ?int $navigationSort = 5;

    protected string $view = 'filament.pages.job-calendar';

    /**
     * Provide calendar events to the view via JobCalendarService.
     *
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        return [
            'calendarServiceClass' => JobCalendarService::class,
        ];
    }
}
