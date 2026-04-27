<?php

namespace Modules\WorksuiteWorkOrders\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use League\Csv\Statement;
use Modules\WorksuiteWorkOrders\Entities\WorkOrder;

class WorkOrdersImportCsvCommand extends Command
{
    protected $signature = 'workorders:import-csv {path} {--dry-run}';
    protected $description = 'Import Work Orders from a CSV with headers: id,title,status,priority,customer_id,scheduled_at,created_at,updated_at';

    public function handle(): int
    {
        $path = $this->argument('path');
        if (!Storage::exists($path)) {
            $this->error("CSV not found at storage/app/{$path}");
            return 1;
        }
        $stream = Storage::readStream($path);
        $csv = Reader::createFromStream($stream);
        $csv->setHeaderOffset(0);
        $stmt = (new Statement());
        $records = $stmt->process($csv);

        $count = 0;
        foreach ($records as $row) {
            $count++;
            if ($this->option('dry-run')) continue;

            $wo = WorkOrder::firstOrNew(['id' => $row['id'] ?? null]);
            $wo->title = $row['title'] ?? '';
            $wo->status = $row['status'] ?? 'draft';
            $wo->priority = $row['priority'] ?? 'normal';
            $wo->customer_id = $row['customer_id'] ?? null;
            $wo->scheduled_at = $row['scheduled_at'] ?? null;
            $wo->created_at = $row['created_at'] ?? now();
            $wo->updated_at = $row['updated_at'] ?? now();
            $wo->save();
        }

        $this->info("Processed {$count} rows from {$path}" . ($this->option('dry-run') ? ' (dry-run)' : ''));
        return 0;
    }
}
