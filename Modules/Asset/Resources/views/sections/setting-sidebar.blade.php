@if (in_array('admin', user_roles()) && in_array(\Modules\Asset\Entities\AssetSetting::MODULE_NAME, user_modules()) && (user()->permission('add_assets_type') == 'all' || user()->permission('add_assets_type') == 'added'))
    <x-setting-menu-item :active="$activeMenu" menu="asset_settings" :href="route('asset-setting.index')"
        :text="__('asset::app.menu.assetSettings')"/>
@endif
