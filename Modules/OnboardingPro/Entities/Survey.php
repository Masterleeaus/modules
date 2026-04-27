<?php
namespace Modules\OnboardingPro\Entities;

use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    protected $table = 'surveys';

    protected $guarded = ['id'];

    protected $casts = [
        'questions' => 'array',
        'active'    => 'boolean',
    ];

    // Scopes

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeForRole($query, string $role)
    {
        return $query->where(function ($q) use ($role) {
            $q->where('role', $role)->orWhere('role', 'all');
        });
    }

    // Relations

    public function surveyUsers()
    {
        return $this->hasMany(SurveyUser::class);
    }

    // Helpers

    public function isCompletedBy($user): bool
    {
        return $this->surveyUsers()
            ->where('user_id', $user->id)
            ->whereNotNull('completed_at')
            ->exists();
    }
}
