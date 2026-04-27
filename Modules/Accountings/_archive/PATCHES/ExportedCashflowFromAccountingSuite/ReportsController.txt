<?php

namespace Modules\AccountingCore\app\Http\Controllers;

use App\ApiClasses\Success;
use App\Http\Controllers\Controller;
use App\Services\AddonService\AddonService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\AccountingCore\app\Models\BasicTransaction;
use Modules\AccountingCore\app\Models\BasicTransactionCategory;

class ReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:accountingcore.reports.index')->only(['index']);
        $this->middleware('permission:accountingcore.reports.generate')->only(['generate']);
        $this->middleware('permission:accountingcore.reports.export')->only(['export']);
        $this->middleware('permission:accountingcore.reports.summary')->only(['summary']);
        $this->middleware('permission:accountingcore.reports.summary.export-pdf')->only(['exportSummaryPdf']);
        $this->middleware('permission:accountingcore.reports.cashflow')->only(['cashflow']);
        $this->middleware('permission:accountingcore.reports.cashflow.export-pdf')->only(['exportCashflowPdf']);
        $this->middleware('permission:accountingcore.reports.category-performance')->only(['categoryPerformance']);
    }

    /**
     * Display reports index page.
     */
    public function index(Request $request)
    {
        // Check if AccountingPro is enabled
        $addonService = app(AddonService::class);
        if ($addonService->isAddonEnabled('AccountingPro')) {
            return redirect()->route('accountingpro.reports.general_ledger.index');
        }

        $categories = BasicTransactionCategory::active()->orderBy('name')->get();

        $breadcrumbs = [
            ['name' => __('Accounting'), 'url' => route('accountingcore.dashboard')],
            ['name' => __('Reports'), 'url' => ''],
        ];

        return view('accountingcore::reports.index', compact('categories', 'breadcrumbs'));
    }

    /**
     * Generate report based on filters.
     */
    public function generate(Request $request)
    {
        $request->validate([
            'dateRange' => 'required|string',
            'reportType' => 'required|in:summary,category,monthly',
        ]);

        $dates = explode(' to ', $request->dateRange);
        $startDate = Carbon::parse($dates[0])->startOfDay();
        $endDate = Carbon::parse($dates[1] ?? $dates[0])->endOfDay();

        $data = match ($request->reportType) {
            'summary' => $this->generateSummaryReport($startDate, $endDate, $request->categoryFilter),
            'category' => $this->generateCategoryReport($startDate, $endDate, $request->categoryFilter),
            'monthly' => $this->generateMonthlyReport($startDate, $endDate, $request->categoryFilter),
        };

        return Success::response($data);
    }

    /**
     * Export report.
     */
    public function export(Request $request)
    {
        $dates = explode(' to ', $request->dateRange);
        $startDate = Carbon::parse($dates[0])->startOfDay();
        $endDate = Carbon::parse($dates[1] ?? $dates[0])->endOfDay();

        $data = match ($request->reportType) {
            'summary' => $this->generateSummaryReport($startDate, $endDate, $request->categoryFilter),
            'category' => $this->generateCategoryReport($startDate, $endDate, $request->categoryFilter),
            'monthly' => $this->generateMonthlyReport($startDate, $endDate, $request->categoryFilter),
        };

        $pdf = Pdf::loadView('accountingcore::reports.export', compact('data'));

        return $pdf->download('accounting-report-'.now()->format('Y-m-d').'.pdf');
    }

    /**
     * Display income/expense summary report.
     */
    public function summary(Request $request)
    {
        // Check if AccountingPro is enabled
        $addonService = app(AddonService::class);
        if ($addonService->isAddonEnabled('AccountingPro')) {
            return redirect()->route('accountingpro.reports.profit_loss.index');
        }

        // Get date range
        $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date')) : now()->startOfMonth();
        $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date')) : now()->endOfMonth();

        // Get summary data
        $summary = BasicTransaction::getSummaryForPeriod($startDate, $endDate);

        // Get category breakdown
        $incomeByCategory = BasicTransaction::income()
            ->forDateRange($startDate, $endDate)
            ->selectRaw('category_id, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('category_id')
            ->with('category')
            ->orderBy('total', 'desc')
            ->get();

        $expenseByCategory = BasicTransaction::expense()
            ->forDateRange($startDate, $endDate)
            ->selectRaw('category_id, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('category_id')
            ->with('category')
            ->orderBy('total', 'desc')
            ->get();

        // Get monthly breakdown
        $monthlyBreakdown = $this->getMonthlyBreakdown($startDate, $endDate);

        // Breadcrumb data
        $breadcrumbs = [
            ['name' => __('Accounting'), 'url' => route('accountingcore.dashboard')],
            ['name' => __('Reports'), 'url' => route('accountingcore.reports.summary')],
            ['name' => __('Income & Expense Summary'), 'url' => ''],
        ];

        return view('accountingcore::reports.summary', compact(
            'summary',
            'incomeByCategory',
            'expenseByCategory',
            'monthlyBreakdown',
            'startDate',
            'endDate',
            'breadcrumbs'
        ));
    }

    /**
     * Display cash flow report.
     */
    public function cashflow(Request $request)
    {
        // Get date range
        $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date')) : now()->startOfMonth();
        $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date')) : now()->endOfMonth();

        // Get opening balance
        $openingBalance = BasicTransaction::getRunningBalance($startDate->copy()->subDay());

        // Get transactions for the period
        $transactions = BasicTransaction::with('category')
            ->forDateRange($startDate, $endDate)
            ->orderBy('transaction_date')
            ->orderBy('created_at')
            ->get();

        // Calculate running balance
        $runningBalance = $openingBalance['balance'];
        $cashflowData = [];

        foreach ($transactions as $transaction) {
            if ($transaction->type === 'income') {
                $runningBalance += $transaction->amount;
            } else {
                $runningBalance -= $transaction->amount;
            }

            $cashflowData[] = [
                'date' => $transaction->transaction_date,
                'transaction' => $transaction,
                'running_balance' => $runningBalance,
            ];
        }

        // Get closing balance
        $closingBalance = BasicTransaction::getRunningBalance($endDate);

        // Breadcrumb data
        $breadcrumbs = [
            ['name' => __('Accounting'), 'url' => route('accountingcore.dashboard')],
            ['name' => __('Reports'), 'url' => route('accountingcore.reports.summary')],
            ['name' => __('Cash Flow'), 'url' => ''],
        ];

        return view('accountingcore::reports.cashflow', compact(
            'openingBalance',
            'closingBalance',
            'cashflowData',
            'startDate',
            'endDate',
            'breadcrumbs'
        ));
    }

    /**
     * Export summary report as PDF.
     */
    public function exportSummaryPdf(Request $request)
    {
        $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date')) : now()->startOfMonth();
        $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date')) : now()->endOfMonth();

        $summary = BasicTransaction::getSummaryForPeriod($startDate, $endDate);

        $incomeByCategory = BasicTransaction::income()
            ->forDateRange($startDate, $endDate)
            ->selectRaw('category_id, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('category_id')
            ->with('category')
            ->orderBy('total', 'desc')
            ->get();

        $expenseByCategory = BasicTransaction::expense()
            ->forDateRange($startDate, $endDate)
            ->selectRaw('category_id, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('category_id')
            ->with('category')
            ->orderBy('total', 'desc')
            ->get();

        $pdf = PDF::loadView('accountingcore::reports.summary-pdf', compact(
            'summary',
            'incomeByCategory',
            'expenseByCategory',
            'startDate',
            'endDate'
        ));

        $filename = 'income-expense-summary-'.$startDate->format('Y-m-d').'-to-'.$endDate->format('Y-m-d').'.pdf';

        return $pdf->download($filename);
    }

    /**
     * Export cash flow report as PDF.
     */
    public function exportCashflowPdf(Request $request)
    {
        $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date')) : now()->startOfMonth();
        $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date')) : now()->endOfMonth();

        $openingBalance = BasicTransaction::getRunningBalance($startDate->copy()->subDay());

        $transactions = BasicTransaction::with('category')
            ->forDateRange($startDate, $endDate)
            ->orderBy('transaction_date')
            ->orderBy('created_at')
            ->get();

        $runningBalance = $openingBalance['balance'];
        $cashflowData = [];

        foreach ($transactions as $transaction) {
            if ($transaction->type === 'income') {
                $runningBalance += $transaction->amount;
            } else {
                $runningBalance -= $transaction->amount;
            }

            $cashflowData[] = [
                'date' => $transaction->transaction_date,
                'transaction' => $transaction,
                'running_balance' => $runningBalance,
            ];
        }

        $closingBalance = BasicTransaction::getRunningBalance($endDate);

        $pdf = PDF::loadView('accountingcore::reports.cashflow-pdf', compact(
            'openingBalance',
            'closingBalance',
            'cashflowData',
            'startDate',
            'endDate'
        ));

        $filename = 'cashflow-'.$startDate->format('Y-m-d').'-to-'.$endDate->format('Y-m-d').'.pdf';

        return $pdf->download($filename);
    }

    /**
     * Get category performance data.
     */
    public function categoryPerformance(Request $request)
    {
        $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date')) : now()->startOfMonth();
        $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date')) : now()->endOfMonth();
        $type = $request->get('type', 'all'); // income, expense, all

        $query = BasicTransactionCategory::active();

        if ($type !== 'all') {
            $query->where('type', $type);
        }

        $categories = $query->get();

        $performanceData = [];

        foreach ($categories as $category) {
            $transactionQuery = $category->transactions()
                ->forDateRange($startDate, $endDate);

            $total = $transactionQuery->sum('amount');
            $count = $transactionQuery->count();

            if ($total > 0) {
                $performanceData[] = [
                    'category' => $category,
                    'total' => $total,
                    'count' => $count,
                    'average' => $count > 0 ? $total / $count : 0,
                ];
            }
        }

        // Sort by total descending
        usort($performanceData, function ($a, $b) {
            return $b['total'] <=> $a['total'];
        });

        // Breadcrumb data
        $breadcrumbs = [
            ['name' => __('Accounting'), 'url' => route('accountingcore.dashboard')],
            ['name' => __('Reports'), 'url' => route('accountingcore.reports.summary')],
            ['name' => __('Category Performance'), 'url' => ''],
        ];

        return view('accountingcore::reports.category-performance', compact(
            'performanceData',
            'startDate',
            'endDate',
            'type',
            'breadcrumbs'
        ));
    }

    /**
     * Get monthly breakdown for a period.
     */
    private function getMonthlyBreakdown(Carbon $startDate, Carbon $endDate)
    {
        $months = [];
        $current = $startDate->copy()->startOfMonth();

        while ($current <= $endDate) {
            $monthStart = $current->copy()->startOfMonth();
            $monthEnd = $current->copy()->endOfMonth();

            if ($monthEnd > $endDate) {
                $monthEnd = $endDate;
            }

            $summary = BasicTransaction::getSummaryForPeriod($monthStart, $monthEnd);

            $months[] = [
                'month' => $current->format('F Y'),
                'start_date' => $monthStart,
                'end_date' => $monthEnd,
                'income' => $summary['income'],
                'expense' => $summary['expense'],
                'profit' => $summary['profit'],
            ];

            $current->addMonth();
        }

        return $months;
    }

    /**
     * Generate summary report data.
     */
    private function generateSummaryReport($startDate, $endDate, $categoryId = null)
    {
        $query = BasicTransaction::forDateRange($startDate, $endDate);

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $income = (clone $query)->income()->sum('amount');
        $expenses = (clone $query)->expense()->sum('amount');
        $balance = $income - $expenses;

        $transactions = $query->with('category')
            ->orderBy('transaction_date', 'desc')
            ->get();

        return [
            'title' => __('Income & Expense Summary'),
            'summary' => [
                'income' => $income,
                'expenses' => $expenses,
                'balance' => $balance,
            ],
            'columns' => [__('Date'), __('Description'), __('Category'), __('Type'), __('Amount')],
            'rows' => $transactions->map(function ($t) {
                return [
                    \App\Helpers\FormattingHelper::formatDate($t->transaction_date),
                    $t->description,
                    $t->category->name,
                    ucfirst($t->type),
                    $t->type === 'income' ? $t->amount : -$t->amount,
                ];
            })->toArray(),
            'totals' => [__('Total'), '', '', '', $balance],
        ];
    }

    /**
     * Generate category report data.
     */
    private function generateCategoryReport($startDate, $endDate, $categoryId = null)
    {
        $query = BasicTransactionCategory::active();

        if ($categoryId) {
            $query->where('id', $categoryId);
        }

        $categories = $query->withSum(['transactions' => function ($q) use ($startDate, $endDate) {
            $q->forDateRange($startDate, $endDate)->income();
        }], 'amount')
            ->withSum(['transactions as expense_total' => function ($q) use ($startDate, $endDate) {
                $q->forDateRange($startDate, $endDate)->expense();
            }], 'amount')
            ->get();

        $totalIncome = $categories->sum('transactions_sum_amount');
        $totalExpenses = $categories->sum('expense_total');

        return [
            'title' => __('Category Breakdown'),
            'summary' => [
                'income' => $totalIncome,
                'expenses' => $totalExpenses,
                'balance' => $totalIncome - $totalExpenses,
            ],
            'columns' => [__('Category'), __('Type'), __('Income'), __('Expenses'), __('Net')],
            'rows' => $categories->map(function ($c) {
                $income = $c->transactions_sum_amount ?? 0;
                $expense = $c->expense_total ?? 0;

                return [
                    $c->name,
                    ucfirst($c->type),
                    $income,
                    -$expense,
                    $income - $expense,
                ];
            })->toArray(),
            'totals' => [__('Total'), '', $totalIncome, -$totalExpenses, $totalIncome - $totalExpenses],
        ];
    }

    /**
     * Generate monthly report data.
     */
    private function generateMonthlyReport($startDate, $endDate, $categoryId = null)
    {
        $query = BasicTransaction::forDateRange($startDate, $endDate);

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $monthlyData = $query->selectRaw('YEAR(transaction_date) as year, MONTH(transaction_date) as month, type, SUM(amount) as total')
            ->groupBy('year', 'month', 'type')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $months = [];
        $rows = [];

        foreach ($monthlyData as $data) {
            $monthKey = $data->year.'-'.str_pad($data->month, 2, '0', STR_PAD_LEFT);
            if (! isset($months[$monthKey])) {
                $months[$monthKey] = ['income' => 0, 'expense' => 0];
            }
            $months[$monthKey][$data->type] = $data->total;
        }

        $totalIncome = 0;
        $totalExpenses = 0;

        foreach ($months as $month => $data) {
            $income = $data['income'];
            $expense = $data['expense'];
            $totalIncome += $income;
            $totalExpenses += $expense;

            $rows[] = [
                Carbon::parse($month.'-01')->format('F Y'),
                $income,
                -$expense,
                $income - $expense,
            ];
        }

        return [
            'title' => __('Monthly Comparison'),
            'summary' => [
                'income' => $totalIncome,
                'expenses' => $totalExpenses,
                'balance' => $totalIncome - $totalExpenses,
            ],
            'columns' => [__('Month'), __('Income'), __('Expenses'), __('Net')],
            'rows' => $rows,
            'totals' => [__('Total'), $totalIncome, -$totalExpenses, $totalIncome - $totalExpenses],
        ];
    }
}
