<?php

namespace App\Notifications;

use App\Channel\checklistSmsChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class customerChecklistNotification extends Notification
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
        if ( ! $notifiable) {
            return null;
        }

        $message_info = $this->generateMassage();
        return [
            'receptor' => $notifiable,
            'template' => $message_info['message'],
            'params' => '',
            'title'  => $message_info['title'],
            'type' => 'normal',
        ];
    }

    public function generateMassage(){

        $checklist_contract = $this->data['checklistContract'];

        $customer = $checklist_contract->contract->customer;

        $language = $this->data['lang'] ;
        $lang = '';
        if ( $language != '1') {
            $lang = $language->title;
        }



        $instagram_link = 'https://instagram.com/myirantech';

        switch ($this->data['section']) {
            case 1 :
                $sms = helperGetSmsTemplate('checklist_office');
                break;
            case 2 :
                if($this->data['extra_data'])
                    $sms = helperGetSmsTemplate('designer_first_design');
                else
                    $sms = helperGetSmsTemplate('accepted_design');

                break;
            case 4 :
                $sms = helperGetSmsTemplate('checklist_technical');
                break ;
            case 5 :
                $sms = helperGetSmsTemplate('checklist_support');
                break ;

        }


        $msg = str_replace('{customer}', $customer->manager, $sms->template);
        $msg = str_replace('{lang}', $lang, $msg);
        $msg = str_replace('{extra_data}', $this->data['extra_data'], $msg);
        $msg = str_replace('{instagram_link}', $instagram_link, $msg);
        $msg = str_replace('{sms_eol}', PHP_EOL, $msg);


        return [
            'message' => $msg ,
            'title'   => $sms->title
        ];
    }


}

