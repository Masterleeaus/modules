<?php

namespace Modules\Accountings\Traits;

trait ResolvesCompany
{
    protected function currentCompanyId(): int
    {
        // Worksuite commonly exposes a global company() helper. We fall back safely.
        try {
            if (function_exists('company')) {
                $c = company();
                if ($c && isset($c->id)) {
                    return (int) $c->id;
                }
            }
        } catch (\Throwable $e) {
            // ignore and fall back
        }

        // Some base controllers set $this->company.
        if (property_exists($this, 'company') && $this->company && isset($this->company->id)) {
            return (int) $this->company->id;
        }

        $user = auth()->user();
        if ($user && isset($user->company_id)) {
            return (int) $user->company_id;
        }

        $sid = session('company_id');
        if ($sid) {
            return (int) $sid;
        }

        abort(403, 'Company context missing');
    }
}
