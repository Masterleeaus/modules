<?php

namespace Modules\Accountings\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Accountings\Entities\Account;
use Modules\Accountings\Entities\JournalEntry;
use Modules\Accountings\Entities\JournalLine;
use Modules\Accountings\Services\JournalAutoCreationService;
use Tests\TestCase;

/**
 * Feature tests for double-entry journal auto-creation.
 */
class JournalAutoCreationTest extends TestCase
{
    use RefreshDatabase;

    private int $orgId = 1;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createTables();
        $this->seedAccounts();
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('journal_lines');
        Schema::dropIfExists('journal_entries');
        Schema::dropIfExists('accounts');
        parent::tearDown();
    }

    private function createTables(): void
    {
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

        if (! Schema::hasTable('journal_entries')) {
            Schema::create('journal_entries', function ($table) {
                $table->id();
                $table->unsignedBigInteger('organization_id')->index();
                $table->string('reference_type', 100)->nullable();
                $table->unsignedBigInteger('reference_id')->nullable();
                $table->text('description')->nullable();
                $table->date('entry_date');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('journal_lines')) {
            Schema::create('journal_lines', function ($table) {
                $table->id();
                $table->unsignedBigInteger('journal_entry_id')->index();
                $table->unsignedBigInteger('account_id')->index();
                $table->decimal('debit', 12, 2)->default(0);
                $table->decimal('credit', 12, 2)->default(0);
                $table->text('description')->nullable();
                $table->unsignedBigInteger('tax_code_id')->nullable();
            });
        }
    }

    private function seedAccounts(): void
    {
        foreach ([
            ['code' => '1000', 'name' => 'Cash and Bank',        'type' => 'asset',     'is_system' => true],
            ['code' => '1100', 'name' => 'Accounts Receivable',  'type' => 'asset',     'is_system' => true],
            ['code' => '2000', 'name' => 'Accounts Payable',     'type' => 'liability', 'is_system' => true],
            ['code' => '5200', 'name' => 'Cleaning Supplies',    'type' => 'expense',   'is_system' => false],
        ] as $account) {
            DB::table('accounts')->insert(array_merge($account, [
                'organization_id' => $this->orgId,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]));
        }
    }

    /** @test */
    public function payment_received_creates_balanced_journal_entry(): void
    {
        $service = new JournalAutoCreationService();

        $payment = (object) [
            'id'              => 1,
            'organization_id' => $this->orgId,
            'amount'          => 110.00,
            'reference'       => 'PAY-001',
            'paid_at'         => now(),
        ];

        $service->onPaymentReceived($payment);

        $entry = JournalEntry::where('reference_type', 'Payment')
            ->where('reference_id', 1)
            ->first();

        $this->assertNotNull($entry, 'Journal entry should be created');

        $lines = JournalLine::where('journal_entry_id', $entry->id)->get();
        $this->assertCount(2, $lines, 'Should have two journal lines');

        $totalDebit  = $lines->sum('debit');
        $totalCredit = $lines->sum('credit');
        $this->assertSame((float) 110.00, (float) $totalDebit,  'Debit total should equal payment amount');
        $this->assertSame((float) 110.00, (float) $totalCredit, 'Credit total should equal payment amount');
    }

    /** @test */
    public function payment_received_debits_cash_credits_ar(): void
    {
        $service = new JournalAutoCreationService();

        $payment = (object) [
            'id'              => 2,
            'organization_id' => $this->orgId,
            'amount'          => 220.00,
            'reference'       => 'PAY-002',
            'paid_at'         => now(),
        ];

        $service->onPaymentReceived($payment);

        $entry = JournalEntry::where('reference_id', 2)->first();
        $this->assertNotNull($entry);

        $cashAccount = Account::where('organization_id', $this->orgId)->where('code', '1000')->first();
        $arAccount   = Account::where('organization_id', $this->orgId)->where('code', '1100')->first();

        $debitLine = JournalLine::where('journal_entry_id', $entry->id)
            ->where('account_id', $cashAccount->id)
            ->first();
        $creditLine = JournalLine::where('journal_entry_id', $entry->id)
            ->where('account_id', $arAccount->id)
            ->first();

        $this->assertNotNull($debitLine,  'Cash/Bank should have a debit line');
        $this->assertNotNull($creditLine, 'Accounts Receivable should have a credit line');
        $this->assertSame(220.00, (float) $debitLine->debit);
        $this->assertSame(220.00, (float) $creditLine->credit);
    }

    /** @test */
    public function supply_purchased_creates_balanced_journal_entry(): void
    {
        $service = new JournalAutoCreationService();

        $purchase = (object) [
            'id'              => 10,
            'organization_id' => $this->orgId,
            'amount'          => 55.00,
            'description'     => 'Cleaning products batch',
            'purchased_at'    => now(),
        ];

        $service->onSupplyPurchased($purchase);

        $entry = JournalEntry::where('reference_id', 10)->first();
        $this->assertNotNull($entry);

        $lines       = JournalLine::where('journal_entry_id', $entry->id)->get();
        $totalDebit  = $lines->sum('debit');
        $totalCredit = $lines->sum('credit');

        $this->assertSame((float) 55.00, (float) $totalDebit);
        $this->assertSame((float) 55.00, (float) $totalCredit);
    }

    /** @test */
    public function zero_amount_payment_does_not_create_journal(): void
    {
        $service = new JournalAutoCreationService();

        $payment = (object) [
            'id'              => 99,
            'organization_id' => $this->orgId,
            'amount'          => 0,
            'paid_at'         => now(),
        ];

        $service->onPaymentReceived($payment);

        $count = JournalEntry::where('reference_type', 'Payment')
            ->where('reference_id', 99)
            ->count();

        $this->assertSame(0, $count, 'No journal should be created for zero-amount payment');
    }
}
