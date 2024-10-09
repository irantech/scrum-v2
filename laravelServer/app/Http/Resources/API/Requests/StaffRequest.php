<?php

namespace App\Http\Resources\API\Requests;

use App\Http\Resources\API\Auth\user;
use App\Http\Resources\API\User\UserCollection;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Resources\Json\JsonResource;

class StaffRequest extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $request_class = $this->requestable_type;
        $request_model = new $request_class ;

        $request = $request_model->where('id' , $this->requestable_id)->first();

        $class_parts = explode('\\', $request_class);
        $base_class = end($class_parts);
        if($base_class == 'ToDoList')
        {
            $base_class = 'todoList';
            $reason = json_decode( $this->reason ) ;
            $reason = [
                'reason' => $reason->reason,
                'starting_time' => Verta::instance($reason->starting_time)->format((' j-%B-Y h:i:s')),
                'ending_time' => Verta::instance($reason->ending_time)->format((' j-%B-Y h:i:s')),
            ];
            $reason = json_encode($reason);
        }
        $base_class = 'App\Http\Resources\API\ToDoList\\'.$base_class;
        
        return [
            'id'                => $this->id,
            'manager_reason'    => $this->manager_reason ,
            'reason'            => $reason ? $reason : $this->reason ,
            'has_confirmed'     => $this->has_confirmed ,
            'request_item'      => new $base_class($request),
            'user_requested'    => new User($this->User) ,
            'request_type'      => end($class_parts) ,
            'created_at'        =>  Verta::instance($this->created_at)->format((' j-%B-Y ')) ,
            'updated_at'        =>  Verta::instance($this->updated_at)->format((' j-%B-Y '))
        ];
    }
}
