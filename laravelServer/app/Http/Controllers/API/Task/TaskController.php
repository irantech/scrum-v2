<?php

namespace App\Http\Controllers\API\Task;

use App\Http\Controllers\API\Contract\SubTaskController;
use App\Http\Controllers\API\ToDoList\ToDoListController;
use App\Http\Controllers\API\User\UserController;
use App\Http\Controllers\Controller;
use App\Http\Resources\API\Contract\ContractTasksCollection;
use App\Http\Resources\API\Task\ArchiveTasksCollection;
use App\Http\Resources\API\Task\GetFeartureTasksCollection;
use App\Http\Resources\API\Task\showTasks;
use App\Http\Resources\API\Task\SingleTask;
use App\Http\Resources\API\Task\Task as TaskResource;
use App\Http\Resources\API\Task\TaskCollection;
use App\Models\Contract;
use App\Models\Section;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use function _HumbugBox7eb78fbcc73e\Amp\Iterator\filter;
use function _HumbugBox7eb78fbcc73e\Amp\Iterator\toArray;


class TaskController extends Controller
{

    protected $table;

    public function __construct()
    {
        $this->middleware('scope:create-tasks')->only(['store', 'update']);

    }

    public function getFeatureTask()
    {
        $tasks = Task::where('feature', 'yes')->get();
        $data = new GetFeartureTasksCollection($tasks);
        return response()->json(['message' => __('scrum.api.get_success'), 'data' => $data]);
    }
    public function showTasks(Request $request)
    {

        if($request->flag == "given_time" || $request->flag == null){
               $tasks_given_time=Task::whereNotNull('delivery_time')
                ->where('status', '!=', 'complete')
                ->get();
            $data_tasks_given_time = new TaskCollection($tasks_given_time);

            $data=[
                'data' => $data_tasks_given_time,
            ];
        }
        if(!$request->has('flag'))
        {
            $user_id = $start_date = $end_date = "";
            $last_reference_user_id = "";
            $last_reference_user = User::where('name', $request->last_reference_user)->first();
            $user = User::where('name', $request->user_name)->first();
            if ($last_reference_user)
                $last_reference_user_id = $last_reference_user->id;
            if ($user)
                $user_id = $user->id;
            if (isset($request['start_date']) && !empty($request['start_date'])) {
                $start_date = Verta::parse($request['start_date'])->datetime()->format('y-m-d');
            }
            if (isset($request['end_date']) && !empty($request['end_date'])) {
                $end_date = Verta::parse($request['end_date'])->datetime()->format('y-m-d');
            }
//----------------------------------------------------------------
            $contracts = Contract::when($last_reference_user_id ?? null, function ($query) use ($last_reference_user_id) {
                $query->whereHas('tasks.lastReferenceTodoList', function ($query) use ($last_reference_user_id) {
                    $query->where('user_id', $last_reference_user_id)
                        ->whereIn('id', function ($subQuery) {
                            $subQuery->selectRaw('max(id)')
                                ->from('todo_lists')
                                ->groupBy('todoable_id');
                        });
                });
            })
                ->when($request['customer_id'] ?? null, function ($query) use ($request) {
                    $query->whereHas('customer', function ($query) use ($request) {
                        $query->where('id', $request['customer_id']);
                    });
                })
                ->when($start_date ?? null, function ($query) use ($start_date) {
                    $query->whereHas('tasks', function ($query) use ($start_date) {
                        $query->whereDate('created_at', '>=', $start_date);
                    });
                })
                ->when($end_date ?? null, function ($query) use ($end_date) {
                    $query->whereHas('tasks', function ($query) use ($end_date) {
                        $query->whereDate('created_at', '<=', $end_date);
                    });
                })
                ->whereHas('tasks')
                ->when($user_id ?? null, function ($query) use ($user_id) {
                    $query->whereHas('tasks.user', function ($query) use ($user_id) {
                        $query->where('user_id', $user_id);
                    });
                })
                ->with(['runningTasks' => function ($query) {
                    $query->selectRaw('it_tasks.*,
                               CASE
                                   WHEN status = "running" THEN DATEDIFF(CURRENT_DATE(), created_at)
                                   ELSE 0
                               END AS days_left')
                        ->orderByDesc('days_left');
                }])
                ->get()
                ->map(function ($contract) {
                    $contract->max_days_left = $contract->runningTasks->max('days_left');
                    return $contract;
                })
                ->sortByDesc('max_days_left');
            $data_contract = new ContractTasksCollection($contracts);
            $data=[
                'data_contract' => $data_contract,
            ];
        }
        if($request->flag == "no_time_given")
        {
//            $tasks_not_assign=Task::doesntHave('contract')->get();
            $tasks_no_time_given=Task::whereNull('delivery_time')
                ->where('status', '!=', 'complete')
                ->get();
            $data_tasks_no_time_given = new TaskCollection($tasks_no_time_given);

            $data=[
                'data_tasks_not_assign' => $data_tasks_no_time_given,
            ];
        }
        if($request->flag == "archive_tasks")
        {
            $tasks_archive=Task::where('status','complete')->get();
            $data_tasks_archive = new TaskCollection($tasks_archive);
            $data=[
                'data_tasks_not_assign'=>$data_tasks_archive
            ];
        }
//        $queries = DB::getQueryLog();
//        dd($queries);



        return \response()->json([
            'status' => 'true',
            'data' => $data
        ]);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $task_list = Task::all();
        $data = new TaskCollection($task_list);
        return response()->json(['message' => __('scrum.api.get_success'), 'data' => $data]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => "required|max:255",
            'section_id' => "required|exists:App\Models\Section,id",
//            'checklist_contract_id'     => "exists:App\Models\ChecklistContract,id",
            'status' => "in:complete,running,hold,cancel",
//            'description' => 'string',
//            'label_list'  => "exists:App\Models\TaskLabel,id",
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], $this->unauthorizedStatus);
        }

