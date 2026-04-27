<?php
namespace Modules\OnboardingPro\Entities;

use Illuminate\Database\Eloquent\Model;

class SurveyUser extends Model
{
    protected $table = 'survey_user';

    protected $guarded = ['id'];

    protected $casts = [
        'responses'    => 'array',
        'completed_at' => 'datetime',
        'step'         => 'integer',
    ];

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }
}
