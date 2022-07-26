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
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;
use Storage;
use PDF;

class UicheckController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        if ($request->ajax() || request('draw')) {

            if (Auth::user()->hasRole('Admin')) {
                $q = SiteDevelopmentCategory::query()
                    ->join('site_developments', 'site_development_categories.id', '=', 'site_developments.site_development_category_id')
                    ->leftjoin('uichecks', 'uichecks.site_development_category_id', '=', 'site_development_categories.id')
                    ->leftjoin('uicheck_user_accesses as uua', 'uua.uicheck_id', '=', 'uichecks.id')
                    ->where('site_developments.is_ui', 1)
                    ->where('uichecks.id', '>', 0)
                    ->select(
                        'site_development_categories.*',
                        'site_developments.id AS site_id',
                        'site_developments.website_id',
                        "uichecks.id AS uicheck_id",
                        "uichecks.issue",
                        "uichecks.website_id AS websiteid",
                        "uichecks.uicheck_type_id",
                        "uichecks.dev_status_id",
                        "uichecks.admin_status_id",
                        "uua.user_id as accessuser"
                    );
            } else {
                $q = SiteDevelopmentCategory::query()
                    ->join('site_developments', 'site_development_categories.id', '=', 'site_developments.site_development_category_id')
                    ->join('uichecks', 'uichecks.site_development_category_id', '=', 'site_development_categories.id')
                    ->leftjoin('uicheck_user_accesses as uua', 'uua.uicheck_id', '=', 'uichecks.id')
                    ->where('uua.user_id', "=", \Auth::user()->id)
                    ->where('site_developments.is_ui', 1)
                    ->where('uichecks.id', '>', 0)
                    ->select(
                        'site_development_categories.*',
                        'site_developments.id AS site_id',
                        'site_developments.website_id',
                        "uichecks.id AS uicheck_id",
                        "uichecks.issue",
                        "uichecks.website_id AS websiteid",
                        "uichecks.uicheck_type_id",
                        "uua.user_id as accessuser",
                        "uichecks.dev_status_id",
                        "uichecks.admin_status_id"
                    );
            }

            //->where('site_development_categories.id','site_developments.site_development_category_id');
            if ($s = request('category_name')) {
                $q = $q->where('uichecks.website_id', $s);
            }
            if ($s = request('sub_category_name')) {
                $q = $q->where('site_development_categories.id', $s);
            }
            $q->groupBy('site_development_categories.id');
            // dd($q->toSql());

            // select 
            //     `site_development_categories`.*, 
            //     `site_developments`.`id` as `site_id`, 
            //     `site_developments`.`website_id`, 
            //     `uichecks`.`id` as `uicheck_id`, 
            //     `uichecks`.`issue`, 
            //     `uichecks`.`website_id` as `websiteid`, 
            //     `uichecks`.`uicheck_type_id`, 
            //     `uua`.`user_id` as `accessuser`, 
            //     `uichecks`.`dev_status_id`, 
            //     `uichecks`.`admin_status_id` 
            // from `site_development_categories` 
            // inner join `site_developments` on `site_development_categories`.`id` = `site_developments`.`site_development_category_id` 
            // left join `uichecks` on `uichecks`.`site_development_category_id` = `site_development_categories`.`id` 
            // left join `uicheck_user_accesses` as `uua` on `uua`.`uicheck_id` = `uichecks`.`id` 
            // where 
            //     `site_developments`.`is_ui` = ? group by `site_development_categories`.`id`

