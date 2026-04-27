<?php

namespace TitanZero\FilamentChatbot\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use TitanZero\FilamentChatbot\Models\ChatbotConversation;

class ChatbotConversationResource extends Resource
{
    protected static ?string $model = ChatbotConversation::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    protected static ?string $navigationLabel = 'Conversations';

    protected static string|\UnitEnum|null $navigationGroup = 'Chatbot';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Conversation Details')
                    ->schema([
                        Forms\Components\TextInput::make('visitor_email')
                            ->email(),
                        Forms\Components\TextInput::make('visitor_name')
                            ->maxLength(255),
                        Forms\Components\Select::make('status')
                            ->options(['open' => 'Open', 'closed' => 'Closed', 'pending' => 'Pending'])
                            ->required(),
                        Forms\Components\Textarea::make('messages')
                            ->disabled(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('visitor_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('visitor_email')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors(['success' => 'closed', 'warning' => 'pending', 'info' => 'open']),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['open' => 'Open', 'closed' => 'Closed', 'pending' => 'Pending']),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => ListChatbotConversations::route('/'),
            'view' => ViewChatbotConversation::route('/{record}'),
        ];
    }
}
