<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobCrew extends Model
{
    use HasFactory;

    protected $table = 'job_crew';

    const ROLE_LEAD    = 'lead';
    const ROLE_SUPPORT = 'support';

    protected $fillable = [
        'job_id',
        'user_id',
        'role',
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
