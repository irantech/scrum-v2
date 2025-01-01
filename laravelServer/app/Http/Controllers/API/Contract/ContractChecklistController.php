<?php

namespace App\Http\Controllers\API\Contract;

use App\Http\Controllers\API\Globals\NotificationHandler;
use App\Http\Controllers\API\ToDoList\ToDoListController;
use App\Http\Controllers\API\User\UserController;
use App\Http\Requests\assignTitleChecklistRequest;
use App\Http\Resources\API\Contract\sumProceessCollection;
use App\Http\Resources\API\ContractChecklist\signChecklistsCollection;
use App\Models\Checklist;
use App\Http\Resources\API\Contract\checklistProcessCollection;
use App\Http\Resources\API\Contract\userChecklistCollection;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChecklistProcessRequest;
use App\Models\ChecklistContract;
use App\Models\ChecklistProcess;
use App\Models\Contract;
use App\Models\role;
use App\Models\Section;
use App\Models\TitleChecklist;
use App\Models\titleChecklistUser;
use App\Models\ToDoList;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\API\Contract\checklistProcess as checklistProcessResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Http\Resources\API\Globals\Section as SectionResource;
use App\Http\Resources\API\ContractChecklist\Checklist as ResourceChecklist;
use App\Http\Resources\API\ContractChecklist\Contract as ResourceContract;

class ContractChecklistController extends Controller
{

    public function __construct()
    {

        $this->middleware('scopes:assign-checklist-contract')->only('assignChecklist');
        $this->middleware('scope:assign-title-checklist-office,assign-title-checklist-support,assign-title-checklist-graphic,assign-title-checklist-programmer,assign-title-checklist-design,assign-title-checklist-sales')->only('assign');
        $this->middleware('scope:reverse-to-office,reverse-to-programmer,reverse-to-graphic,reverse-to-sale,reverse-to-support')->only('reverse');
        $this->middleware('scopes:show-contract-title_checklist')->only('getContractChecklists');
        $this->middleware('scopes:support-approve-design')->only('supportApproveDesign');

    }

    /**
     * get checklist contract
     * @param ChecklistContract $checklistContract
     * @return \Illuminate\Http\JsonResponse
     *
     */


    public function checklistContract(ChecklistContract $checklistContract)
    {
        $data = [
            'id' => $checklistContract->id,
            'contract' => new ResourceContract($checklistContract->contract),
            'checklist' => new ResourceChecklist($checklistContract->checklist)
        ];
        return response()->json(['message' => __('scrum.api.insert_success'), 'data' => $data]);
    }

    /**
     * assign checklist to a contract
     * @param Contract $contract
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function assignChecklist(Contract $contract, Request $request)
    {

        $request->validate([
            'checklists' => 'required',
        ]);
        $contract->checklists()->sync($request->checklists);
        $data = [
            'checklists' => $contract->checklists,
            'contract_checklist' => $contract->checklistContract
        ];

        activity('assign-contract-checklist')
            ->performedOn($contract)
            ->withProperties(
                ['contract' => $contract->title, 'checklists' => $contract->checklists])
            ->log('checklist_assigned');
        return response()->json(['message' => __('scrum.api.insert_success'), 'data' => $data]);
    }


    /**
     * display lists of contract checklists
     *
     * @param Contract $contract
     * @param Checklist $checklist
     * @return \Illuminate\Http\JsonResponse
     */
    public function getContractChecklists(ChecklistContract $checklistContract)
    {

        $titleChecklists = $checklistContract->titleChecklistUser;
        $data = new userChecklistCollection($titleChecklists);

        $grouped = $data->groupBy(function ($item, $key) {
            return $item['titleChecklist_id'];
        });
//        $grouped = $grouped->values();

        $titleChecklistSection = $checklistContract->titleChecklistUser()->groupBy('section_id')->get();
        $section = new userChecklistCollection($titleChecklistSection);

        $signedSections = $checklistContract->checklistProcess()->where('signed', 1)->get();
        $signedSections = new signChecklistsCollection($signedSections);

        return response()->json(['message' => __('scrum.api.get_success'),
            'titleChecklists' => $grouped, 'section' => $section, 'signed' => $signedSections], Response::HTTP_OK);
    }


    // calculate time has used in each section
    public function sumDuration(ChecklistContract $checklistContract)
    {
        $checklist_process = [];

        $checklist_process = $checklistContract->checklistProcess()
            ->groupBy('section_id')
            ->where('duration', '!=', null)
            ->selectRaw('* , SEC_TO_TIME(sum(TIME_TO_SEC(duration))) as sum')
            ->get();


        return response()->json(['message' => __('scrum.api.get_success'), 'data' => new sumProceessCollection($checklist_process)], Response::HTTP_OK);

    }


