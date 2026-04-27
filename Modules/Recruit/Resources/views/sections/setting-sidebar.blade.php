@if (user()->permission('recruit_settings') != 'none' && in_array(\Modules\Recruit\Entities\RecruitSetting::MODULE_NAME, user_modules()))
    <x-setting-menu-item :active="$activeMenu" menu="recruit_settings" :href="route('recruit-settings.index')"
                         :text="__('recruit::app.menu.recruitSetting')"/>
@endif