        $task = new Task();
        $task->title = $request->title;
        $task->description = $request->description;
        $task->status = $request->needChecking ? 'hold' : 'running';
        $task->user_id = Auth::user()->id;
        $task->contract_id = $request->contract_id;
        $task->site_link = $request->site_link;
        $task->theme_link = $request->theme_link;
        $task->feature = $request->feature;
        $task->save();

        $task->taskLabels()->sync($request->label_list);
        $section_order = Section::find($request->section_id);
        $manager = new UserController();
        $user = $manager->findManager($section_order->order);
        $task_request = new Request();
        $task_request['task_type'] = get_class($task);
        $task_request['task_id'] = $task->id;
        $task_request['title'] = $task->title;
        $task_request['user_id'] = $user->id;
        $task_request['task_status'] = '1';

        $toDoList = new ToDoListController();
        $toDoList->assignTaskToUser($task_request);
        return response()->json([
            'success' => true,
            'data' => new  TaskResource($task),
            'message' => __('scrum.api.insert_success', ['item' => trans_choice('scrum.title.task', 1)]),
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $task = Task::findOrFail($id);
        $data = new SingleTask($task);
        return response()->json(['message' => __('scrum.api.get_success'), 'data' => $data]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => "required|max:50",
//            'user_id'     => "required|exists:App\Models\User,id'",
//            'checklist_contract_id'     => 'exists:App\Models\checklist_contract,id',
            'status' => "in:complete,running,hold,cancel",
//            'description' => 'string',
//            'label_list'  => 'exists:App\Models\task_labels,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], $this->unauthorizedStatus);
        }

        $task = Task::findOrFail($id);
        $task->title = $request->title;
        $task->description = $request->description;
        $task->status = 'hold';
        $task->contract_id = $request->contract_id;
        $task->site_link = $request->site_link;
        $task->theme_link = $request->theme_link;
        $task->save();

        $task->taskLabels()->sync($request->label_list);


        return response()->json([
            'success' => true,
            'data' => new  TaskResource($task),
            'message' => __('scrum.api.update_success', ['item' => trans_choice('scrum.title.task', 1)]),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $task_model = new Task();
        $task = $task_model->find($id);
        $task->status = 'complete';
        if (!$task->delivery_time) {
            $task->delivery_time = Carbon::now();
        }
        $task->save();
        $todoLists = $task->lastTodoList()->where('status', 'in_progress')->get();

        if (count($todoLists) > 0) {
            $task_request = new Request();
            $task_request['task_type'] = get_class($task);
            $task_request['task_id'] = $task->id;
            $task_request['status'] = 'done';
            $toDoList = new ToDoListController();
            $toDoList->updateTaskStatus($task_request, $todoLists[0]['id']);
        }


        return response()->json([
            'data' => new  TaskResource($task),
            'message' => __('scrum.api.update_success', ['item' => trans_choice('scrum.title.checklist', 1)])]);
    }

    public function assignSubTask(Task $task, Request $request)
    {

    }

    public function updateDeliveryTime($id, Request $request)
    {
        $task_model = new Task();
        $task = $task_model->find($id);
        $validator = Validator::make($request->all(), [
            'date' => "required"
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], $this->unauthorizedStatus);
        }
        $task->delivery_time = $request->date;
        $task->save();

        return response()->json([
            'success' => true,
            'data' => new  SingleTask($task),
            'message' => __('scrum.api.update_success', ['item' => trans_choice('scrum.title.task', 1)]),
        ], 200);
    }

    public function updateTaskTime($id, Request $request)
    {
        $task_model = new Task();
        $task = $task_model->find($id);
        $validator = Validator::make($request->all(), [
            'date' => "required"
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], $this->unauthorizedStatus);
        }

        $task->delivery_time = $request->date;
        $task->save();

        return response()->json([
            'success' => true,
            'data' => new  SingleTask($task),
            'message' => __('scrum.api.update_success', ['item' => trans_choice('scrum.title.task', 1)]),
        ], 200);
    }

    public function getTaskListPeriod($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => "required",
            'duration' => "required|integer",
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], $this->unauthorizedStatus);
        }


        $days_after = Carbon::parse($request['date'])->addDays($request['duration']);
        $days_before = Carbon::parse($request['date'])->subDays($request['duration']);

        $user = Auth::user();
        $todo_list = $user->taskTodoList()->pluck('todoable_id');
        $task_list = Task::where('delivery_time', '>=', $days_before)->where('delivery_time', '<=', $days_after)->where('id', '!=', $id)->where('status', '!=', 'complete')->whereIn('id', $todo_list)->get();
        return response()->json(['message' => __('scrum.api.get_success'), 'data' => new TaskCollection($task_list)]);
    }

    public function setTaskUser(Task $task, Request $request)
    {

        $validator = Validator::make($request->all(), [
            'user' => 'required|exists:App\Models\User,id',
            'subTaskList' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], $this->unauthorizedStatus);
        }

        $subTaskRequest = new Request();
        $subTaskRequest['user'] = $request['user'];
        $subTaskRequest['subTaskList'] = $request['subTaskList'];
        $subTaskRequest['task'] = $task->id;
        $subTask = new SubTaskController();

        $subTask->assignSubTaskToUser($subTaskRequest);

