<?php

namespace Modules\CleanQuality\Listeners;

use App\Events\CompanyMenuEvent;
use Illuminate\Support\Facades\Route;

class CompanyMenuListener
{
    public function handle(CompanyMenuEvent $event): void
    {
        $module = 'Inspection';
        $menu = $event->menu;

        // Parent
        $menu->add([
            'title' => __('Inspections'),
            'icon' => 'clipboard-check',
            'name' => 'inspection',
            'parent' => null,
            'order' => 920,
            'ignore_if' => [],
            'depend_on' => [],
            // Parent nodes can safely have an empty route.
            'route' => '',
            'module' => $module,
            'permission' => 'view_inspection',
        ]);

        // Schedules
        $menu->add([
            'title' => __('Schedules'),
            'icon' => 'calendar-time',
            'name' => 'inspection_schedules',
            'parent' => 'inspection',
            'order' => 10,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => Route::has('inspection_schedules.index') ? 'inspection_schedules.index' : 'schedules.index',
            'module' => $module,
            'permission' => 'view_inspection',
        ]);

        // Recurring
        $menu->add([
            'title' => __('Recurring'),
            'icon' => 'repeat',
            'name' => 'recurring_inspection_schedules',
            'parent' => 'inspection',
            'order' => 20,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'recurring-inspection_schedules.index',
            'module' => $module,
            'permission' => 'view_inspection',
        ]);

        // Templates (if present)
        if (Route::has('inspection-templates.index')) {
            $menu->add([
                'title' => __('Templates'),
                'icon' => 'list-check',
                'name' => 'inspection_templates',
                'parent' => 'inspection',
                'order' => 30,
                'ignore_if' => [],
                'depend_on' => [],
                'route' => 'inspection-templates.index',
                'module' => $module,
                'permission' => 'view_inspection',
            ]);
        }
    }
}
