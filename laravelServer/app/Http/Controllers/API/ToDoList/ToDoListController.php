<?php

namespace App\Http\Controllers\API\ToDoList;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\Globals\Section as SectionResource;
use App\Http\Resources\API\Globals\SectionCollection;
use App\Http\Resources\API\ToDoList\logCollection;
use App\Http\Resources\API\ToDoList\todoListCollection;
use App\Http\Resources\API\ToDoList\todoList as todoListResource;
use App\Http\Resources\API\ToDoList\userTodoListCollection;
use App\Models\Checklist;
use App\Models\ChecklistContract;
use App\Models\role;
use App\Models\Section;
use App\Models\Task;
use App\Models\taskTime;
use App\Models\ToDoList;
use App\Models\TrainingSession;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\ErrorHandler\Error\UndefinedFunctionError;
use function _HumbugBox7eb78fbcc73e\Amp\Promise\rethrow;
use function Symfony\Component\String\s;
use Spatie\Activitylog\Models\Activity;

class ToDoListController extends Controller
{

    public function __construct() {
        $this->middleware('scope:show-all-user-todolist,show-office-user-todolist,show-programmer-user-todolist,show-graphic-user-todolist,show-support-user-todolist,show-sale-user-todolist,show-designer-user-todolist')->only('allUserTaskList');
    }


    public function assignTaskToUser(Request $request) {

        $validator = Validator::make($request->all(), [
            'title' => [
                'required',
                'max:100',
            ],
            'user_id' => [
                'required',
                'exists:App\Models\User,id'
            ],
            'task_type' =>[
                'required'
            ],
            'task_status' => [
                'required' ,
                'in:1,0'
            ]
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => __( 'scrum.api.insert_error' ),
            ], 400 );
        }

        // to whom
        $user = User::findOrFail($request['user_id']);

        // what is the task ( checklist or ticket )
        $task_class = $request['task_type'];
        $task_model = new $task_class ;
        $task = $task_model->where('id' , $request['task_id'])->first();

        $todoList = new ToDoList();
        $todoList->title = $request['title'];
        $todoList->description = $request['description'];
        $todoList->task_status = $request['task_status'];
        $todoList->status = 'in_progress' ;
        $todoList->starting_time = Carbon::now();

        activity()
            ->causedBy(Auth::user())
            ->withProperties(['manger-assign' => $user->name])
            ->log('change_todo_time');

        $todoList->User()->associate($user);

        $task->todoList()->save($todoList);



