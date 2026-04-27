<?php

namespace App\Filament\Admin\Blocks;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Redberry\PageBuilderPlugin\Abstracts\BaseBlock;

class FeatureGridBlock extends BaseBlock
{
    public static function getBlockSchema(?object $record = null): array
    {
        return [
            TextInput::make('heading')->required(),
            Textarea::make('body')->rows(2),
            Repeater::make('features')->schema([
                TextInput::make('title')->required(),
                Textarea::make('description')->rows(2),
            ])->columns(1)->defaultItems(3),
        ];
    }

    public static function getCategory(): string
    {
        return 'Landing';
    }

    public static function getBlockLabel(array $state, ?int $index = null): string
    {
        return data_get($state, 'heading') ?: 'Feature Grid';
    }
}
