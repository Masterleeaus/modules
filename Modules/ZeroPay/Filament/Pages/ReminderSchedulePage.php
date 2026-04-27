<?php

namespace Modules\ZeroPay\Filament\Pages;

use Filament\Pages\Page;

class ReminderSchedulePage extends Page
{
    protected static ?string $slug = 'zero-pay/reminder-schedule';

    protected static ?string $navigationLabel = 'Reminder Schedule';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bell-alert';

    protected static string|\UnitEnum|null $navigationGroup = 'Collections';

    protected static ?int $navigationSort = 30;

    protected string $view = 'zero_pay::filament.pages.reminder-schedule';
}
