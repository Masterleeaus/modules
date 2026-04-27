<?php

namespace App\Filament\Admin\Blocks;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Redberry\PageBuilderPlugin\Abstracts\BaseBlock;

class ImageTextBlock extends BaseBlock
{
    public static function getBlockSchema(?object $record = null): array
    {
        return [
            TextInput::make('heading')->required(),
            Textarea::make('body')->rows(4),
            FileUpload::make('image')->image()->directory('cms'),
            Toggle::make('image_right')->label('Show image on right'),
        ];
    }

    public static function getCategory(): string
    {
        return 'Content';
    }

    public static function getBlockLabel(array $state, ?int $index = null): string
    {
        return data_get($state, 'heading') ?: 'Image + Text';
    }
}
