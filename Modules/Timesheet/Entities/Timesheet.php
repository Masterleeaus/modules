<?php

namespace Modules\Timesheet\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Timesheet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'project_id',
        'task_id',
        'date',
        'hours',
        'type',
        'notes',
        'workspace',
        'created_by',
    ];

    protected static function newFactory()
    {
        return \Modules\Timesheet\Database\factories\TimesheetFactory::new();
    }
}
