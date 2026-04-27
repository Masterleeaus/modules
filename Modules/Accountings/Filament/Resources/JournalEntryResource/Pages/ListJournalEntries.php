<?php

namespace Modules\Accountings\Filament\Resources\JournalEntryResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\Accountings\Filament\Resources\JournalEntryResource;

class ListJournalEntries extends ListRecords
{
    protected static string $resource = JournalEntryResource::class;
}
