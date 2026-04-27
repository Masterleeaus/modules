@component('mail::message')
# @lang('recruit::modules.setting.jobAlert')

@lang('recruit::modules.setting.alertForOpenings')

@component('mail::text', ['text' => $content])
@endcomponent

@component('mail::button', ['url' => $applyUrl])
@lang('recruit::modules.newJob.viewJob')
@endcomponent

@lang('email.regards'),<br>
{{ config('app.name') }}

@slot('subcopy')
    @lang('recruit::modules.setting.toUnsubscribe') 
    <span class="break-all"> <a href="{{ $url }}">{{ $url }}</a> </span>
@endslot
@endcomponent



