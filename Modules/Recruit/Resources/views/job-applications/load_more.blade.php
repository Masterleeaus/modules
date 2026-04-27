@php
    $changeStatusPermission = user()->permission('change_status');
@endphp

@foreach ($tasks as $key => $application)

    <x-recruit::cards.job-card :draggable="$changeStatusPermission == 'all' ? 'true' : 'false'"
                               :application="$application"/>
@endforeach
