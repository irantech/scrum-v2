<?php

namespace App\Http\Resources\API\Contract;

use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Resources\Json\JsonResource;

class Contract extends JsonResource
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
            'id'               => $this->id,
            'user_id'	       => $this->user_id ,
            'old_id_customer'  => $this->old_id_customer,
            'title'            => $this->title ,
            'contract_code'    => $this->contract_id ,
            'sign_date'	       => $this->sign_date ,
            'end_date'         => $this->end_date ,
            'start_date'       => $this->start_date,
            'description'      => $this->description ,
            'customer'         => new Customer($this->customer),
            'ancillary'        => new AncillaryCollection($this->ancillary) ,
            'domain_link'      => $this->domain_link,
            'theme_link'       => $this->theme_link,
            'jalali_created_at'=> Verta::instance($this->created_at)->formatDatetime(),
            'jalali_sign_date' => Verta::instance($this->sign_date)->formatDate(),
            'jalali_start_date'=>Verta::instance($this->start_date)->formatDate(),
            'jalali_end_date'  => Verta::instance($this->end_date)->formatDate(),
        ];
    }
}
