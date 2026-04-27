<?php

namespace Modules\CleanQuality\Filament\Pages;

use Filament\Pages\Page;
use Modules\CleanQuality\Filament\Widgets\QualityScoreboard;

class CleanQualityDashboard extends Page
{
    protected static ?string $slug = 'clean-quality/dashboard';
    protected static ?string $navigationLabel = 'Quality Dashboard';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';
    protected static string|\UnitEnum|null $navigationGroup = 'Quality';
    protected static ?int $navigationSort = 0;
    protected string $view = 'clean_quality::filament.pages.dashboard';

    protected function getWidgets(): array
    {
        return [
            QualityScoreboard::class,
        ];
    }
}

