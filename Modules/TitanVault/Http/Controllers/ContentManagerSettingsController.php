<?php

namespace Modules\TitanVault\Http\Controllers;

use App\Helper\Reply;
use App\Http\Controllers\AccountBaseController;
use Illuminate\Http\Request;

class ContentManagerSettingsController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('titan_vault::titan_vault.settings');
    }

    /**
     * Show vault settings form.
     */
    public function index()
    {
        abort_403($this->user->permission('manage_vault_settings') !== 'all');

        $this->storageDisk       = setting('titan_vault_storage_disk', config('titan_vault.storage_disk'));
        $this->defaultExpiryDays = setting('titan_vault_default_expiry_days', config('titan_vault.default_expiry_days'));
        $this->requirePassword   = setting('titan_vault_require_password', config('titan_vault.require_password'));

        return view('titan_vault::settings.index', $this->data);
    }

    /**
     * Save vault settings.
     */
    public function update(Request $request)
    {
        abort_403($this->user->permission('manage_vault_settings') !== 'all');

        $request->validate([
            'storage_disk'        => 'required|string|in:local,s3,public',
            'default_expiry_days' => 'required|integer|min:0',
            'require_password'    => 'nullable|boolean',
        ]);

        setting([
            'titan_vault_storage_disk'        => $request->input('storage_disk'),
            'titan_vault_default_expiry_days' => $request->input('default_expiry_days'),
            'titan_vault_require_password'    => $request->boolean('require_password'),
        ])->save();

        return Reply::success(__('titan_vault::titan_vault.settings_saved'));
    }
}
