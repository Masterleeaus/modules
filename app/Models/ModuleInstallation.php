<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ModuleInstallation extends Model
{
    use HasUuids;

    protected $table = 'module_installations';

    protected $fillable = [
        'module_id',
        'version',
        'status',
        'installed_at',
        'last_upgraded_at',
        'installed_by',
        'metadata',
    ];

    protected $casts = [
        'installed_at' => 'datetime',
        'last_upgraded_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function isInstalled(): bool
    {
        return $this->status === 'installed';
    }

    public function hasFailed(): bool
    {
        return $this->status === 'failed';
    }
}
