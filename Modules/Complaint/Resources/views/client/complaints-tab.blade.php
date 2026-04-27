{{--
    Complaint module — client complaints tab entry.
    Pushed to the @stack('client-complaints-tab') in resources/views/clients/show.blade.php.
--}}
@if (in_array('complaint', user_modules()) && user()->permission('view_complaints') !== 'none')
    <li>
        <x-tab :href="route('clients.show', $client->id).'?tab=complaints'" ajax="false"
               :text="__('complaint::modules.complaint')" class="complaints" />
    </li>
@endif
