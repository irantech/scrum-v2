<?php

namespace App\Http\Resources\API\Contract;

use App\Http\Resources\API\ContractChecklist\subTaskCollection;
use App\Http\Resources\API\ContractChecklist\User;
use App\Http\Resources\API\Globals\Section;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Resources\Json\JsonResource;

class reverseDetail extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $result = $this->countReplies($this);
        return [
            'accept_count'      => $result['accept_count'] ,
            'reject_count'      => $result['reject_count'],
        ];
    }
    function countReplies($comments) {
        $accept_count = 0;
        $reject_count = 0;
        foreach ($comments as $comment) {
           if($comment)  {
               $reply =  $comment->lastReplies;
               if($reply['status'] == 'accept') {
                   $accept_count ++ ;
               }else{
                   $reject_count ++ ;
               }
           }
        }
        return [
            'accept_count' => $accept_count ,
            'reject_count' => $reject_count ,
        ];
    }
}
