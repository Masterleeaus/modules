<?php

namespace App\Filament\Admin\Blocks;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Redberry\PageBuilderPlugin\Abstracts\BaseBlock;

class CtaBlock extends BaseBlock
{
    public static function getBlockSchema(?object $record = null): array
    {
        return [
            TextInput::make('heading')->required(),
            Textarea::make('body')->rows(3),
            TextInput::make('button_label'),
            TextInput::make('button_url'),
        ];
    }

    public static function getCategory(): string
    {
        return 'Landing';
    }

    public static function getBlockLabel(array $state, ?int $index = null): string
    {
        return data_get($state, 'heading') ?: 'Call to Action';
    }
}
