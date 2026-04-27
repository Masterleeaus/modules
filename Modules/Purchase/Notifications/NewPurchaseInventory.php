<?php

namespace Modules\Purchase\Notifications;

use App\Models\Company;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Modules\Purchase\Entities\PurchaseNotificationSetting;
use Modules\Purchase\Entities\PurchaseProduct;
use Modules\Purchase\Entities\PurchaseStockAdjustment;

class NewPurchaseInventory extends BaseNotification
{

    private $event;
    private $emailSetting;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($event)
    {
        $this->event = $event;
        $this->emailSetting = PurchaseNotificationSetting::where('company_id', company()->id)->where('slug', 'new-purchase-inventory')->first();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $via = [];

        if ($this->emailSetting->send_email == 'yes' && $notifiable->email != '') {
            array_push($via, 'mail');
        }

        return $via;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $stocks = PurchaseStockAdjustment::whereIn('product_id', $this->event)->get();
        $products = PurchaseProduct::whereIn('id', $this->event)->get();

        $items = [];
        $quantity = [];

        foreach ($products as $product) {
            $items[] = $product->name;
        }

        $inventoryId = '';

        foreach ($stocks as $key => $stock) {

            if ($key == 0) {
                $inventoryId = $stock->inventory_id;
            }

            if($stock->type == 'quantity') {
                $quantity[] = $stock->net_quantity . '(quantity)';
            }
            else {
                $quantity[] = $stock->changed_value . '(value)';
            }
        }

        // Check if $items and $quantity have the same number of elements
        if (count($items) === count($quantity)) {
            $content1 = array_combine($items, $quantity);
        } else {
            // Handle the error case, e.g., log a warning or set $content1 to an empty array
            $content1 = []; // or handle as needed
            // Optionally log a warning
            \Log::warning('Items and quantities do not match in NewPurchaseInventory notification.');
        }

        $url = route('purchase-inventory.show', $inventoryId);
        $url = getDomainSpecificUrl($url, $this->company);

        $content = __('purchase::email.purchaseInventory.text');
        $values = '';

        foreach ($content1 as $key => $abcd) {
            $values .= '<span>'.$key.' : '.$abcd.'</span><br>';
        }

        $newInventory = parent::build();

        $newInventory->subject(__('purchase::email.purchaseInventory.subject'))
            ->markdown('mail.email', [
                'url' => $url,
                'content' => $content . '<br>' .$values,
                'themeColor' => $notifiable->company->header_color,
                'actionText' => __('purchase::email.purchaseInventory.viewInventory'),
                'notifiableName' => $notifiable->name
            ]);

        return $newInventory;
    }

}
