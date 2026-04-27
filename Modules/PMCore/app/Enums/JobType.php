<?php

namespace Modules\PMCore\app\Enums;

enum JobType: string
{
    case RESIDENTIAL = 'residential';
    case BOND = 'bond';
    case CARPET = 'carpet';
    case COMMERCIAL = 'commercial';
    case PRESSURE = 'pressure';
    case POOL = 'pool';

    public function label(): string
    {
        return match ($this) {
            self::RESIDENTIAL => __('Residential Clean'),
            self::BOND        => __('Bond / End-of-Lease Clean'),
            self::CARPET      => __('Carpet Steam Clean'),
            self::COMMERCIAL  => __('Commercial Clean'),
            self::PRESSURE    => __('Pressure Wash'),
            self::POOL        => __('Pool Clean'),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::RESIDENTIAL => '#4A90D9',
            self::BOND        => '#E67E22',
            self::CARPET      => '#8E44AD',
            self::COMMERCIAL  => '#27AE60',
            self::PRESSURE    => '#2980B9',
            self::POOL        => '#16A085',
        };
    }

    public static function getDefault(): self
    {
        return self::RESIDENTIAL;
    }
}
