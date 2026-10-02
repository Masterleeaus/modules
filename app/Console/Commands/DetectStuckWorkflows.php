<?php

namespace App\Console\Commands;

use App\Events\WorkflowStuck;
use App\Models\Job;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class DetectStuckWorkflows extends Command
{
    protected $signature   = 'workflow:detect-stuck';
    protected $description = 'Detect entities stuck in a workflow state beyond the configured threshold and fire WorkflowStuck events.';

    /**
     * Map of entity type → [Eloquent model class, state-column, last-updated column].
     * Extend this array to cover Estimates, Invoices, etc. as those workflows
     * are formalised.
     *
     * @var array<string,array{model:class-string,state_col:string,updated_col:string}>
     */
    private array $entityMap = [
        'job' => [
            'model'       => Job::class,
            'state_col'   => 'status',
            'updated_col' => 'updated_at',
        ],
    ];

    public function handle(): int
    {
        $definitions = config('workflows', []);

        foreach ($definitions as $entityType => $definition) {
            $thresholds = $definition['stuck_thresholds'] ?? [];

            if (empty($thresholds) || ! isset($this->entityMap[$entityType])) {
                continue;
            }

            $map = $this->entityMap[$entityType];

            foreach ($thresholds as $state => $hours) {
                $cutoff = Carbon::now()->subHours($hours);

                $stuck = ($map['model'])::query()
                    ->where($map['state_col'], $state)
                    ->where($map['updated_col'], '<=', $cutoff)
                    ->get();

                foreach ($stuck as $entity) {
                    $hoursStuck = (int) Carbon::parse($entity->{$map['updated_col']})->diffInHours(now());

                    WorkflowStuck::dispatch($entityType, $entity, $state, $hoursStuck);

                    $this->line(
                        sprintf(
                            '[%s] %s #%s has been in "%s" for %dh (threshold: %dh)',
                            now()->toDateTimeString(),
                            $entityType,
                            $entity->getKey(),
                            $state,
                            $hoursStuck,
                            $hours,
                        )
                    );
                }
            }
        }

        $this->info('Stuck-state detection complete.');

        return self::SUCCESS;
    }
}
