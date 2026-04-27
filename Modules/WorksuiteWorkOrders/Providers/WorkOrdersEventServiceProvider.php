<?php

namespace Modules\WorksuiteWorkOrders\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\WorksuiteWorkOrders\Events\{WorkOrderCreated, WorkOrderUpdated, WorkOrderCompleted};
use Modules\WorksuiteWorkOrders\Listeners\SendWorkOrderWebhook;
use Modules\WorksuiteWorkOrders\Listeners\AutoConvertOnCompletion;

class WorkOrdersEventServiceProvider extends ServiceProvider
{
    protected $listen = [
        WorkOrderCreated::class => [SendWorkOrderWebhook::class, \Modules\WorksuiteWorkOrders\Listeners\LogWorkOrderActivity::class],
        WorkOrderUpdated::class => [SendWorkOrderWebhook::class, \Modules\WorksuiteWorkOrders\Listeners\LogWorkOrderActivity::class],
        WorkOrderCompleted::class => [SendWorkOrderWebhook::class, AutoConvertOnCompletion::class, \Modules\WorksuiteWorkOrders\Listeners\LogWorkOrderActivity::class],
    ];
}
