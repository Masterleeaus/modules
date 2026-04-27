<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class JobCalendar extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar';

    protected static string|\UnitEnum|null $navigationGroup = 'Scheduling';

    protected static ?string $navigationLabel = 'Calendar';

    protected static ?int $navigationSort = 5;

    protected string $view = 'filament.pages.job-calendar';
}
