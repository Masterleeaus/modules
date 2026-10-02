<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SignalDispatchLog extends Model
{
    use HasUuids;

    protected $table = 'signal_dispatch_log';

    public $timestamps = false;

    const RESULT_SUCCESS = 'success';
    const RESULT_FAILURE = 'failure';
    const RESULT_RETRY   = 'retry';

    protected $fillable = [
        'id',
        'signal_id',
        'handler',
        'result',
        'attempts',
        'last_error',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'attempts'   => 'integer',
            'created_at' => 'datetime',
        ];
    }

    public function signal(): BelongsTo
    {
        return $this->belongsTo(Signal::class);
    }
}
