<?php

namespace Modules\Recruit\Entities;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Recruit\Observers\RecruitCustomQuestionObserver;

class RecruitCustomQuestion extends BaseModel
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $fillable = [];

    public static function boot()
    {
        parent::boot();
        static::observe(RecruitCustomQuestionObserver::class);
    }
}
