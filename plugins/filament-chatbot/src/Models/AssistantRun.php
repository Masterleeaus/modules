<?php

namespace TitanZero\FilamentChatbot\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssistantRun extends Model
{
    protected $table = 'chatbot_assistant_runs';

    protected $fillable = [
        'thread_id',
        'status',
        'input',
        'output',
        'tool_calls',
        'tool_results',
        'messages',
        'input_tokens',
        'output_tokens',
        'model',
        'error',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'tool_calls'    => 'array',
        'tool_results'  => 'array',
        'messages'      => 'array',
        'input_tokens'  => 'integer',
        'output_tokens' => 'integer',
        'started_at'    => 'datetime',
        'completed_at'  => 'datetime',
    ];

    // Status constants
    public const STATUS_PENDING    = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_COMPLETED  = 'completed';
    public const STATUS_FAILED     = 'failed';

    public function thread(): BelongsTo
    {
        return $this->belongsTo(AssistantThread::class, 'thread_id');
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isProcessing(): bool
    {
        return $this->status === self::STATUS_PROCESSING;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }
}
