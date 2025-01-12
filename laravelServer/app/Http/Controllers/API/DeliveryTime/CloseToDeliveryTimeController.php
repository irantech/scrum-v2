<?php

namespace App\Http\Controllers\API\DeliveryTime;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use \App\Http\Resources\API\Task\TaskCollection;
use Illuminate\Http\Response;

class CloseToDeliveryTimeController extends Controller
{
    public function showDeliveryTasks()
    {
        $three_days_ago=Carbon::now()->subDays(3)->format('Y-m-d');
        $tasks=Task::where('delivery_time','>=',$three_days_ago)->get();
        $data=new TaskCollection($tasks);
        return response()->json(['message' => __('scrum.api.get_success'), 'data' => $data], Response::HTTP_OK);

    }
}
