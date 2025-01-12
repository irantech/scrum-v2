<?php

namespace App\Http\Resources\API\Contract;


use App\Http\Resources\API\ToDoList\todoListCollection;
use App\Http\Resources\API\User\UserCollection;
use Illuminate\Http\Resources\Json\JsonResource;


class subTask extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public function toArray($request)
    {

        $checklist = \App\Models\Checklist::find($this->checklist_id);
        $contract = \App\Models\Contract::find($this->contract_id);
        $users =  $this->titleChecklistUser()->groupBy('user_id')->pluck('user_id');
        $users = \App\Models\User::whereIn('id' , $users)->get();

        $todoList = $this->todoList()->where('status' , '!=' , 'stop')->get() ;

//---------------------------------------------------------------------------------------------------
        $reverse_data=collect(new reverseProcessCollection($this->subTaskProcess));
        $user=collect(new UserCollection($users));
        $user_id=$user->pluck('id');
        $reverse_data_user_id=$reverse_data->pluck('user_section.id')->unique();
        $grouped_by_user_section = $reverse_data->groupBy(function ($item) {
            return $item['user_section']['id'];
        });
        $missing_user_ids = $user_id->diff($reverse_data_user_id);

        $totals_per_user_section = $grouped_by_user_section->map(function ($group) {
            $total_sum = 0;
            $manager_negative_score_sum = 0;
            $manager_positive_score=0;
            $group->each(function ($item) use (&$total_sum, &$manager_negative_score_sum) {
                $count_error = $item['error_reverse_data']['accept_count'];
                $count_Periodic = $item['periodic_reverse_data']['accept_count'];

                $user_score = $count_error + (10 * $count_Periodic) + 1;
                $manager_negative_score = $user_score * 3;

                $total_sum += $user_score;
                $manager_negative_score_sum += $manager_negative_score;
            });

            return [
                'total_sum' => $total_sum,
                'manager_negative_score_sum' => $manager_negative_score_sum,
                'manager_positive_score' => $manager_positive_score
            ];
        });
//        foreach ($reverse_data as $reverse)
//        dd($reverse_data);
//---------------------------------------------------------------------------------------------------

        return [
            'id'               => $this->id,
            'users'            => $user,
            'checklist'        => new reverseChecklist($checklist),
            'contract'         => new reverseContract($contract),
            'todolist'         => new \App\Http\Resources\API\Contract\ToDoListCollection($todoList),
            'process_reversed_count'   => $this->sub_task_process_count,
            'reverse_data'     => $reverse_data,
            'score'            =>$totals_per_user_section
        ];
    }



}
