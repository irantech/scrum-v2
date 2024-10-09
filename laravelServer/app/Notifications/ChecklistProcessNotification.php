<?php

namespace App\Notifications;

use App\Channel\checklistSmsChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Helpers\appHelper;

class ChecklistProcessNotification extends Notification
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
//        if($notifiable->phone_number)
//            return [checklistSmsChannel::class , 'database'];
        return ['database'];
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
            'description'       => $this->data['desc'] ,
            'checklist_contract'          => $this->data['checklist_contract'],
            'contract_title'    => $this->data['contract_title'],
            'checklist_title'   => $this->data['checklist_title'],
            'customer_name'     => $this->data['customer_name']
        ];
    }

    public function toSMS( $notifiable ) :array
    {
        $sms = helperGetSmsTemplate('checklist-process');

        if ($sms->params) {
            $params = [$sms->params => $this->verification_code];

            return [
                'receptor' => $notifiable->phone_number,
                'template' => $sms->template,
                'params' => [$params],
                'title'  => $sms->title,
                'type'  => 'pattern',
            ];
        }
        $msg = str_replace('{checklist}', $this->data['checklist_title'], $sms->template);
        $msg = str_replace('{contract}', $this->data['contract_title'], $msg);
        $msg = str_replace('{customer}', $this->data['customer_name'], $msg);
	 $msg = str_replace('{sms_eol}', PHP_EOL, $msg);
        return [
            'receptor' => $notifiable->phone_number,
            'template' => $msg,
            'params' => '',
            'title'  => $sms->title,
            'type' => 'normal',
        ];
    }
}
