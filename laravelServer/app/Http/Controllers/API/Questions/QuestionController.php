<?php

namespace App\Http\Controllers\API\Questions;

use App\Http\Controllers\Controller;
use App\Http\Requests\AnswerRequest;
use App\Http\Requests\QuestionToTaskRequest;
use App\Http\Requests\StoreRequest;
use App\Http\Resources\API\Question\QuestionStoreResource;
use App\Models\Question;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class QuestionController extends Controller
{
    public function __construct()
    {
        $this->middleware('scopes:store_question')->only('store');
        $this->middleware('scopes:index_question')->only('index');
        $this->middleware('scopes:show_question')->only('show');
        $this->middleware('scopes:delete_question')->only('delete');
        $this->middleware('scopes:update_question')->only('update');
        $this->middleware('scopes:answer_question')->only('answer');
    }
    public function store(StoreRequest $request)
    {
        $user_id=Auth::user()->id;
        $section_id=User::find($user_id)->role()->first()->section_id;
        $question=Question::create([
            'title'=>$request->title,
            'description'=>$request->description,
            'user_id'=>$user_id,
            'section_id'=>$section_id
        ]);
        $data=new QuestionStoreResource($question);
        return response()->json(['message' => __('scrum.api.get_success'), 'data' => $data]);

    }

    public function index()
    {
        $question=Question::all();
        $data=new QuestionStoreResource($question);
        return response()->json(['message' => __('scrum.api.get_success'), 'data' => $data]);

    }

    public function delete(Question $question)
    {
        $data = new QuestionStoreResource($question);
        $question->delete();
        return response()->json(['message' => __('scrum.api.get_success'), 'data' => $data]);
    }

    public function show(Question $question)
    {
        $data = new QuestionStoreResource($question);
        return response()->json(['message' => __('scrum.api.get_success'), 'data' => $data]);
    }

    public function update(StoreRequest $request,Question $question)
    {
        $data=$question->update([
            'title'=>$request->title,
            'description'=>$request->description,
            'answer_time'=>$request->answer_time,
            'answer_description'=>$request->answer_description
      ]);
        $data = new QuestionStoreResource($question);
        return response()->json(['message' => __('scrum.api.get_success'), 'data' => $data]);
    }

    public function answer(AnswerRequest $request,Question $question)
    {
        $data=$question->update([
            'answer_time'=>$request->answer_time,
            'answer_description'=>$request->answer_description
        ]);
        $data = new QuestionStoreResource($question);
        return response()->json(['message' => __('scrum.api.get_success'), 'data' => $data]);

    }

    public function questionToTask(QuestionToTaskRequest $request)
    {
//        dd($request->description);
        $user_id=Auth::user()->id;
        $task=Task::create([
           'title'=>$request->title,
            'contract_id'=>$request->contract_id,
            'description'=>$request->description,
            'site_link'=>$request->site_link,
            'theme_link'=>$request->theme_link,
            'user_id'=>$user_id,
        ]);
    }
}
