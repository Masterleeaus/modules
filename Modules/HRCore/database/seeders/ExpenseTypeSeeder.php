<?php

namespace Modules\HRCore\Database\Seeders;

use App\Enums\Status;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\HRCore\app\Models\ExpenseType;

class ExpenseTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $expenseTypes = [
                // Travel & Transportation
                [
                    'name' => 'Airfare',
                    'code' => 'EXP-AIR',
                    'description' => 'Flight tickets for business travel',
                    'category' => 'travel',
                    'default_amount' => null,
                    'max_amount' => 5000.00,
                    'gl_account_code' => '6001',
                    'requires_receipt' => true,
                    'requires_approval' => true,
                    'status' => Status::ACTIVE->value,
                ],
                [
                    'name' => 'Hotel Accommodation',
                    'code' => 'EXP-HTL',
                    'description' => 'Hotel stays during business trips',
                    'category' => 'travel',
                    'default_amount' => 150.00,
                    'max_amount' => 500.00,
                    'gl_account_code' => '6002',
                    'requires_receipt' => true,
                    'requires_approval' => true,
                    'status' => Status::ACTIVE->value,
                ],
                [
                    'name' => 'Local Transportation',
                    'code' => 'EXP-LCL',
                    'description' => 'Taxi, Uber, public transportation',
                    'category' => 'travel',
                    'default_amount' => 50.00,
                    'max_amount' => 200.00,
                    'gl_account_code' => '6003',
                    'requires_receipt' => true,
                    'requires_approval' => false,
                    'status' => Status::ACTIVE->value,
                ],
                [
                    'name' => 'Car Rental',
                    'code' => 'EXP-CAR',
                    'description' => 'Vehicle rental for business purposes',
                    'category' => 'travel',
                    'default_amount' => null,
                    'max_amount' => 300.00,
                    'gl_account_code' => '6004',
                    'requires_receipt' => true,
                    'requires_approval' => true,
                    'status' => Status::ACTIVE->value,
                ],
                [
                    'name' => 'Mileage Reimbursement',
                    'code' => 'EXP-MIL',
                    'description' => 'Personal vehicle mileage for business use',
                    'category' => 'travel',
                    'default_amount' => 0.65, // per mile
                    'max_amount' => 500.00,
                    'gl_account_code' => '6005',
                    'requires_receipt' => false,
                    'requires_approval' => false,
                    'status' => Status::ACTIVE->value,
                ],

                // Meals & Entertainment
                [
                    'name' => 'Business Meals',
                    'code' => 'EXP-MEL',
                    'description' => 'Meals with clients or during business travel',
                    'category' => 'meals',
                    'default_amount' => 50.00,
                    'max_amount' => 150.00,
                    'gl_account_code' => '6010',
                    'requires_receipt' => true,
                    'requires_approval' => false,
                    'status' => Status::ACTIVE->value,
                ],
                [
                    'name' => 'Client Entertainment',
                    'code' => 'EXP-ENT',
                    'description' => 'Entertainment expenses for client relations',
                    'category' => 'meals',
                    'default_amount' => null,
                    'max_amount' => 500.00,
                    'gl_account_code' => '6011',
                    'requires_receipt' => true,
                    'requires_approval' => true,
                    'status' => Status::ACTIVE->value,
                ],
                [
                    'name' => 'Team Lunch',
                    'code' => 'EXP-TLN',
                    'description' => 'Team building meals and celebrations',
                    'category' => 'meals',
                    'default_amount' => 30.00,
                    'max_amount' => 100.00,
                    'gl_account_code' => '6012',
                    'requires_receipt' => true,
                    'requires_approval' => false,
                    'status' => Status::ACTIVE->value,
                ],

                // Office & Supplies
                [
                    'name' => 'Office Supplies',
                    'code' => 'EXP-OFF',
                    'description' => 'Stationery, pens, notebooks, etc.',
                    'category' => 'office',
                    'default_amount' => 50.00,
                    'max_amount' => 200.00,
                    'gl_account_code' => '6020',
                    'requires_receipt' => true,
                    'requires_approval' => false,
                    'status' => Status::ACTIVE->value,
                ],
                [
                    'name' => 'Software Subscription',
                    'code' => 'EXP-SFT',
                    'description' => 'Software licenses and subscriptions',
                    'category' => 'office',
                    'default_amount' => null,
                    'max_amount' => 1000.00,
                    'gl_account_code' => '6021',
                    'requires_receipt' => true,
                    'requires_approval' => true,
                    'status' => Status::ACTIVE->value,
                ],
                [
                    'name' => 'Computer Equipment',
                    'code' => 'EXP-CEQ',
                    'description' => 'Computer accessories, peripherals',
                    'category' => 'office',
                    'default_amount' => null,
                    'max_amount' => 2000.00,
                    'gl_account_code' => '6022',
                    'requires_receipt' => true,
                    'requires_approval' => true,
                    'status' => Status::ACTIVE->value,
                ],
                [
                    'name' => 'Books & Publications',
                    'code' => 'EXP-BKS',
                    'description' => 'Professional books, journals, subscriptions',
                    'category' => 'office',
                    'default_amount' => 50.00,
                    'max_amount' => 200.00,
                    'gl_account_code' => '6023',
                    'requires_receipt' => true,
                    'requires_approval' => false,
                    'status' => Status::ACTIVE->value,
                ],

                // Professional Development
                [
                    'name' => 'Training & Courses',
                    'code' => 'EXP-TRN',
                    'description' => 'Professional training and online courses',
                    'category' => 'training',
                    'default_amount' => null,
                    'max_amount' => 3000.00,
                    'gl_account_code' => '6030',
                    'requires_receipt' => true,
                    'requires_approval' => true,
                    'status' => Status::ACTIVE->value,
                ],
                [
                    'name' => 'Conference Registration',
                    'code' => 'EXP-CNF',
                    'description' => 'Conference and seminar registration fees',
                    'category' => 'training',
                    'default_amount' => null,
                    'max_amount' => 2000.00,
                    'gl_account_code' => '6031',
                    'requires_receipt' => true,
                    'requires_approval' => true,
                    'status' => Status::ACTIVE->value,
                ],
                [
                    'name' => 'Professional Certification',
                    'code' => 'EXP-CRT',
                    'description' => 'Professional certification exam fees',
                    'category' => 'training',
                    'default_amount' => null,
                    'max_amount' => 1000.00,
                    'gl_account_code' => '6032',
                    'requires_receipt' => true,
                    'requires_approval' => true,
                    'status' => Status::ACTIVE->value,
                ],

                // Communication
                [
                    'name' => 'Mobile Phone Bill',
                    'code' => 'EXP-MOB',
                    'description' => 'Mobile phone bills for business use',
                    'category' => 'communication',
                    'default_amount' => 50.00,
                    'max_amount' => 150.00,
                    'gl_account_code' => '6040',
                    'requires_receipt' => true,
                    'requires_approval' => false,
                    'status' => Status::ACTIVE->value,
                ],
                [
                    'name' => 'Internet Reimbursement',
                    'code' => 'EXP-INT',
                    'description' => 'Home internet for remote work',
                    'category' => 'communication',
                    'default_amount' => 50.00,
                    'max_amount' => 100.00,
                    'gl_account_code' => '6041',
                    'requires_receipt' => true,
                    'requires_approval' => false,
                    'status' => Status::ACTIVE->value,
                ],

                // Marketing
                [
                    'name' => 'Marketing Materials',
                    'code' => 'EXP-MKT',
                    'description' => 'Brochures, business cards, promotional items',
                    'category' => 'marketing',
                    'default_amount' => null,
                    'max_amount' => 1000.00,
                    'gl_account_code' => '6050',
                    'requires_receipt' => true,
                    'requires_approval' => true,
                    'status' => Status::ACTIVE->value,
                ],
                [
                    'name' => 'Online Advertising',
                    'code' => 'EXP-ADS',
                    'description' => 'Social media ads, Google ads, etc.',
                    'category' => 'marketing',
                    'default_amount' => null,
                    'max_amount' => 2000.00,
                    'gl_account_code' => '6051',
                    'requires_receipt' => true,
                    'requires_approval' => true,
                    'status' => Status::ACTIVE->value,
                ],

                // Other
                [
                    'name' => 'Miscellaneous',
                    'code' => 'EXP-MSC',
                    'description' => 'Other business expenses not categorized',
                    'category' => 'other',
                    'default_amount' => null,
                    'max_amount' => 500.00,
                    'gl_account_code' => '6099',
                    'requires_receipt' => true,
                    'requires_approval' => true,
                    'status' => Status::ACTIVE->value,
                ],
                [
                    'name' => 'Parking Fees',
                    'code' => 'EXP-PRK',
                    'description' => 'Parking fees during business activities',
                    'category' => 'other',
                    'default_amount' => 20.00,
                    'max_amount' => 100.00,
                    'gl_account_code' => '6060',
                    'requires_receipt' => false,
                    'requires_approval' => false,
                    'status' => Status::ACTIVE->value,
                ],
                [
                    'name' => 'Shipping & Courier',
                    'code' => 'EXP-SHP',
                    'description' => 'Shipping and courier services',
                    'category' => 'other',
                    'default_amount' => null,
                    'max_amount' => 200.00,
                    'gl_account_code' => '6061',
                    'requires_receipt' => true,
                    'requires_approval' => false,
                    'status' => Status::ACTIVE->value,
                ],
            ];

            foreach ($expenseTypes as $expenseType) {
                ExpenseType::firstOrCreate(
                    ['code' => $expenseType['code']],
                    $expenseType
                );
            }

            DB::commit();
            $this->command->info('Expense types seeded successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Error seeding expense types: '.$e->getMessage());
        }
    }
}
