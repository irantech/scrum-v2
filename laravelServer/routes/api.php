<?php
use App\Http\Controllers\API\Contract\ContractChecklistController;
use App\Http\Controllers\API\Contract\ContractController;
use App\Http\Controllers\API\Contract\SubTaskController;
use App\Http\Controllers\API\Customer\CustomerController;
use App\Http\Controllers\API\DeliveryTime\CloseToDeliveryTime;
use App\Http\Controllers\API\DeliveryTime\CloseToDeliveryTimeController;
use App\Http\Controllers\API\InitialDesign\InitialDesignController;
use App\Http\Controllers\API\Questions\QuestionController;
use App\Http\Controllers\API\Requests\MeetingDetailController;
use App\Http\Controllers\API\Requests\MeetingsController;
use App\Http\Controllers\API\Score\ScoreController;
use App\Http\Controllers\API\Task\TaskController;
use App\Http\Controllers\API\User\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//---------------------------------------------------------- initialdesign
Route::prefix( 'initialdesign' )->group( function () {
    Route::post('store/{checklistContract}',[InitialDesignController::class,'store']);
    Route::get('show/{checklistContract}',[InitialDesignController::class,'show']);
    Route::post('update/{checklistContract}',[InitialDesignController::class,'update']);
});


//---------------------------------------------------------- meetings
Route::prefix( 'meetings' )->middleware(('auth:api'))->group( function () {
    Route::post('store',[MeetingsController::class,'store'])->middleware('scope:create_meeting');
    Route::get('index',[MeetingsController::class,'index'])->middleware('scope:index_meeting');
    Route::get('show/{meeting}',[MeetingsController::class,'show'])->middleware('scope:show_meeting');
    Route::post('update/{meeting}',[MeetingsController::class,'update'])->middleware('scope:update');
});


