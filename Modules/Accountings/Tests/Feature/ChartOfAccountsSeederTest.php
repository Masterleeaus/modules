<?php

namespace Modules\Accountings\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Accountings\Database\Seeders\ChartOfAccountsSeeder;
use Tests\TestCase;

/**
 * Feature tests for the ChartOfAccountsSeeder.
 */
class ChartOfAccountsSeederTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        if (! Schema::hasTable('accounts')) {
            Schema::create('accounts', function ($table) {
                $table->id();
                $table->unsignedBigInteger('organization_id')->index();
                $table->string('code', 20);
                $table->string('name', 191);
                $table->string('type', 30);
                $table->boolean('is_system')->default(false);
                $table->string('xero_account_id', 191)->nullable();
                $table->timestamps();
                $table->unique(['organization_id', 'code'], 'accounts_org_code_unique');
            });
        }
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('accounts');
        parent::tearDown();
    }

    /** @test */
    public function seeder_creates_accounts_for_organization(): void
    {
        $seeder = new ChartOfAccountsSeeder();
        $seeder->run(1);

        $count = DB::table('accounts')->where('organization_id', 1)->count();
        $this->assertGreaterThan(10, $count, 'Should seed at least 10 accounts');
    }

    /** @test */
    public function seeder_includes_required_system_accounts(): void
    {
        $seeder = new ChartOfAccountsSeeder();
        $seeder->run(1);

        $systemCodes = ['1000', '1100', '2000', '2100', '3000', '4000'];

        foreach ($systemCodes as $code) {
            $exists = DB::table('accounts')
                ->where('organization_id', 1)
                ->where('code', $code)
                ->where('is_system', true)
                ->exists();

            $this->assertTrue($exists, "System account {$code} should exist");
        }
    }

    /** @test */
    public function seeder_includes_cleaning_revenue_accounts(): void
    {
        $seeder = new ChartOfAccountsSeeder();
        $seeder->run(1);

        $revenueCodes = ['4000', '4001', '4002', '4003', '4004'];

        foreach ($revenueCodes as $code) {
            $exists = DB::table('accounts')
                ->where('organization_id', 1)
                ->where('code', $code)
                ->where('type', 'revenue')
                ->exists();

            $this->assertTrue($exists, "Revenue account {$code} should exist");
        }
    }

    /** @test */
    public function seeder_is_idempotent(): void
    {
        $seeder = new ChartOfAccountsSeeder();
        $seeder->run(1);
        $countFirst = DB::table('accounts')->where('organization_id', 1)->count();

        $seeder->run(1); // Run twice

        $countSecond = DB::table('accounts')->where('organization_id', 1)->count();

        $this->assertSame($countFirst, $countSecond, 'Running seeder twice should not create duplicates');
    }

    /** @test */
    public function seeder_scopes_accounts_to_organization(): void
    {
        $seeder = new ChartOfAccountsSeeder();
        $seeder->run(1);
        $seeder->run(2);

        $org1Count = DB::table('accounts')->where('organization_id', 1)->count();
        $org2Count = DB::table('accounts')->where('organization_id', 2)->count();

        $this->assertSame($org1Count, $org2Count, 'Both orgs should get same number of accounts');
        $this->assertGreaterThan(0, $org1Count);
    }
}
