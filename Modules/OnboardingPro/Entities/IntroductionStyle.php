<?php
namespace Modules\OnboardingPro\Entities;

use Illuminate\Database\Eloquent\Model;

class IntroductionStyle extends Model
{
    protected $table = 'introduction_styles';

    protected $guarded = ['id'];

    protected $casts = [
        'active' => 'boolean',
    ];

    // Style options: modal, banner, wizard, tooltip
    public static array $styles = ['modal', 'banner', 'wizard', 'tooltip'];

    // Position options: top, bottom
    public static array $positions = ['top', 'bottom'];
}
