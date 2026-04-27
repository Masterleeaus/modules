<?php

namespace Modules\Recruit\Entities;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ApplicationSource extends BaseModel
{
    use HasFactory;

    protected $fillable = [];

    protected $table = 'application_sources';
}
