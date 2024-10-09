<?php

namespace App\Notifications;

use App\Channel\checklistSmsChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class notToAgenciesNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [checklistSmsChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
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
    public function toSMS( $notifiable )
    {
//        if ( ! $notifiable->phone_number) {
//            return null;
//        }
//        env('SMS_ADMIN_PHONE')


        $receiver = [env('SMS_ADMIN_PHONE') , $notifiable];
        $receiver = implode(',',$receiver);

        $message_info = $this->data;
        return [
            'template' => $message_info,
            'type'     => 'normal',
            'receptor' => $receiver,
            'title'    => '',

        ];

    }
}
