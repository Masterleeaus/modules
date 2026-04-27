<?php

namespace App\Filament\Admin\Blocks;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Redberry\PageBuilderPlugin\Abstracts\BaseBlock;

class RichTextBlock extends BaseBlock
{
    public static function getBlockSchema(?object $record = null): array
    {
        return [
            TextInput::make('heading'),
            RichEditor::make('body')->required()->columnSpanFull(),
        ];
    }

    public static function getCategory(): string
    {
        return 'Content';
    }

    public static function getBlockLabel(array $state, ?int $index = null): string
    {
        return data_get($state, 'heading') ?: 'Rich Text';
    }
}
