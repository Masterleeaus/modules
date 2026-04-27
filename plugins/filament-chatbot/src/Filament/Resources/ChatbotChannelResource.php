<?php

namespace TitanZero\FilamentChatbot\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use TitanZero\FilamentChatbot\Models\ChatbotChannel;

class ChatbotChannelResource extends Resource
{
    protected static ?string $model = ChatbotChannel::class;

    protected static ?string $navigationIcon = 'heroicon-o-square-3-stack-3d';

    protected static ?string $navigationLabel = 'Channels';

    protected static ?string $navigationGroup = 'Chatbot';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Channel Configuration')
                    ->schema([
                        Forms\Components\Select::make('chatbot_id')
                            ->relationship('chatbot', 'name')
                            ->required(),
                        Forms\Components\Select::make('type')
                            ->options(['web' => 'Web', 'telegram' => 'Telegram', 'whatsapp' => 'WhatsApp', 'facebook' => 'Facebook'])
                            ->required(),
                        Forms\Components\TextInput::make('name')
                            ->required(),
                        Forms\Components\Toggle::make('is_active')
                            ->default(true),
                        Forms\Components\KeyValue::make('settings')
                            ->label('Channel Settings'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge(),
                Tables\Columns\TextColumn::make('chatbot.name')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options(['web' => 'Web', 'telegram' => 'Telegram', 'whatsapp' => 'WhatsApp', 'facebook' => 'Facebook']),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListChatbotChannels::route('/'),
            'create' => CreateChatbotChannel::route('/create'),
            'edit' => EditChatbotChannel::route('/{record}/edit'),
        ];
    }
}
