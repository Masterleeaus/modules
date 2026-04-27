<?php

namespace TitanZero\FilamentChatbot\Filament\Resources;

use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use TitanZero\FilamentChatbot\Filament\Resources\AssistantRunResource\Pages;
use TitanZero\FilamentChatbot\Models\AssistantRun;

class AssistantRunResource extends Resource
{
    protected static ?string $model = AssistantRun::class;

    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';

    protected static ?string $navigationLabel = 'AI Runs';

    protected static ?string $navigationGroup = 'Assistant';

    protected static ?int $navigationSort = 10;

    protected static ?string $recordTitleAttribute = 'id';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('thread.user_identifier')
                    ->label('User')
                    ->searchable()
                    ->formatStateUsing(fn ($state) => str_replace('user:', '', $state))
                    ->limit(30),

                Tables\Columns\TextColumn::make('thread.assistant_key')
                    ->label('Assistant')
                    ->badge()
                    ->color('info'),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning'  => AssistantRun::STATUS_PENDING,
                        'primary'  => AssistantRun::STATUS_PROCESSING,
                        'success'  => AssistantRun::STATUS_COMPLETED,
                        'danger'   => AssistantRun::STATUS_FAILED,
                    ]),

                Tables\Columns\TextColumn::make('model')
                    ->label('Model')
                    ->default('—'),

                Tables\Columns\TextColumn::make('input_tokens')
                    ->label('↑ Tokens')
                    ->numeric()
                    ->sortable()
                    ->default('—'),

                Tables\Columns\TextColumn::make('output_tokens')
                    ->label('↓ Tokens')
                    ->numeric()
                    ->sortable()
                    ->default('—'),

                Tables\Columns\TextColumn::make('input')
                    ->label('Input')
                    ->limit(60)
                    ->tooltip(fn ($state) => $state),

                Tables\Columns\TextColumn::make('error')
                    ->label('Error')
                    ->limit(50)
                    ->color('danger')
                    ->default('—'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->since(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        AssistantRun::STATUS_PENDING    => 'Pending',
                        AssistantRun::STATUS_PROCESSING => 'Processing',
                        AssistantRun::STATUS_COMPLETED  => 'Completed',
                        AssistantRun::STATUS_FAILED     => 'Failed',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Overview')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('id')->label('Run ID'),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn ($state) => match ($state) {
                                AssistantRun::STATUS_COMPLETED  => 'success',
                                AssistantRun::STATUS_FAILED     => 'danger',
                                AssistantRun::STATUS_PROCESSING => 'primary',
                                default                         => 'warning',
                            }),
                        TextEntry::make('model')->label('Model')->default('—'),
                        TextEntry::make('thread.user_identifier')
                            ->label('User')
                            ->formatStateUsing(fn ($state) => str_replace('user:', '', $state ?? '')),
                        TextEntry::make('thread.assistant_key')->label('Assistant'),
                        TextEntry::make('created_at')->dateTime()->label('Created at'),
                        TextEntry::make('started_at')->dateTime()->label('Started at')->default('—'),
                        TextEntry::make('completed_at')->dateTime()->label('Completed at')->default('—'),
                        TextEntry::make('input_tokens')->label('Input tokens')->default('—'),
                        TextEntry::make('output_tokens')->label('Output tokens')->default('—'),
                    ]),

                Section::make('Input')
                    ->schema([
                        TextEntry::make('input')
                            ->label('')
                            ->columnSpanFull()
                            ->prose(),
                    ]),

                Section::make('Output')
                    ->schema([
                        TextEntry::make('output')
                            ->label('')
                            ->columnSpanFull()
                            ->prose()
                            ->default('—'),
                    ]),

                Section::make('Error')
                    ->visible(fn (AssistantRun $record) => $record->error !== null)
                    ->schema([
                        TextEntry::make('error')
                            ->label('')
                            ->columnSpanFull()
                            ->color('danger'),
                    ]),

                Section::make('Tool Calls')
                    ->visible(fn (AssistantRun $record) => ! empty($record->tool_calls))
                    ->collapsible()
                    ->schema([
                        TextEntry::make('tool_calls')
                            ->label('')
                            ->columnSpanFull()
                            ->formatStateUsing(fn ($state) => json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)),
                    ]),

                Section::make('Tool Results')
                    ->visible(fn (AssistantRun $record) => ! empty($record->tool_results))
                    ->collapsible()
                    ->schema([
                        TextEntry::make('tool_results')
                            ->label('')
                            ->columnSpanFull()
                            ->formatStateUsing(fn ($state) => json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssistantRuns::route('/'),
            'view'  => Pages\ViewAssistantRun::route('/{record}'),
        ];
    }
}
