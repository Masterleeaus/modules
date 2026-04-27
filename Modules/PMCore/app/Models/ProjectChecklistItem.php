<?php

namespace Modules\PMCore\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProjectChecklistItem extends Model
{
    protected $table = 'project_checklist_items';

    protected $fillable = [
        'checklist_id',
        'description',
        'sort_order',
        'is_required',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function checklist(): BelongsTo
    {
        return $this->belongsTo(ProjectChecklist::class, 'checklist_id');
    }

    public function completions()
    {
        return $this->hasMany(ProjectChecklistCompletion::class, 'item_id');
    }

    /**
     * Get the completion record for a specific project.
     */
    public function completionForProject(int $projectId): HasOne
    {
        return $this->hasOne(ProjectChecklistCompletion::class, 'item_id')
            ->where('project_id', $projectId);
    }

    /**
     * Scope: check if item is completed for a specific project.
     */
    public function scopeCompletedForProject($query, int $projectId)
    {
        return $query->whereHas('completions', function ($q) use ($projectId) {
            $q->where('project_id', $projectId);
        });
    }

    /**
     * Scope: required items only.
     */
    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }
}
