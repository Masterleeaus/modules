<?php

namespace Modules\Recruit\Entities;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RecruitJobboardSetting extends BaseModel
{
    use HasFactory;

    protected $guarded = ['id'];
}
