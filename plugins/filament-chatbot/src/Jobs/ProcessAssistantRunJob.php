<?php

namespace TitanZero\FilamentChatbot\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use TitanZero\FilamentChatbot\Models\AssistantRun;
use TitanZero\FilamentChatbot\Services\RunProcessorService;

class ProcessAssistantRunJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public int $timeout = 120;

    public function __construct(
        public readonly AssistantRun $run,
    ) {}

    public function handle(RunProcessorService $processor): void
    {
        $processor->process($this->run);
    }
}
