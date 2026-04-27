@if(user()->permission('view_integrations') == 'all')
    <x-menu-item :active="$activeMenu ?? ''" menu="titan-integrations"
        :href="route('titan-integrations.index')"
        icon="puzzle-piece"
        :text="__('Integrations')" />
@endif
