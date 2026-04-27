<?php

namespace Modules\ProShots\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\GlobalSetting\Entities\GlobalSetting;

class ProShotsSettingController extends Controller
{
    public function index(): \Illuminate\Contracts\View\View
    {
        $pebblelyKey = null;

        if (class_exists(GlobalSetting::class)) {
            $pebblelyKey = GlobalSetting::where('key', 'proshots_pebblely_key')->value('value');
        }

        return view('proshots::settings.index', compact('pebblelyKey'));
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'pebblely_key' => 'required|string|max:255',
        ]);

        if (class_exists(GlobalSetting::class)) {
            GlobalSetting::updateOrCreate(
                ['key' => 'proshots_pebblely_key'],
                ['value' => $request->get('pebblely_key')]
            );
        }

        return redirect()->back()->with([
            'type'    => 'success',
            'message' => __('Pebblely API key updated successfully.'),
        ]);
    }
}
