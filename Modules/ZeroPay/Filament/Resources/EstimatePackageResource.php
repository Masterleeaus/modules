<?php

namespace Modules\ZeroPay\Filament\Resources;

use Modules\ZeroPay\Filament\Resources\EstimatePackageResource\Pages;
use App\Models\EstimatePackage;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EstimatePackageResource extends Resource
{
    protected static ?string $model = EstimatePackage::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-squares-plus';

    protected static string|\UnitEnum|null $navigationGroup = 'Quotes';

    protected static ?string $navigationLabel = 'Quote Packages';

    protected static ?string $modelLabel = 'Quote Package';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Quote Package')
                ->columns(2)
                ->schema([
                                        TextInput::make('estimate_id')->label('Quote ID')->numeric(),
                    Select::make('tier')->label('Tier')->options(['good' => 'Good', 'better' => 'Better', 'best' => 'Best'])->required(),
                    TextInput::make('label')->label('Label')->required()->maxLength(120),
                    TextInput::make('total')->label('Total')->numeric(),
                    Toggle::make('is_recommended')->label('Recommended'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                                TextColumn::make('estimate_id')->label('Quote')->searchable()->sortable(),
                TextColumn::make('tier')->label('Tier')->badge()->searchable()->sortable(),
                TextColumn::make('label')->label('Label')->searchable()->sortable(),
                TextColumn::make('total')->label('Total')->money('usd')->sortable(),
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
            'index' => Pages\ListEstimatePackages::route('/'),
            'create' => Pages\CreateEstimatePackage::route('/create'),
            'edit' => Pages\EditEstimatePackage::route('/{record}/edit'),
        ];
    }
}
