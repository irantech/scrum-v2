<?php

namespace App\Http\Resources\API\PersonsScore;

use App\Http\Resources\API\User\User;
use Carbon\Carbon;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Resources\Json\JsonResource;

class PersonScores extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {

        $details_score=$this->scores()
                ->selectRaw('checklist_contract_id,SUM(user_negative) as sum_user_negative_score, SUM(user_positive) as sum_user_positive_score')
                ->groupBy('checklist_contract_id')
                ->get();
        $score = $this->scores->first();
        $userScores= [
                'user_id' => $this->id,
                'sum_user_negative_score' => $score ? $score->sum_user_negative_score : 0,
                'sum_user_positive_score' => $score ? $score->sum_user_positive_score : 0,
            ];
        return [
            'user'=>new User($this),
            'user_scores'=>$userScores,
            'user_details_scores'=>new DetailsCollection($details_score)
        ];
    }
}
