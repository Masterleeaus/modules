<?php

namespace Modules\PMCore\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectChecklistCompletion extends Model
{
    protected $table = 'project_checklist_completions';

    protected $fillable = [
        'checklist_id',
        'item_id',
        'project_id',
        'completed_by',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function checklist(): BelongsTo
    {
        return $this->belongsTo(ProjectChecklist::class, 'checklist_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(ProjectChecklistItem::class, 'item_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'completed_by');
    }
}
