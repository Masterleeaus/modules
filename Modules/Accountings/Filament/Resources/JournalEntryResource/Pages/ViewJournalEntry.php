<?php

namespace Modules\Accountings\Filament\Resources\JournalEntryResource\Pages;

use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;
use Modules\Accountings\Filament\Resources\JournalEntryResource;

class ViewJournalEntry extends ViewRecord
{
    protected static string $resource = JournalEntryResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Journal Entry')
                ->columns(2)
                ->schema([
                    TextEntry::make('id')->label('ID'),
                    TextEntry::make('entry_date')->label('Date')->date(),
                    TextEntry::make('reference_type')->label('Reference Type'),
                    TextEntry::make('reference_id')->label('Reference ID'),
                    TextEntry::make('description')->label('Description')->columnSpanFull(),
                ]),
            Section::make('Journal Lines')
                ->schema([
                    RepeatableEntry::make('lines')
                        ->label('')
                        ->schema([
                            TextEntry::make('account.code')->label('Account Code'),
                            TextEntry::make('account.name')->label('Account Name'),
                            TextEntry::make('description')->label('Description'),
                            TextEntry::make('debit')->label('Debit')->money('aud'),
                            TextEntry::make('credit')->label('Credit')->money('aud'),
                        ])
                        ->columns(5),
                ]),
        ]);
    }
}
