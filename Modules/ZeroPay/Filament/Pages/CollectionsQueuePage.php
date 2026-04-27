<?php

namespace Modules\ZeroPay\Filament\Pages;

use Filament\Pages\Page;

class CollectionsQueuePage extends Page
{
    protected static ?string $slug = 'zero-pay/collections-queue';

    protected static ?string $navigationLabel = 'Collections Queue';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-queue-list';

    protected static string|\UnitEnum|null $navigationGroup = 'Collections';

    protected static ?int $navigationSort = 20;

    protected string $view = 'zero_pay::filament.pages.collections-queue';
}
