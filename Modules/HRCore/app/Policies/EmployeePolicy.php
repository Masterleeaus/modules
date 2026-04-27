<?php

namespace Modules\HRCore\App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EmployeePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any employees.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('hrcore.view-employees') || $user->can('hrcore.view-own-employees');
    }

    /**
     * Determine whether the user can view the employee.
     */
    public function view(User $user, User $employee): bool
    {
        // Can view all employees
        if ($user->can('hrcore.view-employees')) {
            return true;
        }

        // Can view own profile
        if ($user->id === $employee->id) {
            return true;
        }

        // Can view team members if team leader
        if ($user->can('hrcore.view-own-employees')) {
            // Check if employee is under user's management
            return $this->isUnderManagement($user, $employee);
        }

        return false;
    }

    /**
     * Determine whether the user can create employees.
     */
    public function create(User $user): bool
    {
        return $user->can('hrcore.create-employees');
    }

    /**
     * Determine whether the user can update the employee.
     */
    public function update(User $user, User $employee): bool
    {
        return $user->can('hrcore.edit-employees');
    }

    /**
     * Determine whether the user can edit personal information.
     */
    public function editPersonalInfo(User $user, User $employee): bool
    {
        return $user->can('hrcore.edit-employee-personal-info');
    }

    /**
     * Determine whether the user can edit work information.
     */
    public function editWorkInfo(User $user, User $employee): bool
    {
        return $user->can('hrcore.edit-employee-work-info');
    }

    /**
     * Determine whether the user can edit salary information.
     */
    public function editSalaryInfo(User $user, User $employee): bool
    {
        return $user->can('hrcore.edit-employee-salary-info');
    }

    /**
     * Determine whether the user can manage employee status.
     */
    public function manageStatus(User $user, User $employee): bool
    {
        return $user->can('hrcore.manage-employee-status');
    }

    /**
     * Determine whether the user can manage employee lifecycle.
     */
    public function manageLifecycle(User $user, User $employee): bool
    {
        return $user->can('hrcore.manage-employee-lifecycle');
    }

    /**
     * Determine whether the user can onboard employees.
     */
    public function onboard(User $user): bool
    {
        return $user->can('hrcore.onboard-employees');
    }

    /**
     * Determine whether the user can offboard employees.
     */
    public function offboard(User $user, User $employee): bool
    {
        return $user->can('hrcore.offboard-employees');
    }

    /**
     * Determine whether the user can manage employee transfers.
     */
    public function transfer(User $user, User $employee): bool
    {
        return $user->can('hrcore.manage-employee-transfers');
    }

    /**
     * Determine whether the user can manage employee promotions.
     */
    public function promote(User $user, User $employee): bool
    {
        return $user->can('hrcore.manage-employee-promotions');
    }

    /**
     * Determine whether the user can manage probation.
     */
    public function manageProbation(User $user, User $employee): bool
    {
        return $user->can('hrcore.manage-probation');
    }

    /**
     * Determine whether the user can delete the employee.
     */
    public function delete(User $user, User $employee): bool
    {
        // Cannot delete own account
        if ($user->id === $employee->id) {
            return false;
        }

        return $user->can('hrcore.delete-employees');
    }

    /**
     * Determine whether the user can view employee history.
     */
    public function viewHistory(User $user, User $employee): bool
    {
        // Can view history if can view employee details
        if ($user->can('hrcore.view-employee-history')) {
            return true;
        }

        // HR roles can view history of employees they can manage
        if ($user->can('hrcore.view-employees') || $user->can('hrcore.edit-employees')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can export employee data.
     */
    public function export(User $user): bool
    {
        return $user->can('hrcore.export-employee-data');
    }

    /**
     * Check if employee is under user's management
     */
    protected function isUnderManagement(User $manager, User $employee): bool
    {
        // Direct reporting
        if ($employee->reporting_to_id === $manager->id) {
            return true;
        }

        // Same team check for team leaders
        if ($manager->hasRole('team_leader') && $manager->team_id === $employee->team_id) {
            return true;
        }

        // Check hierarchical reporting (up to 3 levels)
        $currentManager = $employee->reportingTo;
        $levels = 0;

        while ($currentManager && $levels < 3) {
            if ($currentManager->id === $manager->id) {
                return true;
            }
            $currentManager = $currentManager->reportingTo;
            $levels++;
        }

        return false;
    }
}