//        if($subTaskResult) {
//            $todo_request = new Request();
//            $todo_request['task_type'] = get_class($task);
//            $todo_request['task_id'] = $task->id;
//            $todo_request['title'] = $task->title;
//            $todo_request['user_id'] = $request['user'] ;
//            $todo_request['task_status'] = '1';
//
//            $toDoList = new ToDoListController();
//            $toDoList->assignTaskToUser($todo_request);
//        }
        return response()->json([
            'success' => true,
            'data' => new  SingleTask($task),
            'message' => __('scrum.api.api.update_success', ['item' => trans_choice('scrum.title.task', 1)]),
        ], 200);
    }

    public function getFilteredTasks(Request $request)
    {
        $flag = 1;
        if ($request->section_id == 7)
            $request['section_id'] = 2;
        if ($request->status == '') {
            $request['status'] = 'running';
            $flag = 0;
        }
        $start_date=null;
        $end_date=null;
        if (isset($request['start_date']) && !empty($request['start_date'])) {
            $start_date = Verta::parse($request['start_date'])->datetime()->format('y-m-d');
        }
        if (isset($request['end_date']) && !empty($request['end_date'])) {
            $end_date = Verta::parse($request['end_date'])->datetime()->format('y-m-d');
        }

        $start_delivery_time = null;
        $end_delivery_time = null;
        if (isset($request['start_delivery_date']) && !empty($request['start_delivery_date'])) {
            $start_delivery_time = Verta::parse($request['start_delivery_date'])->datetime()->format('y-m-d');
        }
        if (isset($request['end_delivery_date']) && !empty($request['end_delivery_date'])) {
            $end_delivery_time = Verta::parse($request['end_delivery_date'])->datetime()->format('y-m-d');
        }
        $task_model = new Task();
        DB::enableQueryLog();
        $task_list = $task_model->when($request['section_id'] ?? null, function ($q) use ($request) {
            $q->whereHas('firstTodoList', function ($q) use ($request) {
                $q->whereIn('id', function ($subQuery) {
                    $subQuery->selectRaw('min(id)')
                        ->from('todo_lists')
                        ->groupBy('todoable_id');
                })->whereHas('user.role', function ($q) use ($request) {
                    $q->where('section_id', $request->section_id);
                });
            });
        })
            ->where('user_id', Auth::user()->id)
            ->when($request['start_date'] ?? null, function ($q) use ($start_date) {
                $q->whereDate('created_at', '>=', $start_date);
            })
            ->when($request['end_date'] ?? null, function ($q) use ($end_date) {
                $q->whereDate('created_at', '<=', $end_date);
                    })
            ->Where('status', '!=', 'complete')
            ->when($request['contract'] ?? null, function ($q) use ($request) {
                $q->where('contract_id', $request['contract']);
            })
            ->when($request['title'] ?? null, function ($q) use ($request) {
                $q->Where('title', 'like', '%' . $request['title'] . '%');
            });
        if ($flag) {
            $task_list = $task_list->when($request['status'] ?? null, function ($q) use ($request, $flag) {
                $q->Where('status', $request['status']);
            });
        }
        $task_list = $task_list
            ->when($request['has_delivery'] ?? null, function ($q) use ($request) {
                if ($request['has_delivery'] == '1') {
                    $q->whereNull('delivery_time');
                } else {
                    $q->whereNotNull('delivery_time');
                }
            })
            ->when($request['start_delivery_date'] ?? null, function ($q) use ($start_delivery_time) {
                $q->whereDate('delivery_time', '>=', $start_delivery_time);
            })
            ->when($request['end_delivery_date'] ?? null, function ($q) use ($end_delivery_time) {
                $q->whereDate('delivery_time', '<=', $end_delivery_time);
            })->orderBy('delivery_time', 'desc')->orderBy('created_at', 'desc')->get();

        $task_id_list = $task_list->pluck('id');
        if ($flag) {
            $unDoneTaskList = $task_model->where('user_id', Auth::user()->id)->where(function ($query) use ($task_id_list) {
                $query->whereNotIn('id', $task_id_list)->whereNull('delivery_time')->orwhere('status', '!=', 'complete');
            })->get();
            $merged = $task_list->merge($unDoneTaskList);

            $task_list = $merged->all();
        }


//        $queries = DB::getQueryLog();
//        dd($queries);

        $data = new TaskCollection($task_list);


        return response()->json(['message' => __('scrum.api.get_success'), 'data' => $data]);

    }

    public function changeTaskLabel($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'label' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], $this->unauthorizedStatus);
        }

        $task = Task::findOrFail($id);

        $task->taskLabels()->detach();
        $task->taskLabels()->attach($request['label']);
        return response()->json([
            'success' => true,
            'data' => new  SingleTask($task),
            'message' => __('scrum.api.update_success', ['item' => trans_choice('scrum.title.task', 1)]),
        ], 200);
    }

    public function changeTaskTodo($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], $this->unauthorizedStatus);
        }
        $task = Task::findOrFail($id);
        if (!empty($request['status'])) {
            $task->status = $request['status'];
            $task->save();
        }

//        $manager = new UserController() ;
//        $user = $manager->findManager($request->section);

        if ($task->lastTodoList->count() > 0) {
            $task_request = new Request();
            $task_request['task_type'] = get_class($task);
            $task_request['task_id'] = $task->id;
            $task_request['status'] = 'done';
            $todo_id = $task->lastTodoList[0]->id;
            $toDoList = new ToDoListController();
            $toDoList->updateTaskStatus($task_request, $todo_id);
        }
        if ($request['status'] != 'complete') {
            $task_request = new Request();
            $task_request['task_type'] = get_class($task);
            $task_request['task_id'] = $task->id;
            $task_request['title'] = $task->title;
            $task_request['description'] = $request->description;
            $task_request['user_id'] = $request->user;
            $task_request['task_status'] = '1';
            $toDoList = new ToDoListController();
            $toDoList->assignTaskToUser($task_request);
        }
        return response()->json([
            'success' => true,
            'data' => new  SingleTask($task),
            'message' => __('scrum.api.update_success', ['item' => trans_choice('scrum.title.task', 1)]),
        ], 200);
    }
}
