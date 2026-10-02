<?php

namespace Modules\GroundZero\Filament\Resources;

use Modules\GroundZero\Filament\Resources\DriverLocationResource\Pages;
use App\Models\DriverLocation;
use Filament\Actions;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DriverLocationResource extends Resource
{
    protected static ?string $model = DriverLocation::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-map-pin';

    protected static string|\UnitEnum|null $navigationGroup = 'Dispatch';

    protected static ?string $navigationLabel = 'Driver Locations';

    protected static ?string $modelLabel = 'Driver Location';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Driver Location')
                ->columns(2)
                ->schema([
                                        TextInput::make('user_id')->label('User ID')->numeric(),
                    TextInput::make('latitude')->label('Latitude')->numeric(),
                    TextInput::make('longitude')->label('Longitude')->numeric(),
                    TextInput::make('speed')->label('Speed')->numeric(),
                    DateTimePicker::make('recorded_at')->label('Recorded At'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                                TextColumn::make('user_id')->label('User')->searchable()->sortable(),
                TextColumn::make('latitude')->label('Latitude')->searchable()->sortable(),
                TextColumn::make('longitude')->label('Longitude')->searchable()->sortable(),
                TextColumn::make('recorded_at')->label('Recorded')->dateTime()->sortable(),
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
            'index' => Pages\ListDriverLocations::route('/'),
            'create' => Pages\CreateDriverLocation::route('/create'),
            'edit' => Pages\EditDriverLocation::route('/{record}/edit'),
        ];
    }
}
