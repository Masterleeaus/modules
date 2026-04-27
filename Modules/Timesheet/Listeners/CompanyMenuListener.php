<?php

namespace Modules\Timesheet\Listeners;

use App\Events\CompanyMenuEvent;

class CompanyMenuListener
{
    /**
     * Handle the event.
     */
    public function handle(CompanyMenuEvent $event): void
    {
        $module = 'Timesheet';
        $menu = $event->menu;
        $menu->add([
            'title' => 'Timesheet',
            'icon' => 'clock',
            'name' => 'timesheet',
            'parent' => null,
            'order' => 1450,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'timesheet.index',
            'module' => $module,
            'permission' => 'timesheet manage'
        ]);
    }
}
