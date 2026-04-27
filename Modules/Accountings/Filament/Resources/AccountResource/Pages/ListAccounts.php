<?php

namespace Modules\Accountings\Filament\Resources\AccountResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\Accountings\Filament\Resources\AccountResource;

class ListAccounts extends ListRecords
{
    protected static string $resource = AccountResource::class;
}
