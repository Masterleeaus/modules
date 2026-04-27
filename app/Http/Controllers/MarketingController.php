<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class MarketingController extends Controller
{
    public function index(): View|RedirectResponse
    {
        if (auth()->check()) {
            $user = auth()->user();

            if ($user->hasRole('super_admin')) {
                return redirect()->route('platform.dashboard');
            }

            if ($user->hasRole('technician')) {
                return redirect()->route('technician.dashboard');
            }

            return redirect()->route('owner.dashboard');
        }

        return view('marketing.titan-bos-home');
    }
}
