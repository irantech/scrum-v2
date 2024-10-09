<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;

class CheckStaffStartTask
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $titleChecklist = $request->checklistContract->titleChecklistUser()->where('section_id' , $request->section)->first();

        $todolist =  $request->checklistContract->lastTodoList()->where('user_id' , $titleChecklist->user_id)->orderBy('created_at' , 'desc')->first();

        if($todolist && $todolist->status == 'started'){
            return response()->json(['message' => 'شما ابتدا باید دکمه شروع را در لیست کارهای خود بزنید.'], Response::HTTP_FORBIDDEN);
        }
        return $next($request);

    }
}
