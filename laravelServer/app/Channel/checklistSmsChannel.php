<?php

namespace App\Channel;

use _HumbugBox7eb78fbcc73e\___PHPSTORM_HELPERS\this;
use App\Http\Controllers\API\Globals\smsLogController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Notifications\Notification;
use function Symfony\Component\String\s;

class checklistSmsChannel {
    /**
     * Send the given notification.
     *
     * @param  mixed $notifiable
     * @param  \Illuminate\Notifications\Notification $notification
     *
     * @return void
     */
    public function send( $notifiable, Notification $notification ) {
        $variables    = $notification->toSms( $notifiable );
        $api_url      = env( 'SMS_SERVICE_URL' );
        $api_username = env( 'SMS_SERVICE_USERNAME' );
        $api_password = env( 'SMS_SERVICE_PASSWORD' );
        $api_from     = env( 'SMS_FROM_NUMBER' );

        if( $variables['type'] == 'normal'){
            $request_params  = [
                'uname'   => $api_username,
                'pass'    => $api_password,
                'from'    => $api_from,
                'message' => $variables['template'],
                'to'      => explode(',',$variables['receptor']),
                'op'      => 'send'
            ];
        }
        if ( $variables['type'] == 'pattern' )
        {
            $request_params = [
                'op'          => 'pattern',
                'user'        => $api_username,
                'pass'        => $api_password,
                'fromNum'     => $api_from,
                'toNum'       => explode(',',$variables['receptor']),
                'patternCode' => $variables['template'],
                'inputData'   => $variables['params'],
            ];
        }

        $res = Http::post( $api_url, $request_params );

        error_log( PHP_EOL.json_encode( $request_params ), 3, 'SMS.log' );
        $status = $res->status();

        $this->smsLog($status  , $variables);


        return response()->json( $res->json(),$status);
    }


    public function smsLog($status , $variables){
        $sms_log_status = $status ==  200 ?  '1' : '0' ;

        $receptors = explode(',',$variables['receptor']);


            $log_request = new Request();
            $log_request['status']          = $sms_log_status ;
            $log_request['sms_text']        = $variables['template'];
        $log_request['customer_number'] = $receptors;
            $log_request['title']           = $variables['title'];

            $sms_log = new smsLogController();
            $sms_log->store($log_request) ;



    }
}
