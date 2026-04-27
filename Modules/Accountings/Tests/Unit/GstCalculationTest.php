<?php

namespace Modules\Accountings\Tests\Unit;

use Modules\Accountings\Services\FinancialYearService;
use Modules\Accountings\Services\GstReportService;
use Tests\TestCase;

/**
 * Unit tests for GST calculation and financial year derivation.
 *
 * These tests exercise pure logic without requiring a database connection.
 */
class GstCalculationTest extends TestCase
{
    private GstReportService $gstService;
    private FinancialYearService $fyService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->gstService = new GstReportService();
        $this->fyService  = new FinancialYearService();
    }

    // -----------------------------------------------------------------------
    // GST calculation tests
    // -----------------------------------------------------------------------

    /** @test */
    public function gst_inclusive_100_dollars_gives_9_09(): void
    {
        // $100 GST-inclusive at 10% → GST = 100 * 0.10 / 1.10 = 9.0909... ≈ 9.09
        $gst = GstReportService::calculateGst('100.00', 'inclusive', '0.10');

        $this->assertSame('9.09', $gst);
    }

    /** @test */
    public function gst_exclusive_100_dollars_gives_10_00(): void
    {
        // $100 GST-exclusive at 10% → GST = 100 * 0.10 = 10.00
        $gst = GstReportService::calculateGst('100.00', 'exclusive', '0.10');

        $this->assertSame('10.00', $gst);
    }

    /** @test */
    public function gst_calculation_uses_bcmath_not_float(): void
    {
        // Floating point imprecision example: 0.1 + 0.2 ≠ 0.3 in IEEE 754
        // With bcmath this should always be exact
        $gst = GstReportService::calculateGst('0.30', 'exclusive', '0.10');

        $this->assertSame('0.03', $gst);
    }

    /** @test */
    public function gst_on_zero_is_zero(): void
    {
        $gst = GstReportService::calculateGst('0.00', 'inclusive', '0.10');
        $this->assertSame('0.00', $gst);
    }

    // -----------------------------------------------------------------------
    // Financial year tests
    // -----------------------------------------------------------------------

    /** @test */
    public function financial_year_is_auto_calculated_from_invoice_date(): void
    {
        // July 1 2024 → AU FY ends June 2025 → FY2025
        $fy = $this->fyService->fromDate('2024-07-01', 'au');
        $this->assertSame('FY2025', $fy);
    }

    /** @test */
    public function financial_year_june_date_is_end_of_current_fy(): void
    {
        // June 30 2025 → AU FY ends June 2025 → FY2025
        $fy = $this->fyService->fromDate('2025-06-30', 'au');
        $this->assertSame('FY2025', $fy);
    }

    /** @test */
    public function financial_year_july_date_starts_new_au_fy(): void
    {
        // July 1 2025 → AU FY ends June 2026 → FY2026
        $fy = $this->fyService->fromDate('2025-07-01', 'au');
        $this->assertSame('FY2026', $fy);
    }

    /** @test */
    public function financial_year_standard_january_to_december(): void
    {
        $fy = $this->fyService->fromDate('2025-01-15', 'standard');
        $this->assertSame('FY2025', $fy);
    }

    /** @test */
    public function financial_year_standard_december_same_year(): void
    {
        $fy = $this->fyService->fromDate('2025-12-31', 'standard');
        $this->assertSame('FY2025', $fy);
    }
}
