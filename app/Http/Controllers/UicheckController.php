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
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;
use Storage;
use PDF;

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
            $site_development_categories = SiteDevelopmentCategory::select('site_development_categories.*', 'site_developments.id AS site_id','site_developments.website_id', "uichecks.id AS uicheck_id","uichecks.issue","uichecks.website_id AS websiteid","uichecks.uicheck_type_id")
            ->join('site_developments','site_development_categories.id','=','site_developments.site_development_category_id')
            ->leftjoin('uichecks','uichecks.site_development_category_id','=','site_development_categories.id')
            ->leftjoin('uicheck_user_accesses','uicheck_user_accesses.uicheck_id','=','uichecks.id')
            ->where('uicheck_user_accesses.user_id',\Auth::user()->id)
            ->where('site_developments.is_ui', 1);
       
            //->where('site_development_categories.id','site_developments.site_development_category_id');
            // if($data['search_website'] != ''){
            //     $site_development_categories = $site_development_categories->where('uichecks.website_id', $data['store_websites'][0]->id);
            // }
            // if($data['search_category'] != ''){
            //     $site_development_categories = $site_development_categories->where('site_development_categories.id',  $data['search_category']);
            // }
            $site_development_categories->groupBy('site_development_categories.id');
            return datatables()->eloquent($site_development_categories)->toJson();
        }else{
            $data = array();
            $data['all_store_websites'] = StoreWebsite::all();
            $data['allTypes'] = UicheckType::all();
            $data['categories'] = SiteDevelopmentCategory::paginate(20);//all();
            $data['search_website'] = isset($request->store_webs)? $request->store_webs : '';
            $data['search_category'] = isset($request->categories)? $request->categories : '';
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
            
            $html .=  '</tr>';
        }
        if($html != ""){
            return response()->json(['code' => 200, 'html' => $html,'message' => 'Listed successfully!!!']);
        } else{
            return response()->json(['code' => 500, 'message' => "data not found"]);
        }
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
           
            $html .=  '</tr>';
        }
        if($html != ""){
            return response()->json(['code' => 200, 'html' => $html,'message' => 'Listed successfully!!!']);
        } else{
            return response()->json(['code' => 500, 'message' => "data not found"]);
        }
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
           
            $html .=  '</tr>';
        }
        if($html != ""){
            return response()->json(['code' => 200, 'html' => $html,'message' => 'Listed successfully!!!']);
        } else{
            return response()->json(['code' => 500, 'message' => "data not found"]);
        }
    }

}
