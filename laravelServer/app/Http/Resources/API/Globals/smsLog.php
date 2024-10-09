<?php

namespace App\Http\Resources\API\Globals;

use App\Models\Customer;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Resources\Json\JsonResource;

class smsLog extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        return [
            'id'            => $this->id ,
            'customer'      => $this->customer_id ? Customer::find($this->customer_id) : '' ,
            'title'         => $this->title ,
            'sms_text'      => $this->sms_text ,
            'status'        => $this->status ,
            'phone_number'  => json_decode($this->phone_number , true) ,
            'date'          => Verta::instance($this->created_at)->format('Y-n-j')
        ];
    }
}
