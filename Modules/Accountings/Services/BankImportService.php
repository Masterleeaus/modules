<?php

namespace Modules\Accountings\Services;

use Illuminate\Support\Str;
use Modules\Accountings\Entities\BankTransaction;

class BankImportService
{
    /**
     * Minimal CSV importer.
     * Expected headers (any subset): date, description, amount, balance, reference
     * Date formats: Y-m-d or d/m/Y or m/d/Y
     */
    public static function importCsv(int $bankAccountId, string $csvPath): array
    {
        $batch = Str::uuid()->toString();
        $handle = fopen($csvPath, 'r');
        if (!$handle) {
            return ['batch' => $batch, 'inserted' => 0, 'skipped' => 0];
        }

        $headers = fgetcsv($handle);
        if (!$headers) {
            fclose($handle);
            return ['batch' => $batch, 'inserted' => 0, 'skipped' => 0];
        }

        $map = [];
        foreach ($headers as $i => $h) {
            $key = strtolower(trim($h));
            $map[$key] = $i;
        }

        $inserted = 0;
        $skipped = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $date = self::col($row, $map, ['date','txn_date','transaction_date']);
            $desc = self::col($row, $map, ['description','narration','details']);
            $amount = self::col($row, $map, ['amount','value']);
            $balance = self::col($row, $map, ['balance','running_balance']);
            $ref = self::col($row, $map, ['reference','ref']);

            if ($date === null || $amount === null) {
                $skipped++;
                continue;
            }

            $txnDate = self::parseDate($date);
            if (!$txnDate) {
                $skipped++;
                continue;
            }

            $amt = (float)str_replace([',','$'], '', (string)$amount);

            $hash = hash('sha256', $bankAccountId.'|'.$txnDate.'|'.$amt.'|'.(string)$desc.'|'.(string)$ref);

            $exists = BankTransaction::where('bank_account_id', $bankAccountId)
                ->where('hash', $hash)
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            BankTransaction::create([
                'bank_account_id' => $bankAccountId,
                'txn_date' => $txnDate,
                'description' => $desc,
                'reference' => $ref,
                'amount' => $amt,
                'balance' => $balance !== null ? (float)str_replace([',','$'], '', (string)$balance) : null,
                'import_batch' => $batch,
                'source' => 'csv',
                'hash' => $hash,
            ]);

            $inserted++;
        }

        fclose($handle);

        return ['batch' => $batch, 'inserted' => $inserted, 'skipped' => $skipped];
    }

    private static function col(array $row, array $map, array $keys)
    {
        foreach ($keys as $k) {
            if (array_key_exists($k, $map)) {
                $idx = $map[$k];
                return $row[$idx] ?? null;
            }
        }
        return null;
    }

    private static function parseDate(string $raw): ?string
    {
        $raw = trim($raw);
        if ($raw === '') return null;

        // Y-m-d
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $raw)) return $raw;

        // d/m/Y or m/d/Y - try d/m/Y first
        $parts = preg_split('/[\/\.\-]/', $raw);
        if (count($parts) === 3) {
            [$a,$b,$c] = $parts;
            $a=(int)$a; $b=(int)$b; $c=(int)$c;
            if ($c < 100) $c += 2000;

            // assume d/m/Y if a > 12
            if ($a > 12) return sprintf('%04d-%02d-%02d', $c, $b, $a);
            // otherwise try m/d/Y
            return sprintf('%04d-%02d-%02d', $c, $a, $b);
        }

        return null;
    }
}
