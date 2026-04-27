<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttachmentResource\Pages;
use App\Models\Attachment;
use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AttachmentResource extends Resource
{
    protected static ?string $model = Attachment::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-paper-clip';

    protected static string|\UnitEnum|null $navigationGroup = 'Customers & Properties';

    protected static ?string $navigationLabel = 'Attachments';

    protected static ?string $modelLabel = 'Attachment';

    protected static ?int $navigationSort = 30;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Attachment')
                ->columns(2)
                ->schema([
                                        TextInput::make('filename')->label('Filename')->maxLength(200),
                    TextInput::make('disk')->label('Disk')->maxLength(80),
                    TextInput::make('path')->label('Path')->maxLength(255),
                    TextInput::make('mime_type')->label('MIME Type')->maxLength(120),
                    TextInput::make('size')->label('Size')->numeric(),
                    TextInput::make('tag')->label('Tag')->maxLength(80),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                                TextColumn::make('filename')->label('Filename')->searchable()->sortable(),
                TextColumn::make('mime_type')->label('MIME')->searchable()->sortable(),
                TextColumn::make('size')->label('Size')->searchable()->sortable(),
                TextColumn::make('tag')->label('Tag')->searchable()->sortable(),
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
            'index' => Pages\ListAttachments::route('/'),
            'create' => Pages\CreateAttachment::route('/create'),
            'edit' => Pages\EditAttachment::route('/{record}/edit'),
        ];
    }
}