//---------------------------------------------------------- meetingsdetails
Route::prefix( 'meetingsdetails' )->middleware(('auth:api'))->group( function () {
    Route::post('store',[MeetingDetailController::class,'sl
    tore']);
    Route::get('index',[MeetingDetailController::class,'index']);
    Route::get('show/{meetingsDetails}',[MeetingDetailController::class,'show']);
    Route::post('update/{meetingDetail}',[MeetingDetailController::class,'update']);
});


//---------------------------------------------------------- questions
Route::prefix( 'questions' )->middleware(('auth:api'))->group( function () {
    Route::post('/store',[QuestionController::class,'store'])->middleware('scope:index_question');
    Route::get('/index',[QuestionController::class,'index'])->middleware('scope:store_question');
    Route::delete('delete/{question}',[QuestionController::class,'delete'])->middleware('scope:delete_question');
    Route::get('show/{question}',[QuestionController::class,'show'])->middleware('scope:show_question');
    Route::post('update/{question}',[QuestionController::class,'update'])->middleware('scope:update_question');
    Route::post('answer/{question}',[QuestionController::class,'answer'])->middleware('scope:answer_question');
    Route::post('question_to_task',[QuestionController::class,'questionToTask']);
});

//---------------------------------------------------------- Close to delivery time
Route::prefix( 'Close-to-delivery-time' )->group( function () {
    Route::get('tasks',[CloseToDeliveryTimeController::class,'showDeliveryTasks']);

});



//---------------------------------------------------------- scores
Route::prefix( 'scores' )->middleware(('auth:api'))->group( function () {
    Route::get('score/checklist-contract/{checklistContract}', [ScoreController::class, 'score']);
    Route::post('scores_all', [ScoreController::class, 'scoresAll']);
    Route::get('persons_scores', [ScoreController::class, 'personsScores']);
});
//----------------------------------------------------------------------------------------------------------------------------------------



Route::namespace( 'API' )->group( function () {

    Route::prefix( 'curl' )->group( function () {
        Route::get( '/', 'CurlController@index' );
        Route::post( 'do-import', 'CurlController@importPrevData' );
        Route::post( 'new-pre-contract', 'CurlController@newPreContract' );
        Route::post( 'new-customer', 'CurlController@newCustomer' );
        Route::post( 'new-contract', 'CurlController@newContract' );
        Route::post( 'edit-contract', 'CurlController@editContract' );
        Route::post( 'edit-customer', 'CurlController@editCustomer' );
        Route::get( 'get_airline_image', 'CurlController@get_image' );



    } );
    Route::prefix( 'curl' )->namespace('Agency')->group( function () {

            Route::post('/import-data', 'AgencyController@storeTrigerAutomation');
            Route::post('/trigger-store', 'AgencyController@storeTriger');
            Route::post('/update-data', 'AgencyController@update');
            Route::post('/filtered-data', 'AgencyController@showNew');
            Route::post('/all-data', 'AgencyController@index');
            Route::post('/sms', 'AgencyController@sms');
            Route::post('/excel', 'AgencyController@excel');

    });
    Route::get( 'setTodoList', 'ToDoList\ToDoListController@setTodoList' );


    Route::group(['namespace' => 'Auth'], function()
    {
        Route::post( 'login', 'AuthController@login' );
        Route::post( 'register', 'AuthController@register' );
        Route::get( 'force-login/{user}', 'AuthController@force_login' );
    });
    Route::group(['namespace' => 'Upload'] , function(){
        Route::post('upload' , 'UploadController@upload');
        Route::post('uploadEditor' , 'UploadController@uploadEditor');
    });
    Route::group( [ 'middleware' => ['auth:api'] ], function () {
        Route::group(['namespace' => 'Auth'], function()
        {
            Route::get( 'details', 'AuthController@details' );
            Route::get( 'logout', 'AuthController@logout' );
        });
        Route::group(['namespace' => 'Contract'], function()
        {
            Route::apiResource( 'contract', 'ContractController' )->only( [ 'index', 'show' ] );
            Route::apiResource( 'ancillary', 'AncillaryController' );
            Route::middleware('scope:update-title-ancillary')->put( 'ancillary/update-title/{id}', 'AncillaryController@updateTitle' );
            Route::middleware('scope:ancillary-change-base-progress')->put( 'ancillary-change-base-progress', 'AncillaryController@ChangeBaseProgressStatus' );
            Route::middleware('scope:ancillary-change-sub-progress')->put( 'ancillary-change-sub-progress', 'AncillaryController@ChangeSubProgressStatus' );

            Route::get( 'contract_execution_time_management', [ContractController::class,'contractExecutionTimeManagement'] );
            Route::post('contractCount/month' , 'ContractController@countMonthContracts');
            Route::middleware('scope:change-status-sub-progress')->patch( 'change-sub-progress-status', 'ContractController@ChangeSubProgressStatus' );
            Route::middleware('scope:change-base-progress-status')->patch( 'change-base-progress-status', 'ContractController@ChangeBaseProgressStatus' );

            Route::middleware('scope:update-date-contract')->put( 'contracts/update-date/{contract}', 'ContractController@UpdateDate' );
            Route::middleware('scope:update-date-contract')->put( 'contracts/update-domain/{contract}', 'ContractController@UpdateDomain' );
            Route::middleware('scope:update-theme-link-contract')->put( 'contracts/update-theme/{contract}', 'ContractController@UpdateThemeLink' );
            Route::get( 'contracts', 'ContractController@user_contract_list' );
            Route::get( 'contract-type/list', 'ContractTypeController@showAllWithTrashed' );
            Route::put( 'contract-type/{id}/restore', 'ContractTypeController@restore' );
            Route::apiResource( 'contract-type', 'ContractTypeController' );
            Route::get( 'contract-types/list', 'ContractTypeController@index' );

            //contract checklist
            Route::put( 'assign-checklist/{contract}', 'ContractChecklistController@assignChecklist' );

            Route::get('score/checklist-contract/{checklistContract}' , [ScoreController::class,'score']);
            Route::get('scores_all' , [ScoreController::class,'scoresAll']);
            Route::get('persons_scores' , [ScoreController::class,'personsScores']);

            Route::post( 'assignUser/{checklistContract}', 'ContractChecklistController@assign' );
            Route::get('all/checklist-contract/{checklistContract}' , [ContractChecklistController::class,'getContractChecklists']);
            Route::get('sum/checklist-contract/{checklistContract}' , [ContractChecklistController::class,'sumDuration']);
            Route::post('checklist-contract/{checklistContract}/titleChecklist/{titleChecklist}' , 'ContractChecklistController@changeStatus');
            Route::post('process/checklist-contract/{checklistContract}' ,'ContractChecklistController@setContractChecklistProcess');
            Route::get('process/checklist-contract/{checklistContract}' ,'ContractChecklistController@getLastChecklistProcess');
            Route::get('reverse/checklist-contract/{checklistContract}' ,[ContractChecklistController::class,'getChecklistReverse']);
            Route::get('checklist-contract/{checklistContract}' , 'ContractChecklistController@checklistContract');
            Route::post('sign/contract/{contract}/checklist/{checklist}' ,'ContractChecklistController@managerSignChecklist');
            Route::get('contract/{contract}/checklist/{checklist}/process/all' ,'ContractChecklistController@getChecklistProcess');
            Route::put('delivery-date/{checklistContract}' ,[ContractChecklistController::class,'updateChecklistDeliveryTime']);











            Route::post('subTask/create' ,[SubTaskController::class,'create']);
            Route::put('subTask/{sub_task}' ,'SubTaskController@updateFile');
            Route::post('reply/subTask' ,'SubTaskController@reply');
            Route::put('reply/subTask/{sub_task}' ,'SubTaskController@editReply');
            Route::put('reply/file/subTask/{sub_task}' ,'SubTaskController@deleteImage');
            Route::get('trainingSession/all'  , 'TrainingSessionController@index');
            Route::post('trainingSession'  , 'TrainingSessionController@store');
            Route::get('trainingSession/{ChecklistContract}'  , 'TrainingSessionController@getContractTrainingSession');
            Route::put('trainingSession/{TrainingSession}'  , 'TrainingSessionController@update');
            Route::post('subTask'  , [SubTaskController::class,'subTasks']);
            Route::put('subTask/seen/{sub_task}' ,'SubTaskController@seenSubTask');
            Route::get('subTask/order' ,'SubTaskController@orderSubTask');
            Route::get('subTask/addKeyReverse' ,'SubTaskController@addKeySubTask');
        });

        Route::group(['namespace' => 'Progress'], function()
        {
            Route::get( 'base-progress/all', 'BaseProgressController@showAllWithTrashed' );
            Route::put( 'base-progress/{id}/restore', 'BaseProgressController@restore' );
            Route::apiResource( 'base-progress', 'BaseProgressController' );
            Route::apiResource( 'progress', 'SubProgressController' );
            Route::put( 'progress/{id}/restore', 'SubProgressController@restore' );
        });
        Route::group(['namespace' => 'User'], function()
        {
            Route::get( 'user/{id}/contracts', 'UserController@user_contracts' );
            Route::apiResource( 'capability', 'UserCapabilityController' );
            Route::get( 'user/list', 'UserController@listUsers' );
            Route::apiResource( 'user', 'UserController' );
            Route::put( 'user/{id}/restore', 'UserController@restore' );
            Route::get( 'current-role', 'UserController@getCurrentUserRole' );
            Route::apiResource( 'role', 'RoleController' );
            Route::put( 'role/{id}/restore', 'RoleController@restore' );
            Route::apiResource( 'permission', 'PermissionController' );
            Route::put( 'permission/{id}/updatePermissionStatus', 'PermissionController@ChangePreDefined' );
            Route::post( 'user/avatar/upload', 'UserMetaController@saveAvatar' );
            Route::post( 'user/signature/upload', 'UserMetaController@saveSignature' );
            Route::put('user/single/update' , 'UserController@updateUserData');
            Route::get('user/activity/list' , 'UserController@getActivity');
            Route::get('user/todo/list' , 'UserController@todoList');
            Route::put('user/todo/{id}/markAsRead' , 'UserController@markAsRead');
            Route::get('user/managers/get' , [UserController::class,'getManager']);
        });
        Route::group(['namespace' => 'Customer'], function()
        {
            Route::get( 'customer/{id}/contracts', 'CustomerController@customer_contracts' );
            Route::get( 'customers', 'CustomerController@index' );
            Route::post( 'report-customers', 'CustomerController@AllCustomersProjects' );
            Route::get( 'report-customers', 'CustomerController@AllCustomersProjects' );
            Route::get( 'report-customers/{id}', 'CustomerController@CustomersProjects' );
        });
        Route::group(['namespace' => 'Globals'], function()
        {
            Route::apiResource( 'section', 'SectionController' );
            Route::apiResource( 'softwares', 'SoftwareController' );
            Route::get( 'section', 'SectionController@index' );
            Route::get('file-backup', 'BackupController@backupFile');
            Route::get('database-backup', 'BackupController@backupDb');

            Route::post('sms-logs', 'smsLogController@index');
            Route::apiResource( 'holiday', 'HolidayController' );
        });
        Route::group(['namespace' => 'Checklist'], function()
        {
            Route::apiResource( 'checklist', 'ChecklistController' );
            Route::put( 'checklist/{id}/restore', 'ChecklistController@restore' );
            Route::apiResource( 'titleChecklist', 'TitleChecklistController' );
            Route::apiResource('language' , 'LanguageController');
        });
        Route::group(['namespace' => 'ToDoList'] , function(){
            Route::post('todoList' , 'ToDoListController@assignTaskToUser');
            Route::put('todoList/{id}' , 'ToDoListController@updateTaskStatus');
            Route::put('change/todoList/{id}' , 'ToDoListController@changeTodoListTime');
            Route::delete('todoList/{id}/delete' , 'ToDoListController@removeTask');
            Route::post('userTodoList' , 'ToDoListController@userTaskList');
            Route::post('all/todoList' , 'ToDoListController@managerGetUserTodoList');
            Route::resource('taskTime' , 'TaskTimeController');
            Route::get('diffTime/todoList' , 'ToDoListController@updateDiffTime');
            Route::get('todoListLog' , 'ToDoListController@todoListLogList');
            Route::post('stopTodoList' , 'customerHoldController@sendProjectToCustomer');
            Route::get('returnTodoList/{CustomerHold}' , 'customerHoldController@returnProjectFromCustomer');
            Route::get('getCustomerHold/{CustomerHold}' , 'customerHoldController@getCustomerHoldByChecklistContract');
        });
        Route::group(['namespace' => 'Requests'] , function(){
            Route::resource('staffRequest' , 'StaffRequestController');
            Route::post('manager/requests' , 'StaffRequestController@getManagerRequestedList');
            Route::get('staff/requests' , 'StaffRequestController@getStaffRequestedList');
            Route::post('admin/requests' , 'StaffRequestController@getAdminRequestedList');
        });
        Route::group(['namespace' => 'Task'] , function(){
            Route::resource('taskLabel' , 'TaskLabelController');
            Route::put( 'taskLabel/{id}/restore', 'TaskLabelController@restore' );
            Route::resource('task' , 'TaskController');
            Route::post('getTask' , [TaskController::class,'getFilteredTasks']);
            Route::put('task/{id}/setTime' , 'TaskController@updateDeliveryTime');
            Route::post('task/list/period/{id}' , 'TaskController@getTaskListPeriod');
            Route::put('assignSubTask/{task}' , 'TaskController@setTaskUser');
            Route::put('changeTaskLabel/{id}' , 'TaskController@changeTaskLabel');
            Route::get('getFeatureTask' , [TaskController::class,'getFeatureTask']);
        });
        Route::middleware('scope:admin-handle-sms-templates')
            ->apiResource('smsTemplate' , 'SmsTemplateController');
    });
    Route::post('showTasks',[TaskController::class,'showTasks']);

    Route::group(['namespace' => 'Customer'] , function(){
        Route::any( 'customer/{hash}/get-contracts', 'CustomerController@CustomerProjectsByHash' );
        Route::get( 'customer/{hash}/contract/{contract}/stats', 'CustomerController@CustomerProjectStatus' );
        Route::get( 'customer/{hash}/get-complete-percentage/{count}', 'CustomerController@CompletePercentageProjectsByHash' );
    });


});


