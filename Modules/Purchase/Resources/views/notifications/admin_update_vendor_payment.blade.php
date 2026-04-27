@php
    $notificationUser = App\Models\User::where('id', $notification->data['user_id'])
    ->orderByDesc('id')
    ->first();
@endphp
<x-cards.notification :notification="$notification"
                      :link="route('vendor-payments.show', $notification->data['id'])"
                      :image="$notificationUser->image_url"
                      :title="__('purchase::email.updatePayment.subject')" :text="__('purchase::email.updatePayment.text') . ' ' . $notification->data['vendor_name']"
                      :time="$notification->created_at"/>