        return response()->json([
            'success' => true,
            'message' => __( 'scrum.api.insert_success' ),
        ], 200 );
    }

    public function updateTaskStatus(Request $request , $id) {

        $validator = Validator::make($request->all(), [
            'status' => [
                'required',
                'in:started,in_progress,done,delay'
            ],
            'task_type' => [
                'required'
            ],
            'task_id' => [
                'required'

            ]
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => __( 'scrum.api.insert_error' ),
            ], 400 );
        }

        $task_class = $request['task_type'];
        $task_model = new $task_class ;

        $task = $task_model->where('id' , $request['task_id'])->first();
        $todo = $task->todoList()->where('id' , $id)->first();

        $new_data = [
            'status' => $request['status']
        ];
        if($request['status'] == 'in_progress'){
            $new_data['starting_time'] = Carbon::now();
        }
        if($request['status'] == 'done') {
            $new_data['ending_time'] = Carbon::now();

            $diff_time = $new_data['ending_time']->diff(Carbon::parse($todo->starting_time))->format('%H:%I:%S');

            $diff_day = weekDays(Carbon::parse($todo->starting_time) , $new_data['ending_time']);

            if($diff_day < 0) {
                $diff_day =  0 ;
            }
            $diff  = $diff_day . ' ' . $diff_time ;
            $new_data['difference_time'] = $diff;

            $todoStatus  = $this->setTodoStatus($diff , $todo);

            $new_data['todo_status'] = $todoStatus;
        }
        $task->todoList()->where('id' , $id)->update($new_data);
        activity()
            ->causedBy(Auth::user())
            ->withProperties(['change_status' => $request['status']])
            ->log('change_todo_time');
        return response()->json([
            'success' => true,
            'data'    => $task,
            'message' => __( 'scrum.api.insert_success' ),
        ], 200 );
    }

    public function userTaskList(Request $request){
        $user = Auth::user();

        if( $request['type'] == 'task' ||   !empty($request['start_delivery_time']) || !empty($request['end_delivery_time']) || !empty($request['has_delivery']) ) {
        $todo_list = $user->todoLists()
                ->whereHasMorph('todoable', [Task::class] ,function($q) use ($request) {
                    $q->when($request['start_delivery_time'] ?? null, function ($query) use ($request) {
                        $delivery_time = Verta::parse($request['start_delivery_time'])->datetime()->format('y-m-d');
                        $query->whereDate('delivery_time','>=' ,  $delivery_time);
                    });
                    $q->when($request['end_delivery_time'] ?? null, function ($query) use ($request) {
                        $delivery_time = Verta::parse($request['end_delivery_time'])->datetime()->format('y-m-d');
                        $query->whereDate('delivery_time','<=' ,  $delivery_time);
                    });
                    $q->when(!empty($request['has_delivery']) , function ($query) use ($request) {
                        if($request['has_delivery'] == '1') {

                            $query->whereNull('delivery_time' );
                        }else{
                            $query->whereNotNull('delivery_time');
                        }
                    });
                } );
        }else if(isset($request['type']) && $request['type'] == 'checklist' ){
            $todo_list = $user->todoLists()
                ->whereHasMorph('todoable', [ChecklistContract::class] );
        }else if($request['type'] == 'ChecklistContract') {
            $todo_list = $user->todoLists()
                ->whereHasMorph('todoable', [ChecklistContract::class, Task::class]);
        }else{
            $todo_list = $user->todoLists()
                ->whereHasMorph('todoable', [TrainingSession::class]);
        }
        $todo_list->get();


        if(isset($request['status']) && !empty($request['status'])) {
            if(is_array($request['status'])) {
                $todo_list = $todo_list->whereIn('status' , $request['status']);
            }else {
                $todo_list = $todo_list->where('status' , $request['status']);
            }
        }else{
            $todo_list = $todo_list->where('status' , 'in_progress');
        }

        if(isset($request['title']) && !empty($request['title'])) {
            $todo_list = $todo_list->Where('title', 'like', '%' . $request['title'] . '%');
        }



        if(isset($request['start_time']) && !empty($request['start_time']) ) {

            $start_time = Verta::parse($request['start_time'])->datetime()->format('y-m-d');
            $todo_list = $todo_list->whereDate('created_at','>=' ,  $start_time);
        }
        if(isset($request['end_time']) && !empty($request['end_time']) ){
            $end_time = Verta::parse($request['end_time'])->datetime()->format('y-m-d');
            $todo_list = $todo_list->whereDate('created_at','<=' ,  $end_time);
        }
        if(isset($request['done_task_status']) && !empty($request['done_task_status'])) {
                $todo_list = $todo_list->where('todo_status' , $request['done_task_status']);
        }

        $todo_list = $todo_list->get() ;
        $todo_id_list = $todo_list->pluck('id');
        $unDoneTodoList = $user->todoLists()->where('status' , 'in_progress')->whereNotIn('id' ,$todo_id_list )->get();

        $merged = $todo_list->merge($unDoneTodoList);

        $todo_list = $merged->all();
      
        $status_count = [
            'best'   => $user->todoLists()
                ->whereHasMorph('todoable', [ChecklistContract::class] )->where('todo_status' ,'best')->get()->count() ,
            'on-time' => $user->todoLists()
                ->whereHasMorph('todoable', [ChecklistContract::class] )->where('todo_status' ,'on-time')->get()->count() ,
            'bad' => $user->todoLists()
                ->whereHasMorph('todoable', [ChecklistContract::class] )->where('todo_status' , 'bad')->get()->count(),
            'worst' => $user->todoLists()
                ->whereHasMorph('todoable', [ChecklistContract::class] )->where('todo_status' , 'worst')->get()->count(),
        ];

        return response()->json([
            'success' => true,
            'status_count' =>  $status_count,
            'data'    => new todoListCollection($todo_list),
            'message' => __( 'scrum.api.insert_success' ),
        ], 200 );


    }
    public function managerGetUserTodoList(Request $request){

        $section =  $this->findSectionOrder();



        $user = new User();
        if($section == 'all') {
            $user_todo_lists = $user->with([ 'todoLists' => function($query) use ($request){
                if(isset($request['startTodoDate']) && !empty($request['startTodoDate'])  ){
                    $start_time = Verta::parse($request['startTodoDate'])->datetime()->format('y-m-d');
                    $query->whereDate('created_at', '>=' , $start_time);
                }
                if(isset($request['endTodoDate']) && !empty($request['endTodoDate'])) {
                    $end_time = Verta::parse($request['endTodoDate'])->datetime()->format('y-m-d');
                    $query->whereDate('created_at', '<=' , $end_time);
                }
            }])->whereRaw('(select count(*) from `it_todo_lists` where `it_users`.`id` = `it_todo_lists`.`user_id` and `it_todo_lists`.`deleted_at` is null) > 0')->get();
        }else {
            $roles = Role::whereIn('section_id' , $section)->pluck('id') ;
            $user_todo_lists = $user->with(['todoLists' => function($q) use ($request) {
                if(isset($request['startTodoDate']) && !empty($request['startTodoDate']) ) {

                    $start_time = Verta::parse($request['startTodoDate'])->datetime()->format('y-m-d');

                    $q->whereDate('created_at', '>=' , $start_time);
                }
                if(isset($request['endTodoDate']) && !empty($request['endTodoDate']) ){
                    $end_time = Verta::parse($request['endTodoDate'])->datetime()->format('y-m-d');
                    $q->whereDate('created_at', '<=' , $end_time);
                }
            }])->whereIn('role_id' , $roles)->orderBy('username')->get();
        }

        $data =  new userTodoListCollection($user_todo_lists);

        return response()->json([
            'success' => true,
            'data'    => $data,
            'message' => __( 'scrum.api.insert_success' ),
        ], 200 );
    }

    public function findSectionOrder()  {
        if(Auth::user()->tokenCan('manager-approving-office')) {
            $result[] = 1 ;
        }if(Auth::user()->tokenCan('manager-approving-programmer')){
            $result[] = 3 ;
        }if(Auth::user()->tokenCan('manager-approving-graphic')){
            $result[] = 2 ;
        }if(Auth::user()->tokenCan('manager-approving-support')){
            $result[] = 4 ;
        }if(Auth::user()->tokenCan('manager-approving-sales')){
            $result[] = 5 ;
        }if(Auth::user()->tokenCan('support-final-approve-design')){
            $result[] = 7 ;
        }
        if(Auth::user()->tokenCan('show-all-user-todolist')) {
            $result = 'all' ;
        }
        return $result;
    }

    public function assignTask($checklistContract , $user , $status = 1){
        $checklist_title = $checklistContract->checklist->title;
        $customer_name = $checklistContract->contract->customer->name;
        $task_title = $customer_name . ' ( ' . $checklist_title . ' ) ';
        if($status == 0 ) {
            $task_title = $task_title. ' برگشت خورده' ;
        }
        $task_request = new Request();
        $task_request['task_type'] = get_class($checklistContract);
        $task_request['task_id'] = $checklistContract->id;
        $task_request['title'] = $task_title;
        $task_request['user_id'] = $user;
        $task_request['task_status'] = $status;

        $this->assignTaskToUser($task_request);

    }
    public function findManager($section_order) {
        if($section_order == 4 ){
            $section_order =  3 ;
        }

        $section = Section::where('order' , $section_order)->first();
        $role = role::where('section_id' , $section->id)->where('type' , 'manager')->first();

        return User::where('role_id' , $role->id)->first();
    }
    public  function setTodoList() {

        $todoables = ToDoList::pluck('todoable_id');

       $checklistContracts = ChecklistContract::whereNotIn('id', $todoables)->get();
       foreach ($checklistContracts as $checklistContract){
           $checklistProcess = $checklistContract->checklistProcess()->orderBy('id' , 'DESC')->first();
           if($checklistProcess) {
               $sectionList = json_decode($checklistContract->checklist->sections,true);
               if($checklistProcess->status == 4){

                   if($sectionList[count($sectionList) - 1]['id'] != $checklistProcess->section_id) {

                       $current = collect($sectionList)->where('id', $checklistProcess->section_id)->getIterator();
                       if ($current->key() + 1 < collect($sectionList)->count()) {
                           $next = $sectionList[$current->key() + 1];
                           $nextSection = Section::findOrFail($next['id']);
                           $nextSection = new SectionResource($nextSection);

                           if($nextSection) {
                               $user  = $checklistContract->titleChecklistUser()->where('section_id' , $nextSection->id)->first();

                               if($user) {
                                   $this->assignTask($checklistContract , $user->user_id);
                               }else {
                                   break;
                               }

                           }else {
                               break;
                           }
                       }
                   }

               }
               else if($checklistProcess->status == 5) {

                   $section = Section::find($checklistProcess->section_id);

                   if($section->order == 2 ) {
                       $user  = $checklistContract->titleChecklistUser()->where('section_id' , 4)->first();
                      if($user){
                          $this->assignTask($checklistContract , $user->user_id);
                      }

                   }else {
                       $manager = $this->findManager($section->order) ;
                       $this->assignTask($checklistContract , $manager->id);
                   }

               }
               else if($checklistProcess->status == 1) {

                   $signedChecklistProcess = $checklistContract->checklistProcess()->where('section_id' , $checklistProcess->section_id)->where('status' , 5)->where('signed' , 1)->first();
                   if($signedChecklistProcess) {
                       $section = Section::find($checklistProcess->section_id);
                       $manager = $this->findManager($section->order) ;
                       $this->assignTask($checklistContract , $manager->id);
                   }else {
                       $this->assignTask($checklistContract , $checklistProcess->user_id);
                   }
               }
               else if($checklistProcess->status == 2) {

                   $signedChecklistProcess = $checklistContract->checklistProcess()->where('section_id' , $checklistProcess->section_id)->where('status' , 4)->where('signed' , 1)->first();

                   if($signedChecklistProcess) {
                       if($sectionList[count($sectionList) - 1]['id'] != $checklistProcess->section_id) {
                           $current = collect($sectionList)->where('id', $checklistProcess->section_id)->getIterator();
                           if ($current->key() + 1 < collect($sectionList)->count()) {
                               $next = $sectionList[$current->key() + 1];
                               $nextSection = Section::findOrFail($next['id']);
                               $nextSection = new SectionResource($nextSection);
                           }
                       }

                       if($nextSection) {

                           $user  = $checklistContract->titleChecklistUser()->where('section_id' , $nextSection->id)->first();
                           if($user){
                               $this->assignTask($checklistContract , $user->user_id);
                           }

                       }else {
                           return true;
                       }
                   }else {
                       if($checklistProcess->section_id ==  7 ) {
                           $checklistProcess->section_id = 4;
                       }
                       $section = Section::find($checklistProcess->section_id);

                       $manager = $this->findManager($section->order) ;
                       $this->assignTask($checklistContract , $manager->id);
                   }
               }
               else if($checklistProcess->status == 3) {

                   $signedChecklistProcess = $checklistContract->checklistProcess()->where('section_id' , $checklistProcess->section_id)->where('status' , 6)->where('signed' , 1)->first();
                   if($signedChecklistProcess) {
                       $section = Section::find($checklistProcess->section_id);
                       $manager = $this->findManager($section->order) ;
                       $this->assignTask($checklistContract , $manager->id);
                   }else {
                       $user  = $checklistContract->titleChecklistUser()->where('section_id' , 4)->first();
                       if($user) {
                           $this->assignTask($checklistContract , $user->user_id);
                       }
                   }
               }
               else if($checklistProcess->status == 6) {

                   $section = Section::find(4);
                   $manager = $this->findManager($section->order) ;
                   $this->assignTask($checklistContract , $manager->id);
               }
               else if($checklistProcess->status == 0) {


                   $user  = $checklistContract->titleChecklistUser()->where('section_id' , $checklistProcess->section_id)->first();
                   if($user){
                       $this->assignTask($checklistContract , $user->user_id , 0);
                   }

               }
           }
           else{
               $user  = $checklistContract->titleChecklistUser()->where('section_id' , 1)->first();
               if($user) {
                   $this->assignTask($checklistContract , $user->user_id);
               }
           }
       }

    }

    public function changeTodoListTime(Request $request , $id){

        $validator = Validator::make($request->all(), [
            'change_time_reason' => [
                'required'
            ],
            'starting_time' => [
                'required'
            ],
            'ending_time' => [
                'required'
            ]
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => __( 'scrum.api.insert_error' ),
            ], 400 );
        }

        $todo = ToDoList::find($id);

        $diff_time = Carbon::parse($request['ending_time'])->diff(Carbon::parse($request['starting_time']))->format('%H:%I:%S');

        $diff_day = weekDays(Carbon::parse($request['starting_time']) , Carbon::parse($request['ending_time']));
     
        $diff  = $diff_day . ' ' . $diff_time ;

        $todoStatus  = $this->setTodoStatus($diff , $todo);

        $todo->update([
            'starting_time' => $request['starting_time'] ,
            'ending_time' => $request['ending_time'] ,
            'change_time_reason' => $request['change_time_reason'] ,
            'difference_time'  => $diff ,
            'todo_status'         => $todoStatus
        ]);

        activity()
            ->performedOn($todo)
            ->causedBy(Auth::user())
            ->withProperties(['manger-reason' => $request['change_time_reason']])
            ->log('change_todo_time');
        return response()->json([
            'success' => true,
            'data'    => new todoListResource($todo),
            'message' => __( 'scrum.api.insert_success' ),
        ], 200 );


    }

    public function  updateDiffTime() {
        $todolist = ToDoList::where('status' , 'done')->get();
        foreach ( $todolist as $todo) {

            $starting_time = Carbon::parse($todo->starting_time)  ;
            $ending_time = Carbon::parse($todo->ending_time)  ;

            $diff_time = $ending_time->diff($starting_time)->format('%H:%I:%S');

            $diff_day = weekDays($starting_time , $ending_time);
            $diff  = $diff_day . ' ' . $diff_time ;

            $todoStatus  = $this->setTodoStatus($diff , $todo);

            $todo->difference_time = $diff ;
            $todo->todo_status = $todoStatus ;

            $todo->save();


        }
        return response()->json([
            'success' => true,
            'message' => __( 'scrum.api.insert_success' ),
        ], 200 );
    }

    public static function setTodoStatus($diff , $todo) {
    
       if($todo->todoable_type == 'App\Models\ChecklistContract') {
        $checklist = ChecklistContract::find($todo->todoable_id)->checklist;
       }else if($todo->todoable_type == 'App\Models\Task') {
            $task = Task::find($todo->todoable_id);
       }

        $section = User::find($todo->user_id)->roleSection;
        $task_time = null ;
        if(isset($checklist)) {
            $task_time = \App\Models\taskTime::where('section_id' , $section->section->id)->where('tasktimeable_id' , $checklist->id)->where('task_status' , $todo->task_status)->first();
        }else if(isset($task)) {
            if($section->type != 'manager') {
                $task_time = \App\Models\taskTime::where('section_id' , $section->section->id)->where('tasktimeable_id' , $task->id)->where('task_status' , $todo->task_status)->first();
            }
        }


        if(!$task_time) {
            return  '1' ;
        }
//        $task_time = taskTime::where('section_id' , $section->section->id)->where('tasktimeable_id' , $checklist->id)->where('task_status' , $todo->task_status)->first();

        $total_task_duration = calculatePeriodOfTimeInMinutes($task_time->task_day_duration  , $task_time->task_time_duration );

        $total_interval_task_duration = calculatePeriodOfTimeInMinutes($task_time->interval_day_duration  , $task_time->interval_time_duration );
        $total_task_duration_in_minute = $total_task_duration + $total_interval_task_duration ;

        $diff_task_duration_in_minute =  $total_task_duration - $total_interval_task_duration ; 



        $diff_day = explode (" ", $diff);

        $day = (int)$diff_day[0];
        $time = $diff_day[1];

        $total_diff_minute = calculatePeriodOfTimeInMinutes($day  , $time );

        if( $total_diff_minute < $diff_task_duration_in_minute ) {
            return  '1' ;
        }else if($total_diff_minute > $diff_task_duration_in_minute && $total_diff_minute < $total_task_duration) {
            return '2' ;
        }else if( $total_diff_minute > $total_task_duration && $total_diff_minute < $total_task_duration_in_minute ){
            return '3' ;
        }else if($total_diff_minute  > $total_task_duration_in_minute){

       
            return '4' ;
        }
    }
    public function todoListLogList() {
        $data =  Activity::where('description' , 'change_todo_time')->get();
        return response()->json([
            'success' => true,
            'data' => new logCollection($data),
            'message' => __( 'scrum.api.insert_success' ),
        ], 200 );
    }
    public static  function stopReturnLastTodoList($id , $status){
        $checklist_contract = ChecklistContract::find($id);
        $to_do_list = $checklist_contract->lastTodoList()->first();
        if($status == 'stop') {
            $to_do_list->status = 'stop';
        }else {
            $to_do_list->status = 'in_progress';
            $to_do_list->starting_time = Carbon::now();
        }
        $to_do_list->save();
        return $to_do_list;
    }
}
