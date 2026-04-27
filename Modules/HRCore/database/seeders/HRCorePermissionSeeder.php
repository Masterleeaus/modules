<?php

namespace Modules\HRCore\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class HRCorePermissionSeeder extends Seeder
{
    /**
     * HR Core module permissions organized by category
     */
    protected $permissions = [];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Start transaction
        DB::beginTransaction();

        try {
            // Define all HR Core permissions
            $this->definePermissions();

            // Create/update permissions
            $this->createPermissions();

            // Update existing roles with HR permissions
            $this->updateRolePermissions();

            DB::commit();
            $this->command->info('HR Core permissions seeded successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Error seeding HR Core permissions: '.$e->getMessage());
        }
    }

    /**
     * Define all HR Core permissions by category
     */
    protected function definePermissions(): void
    {
        $this->permissions = [
            // Employee Management
            'Employee Management' => [
                // View permissions
                ['name' => 'hrcore.view-employees', 'description' => 'View all employees'],
                ['name' => 'hrcore.view-own-employees', 'description' => 'View employees under their management'],
                ['name' => 'hrcore.view-employee-details', 'description' => 'View detailed employee information'],
                ['name' => 'hrcore.view-employee-personal-info', 'description' => 'View employee personal information'],
                ['name' => 'hrcore.view-employee-work-info', 'description' => 'View employee work information'],
                ['name' => 'hrcore.view-employee-documents', 'description' => 'View employee documents'],

                // Create/Edit permissions
                ['name' => 'hrcore.create-employees', 'description' => 'Create new employees'],
                ['name' => 'hrcore.edit-employees', 'description' => 'Edit employee information'],
                ['name' => 'hrcore.edit-employee-personal-info', 'description' => 'Edit employee personal information'],
                ['name' => 'hrcore.edit-employee-work-info', 'description' => 'Edit employee work information'],
                ['name' => 'hrcore.delete-employees', 'description' => 'Delete employees'],

                // Lifecycle management
                ['name' => 'hrcore.manage-employee-lifecycle', 'description' => 'Manage employee lifecycle transitions'],
                ['name' => 'hrcore.onboard-employees', 'description' => 'Handle employee onboarding'],
                ['name' => 'hrcore.offboard-employees', 'description' => 'Handle employee offboarding'],
                ['name' => 'hrcore.manage-employee-transfers', 'description' => 'Manage employee transfers between teams/departments'],
                ['name' => 'hrcore.manage-employee-promotions', 'description' => 'Manage employee promotions'],
                ['name' => 'hrcore.manage-employee-status', 'description' => 'Change employee status (active/inactive/relieved)'],
                ['name' => 'hrcore.manage-probation', 'description' => 'Manage employee probation status'],

                // History & Audit
                ['name' => 'hrcore.view-employee-history', 'description' => 'View employee change history'],
                ['name' => 'hrcore.export-employee-data', 'description' => 'Export employee data'],
            ],

            // Attendance Management
            'Attendance Management' => [
                ['name' => 'hrcore.view-attendance', 'description' => 'View all attendance records'],
                ['name' => 'hrcore.view-own-attendance', 'description' => 'View own attendance records'],
                ['name' => 'hrcore.view-team-attendance', 'description' => 'View team attendance records'],
                ['name' => 'hrcore.create-attendance', 'description' => 'Create attendance entries'],
                ['name' => 'hrcore.edit-attendance', 'description' => 'Edit attendance records'],
                ['name' => 'hrcore.delete-attendance', 'description' => 'Delete attendance records'],
                ['name' => 'hrcore.approve-attendance', 'description' => 'Approve attendance regularization'],
                ['name' => 'hrcore.web-check-in', 'description' => 'Perform web check-in/out'],
                ['name' => 'hrcore.multiple-check-in', 'description' => 'Allow multiple check-in/out per day'],
                ['name' => 'hrcore.view-attendance-reports', 'description' => 'View attendance reports'],
                ['name' => 'hrcore.export-attendance', 'description' => 'Export attendance data'],
                ['name' => 'hrcore.manage-attendance-settings', 'description' => 'Manage attendance settings'],

                // Attendance Regularization
                ['name' => 'hrcore.view-attendance-regularization', 'description' => 'View all regularization requests'],
                ['name' => 'hrcore.view-own-attendance-regularization', 'description' => 'View own regularization requests'],
                ['name' => 'hrcore.create-attendance-regularization', 'description' => 'Create regularization requests'],
                ['name' => 'hrcore.edit-attendance-regularization', 'description' => 'Edit regularization requests'],
                ['name' => 'hrcore.delete-attendance-regularization', 'description' => 'Delete regularization requests'],
                ['name' => 'hrcore.approve-attendance-regularization', 'description' => 'Approve regularization requests'],

                // Manager Dashboard
                ['name' => 'hrcore.view-manager-attendance-dashboard', 'description' => 'View manager attendance dashboard'],
                ['name' => 'hrcore.view-attendance-statistics', 'description' => 'View attendance statistics'],
            ],

            // Leave Management
            'Leave Management' => [
                ['name' => 'hrcore.view-leaves', 'description' => 'View all leave requests'],
                ['name' => 'hrcore.view-own-leaves', 'description' => 'View own leave requests'],
                ['name' => 'hrcore.view-team-leaves', 'description' => 'View team leave requests'],
                ['name' => 'hrcore.create-leave', 'description' => 'Create leave requests'],
                ['name' => 'hrcore.create-leave-for-others', 'description' => 'Create leave requests for other employees'],
                ['name' => 'hrcore.edit-leave', 'description' => 'Edit leave requests'],
                ['name' => 'hrcore.delete-leave', 'description' => 'Delete leave requests'],
                ['name' => 'hrcore.approve-leave', 'description' => 'Approve leave requests'],
                ['name' => 'hrcore.reject-leave', 'description' => 'Reject leave requests'],
                ['name' => 'hrcore.cancel-leave', 'description' => 'Cancel approved leaves'],
                ['name' => 'hrcore.view-leave-balances', 'description' => 'View leave balances'],
                ['name' => 'hrcore.manage-leave-balances', 'description' => 'Manage leave balances'],
                ['name' => 'hrcore.view-leave-reports', 'description' => 'View leave reports'],
                ['name' => 'hrcore.export-leave-data', 'description' => 'Export leave data'],

                // Leave Types - Full CRUD permissions
                ['name' => 'hrcore.view-leave-types', 'description' => 'View leave types'],
                ['name' => 'hrcore.create-leave-types', 'description' => 'Create leave types'],
                ['name' => 'hrcore.edit-leave-types', 'description' => 'Edit leave types'],
                ['name' => 'hrcore.delete-leave-types', 'description' => 'Delete leave types'],
                ['name' => 'hrcore.manage-leave-types', 'description' => 'Manage leave types (status changes, etc.)'],
            ],

            // Compensatory Off Management
            'Compensatory Off Management' => [
                ['name' => 'hrcore.view-comp-offs', 'description' => 'View all compensatory off requests'],
                ['name' => 'hrcore.view-own-comp-offs', 'description' => 'View own compensatory off requests'],
                ['name' => 'hrcore.view-team-comp-offs', 'description' => 'View team compensatory off requests'],
                ['name' => 'hrcore.create-comp-off', 'description' => 'Create compensatory off requests'],
                ['name' => 'hrcore.edit-comp-off', 'description' => 'Edit compensatory off requests'],
                ['name' => 'hrcore.delete-comp-off', 'description' => 'Delete compensatory off requests'],
                ['name' => 'hrcore.approve-comp-off', 'description' => 'Approve compensatory off requests'],
                ['name' => 'hrcore.reject-comp-off', 'description' => 'Reject compensatory off requests'],
                ['name' => 'hrcore.view-comp-off-reports', 'description' => 'View compensatory off reports'],
                ['name' => 'hrcore.export-comp-off-data', 'description' => 'Export compensatory off data'],
            ],

            // Organization Structure
            'Organization Structure' => [
                // Departments
                ['name' => 'hrcore.view-departments', 'description' => 'View departments'],
                ['name' => 'hrcore.create-departments', 'description' => 'Create departments'],
                ['name' => 'hrcore.edit-departments', 'description' => 'Edit departments'],
                ['name' => 'hrcore.delete-departments', 'description' => 'Delete departments'],

                // Designations
                ['name' => 'hrcore.view-designations', 'description' => 'View designations'],
                ['name' => 'hrcore.create-designations', 'description' => 'Create designations'],
                ['name' => 'hrcore.edit-designations', 'description' => 'Edit designations'],
                ['name' => 'hrcore.delete-designations', 'description' => 'Delete designations'],

                // Teams
                ['name' => 'hrcore.view-teams', 'description' => 'View teams'],
                ['name' => 'hrcore.create-teams', 'description' => 'Create teams'],
                ['name' => 'hrcore.edit-teams', 'description' => 'Edit teams'],
                ['name' => 'hrcore.delete-teams', 'description' => 'Delete teams'],
                ['name' => 'hrcore.manage-team-members', 'description' => 'Manage team members'],

                // Shifts
                ['name' => 'hrcore.view-shifts', 'description' => 'View shifts'],
                ['name' => 'hrcore.create-shifts', 'description' => 'Create shifts'],
                ['name' => 'hrcore.edit-shifts', 'description' => 'Edit shifts'],
                ['name' => 'hrcore.delete-shifts', 'description' => 'Delete shifts'],

                // Organization Hierarchy
                ['name' => 'hrcore.view-organization-hierarchy', 'description' => 'View organization hierarchy'],
                ['name' => 'hrcore.manage-organization-hierarchy', 'description' => 'Manage organization hierarchy'],
            ],

            // Expense Management
            'Expense Management' => [
                ['name' => 'hrcore.view-expenses', 'description' => 'View all expense requests'],
                ['name' => 'hrcore.view-own-expenses', 'description' => 'View own expense requests'],
                ['name' => 'hrcore.view-team-expenses', 'description' => 'View team expense requests'],
                ['name' => 'hrcore.create-expense', 'description' => 'Create expense requests'],
                ['name' => 'hrcore.edit-expense', 'description' => 'Edit expense requests'],
                ['name' => 'hrcore.delete-expense', 'description' => 'Delete expense requests'],
                ['name' => 'hrcore.approve-expense', 'description' => 'Approve expense requests'],
                ['name' => 'hrcore.reject-expense', 'description' => 'Reject expense requests'],
                ['name' => 'hrcore.process-expense', 'description' => 'Process expense payments'],
                ['name' => 'hrcore.view-expense-reports', 'description' => 'View expense reports'],

                // Expense Types
                ['name' => 'hrcore.view-expense-types', 'description' => 'View expense types'],
                ['name' => 'hrcore.create-expense-types', 'description' => 'Create expense types'],
                ['name' => 'hrcore.edit-expense-types', 'description' => 'Edit expense types'],
                ['name' => 'hrcore.delete-expense-types', 'description' => 'Delete expense types'],
                ['name' => 'hrcore.manage-expense-types', 'description' => 'Manage expense types'],
            ],

            // Holiday Management
            'Holiday Management' => [
                ['name' => 'hrcore.view-holidays', 'description' => 'View holidays'],
                ['name' => 'hrcore.create-holidays', 'description' => 'Create holidays'],
                ['name' => 'hrcore.edit-holidays', 'description' => 'Edit holidays'],
                ['name' => 'hrcore.delete-holidays', 'description' => 'Delete holidays'],
                ['name' => 'hrcore.manage-holiday-calendars', 'description' => 'Manage holiday calendars'],
            ],

            // HR Reports
            'HR Reports' => [
                ['name' => 'hrcore.view-hr-reports', 'description' => 'View HR reports'],
                ['name' => 'hrcore.generate-attendance-reports', 'description' => 'Generate attendance reports'],
                ['name' => 'hrcore.generate-leave-reports', 'description' => 'Generate leave reports'],
                ['name' => 'hrcore.generate-expense-reports', 'description' => 'Generate expense reports'],
                ['name' => 'hrcore.generate-employee-reports', 'description' => 'Generate employee reports'],
                ['name' => 'hrcore.export-hr-reports', 'description' => 'Export HR reports'],
            ],

            // HR Settings
            'HR Settings' => [
                ['name' => 'hrcore.manage-hr-settings', 'description' => 'Manage HR module settings'],
                ['name' => 'hrcore.manage-attendance-settings', 'description' => 'Manage attendance settings'],
                ['name' => 'hrcore.manage-leave-settings', 'description' => 'Manage leave settings'],
                ['name' => 'hrcore.manage-expense-settings', 'description' => 'Manage expense settings'],
            ],
        ];
    }

    /**
     * Create all permissions in the database
     */
    protected function createPermissions(): void
    {
        $sortOrder = 1000; // Start from 1000 to avoid conflicts with existing permissions

        foreach ($this->permissions as $category => $categoryPermissions) {
            foreach ($categoryPermissions as $permission) {
                Permission::updateOrCreate(
                    [
                        'name' => $permission['name'],
                        'guard_name' => 'web',
                    ],
                    [
                        'module' => 'HRCore',
                        'description' => $permission['description'].' ('.$category.')',
                        'sort_order' => $sortOrder++,
                    ]
                );

                $this->command->info("Created/Updated permission: {$permission['name']} (HRCore - {$category})");
            }
        }
    }

    /**
     * Update existing roles with appropriate HR permissions
     */
    protected function updateRolePermissions(): void
    {
        // HR Manager - Full HR access
        $this->updateHRManagerPermissions();

        // HR Executive - Limited HR access
        $this->updateHRExecutivePermissions();

        // Team Leader - Team management access
        $this->updateTeamLeaderPermissions();

        // Employee - Basic access
        $this->updateEmployeePermissions();

        // Field Employee - Mobile access
        $this->updateFieldEmployeePermissions();

        // Admin roles
        $this->updateAdminPermissions();
    }

    protected function updateHRManagerPermissions(): void
    {
        $role = Role::where('name', 'hr_manager')->first();
        if (! $role) {
            return;
        }

        // Get all HR Core permissions
        $permissions = Permission::where('module', 'HRCore')->pluck('name')->toArray();

        // Add existing permissions that should remain
        $existingPermissions = $role->permissions()
            ->whereNotIn('module', ['HRCore'])
            ->pluck('name')
            ->toArray();

        $role->syncPermissions(array_merge($permissions, $existingPermissions));
        $this->command->info('Updated HR Manager role with HR Core permissions');
    }

    protected function updateHRExecutivePermissions(): void
    {
        $role = Role::where('name', 'hr_executive')->first();
        if (! $role) {
            return;
        }

        $permissions = [
            // Employee Management - Limited
            'hrcore.view-employees',
            'hrcore.view-employee-details',
            'hrcore.create-employees',
            'hrcore.edit-employee-personal-info',
            'hrcore.edit-employee-work-info',
            'hrcore.onboard-employees',
            'hrcore.view-employee-history',

            // Attendance - Full access except settings
            'hrcore.view-attendance',
            'hrcore.create-attendance',
            'hrcore.edit-attendance',
            'hrcore.view-attendance-reports',
            'hrcore.view-attendance-regularization',
            'hrcore.create-attendance-regularization',
            'hrcore.edit-attendance-regularization',
            'hrcore.view-attendance-statistics',

            // Leave - Process leaves
            'hrcore.view-leaves',
            'hrcore.create-leave',
            'hrcore.edit-leave',
            'hrcore.view-leave-balances',
            'hrcore.view-leave-reports',
            'hrcore.view-leave-types',
            'hrcore.create-leave-types',
            'hrcore.edit-leave-types',
            'hrcore.manage-leave-types',

            // Compensatory Off - Process comp offs
            'hrcore.view-comp-offs',
            'hrcore.create-comp-off',
            'hrcore.edit-comp-off',
            'hrcore.view-comp-off-reports',

            // Organization Structure - View only
            'hrcore.view-departments',
            'hrcore.view-designations',
            'hrcore.view-teams',
            'hrcore.view-shifts',
            'hrcore.view-organization-hierarchy',

            // Holidays - View only
            'hrcore.view-holidays',

            // Reports - View access
            'hrcore.view-hr-reports',
        ];

        // Add existing non-HR permissions
        $existingPermissions = $role->permissions()
            ->whereNotIn('module', ['HRCore'])
            ->pluck('name')
            ->toArray();

        $role->syncPermissions(array_merge($permissions, $existingPermissions));
        $this->command->info('Updated HR Executive role with limited HR Core permissions');
    }

    protected function updateTeamLeaderPermissions(): void
    {
        $role = Role::where('name', 'team_leader')->first();
        if (! $role) {
            return;
        }

        $permissions = [
            // Team management
            'hrcore.view-teams',
            'hrcore.view-own-employees',
            'hrcore.view-employee-details',
            'hrcore.manage-team-members',

            // Attendance - Team level
            'hrcore.view-team-attendance',
            'hrcore.view-attendance-regularization',
            'hrcore.approve-attendance-regularization',
            'hrcore.view-manager-attendance-dashboard',

            // Leave - Team level
            'hrcore.view-team-leaves',
            'hrcore.approve-leave',
            'hrcore.reject-leave',

            // Compensatory Off - Team level
            'hrcore.view-team-comp-offs',
            'hrcore.approve-comp-off',
            'hrcore.reject-comp-off',

            // Expense - Team level
            'hrcore.view-team-expenses',
            'hrcore.approve-expense',
            'hrcore.reject-expense',

            // Own access
            'hrcore.view-own-attendance',
            'hrcore.web-check-in',
            'hrcore.view-own-leaves',
            'hrcore.create-leave',
            'hrcore.view-leave-balances',
            'hrcore.view-own-comp-offs',
            'hrcore.create-comp-off',
            'hrcore.view-own-expenses',
            'hrcore.create-expense',
            'hrcore.view-holidays',
            'hrcore.view-organization-hierarchy',
            'hrcore.view-own-attendance-regularization',
            'hrcore.create-attendance-regularization',
            'hrcore.edit-attendance-regularization',
        ];

        // Add existing non-HR permissions
        $existingPermissions = $role->permissions()
            ->whereNotIn('module', ['HRCore'])
            ->pluck('name')
            ->toArray();

        $role->syncPermissions(array_merge($permissions, $existingPermissions));
        $this->command->info('Updated Team Leader role with team management permissions');
    }

    protected function updateEmployeePermissions(): void
    {
        $role = Role::where('name', 'employee')->first();
        if (! $role) {
            return;
        }

        $permissions = [
            // Basic employee permissions
            'hrcore.view-own-attendance',
            'hrcore.web-check-in',
            'hrcore.view-own-leaves',
            'hrcore.create-leave',
            'hrcore.view-leave-balances',
            'hrcore.view-own-comp-offs',
            'hrcore.create-comp-off',
            'hrcore.view-own-expenses',
            'hrcore.create-expense',
            'hrcore.edit-expense',
            'hrcore.delete-expense',
            'hrcore.view-holidays',
            'hrcore.view-organization-hierarchy',
            'hrcore.view-own-attendance-regularization',
            'hrcore.create-attendance-regularization',
            'hrcore.edit-attendance-regularization',
            'hrcore.delete-attendance-regularization',
        ];

        // Add existing non-HR permissions
        $existingPermissions = $role->permissions()
            ->whereNotIn('module', ['HRCore'])
            ->pluck('name')
            ->toArray();

        $role->syncPermissions(array_merge($permissions, $existingPermissions));
        $this->command->info('Updated Employee role with basic HR permissions');
    }

    protected function updateFieldEmployeePermissions(): void
    {
        $role = Role::where('name', 'field_employee')->first();
        if (! $role) {
            return;
        }

        $permissions = [
            // Field employee specific
            'hrcore.view-own-attendance',
            'hrcore.web-check-in',
            'hrcore.multiple-check-in',  // Field employees may need to check in/out at multiple locations
            'hrcore.view-own-leaves',
            'hrcore.create-leave',
            'hrcore.view-leave-balances',
            'hrcore.view-own-expenses',
            'hrcore.create-expense',
            'hrcore.edit-expense',
            'hrcore.delete-expense',
            'hrcore.view-holidays',
            'hrcore.view-own-attendance-regularization',
            'hrcore.create-attendance-regularization',
            'hrcore.edit-attendance-regularization',
        ];

        // Add existing non-HR permissions
        $existingPermissions = $role->permissions()
            ->whereNotIn('module', ['HRCore'])
            ->pluck('name')
            ->toArray();

        $role->syncPermissions(array_merge($permissions, $existingPermissions));
        $this->command->info('Updated Field Employee role with mobile HR permissions');
    }

    protected function updateAdminPermissions(): void
    {
        // Super Admin gets all permissions
        $superAdmin = Role::where('name', 'super_admin')->first();
        if ($superAdmin) {
            $superAdmin->syncPermissions(Permission::all());
            $this->command->info('Updated Super Admin role with all permissions');
        }

        // Admin gets all HR permissions
        $admin = Role::where('name', 'admin')->first();
        if ($admin) {
            $hrPermissions = Permission::where('module', 'HRCore')->pluck('name')->toArray();
            $existingPermissions = $admin->permissions()
                ->whereNotIn('module', ['HRCore'])
                ->pluck('name')
                ->toArray();

            $admin->syncPermissions(array_merge($hrPermissions, $existingPermissions));
            $this->command->info('Updated Admin role with all HR Core permissions');
        }
    }
}
