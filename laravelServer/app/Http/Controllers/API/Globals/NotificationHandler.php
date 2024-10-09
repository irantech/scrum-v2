<?php

namespace App\Http\Controllers\API\Globals;

use App\Http\Controllers\Controller;
use App\Notifications\ChecklistProcessNotification;
use App\Notifications\customerChecklistNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class NotificationHandler extends Controller
{
    public function sendSMS($sms_type , $receiver , $data){
        switch ($sms_type) {
            case 'ChecklistProcess' :
                Notification::send($receiver, new ChecklistProcessNotification($data));
                break;
            case 'customerChecklist' :
                Notification::send($receiver, new customerChecklistNotification($data));

                break;
        }
    }
}
