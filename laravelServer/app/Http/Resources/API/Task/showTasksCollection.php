<?php

namespace App\Http\Resources\API\Task;

use Illuminate\Http\Resources\Json\ResourceCollection;

class showTasksCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($task) {
            return [
                'status' => $task->status,
                'users' => [
                        'user_id' => $task->user->id,
                        'name' => $task->user->name,
                    ],

            ];
        });
    }
}
