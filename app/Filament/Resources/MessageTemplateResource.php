<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MessageTemplateResource\Pages;
use App\Models\MessageTemplate;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Actions;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MessageTemplateResource extends Resource
{
    protected static ?string $model = MessageTemplate::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-ellipsis';

    protected static string|\UnitEnum|null $navigationGroup = 'Communications';

    protected static ?string $navigationLabel = 'Message Templates';

    protected static ?int $navigationSort = 10;


    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('event')
                ->label('Trigger Event')
                ->options(MessageTemplate::events())
                ->required(),

            Select::make('channel')
                ->label('Channel')
                ->options(['email' => 'Email', 'sms' => 'SMS'])
                ->required(),

            TextInput::make('subject')
                ->label('Email Subject')
                ->maxLength(255)
                ->helperText('Email only. Leave blank for SMS.')
                ->columnSpanFull(),

            Textarea::make('body')
                ->label('Message Body')
                ->required()
                ->rows(8)
                ->helperText('Available variables: {{customer_name}}, {{job_title}}, {{job_date}}, {{technician_name}}, {{company_name}}')
                ->columnSpanFull(),

            Toggle::make('is_active')->label('Active')->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('event')
                    ->label('Event')
                    ->formatStateUsing(fn ($state) => MessageTemplate::events()[$state] ?? $state)
                    ->sortable(),
                TextColumn::make('channel')
                    ->badge()
                    ->color(fn ($state) => $state === 'email' ? 'info' : 'warning')
                    ->sortable(),
                TextColumn::make('subject')
                    ->placeholder('—')
                    ->limit(50),
                IconColumn::make('is_active')->label('Active')->boolean(),
                TextColumn::make('updated_at')->label('Updated')->since()->sortable(),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ])
            ->defaultSort('event');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMessageTemplates::route('/'),
            'create' => Pages\CreateMessageTemplate::route('/create'),
            'edit'   => Pages\EditMessageTemplate::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->where('organization_id', auth()->user()?->organization_id);
    }
}