            return datatables()->eloquent($q)->toJson();
        } else {
            $data = array();
            $data['search_website'] = request('store_webs', '');
            $data['search_category'] = request('categories', '');
            $data['site_development_status_id'] = request('site_development_status_id', '');

            $data['all_store_websites'] = StoreWebsite::orderBy('title')->get(['id', 'title', 'website']);
            $data['allTypes'] = UicheckType::orderBy('name')->pluck("name", "id")->toArray();
            $data['allStatus'] = SiteDevelopmentStatus::orderBy('name')->pluck("name", "id")->toArray();
            $store_websites = StoreWebsite::select('store_websites.*')->join('site_developments', 'store_websites.id', '=', 'site_developments.website_id');
            if ($data['search_website'] != '') {
                $store_websites =  $store_websites->where('store_websites.id', $data['search_website']);
            }
            $data['store_websites'] =  $store_websites->where('is_ui', 1)->groupBy('store_websites.id')->get();
            $data['allUsers'] = User::query()
                ->join('role_user', 'role_user.user_id', 'users.id')
                ->join('roles', 'roles.id', 'role_user.role_id')
                ->where('roles.name', 'Developer')
                ->pluck('users.name', 'users.id')->toArray();

            $data['log_user_id'] = \Auth::user()->id ?? '';

            $q = SiteDevelopmentCategory::query()
                ->join('site_developments', 'site_development_categories.id', '=', 'site_developments.site_development_category_id')
                ->leftjoin('uichecks', 'uichecks.site_development_category_id', '=', 'site_development_categories.id')
                ->select(
                    'site_development_categories.*',
                    'site_developments.id AS site_id',
                    'site_developments.website_id',
                    "uichecks.id AS uicheck_id"
                )
                ->where('uichecks.id', '>', 0);

            // ->where('site_developments.is_ui', 1);

            //->where('site_development_categories.id','site_developments.site_development_category_id');
            if ($data['search_website'] != '') {
                $q = $q->where('uichecks.website_id', $data['store_websites'][0]->id);
            }
            if ($data['search_category'] != '') {
                $q = $q->where('site_development_categories.id',  $data['search_category']);
            }
            $q->groupBy('site_development_categories.id');
            $q->orderBy('site_development_categories.title');
            $data['site_development_categories'] = $q->pluck('site_development_categories.title', 'site_development_categories.id')->toArray();

            // echo '<pre>';
            // print_r($data);
            // exit;
            return view('uicheck.index', $data);


            
        }
    }

    public function access(Request $request) {
        $check = UicheckUserAccess::where("uicheck_id", $request->uicheck_id)->first();
        if (!is_null($check)) {
            $access = UicheckUserAccess::find($check->id);
            $access->delete();
        }
        $array = array(
            "user_id" => $request->id,
            "uicheck_id" => $request->uicheck_id
        );
        UicheckUserAccess::create($array);
        return response()->json(['code' => 200, 'message' => 'Permission Given!!!']);
    }

    public function typeSave(Request $request) {
        $array = array(
            "uicheck_type_id" => $request->type
        );
        Uicheck::where("id", $request->uicheck_id)->update($array);
        return response()->json(['code' => 200, 'message' => 'Type Updated!!!']);
    }

    public function upload_document(Request $request) {
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
                if ($request->file('files')) {
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
            } else {
                return response()->json(["code" => 500, "error" => "Oops, Please fillup required fields"]);
            }
        } else {
            return response()->json(["code" => 500, "error" => "Oops, Please fillup required fields"]);
        }
    }

    public function getDocument(Request $request) {
        $id = $request->get("id", 0);

        if ($id > 0) {
            $devDocuments = UicheckAttachement::with("user", "uicheck")->where("uicheck_id", $id)->latest()->get();
            $html = view('uicheck.ajax.document-list', compact("devDocuments"))->render();
            return response()->json(["code" => 200, "data" => $html]);
        } else {
            return response()->json(["code" => 500, "error" => "Oops, id is required field"]);
        }
    }

    public function typeStore(Request $request) {
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
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        try {
            $uicheck = Uicheck::find($request->id);
            if (empty($uicheck))
                $uicheck = new Uicheck();

            $uicheck->site_development_id = $request->site_development_id;
            $uicheck->site_development_category_id = $request->category;

            if ($request->website_id)
                $uicheck->website_id = $request->website_id;
            if ($request->issue) {
                if ($request->issue != $uicheck->issue) {
                    $this->CreateUiissueHistoryLog($request, $uicheck);
                }
                $uicheck->issue = $request->issue;
            }
            if ($request->developer_status) {
                if ($request->developer_status != $uicheck->developer_status) {
                    $this->CreateUiDeveloperStatusHistoryLog($request, $uicheck);
                }
                $uicheck->dev_status_id = $request->developer_status;
            }
            if ($request->admin_status) {
                if ($request->admin_status != $uicheck->admin_status_id) {
                    $this->createUiAdminStatusHistoryLog($request, $uicheck);
                }
                $uicheck->admin_status_id = $request->admin_status;
            }


            $uicheck->save();
            return response()->json(['code' => 200, 'data' => $uicheck, 'message' => 'Updated successfully!!!']);
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
    public function CreateUiAdminStatusHistoryLog(Request $request, $uicheck) {
        $adminStatusLog = new UiAdminStatusHistoryLog();
        $adminStatusLog->user_id = \Auth::user()->id;
        $adminStatusLog->uichecks_id = $request->id;
        $adminStatusLog->old_status_id = $uicheck->admin_status_id;
        $adminStatusLog->status_id = $request->admin_status;
        $adminStatusLog->save();
    }

    public function getUiAdminStatusHistoryLog(Request $request) {
        $adminStatusLog = UiAdminStatusHistoryLog::select("ui_admin_status_history_logs.*", "users.name as userName", "site_development_statuses.name AS dev_status", "old_stat.name AS old_name")
            ->leftJoin("users", "users.id", "ui_admin_status_history_logs.user_id")
            ->leftJoin("site_development_statuses", "site_development_statuses.id", "ui_admin_status_history_logs.status_id")
            ->leftJoin("site_development_statuses as old_stat", "old_stat.id", "ui_admin_status_history_logs.old_status_id")
            ->where('ui_admin_status_history_logs.uichecks_id', $request->id)
            ->orderBy('ui_admin_status_history_logs.id', "DESC")
            ->get();
        $html = "";
        foreach ($adminStatusLog as $adminStatus) {
            $html .=  '<tr>';
            $html .=  '<td>' . $adminStatus->id . '</td>';
            $html .=  '<td>' . $adminStatus->userName . '</td>';
            $html .=  '<td>' . $adminStatus->old_name . '</td>';
            $html .=  '<td>' . $adminStatus->dev_status . '</td>';

            $html .=  '</tr>';
        }
        if ($html != "") {
            return response()->json(['code' => 200, 'html' => $html, 'message' => 'Listed successfully!!!']);
        } else {
            return response()->json(['code' => 500, 'message' => "data not found"]);
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\CreateUiDeveloperStatusHistoryLog  $createUiDeveloperStatusHistoryLog
     * @return \Illuminate\Http\Response
     */
    public function CreateUiDeveloperStatusHistoryLog(Request $request, $uicheck) {
        $devStatusLog = new UiDeveloperStatusHistoryLog();
        $devStatusLog->user_id = \Auth::user()->id;
        $devStatusLog->uichecks_id = $request->id;
        $devStatusLog->old_status_id = $uicheck->dev_status_id;
        $devStatusLog->status_id = $request->developer_status;
        $devStatusLog->save();
    }

    public function getUiDeveloperStatusHistoryLog(Request $request) {
        $adminStatusLog = UiDeveloperStatusHistoryLog::select("ui_developer_status_history_logs.*", "users.name as userName", "site_development_statuses.name AS dev_status", "old_stat.name AS old_name")
            ->leftJoin("users", "users.id", "ui_developer_status_history_logs.user_id")
            ->leftJoin("site_development_statuses", "site_development_statuses.id", "ui_developer_status_history_logs.status_id")
            ->leftJoin("site_development_statuses as old_stat", "old_stat.id", "ui_developer_status_history_logs.old_status_id")
            ->where('ui_developer_status_history_logs.uichecks_id', $request->id)
            ->orderBy('ui_developer_status_history_logs.id', "DESC")
            ->get();

        $html = "";
        foreach ($adminStatusLog as $adminStatus) {
            $html .=  '<tr>';
            $html .=  '<td>' . $adminStatus->id . '</td>';
            $html .=  '<td>' . $adminStatus->userName . '</td>';
            $html .=  '<td>' . $adminStatus->old_name . '</td>';
            $html .=  '<td>' . $adminStatus->dev_status . '</td>';

            $html .=  '</tr>';
        }
        if ($html != "") {
            return response()->json(['code' => 200, 'html' => $html, 'message' => 'Listed successfully!!!']);
        } else {
            return response()->json(['code' => 500, 'message' => "data not found"]);
        }
    }

    public function CreateUiissueHistoryLog(Request $request, $uicheck) {
        $devStatusLog = new UiCheckIssueHistoryLog();
        $devStatusLog->user_id = \Auth::user()->id;
        $devStatusLog->uichecks_id = $request->id;
        $devStatusLog->old_issue = $uicheck->issue;
        $devStatusLog->issue = $request->issue;
        $devStatusLog->save();
    }

    public function getUiIssueHistoryLog(Request $request) {
        try {
            $getIssueLog = UiCheckIssueHistoryLog::select("ui_check_issue_history_logs.*", "users.name as userName")
                ->leftJoin("users", "users.id", "ui_check_issue_history_logs.user_id")
                ->where('ui_check_issue_history_logs.uichecks_id', $request->id)
                ->orderBy('ui_check_issue_history_logs.id', "DESC")
                ->get();

            $html = "";
            foreach ($getIssueLog as $issueLog) {
                $html .=  '<tr>';
                $html .=  '<td>' . $issueLog->id . '</td>';
                $html .=  '<td>' . $issueLog->userName . '</td>';
                $html .=  '<td>' . $issueLog->old_issue . '</td>';
                $html .=  '<td>' . $issueLog->issue . '</td>';

                $html .=  '</tr>';
            }
            return response()->json(['code' => 200, 'html' => $html, 'message' => 'Listed successfully!!!']);
        } catch (\Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function getUiCheckMessageHistoryLog(Request $request) {
        try {
            $getMessageLog = UiCheckCommunication::select("ui_check_communications.*", "users.name as userName")
                ->leftJoin("users", "users.id", "ui_check_communications.user_id")
                ->where('ui_check_communications.uichecks_id', $request->id)
                ->orderBy('ui_check_communications.id', "DESC")
                ->get();

            $html = "";
            foreach ($getMessageLog as $messageLog) {
                $html .=  '<tr>';
                $html .=  '<td>' . $messageLog->id . '</td>';
                $html .=  '<td>' . $messageLog->userName . '</td>';
                $html .=  '<td>' . $messageLog->message . '</td>';
                $html .=  '<td>' . $messageLog->created_at . '</td>';
                $html .=  '</tr>';
            }
            return response()->json(['code' => 200, 'html' => $html, 'message' => 'Listed successfully!!!']);
        } catch (\Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function CreateUiMessageHistoryLog(Request $request) {
        $messageLog = new UiCheckCommunication();
        $messageLog->user_id = \Auth::user()->id;
        $messageLog->uichecks_id = $request->id;
        $messageLog->message = $request->message;
        $messageLog->save();
        return response()->json(['code' => 200, 'message' => 'Message saved successfully!!!']);
    }
}
