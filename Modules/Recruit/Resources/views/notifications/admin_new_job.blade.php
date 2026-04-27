@php
    $notificationUser = App\Models\User::where('id', $notification->data['user_id'])
    ->orderByDesc('id')
    ->first();
@endphp
<x-cards.notification :notification="$notification" :link="route('jobs.show', $notification->data['job_id'])"
                      :image="$notificationUser->image_url"
                      :title="__('recruit::modules.adminMail.newJobSubject')" :text="$notification->data['heading']"
                      :time="$notification->created_at"/>
