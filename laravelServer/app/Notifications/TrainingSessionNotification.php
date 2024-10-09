<?php

namespace App\Notifications;

use App\Channel\checklistSmsChannel;
use Hekmatinasser\Verta\Verta;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrainingSessionNotification extends Notification
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


        $receiver = [env('SMS_ADMIN_PHONE') , env('SMS_SUPPORT_PHONE') , env('SMS_FANI_PHONE')  , $notifiable];
        $receiver = implode(',',$receiver);

        $message_info = $this->generateMassage();
        return [
            'receptor' => $receiver,
            'template' => $message_info['message'],
            'params'   => '',
            'title'    => $message_info['title'],
            'type'     => 'normal',
        ];

    }


    public function generateMassage(){

        $location_place = '' ;
        $address = '';

        if($this->data['status'] == 'insert')
            $sms = helperGetSmsTemplate('training_session_time_set');
        else if($this->data['status'] == 'update')
            $sms = helperGetSmsTemplate('training_session_time_change');
        else if($this->data['status'] == 'cancel')
            $sms = helperGetSmsTemplate('training_session_time_cancel');

        $checklist_contract = $this->data['checklistContract'];
        $customer = $checklist_contract->contract->customer;
        $training_data = $this->data['trainingSession'];
        $site_link = 'https://instagram.com/myirantech';
        $telephone = '02188866609';

        $location_status = $training_data->location_status ? 'به صورت حضوری' : 'به صورت آنلاین';
        if($training_data->location_status){
            $location_place = $training_data->location_place ?   ' در محل شرکت جنابعالی' : ' در محل شرکت ایران تکنولوژی';
            $address = $training_data->location_place ?   ' ' : 'آدرس: ' . $training_data->address;
        }
        $training_data->session_date = str_replace('-', '/', $training_data->session_date);
        $msg = str_replace('{customer}', $customer->name, $sms->template);
        $msg = str_replace('{session_date}', $training_data->session_date, $msg);
        if(isset($this->data['old_date']) && !empty($this->data['old_date'])) {
            $this->data['old_date'] = str_replace('-', '/', $this->data['old_date']);
            $msg = str_replace('{old_date}',  $this->data['old_date'], $msg);
        }
        if(isset($this->data['cancel_reason']) && !empty($this->data['cancel_reason'])) {
            $msg = str_replace('{cancel_reason}',  $this->data['cancel_reason'], $msg);
        }
        $msg = str_replace('{session_time}', $training_data->session_time, $msg);
        $msg = str_replace('{location_status}', $location_status, $msg);
        $msg = str_replace('{site_link}', $site_link, $msg);
        $msg = str_replace('{phone}', $telephone, $msg);
        $msg = str_replace('{location_place}', $location_place , $msg);
        $msg = str_replace('{address}', $address, $msg);
        $msg = str_replace('{sms_eol}', PHP_EOL, $msg);
        $msg = str_replace('"', '', $msg);

        
        return [
            'message' => $msg ,
            'title'   => $sms->title
        ];

    }



}

