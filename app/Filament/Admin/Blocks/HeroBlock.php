<?php

namespace App\Filament\Admin\Blocks;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Redberry\PageBuilderPlugin\Abstracts\BaseBlock;

class HeroBlock extends BaseBlock
{
    public static function getBlockSchema(?object $record = null): array
    {
        return [
            TextInput::make('eyebrow')->label('Eyebrow'),
            TextInput::make('headline')->required(),
            Textarea::make('subheadline')->rows(3),
            TextInput::make('primary_button_label')->label('Primary button label'),
            TextInput::make('primary_button_url')->label('Primary button URL'),
            TextInput::make('secondary_button_label')->label('Secondary button label'),
            TextInput::make('secondary_button_url')->label('Secondary button URL'),
            FileUpload::make('image')->image()->directory('cms'),
        ];
    }

    public static function getCategory(): string
    {
        return 'Landing';
    }

    public static function getBlockLabel(array $state, ?int $index = null): string
    {
        return data_get($state, 'headline') ?: 'Hero';
    }
}
