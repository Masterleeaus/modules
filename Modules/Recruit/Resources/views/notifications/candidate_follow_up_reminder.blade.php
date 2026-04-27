@php
    $notificationUser = App\Models\User::where('id', $notification->data['user_id'])
    ->orderByDesc('id')
    ->first();
@endphp
<x-cards.notification :notification="$notification"
                      :link="route('job-applications.show', $notification->data['job_application_id'])"
                      :image="$notificationUser->image_url"
                      :title="__('recruit::modules.followUpReminder.subject') . ' #' . $notification->data['id']"
                      :text="$notification->data['heading']"
                      :time="$notification->created_at"/>
