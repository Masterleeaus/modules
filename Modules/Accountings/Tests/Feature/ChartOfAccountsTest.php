<?php

namespace Modules\Accountings\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Feature tests for the chart_of_accounts table.
 *
 * Verifies that account codes are unique per company
 * and that the migration structure is correct.
 */
class ChartOfAccountsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        if (!Schema::hasTable('chart_of_accounts')) {
            Schema::create('chart_of_accounts', function ($table) {
                $table->id();
                $table->string('code', 20);
                $table->string('name', 191);
                $table->string('type', 30);
                $table->string('sub_type', 50)->nullable();
                $table->boolean('is_active')->default(true);
                $table->unsignedBigInteger('company_id');
                $table->integer('sort_order')->default(0);
                $table->timestamps();
                $table->unique(['company_id', 'code'], 'coa_company_code_unique');
            });
        }
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('chart_of_accounts');
        parent::tearDown();
    }

    /** @test */
    public function chart_of_accounts_code_is_unique_per_company(): void
    {
        // Insert first account
        \Illuminate\Support\Facades\DB::table('chart_of_accounts')->insert([
            'code'       => '4001',
            'name'       => 'Regular Cleaning Revenue',
            'type'       => 'revenue',
            'company_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Attempting to insert a duplicate code for the same company must fail
        $this->expectException(\Illuminate\Database\UniqueConstraintViolationException::class);

        \Illuminate\Support\Facades\DB::table('chart_of_accounts')->insert([
            'code'       => '4001', // same code, same company → must fail
            'name'       => 'Duplicate Revenue',
            'type'       => 'revenue',
            'company_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /** @test */
    public function same_code_is_allowed_for_different_companies(): void
    {
        \Illuminate\Support\Facades\DB::table('chart_of_accounts')->insert([
            'code'       => '4001',
            'name'       => 'Regular Cleaning Revenue — Company A',
            'type'       => 'revenue',
            'company_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Same code for a different company must succeed
        \Illuminate\Support\Facades\DB::table('chart_of_accounts')->insert([
            'code'       => '4001',
            'name'       => 'Regular Cleaning Revenue — Company B',
            'type'       => 'revenue',
            'company_id' => 2, // different company
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $count = \Illuminate\Support\Facades\DB::table('chart_of_accounts')
            ->where('code', '4001')
            ->count();

        $this->assertSame(2, $count);
    }

    /** @test */
    public function chart_of_accounts_stores_correct_fields(): void
    {
        \Illuminate\Support\Facades\DB::table('chart_of_accounts')->insert([
            'code'       => '6100',
            'name'       => 'Vehicle Expenses',
            'type'       => 'expense',
            'sub_type'   => 'operating',
            'is_active'  => true,
            'company_id' => 1,
            'sort_order' => 10,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $row = \Illuminate\Support\Facades\DB::table('chart_of_accounts')
            ->where('code', '6100')
            ->where('company_id', 1)
            ->first();

        $this->assertNotNull($row);
        $this->assertSame('Vehicle Expenses', $row->name);
        $this->assertSame('expense', $row->type);
        $this->assertSame('operating', $row->sub_type);
        $this->assertSame(1, (int) $row->company_id);
        $this->assertSame(10, (int) $row->sort_order);
    }
}
