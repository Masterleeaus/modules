<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JobMessageResource\Pages;
use App\Models\JobMessage;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class JobMessageResource extends Resource
{
    protected static ?string $model = JobMessage::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static string|\UnitEnum|null $navigationGroup = 'Communications';

    protected static ?string $navigationLabel = 'Job Messages';

    protected static ?string $modelLabel = 'Job Message';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Job Message')
                ->columns(2)
                ->schema([
                                        TextInput::make('job_id')->label('Job ID')->numeric(),
                    TextInput::make('customer_id')->label('Customer ID')->numeric(),
                    TextInput::make('channel')->label('Channel')->maxLength(80),
                    TextInput::make('event')->label('Event')->maxLength(120),
                    TextInput::make('recipient')->label('Recipient')->maxLength(200),
                    Select::make('status')->label('Status')->options(['pending' => 'Pending', 'sent' => 'Sent', 'failed' => 'Failed', 'delivered' => 'Delivered']),
                    Textarea::make('body')->label('Body')->rows(3)->columnSpanFull(),
                    Textarea::make('error')->label('Error')->rows(3)->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                                TextColumn::make('job_id')->label('Job')->searchable()->sortable(),
                TextColumn::make('channel')->label('Channel')->searchable()->sortable(),
                TextColumn::make('recipient')->label('Recipient')->searchable()->sortable(),
                TextColumn::make('status')->label('Status')->badge()->searchable()->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                Actions\EditAction::make(),
            ])
            ->toolbarActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJobMessages::route('/'),
            'create' => Pages\CreateJobMessage::route('/create'),
            'edit' => Pages\EditJobMessage::route('/{record}/edit'),
        ];
    }
}
