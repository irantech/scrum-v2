<?php

namespace App\Http\Resources\API\PersonsScore;

use App\Http\Resources\API\User\User;
use App\Models\Score;
use Illuminate\Http\Resources\Json\JsonResource;

class ManagerScore extends JsonResource
{
    public function sectionId($user)
    {
        $section_order=$user->role()->first()->section()->first()->order;
        if($section_order == 2 ){
            $section_order =  4 ;
        }
        return \App\Models\Section::where('order' , $section_order)->first()->id;
    }
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $currentSectionId = $this->sectionId($this);

        $details_score = Score::whereHas('users', function ($query) use ($currentSectionId) {
            $query->whereHas('role', function ($query) use ($currentSectionId) {
                $query->whereHas('section', function ($query) use ($currentSectionId) {
                    $query->where('id', $currentSectionId);
                });
            });
        });

        $sum_manager_positive_score=$details_score->sum('manager_positive');
        $sum_manager_negative_score=$details_score->sum('manager_negative');

        $details_manager_score=$details_score
            ->selectRaw('*, SUM(manager_negative) as sum_manager_negative_score, SUM(manager_positive) as sum_manager_positive_score')
            ->groupBy('checklist_contract_id')
            ->get();

        return [
            'user'=>new User($this),
            'sum_manager_positive_score'=>$sum_manager_positive_score,
            'sum_manager_negative_score'=>$sum_manager_negative_score,
            'details_manager_score'=>new ManagerPersonCollection($details_manager_score)
        ];

    }


}
