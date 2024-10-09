<?php

namespace App\Http\Resources\API\Task;

use App\Http\Resources\API\Globals\Section;
use App\Http\Resources\API\User\Role;
use App\Http\Resources\API\User\User;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Resources\Json\JsonResource;

class detail extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {


        $count_error = $this->countReplies($this->subTaskError);
        $count_offer = $this->countReplies($this->subTaskOffer);
        $count_Periodic = $this->countReplies($this->subTaskPeriodic);

        return [
            'section' => new Section($this->roleSection->section),
            'user_section' => new User($this),
            'error_reverse_data' => [
                'total_count'       => count($this->subTaskError),
                'accept_count'      => $count_error ? $count_error['accept_count'] : 0,
                'reject_count'      => $count_error ? $count_error['reject_count'] : 0,
            ],
            'offer_reverse_data' => [
                'total_count'       => count($this->subTaskOffer),
                'accept_count'      => $count_offer ?  $count_offer['accept_count'] :  0,
                'reject_count'      => $count_offer ? $count_offer['reject_count'] : 0,
            ],
            'periodic_reverse_data' => [
                'total_count'       => count($this->subTaskPeriodic),
                'accept_count'      => $count_Periodic ? $count_Periodic['accept_count'] : 0 ,
                'reject_count'      => $count_Periodic ? $count_Periodic['reject_count'] :0,
            ],
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
