<?php

namespace TitanZero\FilamentChatbot\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssistantThread extends Model
{
    protected $table = 'chatbot_assistant_threads';

    protected $fillable = [
        'user_identifier',
        'assistant_key',
        'context',
    ];

    protected $casts = [
        'context' => 'array',
    ];

    public function runs(): HasMany
    {
        return $this->hasMany(AssistantRun::class, 'thread_id');
    }

    public function lastRun(): ?AssistantRun
    {
        return $this->runs()->latest()->first();
    }

    /**
     * Return the full message history for this thread (user + assistant pairs).
     */
    public function messageHistory(): array
    {
        return $this->completedAndFailedRuns()
            ->whereIn('status', [AssistantRun::STATUS_COMPLETED])
            ->orderBy('id')
            ->get()
            ->flatMap(function (AssistantRun $run) {
                $messages = [];

                $messages[] = ['role' => 'user', 'content' => $run->input];

                if ($run->output) {
                    $messages[] = ['role' => 'assistant', 'content' => $run->output];
                }

                return $messages;
            })
            ->values()
            ->toArray();
    }

    /**
     * Query builder scoped to completed and failed runs, ordered by creation.
     */
    public function completedAndFailedRuns(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->runs()
            ->whereIn('status', [AssistantRun::STATUS_COMPLETED, AssistantRun::STATUS_FAILED])
            ->orderBy('id');
    }
}
