<?php

namespace Modules\Asset\Http\Controllers;

use App\Utils\ModuleUtil;
use Illuminate\Routing\Controller;
use Menu;

class DataController extends Controller
{
    public function superadmin_package()
    {
        return [
            [
                'name' => 'assetmanagement_module',
                'label' => __('asset::lang.asset_management'),
                'default' => false,
            ],
        ];
    }

    /**
     * Defines user permissions for the module.
     *
     * @return array
     */
    public function user_permissions()
    {
        return [
            [
                'value' => 'asset.view',
                'label' => __('asset::lang.view_asset'),
                'default' => false,
            ],
            [
                'value' => 'asset.create',
                'label' => __('asset::lang.add_asset'),
                'default' => false,
            ],
            [
                'value' => 'asset.update',
                'label' => __('asset::lang.edit_asset'),
                'default' => false,
            ],
            [
                'value' => 'asset.delete',
                'label' => __('asset::lang.delete_asset'),
                'default' => false,
            ],
            [
                'value' => 'asset.view_all_maintenance',
                'label' => __('asset::lang.view_all_maintenance'),
                'default' => false,
                'is_radio' => true,
                'radio_input_name' => 'view_maintenance',
            ],
            [
                'value' => 'asset.view_own_maintenance',
                'label' => __('asset::lang.view_own_maintenance'),
                'default' => false,
                'is_radio' => true,
                'radio_input_name' => 'view_maintenance',
            ],
        ];
    }

    /**
     * Function to add module taxonomies
     *
     * @return array
     */
    public function addTaxonomies()
    {
        $business_id = request()->session()->get('user.business_id');

        $module_util = new ModuleUtil();
        if (!(auth()->user()->can('superadmin') || $module_util->hasThePermissionInSubscription($business_id, 'assetmanagement_module'))) {
            return ['asset' => []];
        }

        return [
            'asset' => [
                'taxonomy_label' => __('asset::lang.asset_category'),
                'heading' => __('asset::lang.asset_categories'),
                'sub_heading' => __('asset::lang.manage_asset_categories'),
                'enable_taxonomy_code' => false,
                'enable_sub_taxonomy' => false,
                'navbar' => 'asset::layouts.nav',
            ],
        ];
    }

    /**
     * Adds Repair menus
     *
     * @return null
     */
    public function modifyAdminMenu()
    {
        $business_id = session()->get('user.business_id');
        $module_util = new ModuleUtil();
        $is_asset_enabled = (bool) $module_util->hasThePermissionInSubscription($business_id, 'assetmanagement_module');

        $background_color = '';
        if (config('app.env') == 'demo') {
            $background_color = '#2e97bf !important;color:white';
        }

        if ($is_asset_enabled && (auth()->user()->can('superadmin') || auth()->user()->can('asset.view') || auth()->user()->can('asset.view_own_maintenance') || auth()->user()->can('asset.view_all_maintenance'))) {
            Menu::modify('admin-sidebar-menu', function ($menu) use ($background_color) {
                $menu->url(
                    action([\Modules\Asset\Http\Controllers\AssetController::class, 'dashboard']),
                    __('asset::lang.asset_management'),
                    ['icon' => '<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-packages"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 16.5l-5 -3l5 -3l5 3v5.5l-5 3z" /><path d="M2 13.5v5.5l5 3" /><path d="M7 16.545l5 -3.03" /><path d="M17 16.5l-5 -3l5 -3l5 3v5.5l-5 3z" /><path d="M12 19l5 3" /><path d="M17 16.5l5 -3" /><path d="M12 13.5v-5.5l-5 -3l5 -3l5 3v5.5" /><path d="M7 5.03v5.455" /><path d="M12 8l5 -3" /></svg>', 'active' => request()->segment(1) == 'asset', 'style' => 'background-color:' . $background_color]
                )
                    ->order(87);
            });
        }
    }

    /**
     * Parses notification message from database.
     *
     * @return array
     */
    public function parse_notification($notification)
    {
        $notification_data = [];
        if ($notification->type ==
            'Modules\Asset\Notifications\AssetSentForMaintenance' || $notification->type ==
            'Modules\Asset\Notifications\AssetAssignedForMaintenance') {
            $notification_data = [
                'msg' => $notification->data['msg'],
                'icon_class' => 'fas fa-tools bg-green',
                'link' => action([\Modules\Asset\Http\Controllers\AssetMaintenanceController::class, 'index']),
                'read_at' => $notification->read_at,
                'created_at' => $notification->created_at->diffForHumans(),
            ];
        }

        return $notification_data;
    }
}
