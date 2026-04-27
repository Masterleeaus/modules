<?php

namespace Modules\PMCore\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectChecklist extends Model
{
    protected $table = 'project_checklists';

    protected $fillable = [
        'project_id',
        'name',
        'job_type',
        'is_template',
        'created_by',
    ];

    protected $casts = [
        'is_template' => 'boolean',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ProjectChecklistItem::class, 'checklist_id')->orderBy('sort_order');
    }

    public function completions(): HasMany
    {
        return $this->hasMany(ProjectChecklistCompletion::class, 'checklist_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    /**
     * Scope: only template checklists.
     */
    public function scopeTemplates($query)
    {
        return $query->where('is_template', true);
    }

    /**
     * Scope: filter by job type.
     */
    public function scopeForJobType($query, string $jobType)
    {
        return $query->where('job_type', $jobType);
    }
}
