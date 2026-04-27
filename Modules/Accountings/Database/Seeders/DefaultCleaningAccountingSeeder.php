<?php

namespace Modules\Accountings\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DefaultCleaningAccountingSeeder extends Seeder
{
    public function run(): void
    {
        // Template rows: company_id/user_id can be NULL. HasUserScope allows template visibility.
        if (DB::getSchemaBuilder()->hasTable('acc_tax_codes')) {
            $defaults = [
                ['code' => 'GST', 'name' => 'GST 10%', 'rate' => 0.1000],
                ['code' => 'FRE', 'name' => 'GST Free', 'rate' => 0.0000],
                ['code' => 'N-T', 'name' => 'No GST (Out of Scope)', 'rate' => 0.0000],
            ];

            foreach ($defaults as $row) {
                $exists = DB::table('acc_tax_codes')->whereNull('company_id')->whereNull('user_id')->where('code', $row['code'])->exists();
                if (!$exists) {
                    DB::table('acc_tax_codes')->insert(array_merge($row, [
                        'company_id' => null,
                        'user_id' => null,
                        'is_active' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]));
                }
            }
        }

        if (DB::getSchemaBuilder()->hasTable('acc_service_lines')) {
            $services = [
                'Residential', 'Bond / End of Lease', 'Carpet', 'Pressure', 'Pool',
                'Commercial', 'Builders / New Build', 'Car / Detailing', 'Windows', 'General'
            ];

            foreach ($services as $name) {
                $slug = Str::slug($name);
                $exists = DB::table('acc_service_lines')->whereNull('company_id')->whereNull('user_id')->where('slug', $slug)->exists();
                if (!$exists) {
                    DB::table('acc_service_lines')->insert([
                        'company_id' => null,
                        'user_id' => null,
                        'name' => $name,
                        'slug' => $slug,
                        'is_active' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
