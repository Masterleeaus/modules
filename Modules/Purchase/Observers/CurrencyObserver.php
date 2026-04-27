<?php

namespace Modules\Purchase\Observers;

use App\Models\Currency;
use Modules\Purchase\Entities\PurchaseVendor;

class CurrencyObserver
{

    public function deleting(Currency $currency)
    {
        PurchaseVendor::where('currency_id', $currency->id)->update(['currency_id' => companyOrGlobalSetting()->currency_id]);
    }

}
