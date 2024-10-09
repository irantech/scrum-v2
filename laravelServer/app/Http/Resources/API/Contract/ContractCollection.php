<?php

namespace App\Http\Resources\API\Contract;

use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ContractCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Support\Collection
     */
    public function toArray($request)
    {

        return  $this->collection->map(function ($item){
            return [
                'id'               => $item->id,
                'user_id'	       => $item->user_id ,
                'old_id_customer'  => $item->old_id_customer,
                'title'            => $item->title ,
                'contract_code'    => $item->contract_code ,
                'sign_date'	       => $item->sign_date ,
                'end_date'         => $item->end_date ,
                'start_date'       => $item->start_date,
                'customer'         => new Customer($item->customer),
                'ancillary'        => new AncillaryCollection($item->ancillary) ,
                'jalali_created_at'=> Verta::instance($item->created_at)->formatDatetime(),
                'jalali_sign_date' => Verta::instance($item->sign_date)->formatDate(),
                'jalali_start_date'=>Verta::instance($item->start_date)->formatDate(),
                'jalali_end_date'  => Verta::instance($item->end_date)->formatDate(),
                'checklistContract'=> new ChecklistContractCollection($item->checklistContract)
            ];
        });



    }
}
