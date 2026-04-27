<?php

namespace Modules\ZeroPay\Filament\Resources\PaymentResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\ZeroPay\Filament\Resources\PaymentResource;

class CreatePayment extends CreateRecord
{
    protected static string $resource = PaymentResource::class;
}
