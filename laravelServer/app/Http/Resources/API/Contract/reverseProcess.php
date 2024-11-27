<?php

namespace App\Http\Resources\API\Contract;

use App\Http\Resources\API\Checklist\Checklist;
use App\Http\Resources\API\ContractChecklist\subTaskCollection;
use App\Http\Resources\API\Globals\Section;
use App\Http\Resources\API\User\User;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Resources\Json\JsonResource;

class reverseProcess extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {

        $count_error = $this->countReplies($this->subError);
        $count_offer = $this->countReplies($this->subOffer);
        $count_Periodic = $this->countReplies($this->subPeriodic);

        return [
            'id' => $this->id,
            'section' => new Section($this->section),
            'user_section' => new User($this->getUserSection($this->checklist_contract_id, $this->section)),
            'date' => Verta::instance($this->created_at)->format((' j %B Y ')),
            'error_reverse_data' => [
                'total_count' => count($this->subError),
                'accept_count' => $count_error ? $count_error['accept_count'] : 0,
                'reject_count' => $count_error ? $count_error['reject_count'] : 0,
            ],
            'offer_reverse_data' => [
                'total_count' => count($this->subOffer),
                'accept_count' => $count_offer ? $count_offer['accept_count'] : 0,
                'reject_count' => $count_offer ? $count_offer['reject_count'] : 0,
            ],
            'periodic_reverse_data' => [
                'total_count' => count($this->subPeriodic),
                'accept_count' => $count_Periodic ? $count_Periodic['accept_count'] : 0,
                'reject_count' => $count_Periodic ? $count_Periodic['reject_count'] : 0,
            ],
        ];
    }

    private function getUserSection($contract_checklist, $section)
    {

        $contract_checklist = \App\Models\ChecklistContract::find($contract_checklist);
        $user = $contract_checklist->titleChecklistUser->where('section_id', $section->id)->first();
        return \App\Models\User::find($user->user_id);
    }

    function countReplies($comments)
    {

        $accept_count = 0;
        $reject_count = 0;
        dd($comments);
        foreach ($comments as $comment) {

            if ($comment) {

                $reply = $comment->lastReplies()->first();
                if ($reply['status'] == 'accept') {
                    $accept_count++;

                }
                if ($reply['status'] == 'reject') {
                    $reject_count++;


                }
            }

        }
        return [
            'accept_count' => $accept_count,
            'reject_count' => $reject_count,
        ];
    }


}