    /**
     * assign title checklist to a user for a contract
     *
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function assign(ChecklistContract $checklistContract, assignTitleChecklistRequest $request)
    {

        DB::beginTransaction();
        try {
            $section = Section::findOrFail($request->section);
            if ($this->canManagerAssign($section)) {
                $user = User::find($request->user);
                foreach ($request->titleChecklist as $title) {
                    $titleExist = titleChecklistUser::where('titleChecklist_id', $title)
                        ->where('checklist_contract_id', $checklistContract->id)
                        ->where('section_id', $section->id)->first();
                    // check if this title checklist has assigned to a user
                    if ($titleExist) {
                        $titleExist->user_id = $request->user;
                        $titleExist->save();
                    } // if not create new one
                    else {
                        $titleChecklist = TitleChecklist::findOrFail($title);
                        $titleChecklist->users()->attach($request->user, [
                            'checklist_contract_id' => $checklistContract->id,
                            'section_id' => $section->id]);
                    }
                }
                if ($section->order == 1) {
//                    $manager = $this->findManager($section->order) ;
                    $this->assignTask($checklistContract, $user);
                } else {
                    $user_controller = new UserController();
                    $sectionManager = $user_controller->findManager($section->order);
                    $last_checklist_todolist = $checklistContract->todoList()->where('user_id', $sectionManager->id)->where('status', 'in_progress')->first();

                    if ($last_checklist_todolist) {
                        $this->updateTask($checklistContract, 'done');
                        $this->assignTask($checklistContract, $user);
                    }
                }
                // activity log for assigning user to title checklist


                $titleChecklists = $checklistContract->titleChecklistUser()->where('section_id', $section->id)->get('titleChecklist_id');
                $titleChecklists = TitleChecklist::select('id', 'title')->find($titleChecklists);
                activity('title_checklist_assign_to_user')
                    ->performedOn($checklistContract)
                    ->withProperties(
                        ['checklist' => ['id' => $checklistContract->checklist->id, 'title' => $checklistContract->checklist->title],
                            'contract' => ['id' => $checklistContract->contract->id, 'title' => $checklistContract->contract->title],
                            'section' => ['id' => $section->id, 'title' => $section->title],
                            'user' => ['id' => $user->id, 'name' => $user->name],
                            'titleChecklist' => $titleChecklists])
                    ->log('user_assigned_title_checklist');

                //send notification who has assigned to the checklist
                $this->handleSMS('checklist_process', $checklistContract, 2, $user, $section);

            } else
                return response()->json(['message' => __('scrum.api.get_forbidden_error')], Response::HTTP_FORBIDDEN);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        DB::commit();
        return response()->json(['message' => __('scrum.api.assign_checklist_user')], Response::HTTP_OK);
    }

    /**
     * check and uncheck the title checklist in a contract ( done or not done)
     *
     * @param $contract
     * @param $titleChecklist
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     */

