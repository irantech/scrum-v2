<?php

namespace App\Http\Controllers\API\Score;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\Contract\reverseChecklist;
use App\Http\Resources\API\Contract\reverseContract;
use App\Http\Resources\API\Contract\reverseProcessCollection;
use App\Http\Resources\API\PersonsScore\ManagerScoreCollection;
use App\Http\Resources\API\PersonsScore\PersonScoresCollection;
use App\Http\Resources\API\Score\ManagerScorePositive;
use App\Http\Resources\API\Score\Score;
use App\Http\Resources\API\Score\UserPositiveCollection;
use App\Http\Resources\API\Score\UserScore;
use App\Http\Resources\API\Score\UserScorePositive;
use App\Http\Resources\API\User\User as User_resource;
use App\Http\Resources\API\User\UserCollection;
use App\Models\ChecklistContract;
use App\Models\role;
use App\Models\Score as Scores;
use App\Models\Section;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ScoreController extends Controller
{
    public function personsScores()
    {

        $users = User::with(['scores' => function ($query) {
            $query->selectRaw('*, SUM(user_negative) as sum_user_negative_score, SUM(user_positive) as sum_user_positive_score')
                ->groupBy('user_id');
        }])
            ->whereHas('role', function ($query) {
                $query->where('type', '!=', 'manager');
            })
            ->get();
        $data1 = new PersonScoresCollection($users);

        $manager = User::with('scores')
            ->whereHas('role', function ($query) {
                $query->where('type', 'manager');
            })
            ->get();
        $data2 = new ManagerScoreCollection($manager);

        $data = ['users' => $data1, 'managers' => $data2];

        return response()->json(['message' => __('scrum.api.get_success'), 'data' => $data], Response::HTTP_OK);

    }

    public function scoresAll()
    {
        $checklist_contracts = ChecklistContract::all();
        foreach ($checklist_contracts as $checklist_contract) {
            $calculateScore = $this->calculateScore($checklist_contract);
            if ($calculateScore['checklist']->resource != null){
                $scores = Score::make($calculateScore);
                $this->scoreOneUser($scores, $checklist_contract);
            }

        }
        return response()->json(['message' => __('scrum.api.get_success'), Response::HTTP_OK]);

    }

    public function score(ChecklistContract $checklistContract)
    {
        $calculateScore = $this->calculateScore($checklistContract);
        $scores=[];
        if ($calculateScore['checklist']->resource != null){
            $scores = Score::make($calculateScore);
            $this->scoreOneUser($scores, $checklistContract);
        }

        return response()->json(['message' => __('scrum.api.get_success'), 'data' => $scores], Response::HTTP_OK);

    }
    public function scoreOneUser($scores, $checklist_contract)
    {
        $scores_json=json_decode($scores->toJson());
        foreach ($scores_json->positive_score as $calculate_positive_score) {
//            dd($calculate_positive_score->manager);
            $user_positive_id=$calculate_positive_score->user->id;
            $table_positive_score=User::find($user_positive_id)->scores()->where('checklist_contract_id', $checklist_contract->id);

            if (!$table_positive_score->exists()) {
                $score_save = Scores::create([
                    'manager_positive' => $calculate_positive_score->manager->manager_positive_score,
                    'user_positive' => 1,
                    'checklist_contract_id' => $checklist_contract->id
                ]);
                $score_save->users()->attach($calculate_positive_score->user->id);
            }
        }

        foreach ($scores_json->negative_score as $calculate_negative_score) {
            $user_negative_id=$calculate_negative_score->user->id;
            $table_negative_score = User::find($user_negative_id)->scores()->where('checklist_contract_id', $checklist_contract->id);

            if (!$table_negative_score->exists()) {
                $score_save = Scores::create([
                    'manager_negative' => $calculate_negative_score->manager_negative_score_sum,
                    'user_negative' => $calculate_negative_score->user_negative_score_sum,
                    'checklist_contract_id' => $checklist_contract->id
                ]);
                $score_save->users()->attach($calculate_negative_score->user->id);
            } else {
                if ($table_negative_score->first()->user_negative == 0) {
                    Scores::update([
                        'manager_negative' => $calculate_negative_score->manager_negative_score_sum,
                        'user_negative' => $calculate_negative_score->user_negative_score_sum,
                        'user_positive' => 0,
                        'manager_positive' => 0,
                        'checklist_contract_id' => $checklist_contract->id
                    ]);
                } elseif ($table_negative_score->first()->user_negative != $calculate_negative_score->user_negative_score_sum) {
                    Scores::update([
                        'manager_negative' => $calculate_negative_score->manager_negative_score_sum,
                        'user_negative' => $calculate_negative_score->user_negative_score_sum,
                        'checklist_contract_id' => $checklist_contract->id
                    ]);
                }
            }
        }


    }

    public function calculateScore($checklistContract)
    {
        $checklist = \App\Models\Checklist::find($checklistContract->checklist_id);
        $contract = \App\Models\Contract::find($checklistContract->contract_id);
        $users_id = $checklistContract->titleChecklistUser()->groupBy('user_id')->pluck('user_id');
        $users = \App\Models\User::whereIn('id', $users_id)->get();
        $todoList = $checklistContract->todoList()->where('status', '!=', 'stop')->get();

//---------------------------------------------------------------------------------------------------
        $reverse_data = collect(new reverseProcessCollection($checklistContract->subTaskProcess));
        $user = collect(new UserCollection($users));
        $user_id = $user->pluck('id');
        $reverse_data_user_id = $reverse_data->pluck('user_section.id')->unique();

        $grouped_by_user_section = $reverse_data->groupBy(function ($item) {
            return $item['user_section']['id'];
        });

        $positive_score_user_ids = $user_id->diff($reverse_data_user_id)->toArray();

        $totals_per_user_section = $grouped_by_user_section->map(function ($group) {
            $user_negative_score_sum = 0;
            $manager_negative_score_sum = 0;
            $group->each(function ($item) use (&$user_negative_score_sum, &$manager_negative_score_sum) {
                $count_error = $item['error_reverse_data']['accept_count'];
                $count_Periodic = $item['periodic_reverse_data']['accept_count'];

                $user_score = $count_error + (10 * $count_Periodic) + 1;
                $manager_negative_score = $user_score * 3;

                $user_negative_score_sum += $user_score;
                $manager_negative_score_sum += $manager_negative_score;
            });

            $user_section_id = $group->first()['user_section']['id'];
//            $select_user = new User_resource(User::firstWhere('id', $user_section_id));
            $select_user=UserScore::make(User::firstWhere('id', $user_section_id))->additional([
                'negative_score_sum'=>$user_negative_score_sum
            ]);
            $section_order = User::firstWhere('id', $user_section_id)->role()->first()->section()->first()->order;
            if ($section_order == 2) {
                $section_order = 4;
            }
            $section = Section::where('order', $section_order)->first();
            $role = role::where('section_id', $section->id)->where('type', 'manager')->first();

            $manager = new User_resource(User::where('role_id', $role->id)->first());
            $manager=UserScore::make(User::firstWhere('id', $user_section_id))->additional([
                'negative_score_sum'=>$manager_negative_score_sum
            ]);

            return [
                'user_negative_score_sum' => $user_negative_score_sum,
                'manager_negative_score_sum' => $manager_negative_score_sum,
                'user' => $select_user,
                'manager' => $manager
            ];
        });

        $positive_score_users = User::whereIn('id', $positive_score_user_ids)->get();
        $positive_user_manager=array();
        foreach ($positive_score_users as $positive_score_user)
            array_push($positive_user_manager,$this->calculatePositiveScores($positive_score_user));


        return [
            'id' => $checklistContract->id,
            'users' => $user,
            'checklist' => new reverseChecklist($checklist),
            'contract' => new reverseContract($contract),
            'todolist' => new \App\Http\Resources\API\Contract\ToDoListCollection($todoList),
            'process_reversed_count' => $checklistContract->sub_task_process_count,
            'reverse_data' => $reverse_data,
            'manager_user_negative' => $totals_per_user_section,
            'manager_user_positive' => $positive_user_manager,
        ];

    }

    public function calculatePositiveScores($positive_score_user)
    {
        $section_order=$positive_score_user->role()->first()->section()->first()->order;
        if($section_order == 2 ){
            $section_order =  4 ;
        }
        $section = \App\Models\Section::where('order' , $section_order)->first();
        $role = \App\Models\role::where('section_id' , $section->id)->where('type' , 'manager')->first();
        return [

            'user'=>new UserScorePositive($positive_score_user),
            'manager'=>new ManagerScorePositive( User::where('role_id' , $role->id)->first()),
        ];
    }


}
