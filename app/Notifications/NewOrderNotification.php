<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;

class NewOrderNotification extends Notification
{
    use Queueable;

    protected $createdOrder;
    protected $items;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($createdOrder, $items)
    {
        $this->createdOrder = $createdOrder;
        $this->items = $items;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $mailmessage = new MailMessage;
        
        if ($this->createdOrder['delivery_method'] !== 'self_delivery') {
            $apt = $this->createdOrder['apt'];
            if ($this->createdOrder['apt']) $apt = ", кв. $apt";
            $address = $this->createdOrder['address'] . $apt;      
        }
        else {
            $address = 'Самовывоз';
        }

        if ($this->createdOrder['payment_method'] === 'cash') {
            $paymentMethod = 'Наличными при получении';
        }
        else if ($this->createdOrder['payment_method'] === 'online') {
            $paymentMethod = 'Онлайн';
        } else {
            $paymentMethod = 'Картой при получении';
        }

        // $date = $this->createdOrder['delivery_time'];
        
        $date = strtotime($this->createdOrder['delivery_time']);
        $date = date('d.m', $date) . ", с " . date("H", $date) + 5 . " до " . date("H", $date) + 7;
                
        $mailmessage
            ->subject("Новый заказ!")
            ->greeting('Привет!')
            ->line("Создан новый заказ: " . $this->createdOrder['id'])
            ->line("Доставка / самовывоз: $address")
            ->line("Имя: " . $this->createdOrder['name'])
            ->line("Телефон: " . $this->createdOrder['phone'])
            ->line("Оплата: " . $paymentMethod)
            ->line("Время получения: " . $date);
            

        if ($this->createdOrder['receiver_name']) {
            $mailmessage->line("Имя получателя: " . $this->createdOrder['receiver_name']);
        }

        if ($this->createdOrder['receiver_phone']) {
            $mailmessage->line("Телефон получателя: " . $this-> createdOrder['receiver_phone']);
        }

        foreach ($this->items as $key => $val) {
            $mailmessage->line($val['Name'] . "  |  " . $val['Quantity'] . " шт.  |  " . $val['Amount'] / 100 . " р.");
        }

        $mailmessage->action('Посмотреть', url('/admin/orders'));

        return $mailmessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
