<?php

namespace App\Http\Controllers;

use App\TimeDoctor\TimeDoctorAccount;
use App\TimeDoctor\TimeDoctorMember;
use App\TimeDoctor\TimeDoctorProject;
use App\TimeDoctor\TimeDoctorTask;

use App\User;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Library\TimeDoctor\Src\Timedoctor;
use stdClass;
use Storage;

class TimeDoctorController extends Controller
{
    public $timedoctor;
    public function __construct()
    {
        $this->timedoctor = Timedoctor::getInstance();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function userList(Request $request)
    {
        $members = TimeDoctorMember::all();
        $users = User::all('id', 'name');
        
        return view(
            'time-doctor.users',
            [
                'members' => $members,
                'users' => $users,
            ]
        );        
    }

    public function getProjects(Request $request)
    {
        $projects = TimeDoctorProject::all();
        return view(
            'time-doctor.projects', 
            [
                'projects' => $projects,
            ]
        );
    }

    public function getTasks(Request $request)
    {
        $tasks = TimeDoctorTask::all();
        return view(
            'time-doctor.tasks', 
            [
                'tasks' => $tasks,
            ]
        );
    }

    public function saveUserAccount(Request $request){
        try {
            $time_doctor_acount = new TimeDoctorAccount();
            $time_doctor_acount->time_doctor_email = $request->email;
            $time_doctor_acount->time_doctor_password = $request->password;
            if ($time_doctor_acount->save()) {
                return response()->json(['code' => 200, 'data' => [], 'message' => 'TimeDoctor Account Added successfully']);
            } else {
                return response()->json(['code' => 500, 'data' => [], 'message' => 'Something went wrong']);
            }
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'data' => [], 'message' => 'Something went wrong']);
        }
    }

    public function getAuthTokens(Request $request){
        try {
            $getToken = $this->timedoctor->generateAuthToken( $request->id );
            if( $getToken ){
                return response()->json(['code' => 200, 'data' => [], 'message' => 'Auth token generated successfully']);
            } else {
                return response()->json(['code' => 500, 'data' => [], 'message' => 'Something went wrong']);
            }
        } catch (Exception $e) { 
            return response()->json(['code' => 500, 'data' => [], 'message' => 'Something went wrong']);
        }
    }

    public function displayUserAccountList(Request $request){
        $timeDoctorAccounts = TimeDoctorAccount::all();
        $html = "";
        $i = 1;
        foreach($timeDoctorAccounts as $account){
            $html .= "<tr>";
            $html .= "<td>".$i++."</td>";
            $html .= "<td>".$account->time_doctor_email."</td>";
            $html .= "<td>".$account->time_doctor_password."</td>";
            if($account->auth_token == ""){
                $html .= "<td><button type='button' class='btn btn-secondary get_token' data-id=".$account->id.">Get Token</button></td>";
            } else {
                $html .= "<td style='vertical-align:middle;'>".$account->auth_token."</td>";
            }
            $html .= "</tr>";
        }
        return $html;
    }

    public function refreshUsersById(Request $request)
    {
        \Artisan::call('timedoctor:refresh_users', ['id' => $request->time_doctor_account]);

        return redirect()->back();
    }

    public function refreshProjectsById(Request $request)
    {
        \Artisan::call('timedoctor:refresh_projects', ['id' => $request->time_doctor_account]);

        return redirect()->back();
    }

    public function saveProject(Request $request)
    {
        try {
            $time_doctor_acount = TimeDoctorAccount::find($request->time_doctor_account);
            $companyId = $time_doctor_acount->company_id;
            $accessToken = $time_doctor_acount->auth_token;        
            $createProject = $this->timedoctor->createProject( $companyId, $accessToken, $request->all() );
            if( $createProject ){
                return response()->json(['code' => 200, 'data' => [], 'message' => 'Time doctor project created successfully']);
            } else {
                return response()->json(['code' => 500, 'data' => [], 'message' => 'Something went wrong']);
            }
        } catch (Exception $e) { 
            return response()->json(['code' => 500, 'data' => [], 'message' => 'Something went wrong']);
        }
    }

    public function refreshTasksById(Request $request)
    {
        \Artisan::call('timedoctor:refresh_tasks', ['id' => $request->time_doctor_account]);

        return redirect()->back();
    }

    public function saveTask(Request $request)
    {
        try {
            $time_doctor_acount = TimeDoctorAccount::find($request->time_doctor_account);
            $companyId = $time_doctor_acount->company_id;
            $accessToken = $time_doctor_acount->auth_token;        
            $createTask = $this->timedoctor->createTask( $companyId, $accessToken, $request->all() );
            if( $createTask ){
                return response()->json(['code' => 200, 'data' => [], 'message' => 'Time doctor project created successfully']);
            } else {
                return response()->json(['code' => 500, 'data' => [], 'message' => 'Something went wrong']);
            }
        } catch (Exception $e) { 
            return response()->json(['code' => 500, 'data' => [], 'message' => 'Something went wrong']);
        }
    }

    public function getTasksById(Request $request){
        try{
            $time_doctortask = TimeDoctorTask::find($request->taskId);
            if($time_doctortask){
                $res['name'] = $time_doctortask->summery;
                $res['description'] = $time_doctortask->description;
                $res['project_id'] = $time_doctortask->time_doctor_project_id;
                return response()->json(['code' => 200, 'data' => $res, 'message' => 'Time doctor task data fetched successfully']);
            } else {
                return response()->json(['code' => 500, 'data' => [], 'message' => 'Task not found']);   
            }
        } catch (Exception $e) { 
            return response()->json(['code' => 500, 'data' => [], 'message' => 'Something went wrong']);
        }
    }
    
    public function updateTasksById(Request $request)
    {
        try {
            $time_doctor_task = TimeDoctorTask::find($request->time_doctor_task_id);
            if( $time_doctor_task ){
                $res['taskId'] = $time_doctor_task->time_doctor_task_id;
                $res['taskName'] = $request->edit_time_doctor_task_name;
                $res['taskDescription'] = $request->edit_time_doctor_task_description;
                $res['taskProject'] = $time_doctor_task->time_doctor_project_id;
                $companyId = $time_doctor_task->account->company_id;
                $accessToken = $time_doctor_task->account->auth_token;
                $updateTask = $this->timedoctor->updateTask( $companyId, $accessToken, $res );
                if( $updateTask ){
                    $time_doctor_task->summery = $request->edit_time_doctor_task_name;
                    $time_doctor_task->description = $request->edit_time_doctor_task_description;
                    $time_doctor_task->save();
                    return response()->json(['code' => 200, 'data' => [], 'message' => 'Time doctor project created successfully']);
                } else {
                    return response()->json(['code' => 500, 'data' => [], 'message' => 'Something went wrong']);
                }
            } else {
                return response()->json(['code' => 500, 'data' => [], 'message' => 'Task not found']);
            }
        } catch (Exception $e) { 
            return response()->json(['code' => 500, 'data' => [], 'message' => 'Something went wrong']);
        }
    }
    
    public function getProjectsById(Request $request){
        try{
            $time_doctor_project = TimeDoctorProject::find($request->projectId);
            if($time_doctor_project){
                $res['name'] = $time_doctor_project->time_doctor_project_name;
                $res['description'] = $time_doctor_project->time_doctor_project_description;
                return response()->json(['code' => 200, 'data' => $res, 'message' => 'Time doctor project data fetched successfully']);
            } else {
                return response()->json(['code' => 500, 'data' => [], 'message' => 'Program not found']);   
            }
        } catch (Exception $e) { 
            return response()->json(['code' => 500, 'data' => [], 'message' => 'Something went wrong']);
        }
    }

    public function updateProjectById(Request $request)
    {
        try {
            $time_doctor_project = TimeDoctorProject::find($request->time_doctor_program_id);
            if( $time_doctor_project ){
                $res['projectId'] = $time_doctor_project->time_doctor_project_id;
                $res['projectName'] = $request->edit_time_doctor_project_name;
                $res['projectDescription'] = $request->edit_time_doctor_project_description;
                $companyId = $time_doctor_project->account_detail->company_id;
                $accessToken = $time_doctor_project->account_detail->auth_token;
                $updateProject = $this->timedoctor->updateProject( $companyId, $accessToken, $res );
                if( $updateProject ){
                    $time_doctor_project->time_doctor_project_name = $request->edit_time_doctor_project_name;
                    $time_doctor_project->time_doctor_project_description = $request->edit_time_doctor_project_description;
                    $time_doctor_project->save();
                    return response()->json(['code' => 200, 'data' => [], 'message' => 'Time doctor project created successfully']);
                } else {
                    return response()->json(['code' => 500, 'data' => [], 'message' => 'Something went wrong']);
                }
            } else {
                return response()->json(['code' => 500, 'data' => [], 'message' => 'Task not found']);
            }
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'data' => [], 'message' => 'Something went wrong']);
        }
    }

    public function userTreckTime(Request $request){
        return view('time-doctor.track-users');
    }

    public function linkUser(Request $request)
    {

        $bodyContent = $request->getContent();
        $jsonDecodedBody = json_decode($bodyContent);
        
        $userId = $jsonDecodedBody->user_id;
        $timeDoctorUserId = $jsonDecodedBody->time_doctor_user_id;

        if (! $userId || ! $timeDoctorUserId) {
            return response()->json(
                [
                    'error' => 'Missing parameters',
                ],
                400
            );
        }

        TimeDoctorMember::linkUser($timeDoctorUserId, $userId);

        return response()->json([
            'message' => 'link success',
        ]);
    }

    public function sendInvitations(Request $request)
    {
        $members = TimeDoctorAccount::where('auth_token', '!=', '')->whereNotNull('company_id')->get();
        $users = User::all('id', 'name', 'email');
        return view(
            'time-doctor.create-accounts',
            [
                'title' => 'Create TimeDoctor Account',
                'members' => $members,
                'users' => $users,
            ]
        );
    }

    public function sendSingleInvitation(Request $request)
    {
        try {
            $data = [
                'email' => $request->email,
                'name'  => $request->name,
            ];
            $userId = $request->userId;
            $time_doctor_acount = TimeDoctorAccount::find($request->tda);
            if ($time_doctor_acount):
                $companyId = $time_doctor_acount->company_id;
                $accessToken = $time_doctor_acount->auth_token; 
                $inviteResponse = $this->timedoctor->sendSingleInvitation( $companyId, $accessToken, $data );
                switch ($inviteResponse['code']) {
                    case '401':
                        return response()->json(['code' => 500, 'data' => [], 'message' => 'Time Doctro Account user\'s Token ID is invalid or access is denied.']);
                        break;
                    case '403':
                        return response()->json(['code' => 500, 'data' => [], 'message' => 'Time Doctro Account user don\'t have permission to perform this action']);
                        break;
                    case '409':
                        return response()->json(['code' => 500, 'data' => [], 'message' => 'Given email is invalid.']);
                        break;
                    case '500':
                    case '422':
                    case '404':
                        return response()->json(['code' => 500, 'data' => [], 'message' => 'Something went wrong']);
                        break;
                    default:
                        $lastRow = TimeDoctorAccount::create([
                            'time_doctor_email'=> $request->email 
                        ]);
                        TimeDoctorMember::create([
                            'time_doctor_user_id' => $inviteResponse['data']['time_doctor_user_id'],
                            'time_doctor_account_id' => $lastRow->id,
                            'email' => $request->email,
                            'user_id' => $userId
                        ]);
                        return response()->json(['code' => 200, 'data' => [], 'message' => 'Time doctor account created successfully']);
                        break;
                }
            else:
                return response()->json(['code' => 500, 'data' => [], 'message' => 'Something went wrong']);
            endif;
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'data' => [], 'message' => $e->getMessage()]);
        }
    }

    public function sendBulkInvitation(Request $request)
    {
        try {
            $time_doctor_acount = TimeDoctorAccount::find($request->tda);
            if ($time_doctor_acount):
                $companyId = $time_doctor_acount->company_id;
                $accessToken = $time_doctor_acount->auth_token; 
                $users = User::whereIn('id',$request->ids)->get();
                $payloadUsers = [];
                foreach($users as $u){
                    $obj = new stdClass;
                    $obj->email = $u->email;
                    $obj->name = $u->name;
                    
                    array_push($payloadUsers, $obj);
                }

                $bulkInvitePayload = [
                    "users"=> $payloadUsers,
                    "noSendEmail" => "false"
                ];

                $bulkInviteResponse = $this->timedoctor->sendBulkInvitation( $companyId, $accessToken, $bulkInvitePayload );
                switch ($bulkInviteResponse['code']) {
                    case '401':
                        return response()->json(['code' => 500, 'data' => [], 'message' => 'Time Doctro Account user\'s Token ID is invalid or access is denied.']);
                        break;
                    case '403':
                        return response()->json(['code' => 500, 'data' => [], 'message' => 'Time Doctro Account user don\'t have permission to perform this action']);
                        break;
                    case '409':
                        return response()->json(['code' => 500, 'data' => [], 'message' => 'Given email is invalid.']);
                        break;
                    case '500':
                    case '422':
                    case '404':
                        return response()->json(['code' => 500, 'data' => [], 'message' => 'Something went wrong']);
                        break;
                    default:
                        $response = $bulkInviteResponse['data']['response']['data'];
                        foreach($users as $u){
                            $userId = $u->id;
                            $email = $u->email;
                            $time_doctor_user_id = "";
                            foreach($response as $key => $val){
                                if($key == $email && $val->status == 'sent') {
                                    $time_doctor_user_id = $val->userId;
                                }
                            }

                            if ($time_doctor_user_id != "") {
                                $lastRow = TimeDoctorAccount::create([
                                    'time_doctor_email'=> $email 
                                ]);
                                TimeDoctorMember::create([
                                    'time_doctor_user_id' => $time_doctor_user_id,
                                    'time_doctor_account_id' => $lastRow->id,
                                    'email' => $email,
                                    'user_id' => $userId
                                ]);
                            }
                        }

                        return response()->json(['code' => 200, 'data' => [], 'message' => 'Time doctor account created successfully']);
                        break;
                }
            else:
                return response()->json(['code' => 500, 'data' => [], 'message' => 'Something went wrong']);
            endif;
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'data' => [], 'message' => $e->getMessage()]);
        }
    }
}
