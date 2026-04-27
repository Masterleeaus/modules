<?php

namespace TitanZero\FilamentChatbot\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use TitanZero\FilamentChatbot\Models\Chatbot;

class ChatbotResource extends Resource
{
    protected static ?string $model = Chatbot::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationLabel = 'Chatbots';

    protected static string|\UnitEnum|null $navigationGroup = 'Chatbot';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('General')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->rows(3),
                        Forms\Components\Toggle::make('is_active')
                            ->default(true),
                    ]),
                Forms\Components\Section::make('Configuration')
                    ->schema([
                        Forms\Components\Select::make('ai_provider')
                            ->options(['openai' => 'OpenAI', 'anthropic' => 'Anthropic', 'gemini' => 'Google Gemini'])
                            ->required(),
                        Forms\Components\TextInput::make('ai_model'),
                        Forms\Components\Textarea::make('system_prompt')
                            ->rows(4),
                        Forms\Components\Slider::make('temperature')
                            ->minValue(0)
                            ->maxValue(2)
                            ->step(0.1)
                            ->default(0.7),
                    ]),
                Forms\Components\Section::make('Appearance')
                    ->schema([
                        Forms\Components\FileUpload::make('logo')
                            ->image()
                            ->maxSize(5120),
                        Forms\Components\TextInput::make('primary_color')
                            ->placeholder('#000000'),
                        Forms\Components\Select::make('position')
                            ->options(['bottom-right' => 'Bottom Right', 'bottom-left' => 'Bottom Left', 'top-right' => 'Top Right', 'top-left' => 'Top Left']),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ai_provider')
                    ->badge(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active'),
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

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListChatbots::route('/'),
            'create' => CreateChatbot::route('/create'),
            'edit' => EditChatbot::route('/{record}/edit'),
        ];
    }
}
