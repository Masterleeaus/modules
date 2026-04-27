<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class DispatchMap extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-map';

    protected static string|\UnitEnum|null $navigationGroup = 'Dispatch';

    protected static ?string $navigationLabel = 'Live Map';

    protected static ?int $navigationSort = 5;

    protected string $view = 'filament.pages.dispatch-map';
}
