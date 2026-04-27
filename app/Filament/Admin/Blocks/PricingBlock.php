<?php

namespace App\Filament\Admin\Blocks;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Redberry\PageBuilderPlugin\Abstracts\BaseBlock;

class PricingBlock extends BaseBlock
{
    public static function getBlockSchema(?object $record = null): array
    {
        return [
            TextInput::make('heading')->required(),
            Textarea::make('body')->rows(2),
            Repeater::make('tiers')->schema([
                TextInput::make('name')->required(),
                TextInput::make('price')->required(),
                Textarea::make('description')->rows(2),
                Textarea::make('features')->helperText('One feature per line')->rows(5),
                Toggle::make('highlight')->label('Highlight this tier'),
            ])->defaultItems(3),
        ];
    }

    public static function getCategory(): string
    {
        return 'Landing';
    }

    public static function getBlockLabel(array $state, ?int $index = null): string
    {
        return data_get($state, 'heading') ?: 'Pricing';
    }
}
