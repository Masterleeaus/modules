<?php

namespace Modules\GroundZero\Filament\Pages;

use Filament\Pages\Page;
use Modules\GroundZero\Filament\Widgets\DispatchStatsWidget;

class DispatchBoardPage extends Page
{
    protected static ?string $slug = 'ground-zero/dispatch';

    protected static ?string $navigationLabel = 'Dispatch Board';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-map';

    protected static string|\UnitEnum|null $navigationGroup = 'Operations';

    protected static ?int $navigationSort = 0;

    protected string $view = 'ground_zero::filament.pages.dispatch-board';

    protected function getWidgets(): array
    {
        return [
            DispatchStatsWidget::class,
        ];
    }
}
