<?php

namespace App\Filament\Admin\Blocks;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Redberry\PageBuilderPlugin\Abstracts\BaseBlock;

class FaqBlock extends BaseBlock
{
    public static function getBlockSchema(?object $record = null): array
    {
        return [
            TextInput::make('heading')->required(),
            Repeater::make('items')->schema([
                TextInput::make('question')->required(),
                Textarea::make('answer')->rows(3),
            ])->defaultItems(4),
        ];
    }

    public static function getCategory(): string
    {
        return 'Landing';
    }

    public static function getBlockLabel(array $state, ?int $index = null): string
    {
        return data_get($state, 'heading') ?: 'FAQ';
    }
}
