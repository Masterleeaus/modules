<?php

namespace Modules\TitanStudio\Filament\Resources;

use Modules\TitanStudio\Filament\Resources\CmsPageResource\Pages;
use App\Models\CmsPage;
use Filament\Forms;
use Filament\Forms\Components\Builder;
use Filament\Resources\Resource;
use Filament\Actions;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CmsPageResource extends Resource
{
    protected static ?string $model = CmsPage::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|\UnitEnum|null $navigationGroup = 'Content Management';

    protected static ?string $navigationLabel = 'CMS Pages';

    protected static ?int $navigationSort = 900;


    protected static ?string $modelLabel = 'CMS Page';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Schemas\Components\Section::make('Page')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, callable $set): mixed => filled($state) ? $set('slug', Str::slug((string) $state)) : null),
                    Forms\Components\TextInput::make('slug')
                        ->required()
                        ->unique(ignoreRecord: true),
                    Forms\Components\Select::make('status')
                        ->options([
                            'draft' => 'Draft',
                            'published' => 'Published',
                        ])
                        ->default('draft')
                        ->required(),
                    Forms\Components\DateTimePicker::make('published_at'),
                    Forms\Components\Textarea::make('summary')
                        ->columnSpanFull(),
                ]),

            Schemas\Components\Section::make('Existing Site CMS Builder')
                ->description('This editor imports the current marketing homepage into editable blocks. It does not depend on Redberry internals, so it remains stable if plugins change.')
                ->schema([
                    Builder::make('website_content')
                        ->label('Page sections')
                        ->blocks([
                            Builder\Block::make('HeroBlock')
                                ->label('Hero')
                                ->schema([
                                    Forms\Components\TextInput::make('eyebrow'),
                                    Forms\Components\TextInput::make('headline')->required(),
                                    Forms\Components\Textarea::make('subheadline')->rows(3),
                                    Forms\Components\TextInput::make('primary_button_label'),
                                    Forms\Components\TextInput::make('primary_button_url'),
                                    Forms\Components\TextInput::make('secondary_button_label'),
                                    Forms\Components\TextInput::make('secondary_button_url'),
                                    Forms\Components\TextInput::make('note'),
                                ]),
                            Builder\Block::make('FeatureGridBlock')
                                ->label('Feature grid')
                                ->schema([
                                    Forms\Components\TextInput::make('heading')->required(),
                                    Forms\Components\Textarea::make('body')->rows(2),
                                    Forms\Components\Repeater::make('features')
                                        ->schema([
                                            Forms\Components\TextInput::make('title')->required(),
                                            Forms\Components\Textarea::make('description')->rows(2),
                                        ])
                                        ->columns(1),
                                ]),
                            Builder\Block::make('PricingBlock')
                                ->label('Pricing')
                                ->schema([
                                    Forms\Components\TextInput::make('heading')->required(),
                                    Forms\Components\Textarea::make('body')->rows(2),
                                    Forms\Components\Repeater::make('tiers')
                                        ->schema([
                                            Forms\Components\TextInput::make('name')->required(),
                                            Forms\Components\TextInput::make('price')->required(),
                                            Forms\Components\Textarea::make('description')->rows(2),
                                            Forms\Components\TagsInput::make('features')
                                                ->placeholder('Add a feature'),
                                            Forms\Components\Toggle::make('highlight'),
                                        ])
                                        ->columns(1),
                                ]),
                            Builder\Block::make('FaqBlock')
                                ->label('FAQ')
                                ->schema([
                                    Forms\Components\TextInput::make('heading')->required(),
                                    Forms\Components\Repeater::make('items')
                                        ->schema([
                                            Forms\Components\TextInput::make('question')->required(),
                                            Forms\Components\Textarea::make('answer')->rows(3),
                                        ]),
                                ]),
                            Builder\Block::make('CtaBlock')
                                ->label('Call to action')
                                ->schema([
                                    Forms\Components\TextInput::make('heading')->required(),
                                    Forms\Components\Textarea::make('body')->rows(3),
                                    Forms\Components\TextInput::make('button_label'),
                                    Forms\Components\TextInput::make('button_url'),
                                ]),
                            Builder\Block::make('RichTextBlock')
                                ->label('Rich text')
                                ->schema([
                                    Forms\Components\TextInput::make('heading'),
                                    Forms\Components\RichEditor::make('body')->columnSpanFull(),
                                ]),
                        ])
                        ->collapsible()
                        ->reorderable()
                        ->columnSpanFull(),
                ]),

            Schemas\Components\Section::make('SEO')
                ->columns(2)
                ->collapsed()
                ->schema([
                    Forms\Components\TextInput::make('meta_title'),
                    Forms\Components\Textarea::make('meta_description'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('slug')->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\TextColumn::make('published_at')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                    ]),
            ])
            ->recordActions([
                Actions\EditAction::make(),
                Actions\Action::make('view')
                    ->label('View')
                    ->url(fn (CmsPage $record): string => $record->slug === 'home' ? url('/') : url('/pages/'.$record->slug), true)
                    ->icon('heroicon-o-arrow-top-right-on-square'),
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
            'index' => Pages\ListCmsPages::route('/'),
            'create' => Pages\CreateCmsPage::route('/create'),
            'edit' => Pages\EditCmsPage::route('/{record}/edit'),
        ];
    }
}