    public function changeStatus(ChecklistContract $checklistContract, TitleChecklist $titleChecklist, Request $request)
    {
        if (!$this->isTodoListTaskStarted($checklistContract, $request->section))
            return response()->json(['message' => 'شما ابتدا باید دکمه شروع را در لیست کارهای خود بزنید.'], Response::HTTP_FORBIDDEN);
        $request->validate([
            'section' => 'required|exists:App\Models\Section,id'
        ]);
        $userTitleChecklist = $checklistContract->titleChecklistUser()
            ->where('titleChecklist_id', $titleChecklist->id)
            ->where('section_id', $request->section)->first();
        $section = Section::findOrFail($request->section);

        if ($this->canChangeStatus($section, $userTitleChecklist->user_id)) {
            $userTitleChecklist->status == 0 ? $userTitleChecklist->status = 1 : $userTitleChecklist->status = 0;
            $userTitleChecklist->save();

            activity('change_title_checklist_status')
                ->performedOn($checklistContract)
                ->withProperties(
                    ['contract' => ['id' => $checklistContract->contract->id, 'title' => $checklistContract->contract->title],
                        'titleChecklist' => ['id' => $titleChecklist->id, 'title' => $titleChecklist->title],
                        'status' => $userTitleChecklist->status])
                ->log('change_title_checklist_status');

            return response()->json(['message' => __('scrum.api.change_titleChecklist_status')], Response::HTTP_OK);
        } else {
            return response()->json(['message' => __('scrum.api.get_forbidden_error')], Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * store a checklist process when one process ends (staff approve , manager approve , reverse)
     *
     * @param $contract
     * @param $checklist
     * @param ChecklistProcessRequest $request
     * @return \Illuminate\Http\JsonResponse
     *
     */

    public function setContractChecklistProcess(ChecklistContract $checklistContract, ChecklistProcessRequest $request)
    {
        switch ($request->type) {
            case('confirm') :
                return $this->approve($request, $checklistContract);
            case('managerConfirm') :
                return $this->managerApprove($request, $checklistContract);
            case('supportApprove'):
                return $this->supportApproveDesign($request, $checklistContract);
            case('sign') :
                return $this->managerSignChecklist($request, $checklistContract, $request['has_sms']);
            case('staffSign') :
                return $this->staffSignChecklist($request, $checklistContract);
            case('supportSign') :
                return $this->supportSignChecklist($request, $checklistContract, $request['has_sms']);
            default:
                return $this->reverseCheck($request, $checklistContract);
        }
    }

    /**
     * @param $request
     * @param $checklistContract
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function approve($request, $checklistContract)
    {
        if (!$this->isTodoListTaskStarted($checklistContract, $request->section))
            return response()->json(['message' => 'شما ابتدا باید دکمه شروع را در لیست کارهای خود بزنید.'], Response::HTTP_FORBIDDEN);;
        //find user who is assigned for this contract checklist
        $titleChecklist = $checklistContract->titleChecklistUser()
            ->where('section_id', $request->section)->first();
        $user = $titleChecklist ? $titleChecklist->user_id : null;
        $section = Section::find($request->section);
        if ($this->canChangeStatus($section, $user)) {
            $titleCount = $checklistContract->titleChecklistUser()
                ->where('section_id', $request->section)->count();
            $checkedCount = $checklistContract->titleChecklistUser()
                ->where('section_id', $request->section)->where('status', 1)->count();
            if ($titleCount == $checkedCount) {
                $this->createProcess($checklistContract, $request, 1);
                //save activity log
                $this->saveLog($checklistContract, $section, $request->type);
                $next = $checklistContract->checklistProcess()->where('section_id', $request->section)
                    ->where('signed', 1)->where('status', 5)->first();
                if ($next) {
                    $this->updateTask($checklistContract, 'done');

                    if ($section->order == 2) {
                        $section_next = Section::where('id', 4)->first();

                        $title_checklist = $checklistContract->titleChecklistUser()
                            ->where('section_id', $section_next->id)->first();
                        if ($title_checklist) {
                            $user = User::where('id', $title_checklist->user_id)->first();

                            $this->assignTask($checklistContract, $user);
                        }
                    } else {
                        $user_controller = new UserController();
                        $manager = $user_controller->findManager($section->order);
                        $this->assignTask($checklistContract, $manager);
                    }

                }


                $type = $next ? 'managerConfirm' : '';
                return response()->json(['message' => __('scrum.api.insert_success'), 'data' => $type], Response::HTTP_OK);
            } else {
                return response()->json(['message' => __('همه موارد باید چک شوند.')], Response::HTTP_FORBIDDEN);
            }
        } else {
            return response()->json(['message' => __('scrum.api.get_forbidden_error')], Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * manager approves when a section done completely
     *
     * @param $request
     * @param $checklistContract
     * @return \Illuminate\Http\JsonResponse
     */
    public function managerApprove($request, $checklistContract)
    {

        $section = $request->section == 7 ? 4 : $request->section;

        if (!$this->isManagerStartTodoListTask($checklistContract, $section))
            return response()->json(['message' => 'شما ابتدا باید دکمه شروع را در لیست کارهای خود بزنید.'], Response::HTTP_FORBIDDEN);;
        $section = Section::find($request->section);

        if ($this->canManagerApprove($section)) {

            $nextSection = null;
            $sectionList = json_decode($checklistContract->checklist->sections, true);
            $lastActiveSectionProcess = $checklistContract->checklistProcess()->where('section_id', $request->section)->whereNull('signed')->orderBy('created_at', 'desc')->first();

            switch ($section->order) {
                case 1 :
                    $titleCount = $checklistContract->titleChecklistUser()
                        ->where('section_id', $request->section)->count();

                    $checkedCount = $checklistContract->titleChecklistUser()
                        ->where('section_id', $request->section)->where('status', 1)->count();
                    if ($titleCount != $checkedCount) {
                        return response()->json(['message' => 'ابتدا همه موارد را چک کنید'], Response::HTTP_FORBIDDEN);
                    }
                    $checklistProcess = $this->createProcess($checklistContract, $request, 2);

                    $ifSigned = $checklistContract->checklistProcess()
                        ->where('section_id', $section->id)->where('signed', 1)->where('status', 4)->first();
                    if ($ifSigned) {
                        if ($sectionList[count($sectionList) - 1]['id'] != $section->id) {
                            $current = collect($sectionList)->where('id', $checklistProcess->section_id)->getIterator();
                            if ($current->key() + 1 < collect($sectionList)->count()) {
                                $next = $sectionList[$current->key() + 1];
                                $nextSection = Section::findOrFail($next['id']);
                                $nextSection = new SectionResource($nextSection);
                            }
                        }
                        if ($nextSection) {
                            $this->sendNotification($checklistContract, $nextSection);
                        }
                    }
//                    else {
//                        $this->updateTask($checklistContract , 'done');
//                        $manager = $this->findManager($section->order) ;
//                        $this->assignTask($checklistContract , $manager);
//                    }

                    return response()->json(['message' => __('scrum.api.insert_success'), 'data' => $nextSection], Response::HTTP_OK);
                case 2 :
                    $ifSupportSign = $checklistContract->checklistProcess()->where('section_id', $request->section)->where('status', 6)->orderBy('created_at', 'desc')->first();
                    if (!$ifSupportSign)
                        return response()->json(['message' => 'ابتدا تایید اولیه طرح باید امضا بخوره'], Response::HTTP_FORBIDDEN);
                    if ($lastActiveSectionProcess->status == 3) {
                        $checklistProcess = $this->createProcess($checklistContract, $request, 2);
                        $ifSigned = $checklistContract->checklistProcess()
                            ->where('section_id', $section->id)->where('signed', 1)->where('status', 4)->first();
                        if ($ifSigned) {
                            if ($sectionList[count($sectionList) - 1]['id'] != $section->id) {
                                $current = collect($sectionList)->where('id', $checklistProcess->section_id)->getIterator();
                                if ($current->key() + 1 < collect($sectionList)->count()) {
                                    $next = $sectionList[$current->key() + 1];
                                    $nextSection = Section::findOrFail($next['id']);
                                    $nextSection = new SectionResource($nextSection);
                                }
                            }
                            if ($nextSection) {
                                $this->sendNotification($checklistContract, $nextSection);
                            }
                        }
                        return response()->json(['message' => __('scrum.api.insert_success'), 'data' => $nextSection], Response::HTTP_OK);
                    } else {
                        return response()->json(['message' => 'ابتدا باید کارمند این بخش را تایید کند'], Response::HTTP_FORBIDDEN);
                    }
                case 3:
                case 4:
                case 5:
                case 6:
                    if ($lastActiveSectionProcess->status == 1) {
                        $ifStaffSigned = $checklistContract->checklistProcess()
                            ->where('section_id', $section->id)->where('signed', 1)->where('status', 5)->first();
                        if (!$ifStaffSigned) {
                            return response()->json(['message' => 'ابتدا باید کارمند این بخش را امضا کند'], Response::HTTP_FORBIDDEN);
                        }
                        $checklistProcess = $this->createProcess($checklistContract, $request, 2);
                        $ifSigned = $checklistContract->checklistProcess()
                            ->where('section_id', $section->id)->where('signed', 1)->where('status', 4)->first();
                        if ($ifSigned) {
                            if ($sectionList[count($sectionList) - 1]['id'] !== $section->id) {
                                $current = collect($sectionList)->where('id', $checklistProcess->section_id)->getIterator();
                                if ($current->key() + 1 < collect($sectionList)->count()) {
                                    $next = $sectionList[$current->key() + 1];
                                    $nextSection = Section::findOrFail($next['id']);
                                    $nextSection = new SectionResource($nextSection);
                                }
                            }
                            if ($nextSection) {
                                $this->sendNotification($checklistContract, $nextSection);
                            }
                        }
                        return response()->json(['message' => __('scrum.api.insert_success'), 'data' => $nextSection], Response::HTTP_OK);
                    } else {
                        return response()->json(['message' => 'ابتدا باید کارمند این بخش را تایید کند'], Response::HTTP_FORBIDDEN);
                    }
                    break;
//                case 6 :
//                    $titleCount = $checklistContract->titleChecklistUser()
//                        ->where('section_id' , $request->section)->count();
//
//                    $checkedCount =  $checklistContract->titleChecklistUser()
//                        ->where('section_id' , $request->section)->where('status' , 1)->count();
//                    if($titleCount != $checkedCount) {
//                        return response()->json(['message' => 'ابتدا همه موارد را چک کنید'], Response::HTTP_FORBIDDEN);
//                    }
//                    $checklistProcess = $this->createProcess($checklistContract , $request , 2);

//                    if($sectionList[count($sectionList) - 1]['id'] !== $section->id) {
//                        $current = collect($sectionList)->where('id', $checklistProcess->section_id)->getIterator();
//                        if ($current->key() + 1 < collect($sectionList)->count()) {
//                            $next = $sectionList[$current->key() + 1];
//                            $nextSection = Section::findOrFail($next['id']);
//                            $nextSection = new SectionResource($nextSection);
//                        }
//                    }
//                    if($nextSection) {
//                        $this->sendNotification($checklistContract , $nextSection );
//                    }
                    return response()->json(['message' => __('scrum.api.insert_success'), 'data' => $nextSection], Response::HTTP_OK);
            }
        } else {
            return response()->json(['message' => __('scrum.api.get_forbidden_error')], Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * @param $request
     * @param $checklistContract
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function reverse($request, $checklistContract)
    {
        $section = Section::find($request->activeSection);

        //find user who is assigned for this contract checklist

        if ($this->canReverse($section)) {
            $checklistProcess = $this->createProcess($checklistContract, $request, 0);
            $nextSection = Section::findOrFail($request->section);
            //send notification after reversing one section
            $this->sendNotification($checklistContract, $nextSection, $checklistProcess->status);
            //save activity log
            $this->saveLog($checklistContract, $section, 'backward');
        } else {
            return false;
        }
        return [
            'section' => new SectionResource($nextSection),
            'process' => new checklistProcessResource($checklistProcess)
        ];
    }

    private function reverseCheck($request, $checklistContract)
    {

        if ($request->section == 0) {
            $sectionList = ['3', '2'];
            foreach ($sectionList as $key => $section) {
                $request->section = $section;
                $result = $this->reverse($request, $checklistContract);
                if (!$result) {
                    return response()->json(['message' => __('scrum.api.get_forbidden_error')], Response::HTTP_FORBIDDEN);
                }
                $result_process[$key] = $result['process'];
                $result_section[$key] = $result['section'];
            }
            return response()->json(['message' => __('scrum.api.insert_success'), 'data' => $result_section, 'process' => $result_process], Response::HTTP_OK);
        } else {
            $result = $this->reverse($request, $checklistContract);
            if (!$result) {
                return response()->json(['message' => __('scrum.api.get_forbidden_error')], Response::HTTP_FORBIDDEN);
            } else {
                return response()->json(['message' => __('scrum.api.insert_success'), 'data' => $result['section'], 'process' => $result['process']], Response::HTTP_OK);

            }
        }
    }

    /**
     * support approves design section
     * @param $request
     * @param $checklistContract
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function supportApproveDesign($request, $checklistContract)
    {
        if (!$this->isSupportStartTodoListTask($checklistContract))
            return response()->json(['message' => 'شما ابتدا باید دکمه شروع را در لیست کارهای خود بزنید.'], Response::HTTP_FORBIDDEN);;
        if (Auth::user()->tokenCan('support-approve-design')) {
            $section = Section::find($request->section);
            $lastProcess = $checklistContract->checklistProcess()->where('section_id', $request->section)->whereNull('signed')->orderBy('created_at', 'desc')->first();
            $ifSigned = $checklistContract->checklistProcess()->where('section_id', $request->section)->where('signed', 1)->orderBy('created_at', 'desc')->first();
            if (!$ifSigned) {
                return response()->json(['message' => 'ابتدا کارمند امضا کند'], Response::HTTP_FORBIDDEN);
            }
            if ($lastProcess->status == 1) {
                $this->createProcess($checklistContract, $request, 3);

                //test
                $signed = $checklistContract->checklistProcess()->where('section_id', $section->id)
                    ->where('signed', 1)->where('status', 6)->first();
                if ($signed) {
                    $section = Section::where('order', 5)->first();
                    $this->updateTask($checklistContract, 'done');
                    $user_controller = new UserController();
                    $manager = $user_controller->findManager($section->order);
                    $this->assignTask($checklistContract, $manager);
                }


                //save activity log
                $this->saveLog($checklistContract, $section, $request->type);
                return response()->json(['message' => __('scrum.api.insert_success')], Response::HTTP_OK);
            }
        }
        return response()->json(['message' => __('scrum.api.get_forbidden_error')], Response::HTTP_FORBIDDEN);
    }

    /**
     * managers sign checklist contract and send sms to a customer
     *
     * @param Request $request
     * @param ChecklistContract $checklistContract
     * @return \Illuminate\Http\JsonResponse
     *
     */

    public function managerSignChecklist($request, $checklistContract, $has_sms = true)
    {
//        $section = $request->section == 7 ? 4 : $request->section ;
//        if ( !$this->isManagerStartTodoListTask($checklistContract , $section) )
//            return response()->json(['message' => 'شما ابتدا باید دکمه شروع را در لیست کارهای خود بزنید.'], Response::HTTP_FORBIDDEN);;
        $checkIfSigned = $checklistContract->checklistProcess()->where('section_id', $request->section)->where('signed', 1)->where('status', 4)->first();

        if (!$checkIfSigned) {
            $nextSection = null;
            $section = Section::find($request->section);
            if ($this->canManagerSign($section->order)) {
                $ifStaffSign = $checklistContract->checklistProcess()->where('section_id', $section->id)->where('signed', 1)->where('status', 5)->orderBy('created_at', 'DESC')->first();
                if ($section->order == 1 || $ifStaffSign) {
                    $ifApproved = $checklistContract->checklistProcess()->where('section_id', $section->id)->where('status', 2)->orderBy('created_at', 'DESC')->first();

                    if ($ifApproved) {
                        if ($section->order === 2) {
                            $ifSupportSign = $checklistContract->checklistProcess()->where('section_id', $section->id)->where('signed', 1)->where('status', 6)->orderBy('created_at', 'DESC')->first();
                            if (!$ifSupportSign)
                                return response()->json(['message' => __('ابتدا تایید اولیه طرح زده شود.')], Response::HTTP_FORBIDDEN);
                        }
                        $sectionList = json_decode($checklistContract->checklist->sections, true);

                        $checklistProcess = $this->createProcess($checklistContract, $request, 4, 1);

                        if ($sectionList[count($sectionList) - 1]['id'] !== $section->id) {
                            $current = collect($sectionList)->where('id', $checklistProcess->section_id)->getIterator();
                            if ($current->key() + 1 < collect($sectionList)->count()) {
                                $next = $sectionList[$current->key() + 1];
                                $nextSection = Section::findOrFail($next['id']);
                                $nextSection = new SectionResource($nextSection);
                            }
                        }

                        if ($nextSection)
                            $this->sendNotification($checklistContract, $nextSection);
                        else {
                            $this->updateTask($checklistContract, 'done');
                        }
                        //send notification after approving manager
                        if ($has_sms && $section->order !== 6 && ($checklistContract->checklist->id == 5 || $checklistContract->checklist->id == 8 || $checklistContract->checklist->id == 15 || $checklistContract->checklist->id == 16) && $section->order != 3) {
                            $this->handleSMS('customer', $checklistContract, $section->order);
                        }
                        //save activity log
                        //            $this->saveLog($contracts ,$section ,$checklists ,$request->type);

                        return response()->json(['message' => __('scrum.api.insert_success'), 'data' => $nextSection], Response::HTTP_OK);
                    } else {
                        return response()->json(['message' => __('ابتدا تایید کنید.')], Response::HTTP_FORBIDDEN);
                    }
                } else {
                    return response()->json(['message' => __('ابتدا کارمند باید تایید کند.')], Response::HTTP_FORBIDDEN);
                }

            } else {
                return response()->json(['message' => __('scrum.api.get_forbidden_error')], Response::HTTP_FORBIDDEN);
            }
        } else {
            return response()->json(['message' => __('قبلا امضا شده')], Response::HTTP_FORBIDDEN);
        }
    }

    public function staffSignChecklist($request, $checklistContract)
    {
        $checkIfSigned = $checklistContract->checklistProcess()->where('section_id', $request->section)->where('signed', 1)->where('status', 5)->first();
        if (!$checkIfSigned) {
            $section = Section::find($request->section);
            $titleUser = $checklistContract->titleChecklistUser()->where('section_id', $section->id)->first();

            $user = $titleUser ? $titleUser->user_id : null;

            if ($this->canStaffSign($section->order, $user)) {
                $ifApproved = $checklistContract->checklistProcess()->where('section_id', $section->id)->where('status', 1)->orderBy('created_at', 'DESC')->first();
                if ($ifApproved) {

                    $checklistProcess = new ChecklistProcess();
                    $checklistProcess->user_id = Auth::user()->id;
                    $checklistProcess->section_id = $request->section;
                    $checklistProcess->checklist_contract_id = $checklistContract->id;
                    $checklistProcess->description = $request->description;
                    $checklistProcess->status = 5;
                    $checklistProcess->signed = 1;
                    $checklistProcess->save();
                    $this->updateTask($checklistContract, 'done');
                    if ($section->order == 2) {
                        $support_section = Section::where('order', 5)->first();
                        $titleChecklist = $checklistContract->titleChecklistUser()->where('section_id', $support_section->id)->first();

                        if ($titleChecklist) {
                            $support_user = User::findOrFail($titleChecklist->user_id);
                            $this->assignTask($checklistContract, $support_user);
                        } else {
                            $user_controller = new UserController();
                            $support_manager = $user_controller->findManager(5);
                            $this->assignTask($checklistContract, $support_manager);
                        }
                    } else {
                        $user_controller = new UserController();
                        $manager = $user_controller->findManager($section->order);
                        $this->assignTask($checklistContract, $manager);
                    }

                    //send notification after approving manager
//            if($request->section !== 5) {
//                $this->sendNotification($contracts ,$checklists , $request->activeSection +  1 );
//            }
                    //save activity log
//            $this->saveLog($contracts ,$section ,$checklists ,$request->type);

                    return response()->json(['message' => __('scrum.api.insert_success')], Response::HTTP_OK);

                } else {
                    return response()->json(['message' => __('ابتدا تایید کنید.')], Response::HTTP_FORBIDDEN);
                }

            } else {
                return response()->json(['message' => __('scrum.api.get_forbidden_error')], Response::HTTP_FORBIDDEN);
            }
        } else {
            return response()->json(['message' => __('قبلا امضا شده')], Response::HTTP_FORBIDDEN);
        }
    }

    public function supportSignChecklist($request, $checklistContract, $has_sms = true)
    {
        $checkIfSigned = $checklistContract->checklistProcess()->where('section_id', $request->section)->where('signed', 1)->where('status', 6)->first();
        if (!$checkIfSigned) {
            if (Auth::user()->tokenCan('support-approve-design')) {
                $ifApproved = $checklistContract->checklistProcess()->where('section_id', $request->section)->where('status', 3)->orderBy('created_at', 'DESC')->first();
                $ifStaffSigned = $checklistContract->checklistProcess()->where('section_id', $request->section)->where('signed', 1)->where('status', 5)->orderBy('created_at', 'DESC')->first();
                if ($ifApproved && $ifStaffSigned) {
                    $checklistProcess = $this->createProcess($checklistContract, $request, 6, 1);

                    //send notification after approving manager
//            if($request->section !== 5) {
//                $this->sendNotification($contracts ,$checklists , $request->activeSection +  1 );
//            }
                    //save activity log
//            $this->saveLog($contracts ,$section ,$checklists ,$request->type);

                    $section = Section::findOrFail($request->section);
                    $ticket_number = $checklistContract->checklistProcess()->where('ticket_number', '!=', '')->orderBy('created_at', 'DESC')->first();
                    //tests


                    $this->updateTask($checklistContract, 'done');
                    $user_controller = new UserController();
                    $manager = $user_controller->findManager(5);
                    $this->assignTask($checklistContract, $manager);


                    // send customer sms
                    if ($has_sms && ($checklistContract->checklist->id == 5 || $checklistContract->checklist->id == 8 || $checklistContract->checklist->id == 15 || $checklistContract->checklist->id == 16) && $section->order != 3 && $section->order != 6) {

                        $this->handleSMS('customer', $checklistContract, $section->order, null, null, $ticket_number->ticket_number);
                    }

                    return response()->json(['message' => __('scrum.api.insert_success')], Response::HTTP_OK);

                } else {
                    return response()->json(['message' => __('ابتدا تایید کنید.')], Response::HTTP_FORBIDDEN);
                }

            } else {
                return response()->json(['message' => __('scrum.api.get_forbidden_error')], Response::HTTP_FORBIDDEN);
            }
        } else {
            return response()->json(['message' => __('قبلا امضا شده')], Response::HTTP_FORBIDDEN);
        }
    }

    public function createProcess($checklistContract, $request, $status, $sign = null)
    {
        $checklistProcess = new ChecklistProcess();
        $checklistProcess->user_id = Auth::user()->id;
        $checklistProcess->section_id = $request->section;
        $checklistProcess->checklist_contract_id = $checklistContract->id;
        $checklistProcess->description = $request->description;
        $checklistProcess->duration = $request->duration;
        $checklistProcess->status = $status;
        $checklistProcess->signed = $sign ? 1 : null;
        $checklistProcess->ticket_number = $request->ticket_number;
        $checklistProcess->save();
        return $checklistProcess;
    }

    public function assignTask($checklistContract, $user, $status = 1)
    {
        $checklist_title = $checklistContract->checklist->title;
        $customer_name = $checklistContract->contract->customer->name;
        $task_title = $customer_name . ' ( ' . $checklist_title . ' ) ';
        if ($status == 0) {
            $task_title = $task_title . ' برگشت خورده';
        }
        $task_request = new Request();
        $task_request['task_type'] = get_class($checklistContract);
        $task_request['task_id'] = $checklistContract->id;
        $task_request['title'] = $task_title;
        $task_request['user_id'] = $user->id;
        $task_request['task_status'] = $status;
        $toDoList = new ToDoListController();
        $toDoList->assignTaskToUser($task_request);

    }

    public function updateTask($checklistContract, $status)
    {
        if ($checklistContract->lastTodoList->count() > 0) {
            $task_request = new Request();
            $task_request['task_type'] = get_class($checklistContract);
            $task_request['task_id'] = $checklistContract->id;
            $task_request['status'] = $status;
            $todo_id = $checklistContract->lastTodoList[0]->id;
            $toDoList = new ToDoListController();
            $toDoList->updateTaskStatus($task_request, $todo_id);
        }

    }

    /**
     * display last process of checklist process
     *
     * @param $contract
     * @param $checklist
     * @return \Illuminate\Http\JsonResponse
     *
     */

    public function getLastChecklistProcess(ChecklistContract $checklistContract)
    {

        $data = null;
        $lastProcess = $checklistContract->checklistProcess()->whereNull('signed')->orderBy('created_at', 'desc')->first();
        $sectionList = json_decode($checklistContract->checklist->sections, true);

        $activeSection = 0;
        if ($lastProcess) {

            $current = collect($sectionList)->where('id', $lastProcess->section_id)->getIterator();

            $ifManagerSigned = $checklistContract->checklistProcess()->where('section_id', $lastProcess->section_id)->where('signed', 1)->where('status', 4)->first();

            if (($lastProcess->section_id === 1 && $lastProcess->status == 2) || ($lastProcess->status == 2 && $ifManagerSigned)) {
                if ($current->key() + 1 < count($sectionList))
                    $activeSection = $current->key() + 1;
                else
                    $activeSection = $current->key();
            } else {
                $activeSection = $current->key();
            }
            $data = new checklistProcessResource($lastProcess);
        }
        $section = Section::findOrFail($sectionList[$activeSection]['id']);

        $section = new SectionResource($section);
        return response()->json(['message' => __('scrum.api.get_success'), 'data' => $data, 'nextSection' => $section], Response::HTTP_OK);
    }

    /**
     * display all process of a contract checklist
     *
     * @param $contract
     * @param $checklist
     * @return \Illuminate\Http\JsonResponse
     *
     */

    public function getChecklistProcess($contract, $checklist)
    {
        $processList = ChecklistProcess::where('contract_id', $contract)
            ->where('checklist_id', $checklist)->orderBy('created_at', 'asc')->get();
        $data = new checklistProcessCollection($processList);
        return response()->json(['message' => __('scrum.api.get_success'), 'data' => $data], Response::HTTP_OK);
    }

    /**
     * display the last reversed section checklist contract
     *
     * @param $checklistContract
     * @return \Illuminate\Http\JsonResponse
     *
     */

    public function getChecklistReverse(ChecklistContract $checklistContract)
    {
        $ReverseProcess = $checklistContract->checklistProcess()->where('status', 0)->orderBy('created_at', 'desc')->get();
        $data = new checklistProcessCollection($ReverseProcess);
        return response()->json(['message' => __('scrum.api.get_success'), 'data' => $data], Response::HTTP_OK);
    }


    public function sendNotification($checklistContract, $section, $status = 1)
    {
        $titleChecklist = $checklistContract->titleChecklistUser()->where('section_id', $section->id)->first();
        if ($titleChecklist) {
            $user = User::findOrFail($titleChecklist->user_id);
        } else {
            $user_controller = new UserController();
            $user = $user_controller->findManager($section->order);
        }

        $this->handleSMS('checklist_process', $checklistContract, $status, $user, $section);

        $this->updateTask($checklistContract, 'done');
        $has_in_progress_todo_list = $checklistContract->todoList()->where('user_id', $user->id)->where('status', 'in_progress')->first();
        if (!$has_in_progress_todo_list) {
            $this->assignTask($checklistContract, $user, $status);
        }


    }

    public function saveLog($checklistContract, $section, $type)
    {
        activity('checklist-process')
            ->performedOn($checklistContract)
            ->withProperties(
                ['contract' => ['id' => $checklistContract->contract->id, 'title' => $checklistContract->contract->title],
                    'section' => ['id' => $section->id, 'title' => $section->title],
                    'checklist' => ['id' => $checklistContract->checklist->id, 'title' => $checklistContract->checklist->title],
                    'type' => $type])
            ->log('contract_checklist_process');
    }


    // check permissions for checklists actions

    /**
     * check if employee can check titleChecklists in a contract checklist
     *
     * @param $section
     * @param $user
     * @return bool
     *
     */
    public function canChangeStatus($section, $user): bool
    {

        if (Auth::user()->getRole()->title == 'admin')
            return true;
        else
            switch ($section->order) {
                case(1) :
                    return (Auth::user()->getRole()->title == 'administrative manager' || (Auth::user()->id == $user && Auth::user()->tokenCan('staff-approving-office')));
                case(4):
                    return (Auth::user()->getRole()->title == 'graphic manager' || (Auth::user()->id == $user && Auth::user()->tokenCan('staff-approving-graphic')));
                case(3) :
                    return (Auth::user()->getRole()->title == 'technical Manager' || (Auth::user()->id == $user && Auth::user()->tokenCan('staff-approving-programmer')));
                case(5):
                    return (Auth::user()->getRole()->title == 'support Manager' || (Auth::user()->id == $user && Auth::user()->tokenCan('staff-approving-support')));
                case(6) :
                    return (Auth::user()->getRole()->title == 'sales manager' || (Auth::user()->id == $user && Auth::user()->tokenCan('staff-approving-sale')));
                case(2) :
                    return (Auth::user()->getRole()->title == 'graphic manager' || (Auth::user()->id == $user && Auth::user()->tokenCan('staff-approving-design')));
                default:
                    return false;
            }
    }

    /**
     * check if manager can submit a section completed in a contract checklist
     *
     * @param $section
     * @return bool
     *
     */
    public function canManagerApprove($section): bool
    {
        switch ($section->order) {
            case(1) :
                return Auth::user()->tokenCan('manager-approving-office');
            case(3) :
                return Auth::user()->tokenCan('manager-approving-programmer');
            case(4) :
                return Auth::user()->tokenCan('manager-approving-graphic');
            case(5):
                return Auth::user()->tokenCan('manager-approving-support');
            case(6):
                return Auth::user()->tokenCan('manager-approving-sales');
            case(2) :
                return Auth::user()->tokenCan('support-final-approve-design');
            default:
                return false;
        }
    }

    /**
     * check if employee can submit a completed titleChecklists section in contract checklist
     *
     * @param $section
     * @param $user
     * @return bool
     *
     */
    public function canStaffApprove($section, $user): bool
    {
        if ($user == Auth::user()->id) {
            return true;
        }
        switch ($section) {
            case(1):
                return Auth::user()->tokenCan('manager-approving-office');
            case(2):
                return Auth::user()->tokenCan('staff-approving-graphic');
            case(3):
                return Auth::user()->tokenCan('manager-approving-technical');
            case(4):
                return Auth::user()->tokenCan('manager-approving-support');
            case(5):
                return Auth::user()->tokenCan('manager-approving-sales');
            default:
                return false;
        }
        return false;
    }

    /**
     * check if manager can assign titleChecklist to a user
     *
     * @param $section
     * @return bool
     *
     */
    public function canManagerAssign($section): bool
    {
        switch ($section->order) {
            case(1):
                return Auth::user()->tokenCan('assign-title-checklist-office');
            case(2) :
                return Auth::user()->tokenCan('assign-title-checklist-design');
            case(3) :
                return Auth::user()->tokenCan('assign-title-checklist-programmer');
            case(4) :
                return Auth::user()->tokenCan('assign-title-checklist-graphic');
            case(5) :
                return Auth::user()->tokenCan('assign-title-checklist-support');
            case(6) :
                return Auth::user()->tokenCan('assign-title-checklist-sales');
            default :
                return false;
        }
    }

    public function canReverse($section): bool
    {
        switch ($section->order) {
            case(1):
                return Auth::user()->tokenCan('reverse-to-office');
            case(2) :
                return Auth::user()->tokenCan('support-reverse-design');
            case(3) :
                return Auth::user()->tokenCan('reverse-to-programmer');
            case(4) :
                return Auth::user()->tokenCan('reverse-to-graphic') || Auth::user()->tokenCan('reverse-to-programmer');
            case(5) :
                return Auth::user()->tokenCan('reverse-to-support') || Auth::user()->tokenCan('reverse-to-graphic') || Auth::user()->tokenCan('reverse-to-programmer');
            case(6) :
                return Auth::user()->tokenCan('reverse-to-sale') || Auth::user()->tokenCan('reverse-to-support') || Auth::user()->tokenCan('reverse-to-graphic') || Auth::user()->tokenCan('reverse-to-programmer');
            default :
                return false;
        }
    }

    public function canManagerSign($section): bool
    {
        switch ($section) {
            case(1):
                return Auth::user()->tokenCan('manager-sign-office-checklist');
            case(2):
                return Auth::user()->tokenCan('manager-sign-designer-checklist');
            case(3):
                return Auth::user()->tokenCan('manager-sign-programmer-checklist');
            case(4):
                return Auth::user()->tokenCan('manager-sign-graphic-checklist');
            case(5):
                return Auth::user()->tokenCan('manager-sign-support-checklist');
            case(6):
                return Auth::user()->tokenCan('manager-sign-sale-checklist');
            default:
                return false;
        }
    }

    public function canStaffSign($section, $user): bool
    {
        if (Auth::user()->getRole()->title == 'admin')
            return true;
        else
            switch ($section) {
                case(1):
                    return (Auth::user()->getRole()->title == 'administrative manager' || $user == Auth::user()->id && Auth::user()->tokenCan('staff-sign-office-checklist'));
                case(2):
                    return (Auth::user()->getRole()->title == 'graphic manager' || $user == Auth::user()->id && Auth::user()->tokenCan('staff-sign-designer-checklist'));
                case(3):
                    return (Auth::user()->getRole()->title == 'technical Manager' || $user == Auth::user()->id && Auth::user()->tokenCan('staff-sign-programmer-checklist'));
                case(4):
                    return (Auth::user()->getRole()->title == 'graphic manager' || $user == Auth::user()->id && Auth::user()->tokenCan('staff-sign-graphic-checklist'));
                case(5):
                    return (Auth::user()->getRole()->title == 'support Manager' || $user == Auth::user()->id && Auth::user()->tokenCan('staff-sign-support-checklist'));
                case(6):
                    return (Auth::user()->getRole()->title == 'sales manager' || $user == Auth::user()->id && Auth::user()->tokenCan('staff-sign-sale-checklist'));
                default:
                    return false;
            }
    }

    public function isTodoListTaskStarted($checklistContract, $section): bool
    {
        $titleChecklist = $checklistContract->titleChecklistUser()->where('section_id', $section)->first();
        $todolist = $checklistContract->lastTodoList()->where('user_id', $titleChecklist->user_id)->orderBy('created_at', 'desc')->first();

        if ($todolist && $todolist->status == 'started') {
            return false;
        }
        return true;
    }

    public function isManagerStartTodoListTask($checklistContract, $section): bool
    {

        $section = Section::find($section);
        $user_controller = new UserController();
        $manager = $user_controller->findManager($section->order);

        $todolist = $checklistContract->lastTodoList()->where('user_id', $manager->id)->orderBy('created_at', 'desc')->first();

        if ($todolist && $todolist->status == 'started') {
            return false;
        }
        return true;
    }

    public function isSupportStartTodoListTask($checklistContract): bool
    {

        $section = Section::where('order', 5)->first();
        $titleChecklist = $checklistContract->titleChecklistUser()->where('section_id', $section->id)->first();
        if ($titleChecklist) {
            $support_user = User::findOrFail($titleChecklist->user_id);

            $todolist = $checklistContract->lastTodoList()->where('user_id', $support_user->id)->orderBy('created_at', 'desc')->first();
            if ($todolist && $todolist->status == 'started') {
                return false;
            }
        }


        return true;
    }


    public function handleSMS($type, $checklistContract, $order, $user = null, $section = null, $ticket_number = null)
    {
        if ($type == 'customer') {
            $include_date_condition = $checklistContract->contract()->whereDate('created_at', '>=', '2022-06-18')->first();
            $lang = $checklistContract->checklist->language;
            $receiver = [env('SMS_ADMIN_PHONE'), env('SMS_SUPPORT_PHONE'), env('SMS_FANI_PHONE')];
            if ($include_date_condition) {
                $receiver[] = $checklistContract->contract->customer->phone_number;
            }
            $receiver = implode(',', $receiver);
            $data = [
                'checklistContract' => $checklistContract,
                'section' => $order,
                'extra_data' => $ticket_number ? $ticket_number : '',
                'lang' => $lang
            ];


            $notification = new NotificationHandler();
            $notification->sendSMS('customerChecklist', $receiver, $data);
        } else if ('checklist_process') {
            if ($order === 2) {
                $data['desc'] = ' مدیر بخش شما چک لیست ' . ' " ' . $checklistContract->checklist->title . ' " ' . $checklistContract->contract->title . ' مشتری ' . $checklistContract->contract->customer->name . ' در بخش ' . $section->title . 'را به شما اختصاص داده است';
            } else if ($order === 0) {
                $data['desc'] = 'چک لیست سایت ' . $checklistContract->checklist->title . '  ' . $checklistContract->contract->customer->name . '  ' . $checklistContract->contract->title . ' به دلایلی برگشت خورد . ';
            } else {
                $data['desc'] = 'چک لیست سایت ' . $checklistContract->checklist->title . '  ' . $checklistContract->contract->customer->name . '  ' . $checklistContract->contract->title . ' در لیست کارهای شما قرار گرفت .';
            }
            $data['checklist_contract'] = $checklistContract->id;
            $data['contract_title'] = $checklistContract->contract->title;
            $data['checklist_title'] = $checklistContract->checklist->title;
            $data['customer_name'] = $checklistContract->contract->customer->name;

            $notification = new NotificationHandler();
            $notification->sendSMS('ChecklistProcess', $user, $data);

        }


    }

}
