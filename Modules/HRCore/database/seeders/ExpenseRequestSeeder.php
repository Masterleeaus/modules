<?php

namespace Modules\HRCore\Database\Seeders;

use App\Enums\ExpenseRequestStatus;
use App\Enums\Status;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\HRCore\app\Models\ExpenseRequest;
use Modules\HRCore\app\Models\ExpenseType;

class ExpenseRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            // Get some users to create expense requests for
            $employees = User::whereHas('roles', function ($query) {
                $query->whereIn('name', ['employee', 'team_leader', 'hr_executive']);
            })->take(10)->get();

            if ($employees->isEmpty()) {
                // If no employees with specific roles, get any non-admin users
                $employees = User::whereHas('roles', function ($query) {
                    $query->whereNotIn('name', ['super_admin', 'admin']);
                })->take(10)->get();
            }

            if ($employees->isEmpty()) {
                // Last resort: get any users
                $employees = User::take(10)->get();
            }

            if ($employees->isEmpty()) {
                $this->command->warn('No users found. Please run user seeders first.');

                return;
            }

            // Get expense types
            $expenseTypes = ExpenseType::where('status', Status::ACTIVE->value)->get();

            if ($expenseTypes->isEmpty()) {
                $this->command->warn('No expense types found. Please run ExpenseTypeSeeder first.');

                return;
            }

            // Get managers for approval
            $managers = User::whereHas('roles', function ($query) {
                $query->whereIn('name', ['hr_manager', 'team_leader', 'admin']);
            })->get();

            if ($managers->isEmpty()) {
                // If no managers, use the first user as manager
                $managers = User::take(1)->get();
            }

            $expenseRequests = [];
            $currentDate = Carbon::now();

            // Get the last expense number to continue from there
            $lastExpense = ExpenseRequest::orderBy('id', 'desc')->first();
            $expenseCounter = 1;
            if ($lastExpense && preg_match('/EXP-\d{6}-(\d{4})/', $lastExpense->expense_number, $matches)) {
                $expenseCounter = intval($matches[1]) + 1;
            }

            // Create expense requests for the last 3 months
            foreach ($employees as $employee) {
                // Create 2-5 expense requests per employee
                $numRequests = rand(2, 5);

                for ($i = 0; $i < $numRequests; $i++) {
                    $expenseType = $expenseTypes->random();
                    $expenseDate = $currentDate->copy()->subDays(rand(1, 90));
                    $status = $this->getRandomStatus();

                    // Generate amount based on expense type
                    $amount = $this->generateAmount($expenseType);

                    $expenseRequest = [
                        'expense_number' => 'EXP-'.date('Ym', strtotime($expenseDate)).'-'.str_pad($expenseCounter++, 4, '0', STR_PAD_LEFT),
                        'expense_type_id' => $expenseType->id,
                        'user_id' => $employee->id,
                        'expense_date' => $expenseDate,
                        'amount' => $amount,
                        'currency' => 'USD',
                        'title' => $expenseType->name.' - '.$expenseDate->format('M d, Y'),
                        'description' => $this->generateDescription($expenseType),
                        'status' => $status,
                        'project_code' => rand(0, 1) == 1 ? 'PRJ-'.str_pad(rand(1, 100), 3, '0', STR_PAD_LEFT) : null,
                        'cost_center' => rand(0, 1) == 1 ? 'CC-'.str_pad(rand(1, 20), 3, '0', STR_PAD_LEFT) : null,
                        'created_by_id' => $employee->id,
                        'updated_by_id' => $employee->id,
                        'created_at' => $expenseDate->copy()->addHours(rand(1, 24)),
                        'updated_at' => $expenseDate->copy()->addHours(rand(25, 48)),
                    ];

                    // Add approval details for approved/rejected expenses
                    if ($status === ExpenseRequestStatus::APPROVED->value || $status === ExpenseRequestStatus::PROCESSED->value) {
                        $manager = $managers->random();
                        $expenseRequest['approved_by_id'] = $manager->id;
                        $expenseRequest['approved_at'] = $expenseDate->copy()->addDays(rand(1, 3));
                        $expenseRequest['approval_remarks'] = 'Approved for reimbursement';
                        $expenseRequest['approved_amount'] = $amount; // Usually approved for full amount
                    }

                    if ($status === ExpenseRequestStatus::REJECTED->value) {
                        $manager = $managers->random();
                        $expenseRequest['rejected_by_id'] = $manager->id;
                        $expenseRequest['rejected_at'] = $expenseDate->copy()->addDays(rand(1, 3));
                        $expenseRequest['rejection_reason'] = $this->getRandomRejectionReason();
                    }

                    // Add payment details for processed expenses
                    if ($status === ExpenseRequestStatus::PROCESSED->value) {
                        $expenseRequest['processed_by_id'] = $managers->random()->id;
                        $expenseRequest['processed_at'] = $expenseDate->copy()->addDays(rand(5, 10));
                        $expenseRequest['payment_reference'] = 'PAY-'.strtoupper(uniqid());
                        $expenseRequest['processing_notes'] = 'Payment processed via '.$this->getRandomPaymentProcessor();
                    }

                    // Add attachments simulation
                    if ($expenseType->requires_receipt && rand(0, 10) > 2) {
                        $attachments = [];
                        $numAttachments = rand(1, 3);
                        for ($j = 0; $j < $numAttachments; $j++) {
                            $attachments[] = [
                                'name' => 'receipt_'.uniqid().'.pdf',
                                'path' => 'receipts/'.date('Y/m', strtotime($expenseDate)).'/receipt_'.uniqid().'.pdf',
                                'size' => rand(100000, 500000), // Random size between 100KB and 500KB
                                'mime_type' => 'application/pdf',
                            ];
                        }
                        // Store as array, not JSON string - Laravel will handle the conversion
                        $expenseRequest['attachments'] = $attachments;
                    }

                    $expenseRequests[] = $expenseRequest;
                }
            }

            // Insert all expense requests
            foreach ($expenseRequests as $request) {
                ExpenseRequest::create($request);
            }

            DB::commit();
            $this->command->info('Created '.count($expenseRequests).' expense requests successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Error seeding expense requests: '.$e->getMessage());
        }
    }

    private function getRandomStatus(): string
    {
        $statuses = [
            ExpenseRequestStatus::PENDING->value,
            ExpenseRequestStatus::APPROVED->value,
            ExpenseRequestStatus::REJECTED->value,
            ExpenseRequestStatus::PROCESSED->value,
        ];
        $weights = [30, 35, 15, 20]; // Weighted probability

        return $this->weightedRandom($statuses, $weights);
    }

    private function weightedRandom(array $values, array $weights): string
    {
        $total = array_sum($weights);
        $random = rand(1, $total);

        foreach ($values as $i => $value) {
            $random -= $weights[$i];
            if ($random <= 0) {
                return $value;
            }
        }

        return $values[0];
    }

    private function generateAmount(ExpenseType $expenseType): float
    {
        if ($expenseType->default_amount) {
            // Vary around default amount
            $variation = $expenseType->default_amount * 0.3; // 30% variation
            $amount = $expenseType->default_amount + (rand(-100, 100) / 100) * $variation;
        } else {
            // Random amount up to max
            $max = $expenseType->max_amount ?: 500;
            $amount = rand(10, min($max * 100, 50000)) / 100;
        }

        return round($amount, 2);
    }

    private function getRandomPaymentMethod(): string
    {
        $methods = ['cash', 'personal_card', 'corporate_card', 'bank_transfer', 'check'];

        return $methods[array_rand($methods)];
    }

    private function getRandomPaymentProcessor(): string
    {
        $processors = ['Bank Transfer', 'PayPal', 'Direct Deposit', 'Company Check', 'Wire Transfer'];

        return $processors[array_rand($processors)];
    }

    private function generateDescription(ExpenseType $expenseType): string
    {
        $descriptions = [
            'travel' => [
                'Business trip to client location',
                'Team meeting travel expenses',
                'Conference travel arrangements',
                'Customer visit transportation',
                'Sales meeting travel costs',
            ],
            'meals' => [
                'Client lunch meeting',
                'Team dinner for project completion',
                'Working lunch with stakeholders',
                'Business breakfast meeting',
                'Client entertainment dinner',
            ],
            'office' => [
                'Monthly office supplies purchase',
                'Software license renewal',
                'Equipment for home office',
                'Stationery and printing supplies',
                'Computer accessories purchase',
            ],
            'training' => [
                'Professional development course',
                'Industry conference registration',
                'Online certification program',
                'Skills training workshop',
                'Technical training subscription',
            ],
            'communication' => [
                'Monthly mobile phone bill',
                'Internet service for remote work',
                'Video conferencing subscription',
                'Communication tools subscription',
                'International calling charges',
            ],
            'marketing' => [
                'Trade show booth materials',
                'Digital advertising campaign',
                'Promotional materials printing',
                'Social media advertising',
                'Marketing collateral design',
            ],
            'other' => [
                'Miscellaneous business expense',
                'Client meeting parking fees',
                'Document shipping costs',
                'Business registration fees',
                'Professional membership dues',
            ],
        ];

        $category = $expenseType->category;
        $categoryDescriptions = $descriptions[$category] ?? $descriptions['other'];

        return $categoryDescriptions[array_rand($categoryDescriptions)];
    }

    private function generateMerchantName(ExpenseType $expenseType): string
    {
        $merchants = [
            'travel' => ['Delta Airlines', 'United Airlines', 'Hilton Hotels', 'Marriott', 'Uber', 'Lyft', 'Enterprise Rent-A-Car'],
            'meals' => ['The Business Lounge', 'Starbucks', 'Restaurant Plaza', 'Corner Cafe', 'Downtown Grill'],
            'office' => ['Office Depot', 'Staples', 'Amazon Business', 'Best Buy', 'Apple Store'],
            'training' => ['Udemy', 'Coursera', 'LinkedIn Learning', 'Conference Center', 'Training Institute'],
            'communication' => ['Verizon', 'AT&T', 'Comcast', 'T-Mobile', 'Spectrum'],
            'marketing' => ['Google Ads', 'Facebook Business', 'PrintShop Pro', 'Marketing Agency Inc', 'Design Studio'],
            'other' => ['City Parking', 'FedEx', 'UPS', 'Professional Services LLC', 'Business Center'],
        ];

        $category = $expenseType->category;
        $categoryMerchants = $merchants[$category] ?? $merchants['other'];

        return $categoryMerchants[array_rand($categoryMerchants)];
    }

    private function generateNotes(ExpenseType $expenseType, string $status): ?string
    {
        if (rand(0, 10) > 6) {
            return null; // 40% chance of no notes
        }

        $notes = [
            ExpenseRequestStatus::PENDING->value => [
                'Receipt attached for review',
                'Urgent - please approve ASAP',
                'Part of Q3 budget allocation',
                'Related to Project Alpha',
                'Client billable expense',
                'Waiting for manager approval',
                'Submitted for reimbursement',
            ],
            ExpenseRequestStatus::APPROVED->value => [
                'Approved within budget limits',
                'Valid business expense',
                'Documentation complete',
                'Approved for immediate reimbursement',
                'Ready for processing',
            ],
            ExpenseRequestStatus::REJECTED->value => [
                'Missing required documentation',
                'Exceeds budget allocation',
                'Not a valid business expense',
                'Requires additional approval',
            ],
            ExpenseRequestStatus::PROCESSED->value => [
                'Payment processed successfully',
                'Reimbursed via direct deposit',
                'Check issued and mailed',
                'Credited to employee account',
            ],
        ];

        $statusNotes = $notes[$status] ?? $notes[ExpenseRequestStatus::PENDING->value];

        return $statusNotes[array_rand($statusNotes)];
    }

    private function getRandomRejectionReason(): string
    {
        $reasons = [
            'Receipt not provided',
            'Exceeds maximum allowable amount',
            'Not a valid business expense',
            'Missing manager pre-approval',
            'Incorrect expense category',
            'Outside of policy guidelines',
            'Duplicate submission',
            'Insufficient documentation',
        ];

        return $reasons[array_rand($reasons)];
    }
}
