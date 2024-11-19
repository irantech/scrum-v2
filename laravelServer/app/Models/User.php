<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes , LogsActivity;

    protected static $logName = 'کاربر';
    protected static $logAttributes = ['name' , 'username' , 'email' , 'phone_number'];

//--------------------------------------------------------------------------------------

    public function meetingDetails()
    {
        return $this->hasMany(MeetingDetail::class);
    }
    public function meetings()
    {
        return $this->hasMany(Meeting::class);
    }


//--------------------------------------------------------------------------------------

    public function getDescriptionForEvent(string $eventName): string
    {
        switch ($eventName) {
            case 'updated' :
                $eventName = 'آپدیت شد.';
                break;
            case 'created' :
                $eventName = 'ایجاد شد.' ;
                break;
            case 'deleted' :
                $eventName = 'حذف شد.';
                break;
        }
        return " کاربر {$eventName}";
    }

    public function tapActivity(Activity $activity)
    {
        $req = Request::header();
        $activity->properties = $activity->properties->put('URL', $req['origin'][0] . $req['originpath'][0]);
    }

    public function findForPassport($identifier)
    {
        return $this->orWhere('email', $identifier)->orWhere('username',$identifier)->first();
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable
        = [
            'username','name', 'email', 'password','phone_number'
        ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden
        = [
            'password', 'remember_token',
        ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts
        = [
            'email_verified_at' => 'datetime',
        ];

    public function meta()
    {
        return $this->hasMany(UserMeta::class);
    }

    public function capability()
    {
        return $this->hasMany(UserCapability::class);
    }

    public function contract()
    {
        return $this->hasMany(Contract::class, 'user_id');
    }

    public function scopeIdDescending($query)
    {
        return $query->orderBy('id', 'DESC');
    }

    public function getRole() {
      return role::find($this->role_id) ;
//        return $this->hasOne('\App\role' , 'role_id');
    }
    public function role() {
        return $this->belongsTo(role::class , 'role_id');
    }

    public function roleSection() {
       return $this->belongsTo(role::class  , 'role_id')->with('section');
    }


    public function titleChecklists() {
        return $this->belongsToMany(TitleChecklist::class , 'title_checklist_user','user_id' , 'titleChecklist_id');
    }
    public  function userMetas(){

        return $this->hasMany(UserMeta::class);
    }
    public function avatar() {
      $avatar = $this->userMetas()->where('key' , 'avatar')->first('value');
      return $avatar ? $avatar->value : null;
    }
    public function signature() {
        $signature =  $this->userMetas()->where('key' , 'signature')->first('value');
        return $signature ? $signature->value : null;
    }

    public function unread() {
       return $this->unreadNotifications()->count();
    }

    public function todoLists(){
        return $this->hasMany(ToDoList::class);
    }

    public function taskTodoList(){
        return $this->hasMany(ToDoList::class)->where('todoable_type', ['App\\Models\\Task']);
    }
    public function todoListsCount(){
        return $this->hasMany(ToDoList::class)
            ->whereHasMorph('todoable', [ChecklistContract::class , Task::class , TrainingSession::class])
            ->where('status' , '!=' , 'done')->count();
    }

    public function trainingSessionCount(){
        return TrainingSession::where('status' , 'set_time')->get()->count();
    }

    public function NotSetTrainingSessionForContract()
    {
        return ChecklistContract::whereHas('checklistProcess', function ($query) {
            $query->where('section_id', 4)->where('status' , 4 )->where('signed', 1) ;
        })->whereHas('contract', function ($query) {
            $query->where('created_at', '>' , '2022-10-04 09:30:46');
        })->doesntHave('trainingSessions')->whereIn('checklist_id' , [5,7])->count();
    }

    public function trainingSessions() {
        return $this->hasMany(TrainingSession::class);
    }
    public function requests(){
        return $this->hasMany(StaffRequest::class, 'user_id');
    }
    public function managerRequests(){
        return $this->hasMany(StaffRequest::class , 'manager_id');
    }
    public function managerRequestsCount(){
        return $this->hasMany(StaffRequest::class , 'manager_id')->whereNull('has_confirmed')->count();
    }
    public function staffRequests(){
        return $this->hasMany(StaffRequest::class)->whereNotNull('has_confirmed')->count();
    }
    public function customerholds(){
        return $this->hasMany(CustomerHold::class);
    }
    public function taskOwner() {
        return $this->hasMany(Task::class);
    }
    public function taskSubTasks() {
        return $this->hasMany(SubTask::class, 'assigned', 'id' )->where('subtaskable_type', 'App\Models\Task');
    }
    public function subTaskError() {
        return $this->hasMany(SubTask::class, 'assigned', 'id' )->where('subtaskable_type', 'App\Models\Task')->where('status' , 'error');
    }
    public function subTaskOffer() {
        return $this->hasMany(SubTask::class, 'assigned', 'id' )->where('subtaskable_type', 'App\Models\Task')->where('status' , 'offer');
    }
    public function subTaskPeriodic() {
        return $this->hasMany(SubTask::class, 'assigned', 'id' )->where('subtaskable_type', 'App\Models\Task')->where('status' , 'periodical');
    }
}
