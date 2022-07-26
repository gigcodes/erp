<?php

namespace App\Http\Controllers;

use App\Uicheck;
use App\UicheckType;
/*use Illuminate\Http\Request;
use App\SiteDevelopment;
use App\SiteDevelopmentArtowrkHistory;
use App\SiteDevelopmentCategory;
use App\SiteDevelopmentMasterCategory;
use App\StoreWebsite;
use DB;
*/
use Auth;
use DB;
use App\User;
use App\UicheckUserAccess;
use App\UicheckAttachement;
use App\StoreWebsite;
use App\UiAdminStatusHistoryLog;
use App\UiDeveloperStatusHistoryLog;
use App\SiteDevelopmentStatus;
use App\SiteDevelopmentCategory;
use App\UiCheckIssueHistoryLog;
use App\UiCheckCommunication;
use App\UiCheckAssignToHistory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;
use Storage;
use PDF;
use User as GlobalUser;

class UicheckController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        //dd($data['store_websites']);
        if ($request->ajax()) {
            
            if (Auth::user()->hasRole('Admin')){
                $site_development_categories = SiteDevelopmentCategory::select('site_development_categories.*', 'site_developments.id AS site_id','site_developments.website_id', "uichecks.id AS uicheck_id","uichecks.issue","uichecks.website_id AS websiteid","uichecks.uicheck_type_id","uua.user_id as accessuser","uichecks.dev_status_id","uichecks.admin_status_id")
                ->join('site_developments','site_development_categories.id','=','site_developments.site_development_category_id')
                ->leftjoin('uichecks','uichecks.site_development_category_id','=','site_development_categories.id')
                ->leftjoin('uicheck_user_accesses as uua','uua.uicheck_id','=','uichecks.id')
                ->where('site_developments.is_ui', 1);
            }else{
                $site_development_categories = SiteDevelopmentCategory::select('site_development_categories.*', 'site_developments.id AS site_id','site_developments.website_id', "uichecks.id AS uicheck_id","uichecks.issue","uichecks.website_id AS websiteid","uichecks.uicheck_type_id","uua.user_id as accessuser","uichecks.dev_status_id","uichecks.admin_status_id")
                ->join('site_developments','site_development_categories.id','=','site_developments.site_development_category_id')
                ->join('uichecks','uichecks.site_development_category_id','=','site_development_categories.id')
                ->leftjoin('uicheck_user_accesses as uua','uua.uicheck_id','=','uichecks.id')
                ->where('uua.user_id',"=",\Auth::user()->id)
                ->where('site_developments.is_ui', 1);
            }
            
            //->where('site_development_categories.id','site_developments.site_development_category_id');
            if(isset($request->category_name) &&  $request->category_name != ''){
                $site_development_categories = $site_development_categories->where('uichecks.website_id', $request->category_name);
            }
            if(isset($request->sub_category_name) && $request->sub_category_name != ''){
                $site_development_categories = $site_development_categories->where('site_development_categories.id',  $request->sub_category_name);
            }
            if(isset($request->dev_status) &&  $request->dev_status != ''){
                $site_development_categories = $site_development_categories->where('uichecks.dev_status_id', $request->dev_status);
            }
            if(isset($request->admin_status) && $request->admin_status != ''){
                $site_development_categories = $site_development_categories->where('uichecks.admin_status_id',  $request->admin_status);
            }
            if(isset($request->assign_to) && $request->assign_to != ''){
                $site_development_categories = $site_development_categories->where('uua.user_id',  $request->assign_to);
            }
            $site_development_categories->groupBy('site_development_categories.id');
            return datatables()->eloquent($site_development_categories)->toJson();
        }else{
            $data = array();
            $data['all_store_websites'] = StoreWebsite::all();
            $data['users'] = User::select('id', 'name')->get();
            $data['allTypes'] = UicheckType::all();
            $data['categories'] = SiteDevelopmentCategory::paginate(20);//all();
            $data['search_website'] = isset($request->store_webs)? $request->store_webs : '';
            $data['search_category'] = isset($request->categories)? $request->categories : '';
            $data['user_id'] = isset($request->user_id)? $request->user_id : '';
            $data['assign_to'] = isset($request->assign_to)? $request->assign_to : '';
            $data['dev_status'] = isset($request->dev_status)? $request->dev_status : '';
            $data['admin_status'] = isset($request->admin_status)? $request->admin_status : '';
            $data['site_development_status_id'] = isset($request->site_development_status_id)? $request->site_development_status_id : [];
            $data['allStatus'] = SiteDevelopmentStatus::pluck("name", "id")->toArray();
            $store_websites = StoreWebsite::select('store_websites.*')->join('site_developments','store_websites.id','=','site_developments.website_id');
            if($data['search_website'] != ''){
                $store_websites =  $store_websites->where('store_websites.id', $data['search_website']);
            }
            $data['store_websites'] =  $store_websites->where('is_ui', 1)->groupBy('store_websites.id')->get();
            // $data['allUsers'] = User::select('id', 'name')->get();
            $data['allUsers'] = User::join('role_user', 'role_user.user_id', 'users.id')->join('roles', 'roles.id', 'role_user.role_id')
                ->where('roles.name', 'Developer')->select('users.name', 'users.id')->get();
            $data['log_user_id'] = \Auth::user()->id ?? '';
            $site_development_categories = SiteDevelopmentCategory::select('site_development_categories.*', 'site_developments.id AS site_id','site_developments.website_id', "uichecks.id AS uicheck_id")
            ->join('site_developments','site_development_categories.id','=','site_developments.site_development_category_id')
            ->leftjoin('uichecks','uichecks.site_development_category_id','=','site_development_categories.id');
            // ->where('site_developments.is_ui', 1);
       
            //->where('site_development_categories.id','site_developments.site_development_category_id');
            if($data['search_website'] != ''){
                $site_development_categories = $site_development_categories->where('uichecks.website_id', $data['store_websites'][0]->id);
            }
            if($data['search_category'] != ''){
                $site_development_categories = $site_development_categories->where('site_development_categories.id',  $data['search_category']);
            }
            $data['site_development_categories'] = $site_development_categories->groupBy('site_development_categories.id');
            return view('uicheck.index', $data );
        }            
    }

    public function access(Request $request){
        $check = UicheckUserAccess::where("uicheck_id",$request->uicheck_id)->first();
        if(!is_null($check)){
            $access = UicheckUserAccess::find($check->id);
            $access->delete();
        }
        $this->CreateUiAssignToHistoryLog($request, $check);
        $array = array(
            "user_id" => $request->id,
            "uicheck_id" => $request->uicheck_id
        );
        UicheckUserAccess::create($array);
        return response()->json(['code' => 200,'message' => 'Permission Given!!!']);
    }

    public function typeSave(Request $request){
        $array = array(
            "uicheck_type_id" => $request->type
        );
        Uicheck::where("id",$request->uicheck_id)->update($array);
        return response()->json(['code' => 200,'message' => 'Type Updated!!!']);
    }

    public function upload_document(Request $request){
        $uicheck_id = $request->uicheck_id;
        $subject = $request->subject;
        $description = $request->description;
        
        if ($uicheck_id > 0 && !empty($subject)) {    
            if ($request->hasfile('files')) {
                $path = public_path('uicheckdocs');
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }
                $uicheckDocName = '';
                if($request->file('files')) {
                    $file = $request->file('files')[0];
                    $uicheckDocName = uniqid() . '_' . trim($file->getClientOriginalName());
                    $file->move($path, $uicheckDocName);
                }
                $docArray = array(
                    "user_id" => \Auth::id(),
                    "filename" => $uicheckDocName,
                    "uicheck_id" => $uicheck_id,
                    "subject" => $subject,
                    "description" => $description
                );        
                UicheckAttachement::create($docArray);   
                return response()->json(["code" => 200, "success" => "Done!"]);                
            }else{
                return response()->json(["code" => 500, "error" => "Oops, Please fillup required fields"]);
            }
        } else {
            return response()->json(["code" => 500, "error" => "Oops, Please fillup required fields"]);
        }
    }

    public function getDocument(Request $request)
    {
        $id = $request->get("id", 0);

        if ($id > 0) {
            $devDocuments = UicheckAttachement::with("user","uicheck")->where("uicheck_id", $id)->latest()->get();
            $html = view('uicheck.ajax.document-list', compact("devDocuments"))->render();
            return response()->json(["code" => 200, "data" => $html]);
        } else {
            return response()->json(["code" => 500, "error" => "Oops, id is required field"]);
        }
    }

    public function typeStore(Request $request)
    {
        // $request->validate($request, [
        //     'name' => 'required|string'
        // ]);
        $data = $request->except('_token');
        UicheckType::create($data);
        return redirect()->back()->with('success', 'You have successfully created a status!');
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            $uicheck = Uicheck::find($request->id);
            if(empty($uicheck))
                $uicheck = new Uicheck();

            $uicheck->site_development_id = $request->site_development_id;
            $uicheck->site_development_category_id = $request->category;

            if($request->website_id)
                $uicheck->website_id = $request->website_id;
            if($request->issue){
                if($request->issue != $uicheck->issue){
                    $this->CreateUiissueHistoryLog($request, $uicheck);
                }
                $uicheck->issue = $request->issue;
            }
            if($request->developer_status){
                if($request->developer_status != $uicheck->developer_status){
                    $this->CreateUiDeveloperStatusHistoryLog($request, $uicheck);
                }
                $uicheck->dev_status_id = $request->developer_status;
            }
            if($request->admin_status){
                if($request->admin_status != $uicheck->admin_status_id){
                    $this->createUiAdminStatusHistoryLog($request, $uicheck);
                }
                $uicheck->admin_status_id = $request->admin_status;
            }
                

            $uicheck->save();
            return response()->json(['code' => 200, 'data' => $uicheck,'message' => 'Updated successfully!!!']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CreateUiAdminStatusHistoryLog  $createUiAdminStatusHistoryLog
     * @return \Illuminate\Http\Response
     */
    public function CreateUiAdminStatusHistoryLog(Request $request, $uicheck)
    {
        $adminStatusLog = new UiAdminStatusHistoryLog();
        $adminStatusLog->user_id = \Auth::user()->id;
        $adminStatusLog->uichecks_id = $request->id;
        $adminStatusLog->old_status_id = $uicheck->admin_status_id;
        $adminStatusLog->status_id = $request->admin_status;
        $adminStatusLog->save();
    }

    public function getUiAdminStatusHistoryLog(Request $request)
    {
        $adminStatusLog = UiAdminStatusHistoryLog::select("ui_admin_status_history_logs.*", "users.name as userName", "site_development_statuses.name AS dev_status", "old_stat.name AS old_name")
        ->leftJoin("users", "users.id", "ui_admin_status_history_logs.user_id")
        ->leftJoin("site_development_statuses", "site_development_statuses.id", "ui_admin_status_history_logs.status_id")
        ->leftJoin("site_development_statuses as old_stat", "old_stat.id", "ui_admin_status_history_logs.old_status_id")
        ->where('ui_admin_status_history_logs.uichecks_id', $request->id)
        ->orderBy('ui_admin_status_history_logs.id', "DESC")
        ->get();
        $html = "";
        foreach($adminStatusLog AS $adminStatus) {
            $html .=  '<tr>';
            $html .=  '<td>'.$adminStatus->id.'</td>';
            $html .=  '<td>'.$adminStatus->userName.'</td>';
            $html .=  '<td>'.$adminStatus->old_name.'</td>';
            $html .=  '<td>'.$adminStatus->dev_status.'</td>';
            $html .=  '<td>'.$adminStatus->created_at.'</td>';
            
            $html .=  '</tr>';
        }
        return response()->json(['code' => 200, 'html' => $html,'message' => 'Listed successfully!!!']);
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\CreateUiDeveloperStatusHistoryLog  $createUiDeveloperStatusHistoryLog
     * @return \Illuminate\Http\Response
     */
    public function CreateUiDeveloperStatusHistoryLog(Request $request, $uicheck)
    {
        $devStatusLog = new UiDeveloperStatusHistoryLog();
        $devStatusLog->user_id = \Auth::user()->id;
        $devStatusLog->uichecks_id = $request->id;
        $devStatusLog->old_status_id = $uicheck->dev_status_id;
        $devStatusLog->status_id = $request->developer_status;
        $devStatusLog->save();
    }

    public function getUiDeveloperStatusHistoryLog(Request $request)
    {
        $adminStatusLog = UiDeveloperStatusHistoryLog::select("ui_developer_status_history_logs.*", "users.name as userName", "site_development_statuses.name AS dev_status", "old_stat.name AS old_name")
        ->leftJoin("users", "users.id", "ui_developer_status_history_logs.user_id")
        ->leftJoin("site_development_statuses", "site_development_statuses.id", "ui_developer_status_history_logs.status_id")
        ->leftJoin("site_development_statuses as old_stat", "old_stat.id", "ui_developer_status_history_logs.old_status_id")
        ->where('ui_developer_status_history_logs.uichecks_id', $request->id)
        ->orderBy('ui_developer_status_history_logs.id', "DESC")
        ->get();

        $html = "";
        foreach($adminStatusLog AS $adminStatus) {
            $html .=  '<tr>';
            $html .=  '<td>'.$adminStatus->id.'</td>';
            $html .=  '<td>'.$adminStatus->userName.'</td>';
            $html .=  '<td>'.$adminStatus->old_name.'</td>';
            $html .=  '<td>'.$adminStatus->dev_status.'</td>';
            $html .=  '<td>'.$adminStatus->created_at.'</td>';
            $html .=  '</tr>';
        }
        return response()->json(['code' => 200, 'html' => $html,'message' => 'Listed successfully!!!']);
        
    }

    public function CreateUiissueHistoryLog(Request $request, $uicheck)
    {
        $devStatusLog = new UiCheckIssueHistoryLog();
        $devStatusLog->user_id = \Auth::user()->id;
        $devStatusLog->uichecks_id = $request->id;
        $devStatusLog->old_issue = $uicheck->issue;
        $devStatusLog->issue = $request->issue;
        $devStatusLog->save();
    }

    public function getUiIssueHistoryLog(Request $request)
    {
        try{
            $getIssueLog = UiCheckIssueHistoryLog::select("ui_check_issue_history_logs.*", "users.name as userName")
            ->leftJoin("users", "users.id", "ui_check_issue_history_logs.user_id")
            ->where('ui_check_issue_history_logs.uichecks_id', $request->id)
            ->orderBy('ui_check_issue_history_logs.id', "DESC")
            ->get();

            $html = "";
            foreach($getIssueLog AS $issueLog) {
                $html .=  '<tr>';
                $html .=  '<td>'.$issueLog->id.'</td>';
                $html .=  '<td>'.$issueLog->userName.'</td>';
                $html .=  '<td>'.$issueLog->old_issue.'</td>';
                $html .=  '<td>'.$issueLog->issue.'</td>';
                $html .=  '<td>'.$issueLog->created_at.'</td>';
            
                $html .=  '</tr>';
            }
            return response()->json(['code' => 200, 'html' => $html,'message' => 'Listed successfully!!!']);
        } catch (\Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function getUiCheckMessageHistoryLog(Request $request)
    {
        try{
            $getMessageLog = UiCheckCommunication::select("ui_check_communications.*", "users.name as userName")
            ->leftJoin("users", "users.id", "ui_check_communications.user_id")
            ->where('ui_check_communications.uichecks_id', $request->id)
            ->orderBy('ui_check_communications.id', "DESC")
            ->get();

            $html = "";
            foreach($getMessageLog AS $messageLog) {
                $html .=  '<tr>';
                $html .=  '<td>'.$messageLog->id.'</td>';
                $html .=  '<td>'.$messageLog->userName.'</td>';
                $html .=  '<td>'.$messageLog->message.'</td>';
                $html .=  '<td>'.$messageLog->created_at.'</td>';
                $html .=  '</tr>';
            }
            return response()->json(['code' => 200, 'html' => $html,'message' => 'Listed successfully!!!']);
        } catch (\Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function CreateUiMessageHistoryLog(Request $request)
    {
        $messageLog = new UiCheckCommunication();
        $messageLog->user_id = \Auth::user()->id;
        $messageLog->uichecks_id = $request->id;
        $messageLog->message = $request->message;
        $messageLog->save();
        return response()->json(['code' => 200, 'message' => 'Message saved successfully!!!']);
    }

    public function CreateUiAssignToHistoryLog(Request $request, $uicheck)
    {
        $messageLog = new UiCheckAssignToHistory();
        $messageLog->user_id = \Auth::user()->id;
        $messageLog->uichecks_id = $request->uicheck_id;
        $messageLog->assign_to = $request->id;
        $messageLog->old_assign_to = $uicheck->user_id ?? '';
        $messageLog->save();
        return response()->json(['code' => 200, 'message' => 'Message saved successfully!!!']);
    }

    public function getUiCheckAssignToHistoryLog(Request $request)
    {
        try{
            $getMessageLog = UiCheckAssignToHistory::select("ui_check_assign_to_histories.*", "users.name as userName", "assignTo.name AS assignToName")
            ->leftJoin("users", "users.id", "ui_check_assign_to_histories.user_id")
            ->leftJoin("users AS assignTo", "assignTo.id", "ui_check_assign_to_histories.assign_to")
            ->where('ui_check_assign_to_histories.uichecks_id', $request->id)
            ->orderBy('ui_check_assign_to_histories.id', "DESC")
            ->get();

            $html = "";
            foreach($getMessageLog AS $messageLog) {
                $html .=  '<tr>';
                $html .=  '<td>'.$messageLog->id.'</td>';
                $html .=  '<td>'.$messageLog->userName.'</td>';
                $html .=  '<td>'.$messageLog->assignToName.'</td>';
                $html .=  '<td>'.$messageLog->created_at.'</td>';
                $html .=  '</tr>';
            }
            return response()->json(['code' => 200, 'html' => $html,'message' => 'Listed successfully!!!']);
        } catch (\Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }


}
