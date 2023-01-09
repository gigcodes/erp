<?php

namespace App\Http\Controllers;

use App\BugEnvironment;
use App\BugSeveritiesHistory;
use App\BugSeverity;
use App\BugStatus;
use App\BugStatusHistory;
use App\BugTracker;
use App\BugTrackerHistory;
use App\BugType;
use App\BugUserHistory;
use App\ChatMessage;
use App\DeveloperTask;
use App\SiteDevelopment;
use App\SiteDevelopmentCategory;
use App\StoreWebsite;
use App\Task;
use App\TestCase;
use App\TestCaseHistory;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BugTrackingController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Bug Tracking';

        $bugStatuses = BugStatus::get();
        $bugEnvironments = BugEnvironment::get();
        $bugSeveritys = BugSeverity::get();
        $bugTypes = BugType::get();
        $users = User::get();
        $filterCategories = SiteDevelopmentCategory::orderBy('title')->pluck('title')->toArray();
        $filterWebsites = StoreWebsite::orderBy('website')->get();

        return view(
            'bug-tracking.index', [
                'title' => $title,
                'bugTypes' => $bugTypes,
                'bugEnvironments' => $bugEnvironments,
                'bugSeveritys' => $bugSeveritys,
                'bugStatuses' => $bugStatuses,
                'filterCategories' => $filterCategories,
                'users' => $users,
                'allUsers' => $users,
                'filterWebsites' => $filterWebsites,
            ]
        );
    }

    public function records(Request $request)
    {
        if (Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Lead Tester')) {
            if (request('sort') == '1') {
                $records = BugTracker::with(['chatlatest'])->Select('bug_trackers.*')->leftJoin('chat_messages', 'chat_messages.bug_id', '=', 'bug_trackers.id')->where('bug_id', '!=', '')->orderBy('chat_messages.created_at', 'desc');
            } else {
                $records = BugTracker::orderBy('id', 'desc');
            }
        } else {
            if (request('sort') == '1') {
                $records = BugTracker::with(['chatlatest'])->Select('bug_trackers.*')->leftJoin('chat_messages', 'chat_messages.bug_id', '=', 'bug_trackers.id')->where('bug_id', '!=', '')->where(
                    function ($query) {
                        $query->where('assign_to', Auth::user()->id);
                        $query->orWhere('created_by', Auth::user()->id);
                    }
                )->orderBy('chat_messages.created_at', 'desc');
            } else {
                $records = BugTracker::where(
                    function ($query) {
                        $query->where('assign_to', Auth::user()->id);
                        $query->orWhere('created_by', Auth::user()->id);
                    }
                )->orderBy('id', 'desc');
            }
        }

        if ($keyword = request('bug_id')) {
            $multiple_keyword = explode(',', $keyword);

            $records = $records->whereIn('id', $multiple_keyword);
        }

        if ($keyword = request('summary')) {
            $records = $records->where(
                function ($q) use ($keyword) {
                    $q->where('summary', 'LIKE', "%$keyword%");
                }
            );
        }
        if ($keyword = request('bug_type')) {
            $records = $records->WhereIn('bug_type_id', $keyword);
        }
        if ($keyword = request('bug_enviornment')) {
            $records = $records->WhereIn('bug_environment_id', $keyword);
        }
        if ($keyword = request('bug_severity')) {
            $records = $records->WhereIn('bug_severity_id', $keyword);
        }
        if ($keyword = request('created_by')) {
            $records = $records->WhereIn('created_by', $keyword);
        }
        if ($keyword = request('assign_to_user')) {
            $records = $records->WhereIn('assign_to', $keyword);
        }
        if ($keyword = request('bug_status')) {
            $records = $records->WhereIn('bug_status_id', $keyword);
        }
        if ($keyword = request('module_id')) {
            //$records = $records->WhereIn('module_id', 'LIKE', "%$keyword%");
            $records = $records->WhereIn('module_id', $keyword);
        }
        if ($keyword = request('step_to_reproduce')) {
            $records = $records->where(
                function ($q) use ($keyword) {
                    $q->where('step_to_reproduce', 'LIKE', "%$keyword%");
                }
            );
        }
        if ($keyword = request('url')) {
            $records = $records->where(
                function ($q) use ($keyword) {
                    $q->where('url', 'LIKE', "%$keyword%");
                }
            );
        }
        if ($keyword = request('website')) {
            $records = $records->WhereIn('website', $keyword);
        }
        if ($keyword = request('date')) {
            $records = $records->where(
                function ($q) use ($keyword) {
                    $q->whereDate('created_at', $keyword);
                }
            );
        }

        $records_count = $records->count();
        $records = $records->take(10)->get();

        $records = $records->map(
            function ($bug) {
                $bug->bug_type_id_val = $bug->bug_type_id;
                $bug->website_id_val = $bug->website;
                $bug->bug_type_id = BugType::where('id', $bug->bug_type_id)->value('name');
                $bug->bug_environment_id = BugEnvironment::where('id', $bug->bug_environment_id)->value('name');
                $bug->created_by = User::where('id', $bug->created_by)->value('name');
                $bug->created_at_date = \Carbon\Carbon::parse($bug->created_at)->format('d-m-Y');
                //            $bug->bug_severity_id = BugSeverity::where('id',$bug->bug_severity_id)->value('name');
                $bug->bug_color = BugStatus::where('id', $bug->bug_status_id)->value('bug_color');
                $bug->bug_history = BugTrackerHistory::where('bug_id', $bug->id)->get();
                $bug->website = StoreWebsite::where('id', $bug->website)->value('title');
                $bug->summary_short = Str::limit($bug->summary, 10, '..');
                $bug->step_to_reproduce_short = Str::limit($bug->step_to_reproduce, 60, '..');
                $bug->url_short = Str::limit($bug->url, 5, '..');
                $last_chat_res = ChatMessage::select('message')->where('bug_id', $bug->id)->orderBy('id', 'desc')->limit(1)->get()->toArray();
                $last_chat_message = '';
                if (isset($last_chat_res[0]['message']) && $last_chat_res[0]['message'] != '') {
                    $last_chat_message = $last_chat_res[0]['message'];
                }
                $bug->last_chat_message_short = substr($last_chat_message, 0, 28);
                $bug->last_chat_message_long = $last_chat_message;

                return $bug;
            }
        );

        return response()->json(
            [
                'code' => 200,
                'data' => $records,
                'total' => $records_count,
            ]
        );
    }

    public function recordTrackingAjax(Request $request)
    {
        $title = 'Bug Tracking';

        $page = $_REQUEST['page'];

        $page = $page * 10;

        $bugStatuses = BugStatus::get();
        $bugEnvironments = BugEnvironment::get();
        $bugSeveritys = BugSeverity::get();
        $bugTypes = BugType::get();
        $users = User::get();
        $filterCategories = SiteDevelopmentCategory::orderBy('title')->pluck('title')->toArray();
        $filterWebsites = StoreWebsite::orderBy('website')->get();

        if (Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Lead Tester')) {
            if (request('sort') == '1') {
                $records = BugTracker::with(['chatlatest'])->Select('bug_trackers.*')->leftJoin('chat_messages', 'chat_messages.bug_id', '=', 'bug_trackers.id')->where('bug_id', '!=', '')->orderBy('chat_messages.created_at', 'desc')->offset($page)->limit(10);
            } else {
                $records = BugTracker::orderBy('id', 'desc')->offset($page)->limit(10);
            }
        } else {
            if (request('sort') == '1') {
                $records = BugTracker::with(['chatlatest'])->Select('bug_trackers.*')->leftJoin('chat_messages', 'chat_messages.bug_id', '=', 'bug_trackers.id')->where('bug_id', '!=', '')->where(
                    function ($query) {
                        $query->where('assign_to', Auth::user()->id);
                        $query->orWhere('created_by', Auth::user()->id);
                    }
                )->orderBy('chat_messages.created_at', 'desc')->offset($page)->limit(10);
            } else {
                $records = BugTracker::where(
                    function ($query) {
                        $query->where('assign_to', Auth::user()->id);
                        $query->orWhere('created_by', Auth::user()->id);
                    }
                )->orderBy('id', 'desc')->offset($page)->limit(10);
            }
        }

        if ($keyword = request('bug_id')) {
            $records = $records->where('id', $keyword);
        }

        if ($keyword = request('summary')) {
            $records = $records->where(
                function ($q) use ($keyword) {
                    $q->where('summary', 'LIKE', "%$keyword%");
                }
            );
        }
        if ($keyword = request('bug_type')) {
            $records = $records->WhereIn('bug_type_id', $keyword);
        }
        if ($keyword = request('bug_enviornment')) {
            $records = $records->WhereIn('bug_environment_id', $keyword);
        }
        if ($keyword = request('bug_severity')) {
            $records = $records->WhereIn('bug_severity_id', $keyword);
        }
        if ($keyword = request('created_by')) {
            $records = $records->WhereIn('created_by', $keyword);
        }
        if ($keyword = request('assign_to_user')) {
            $records = $records->WhereIn('assign_to', $keyword);
        }
        if ($keyword = request('bug_status')) {
            $records = $records->WhereIn('bug_status_id', $keyword);
        }
        if ($keyword = request('module_id')) {
            // $records = $records->WhereIn('module_id', 'LIKE', "%$keyword%");
            $records = $records->WhereIn('module_id', $keyword);
        }
        if ($keyword = request('step_to_reproduce')) {
            $records = $records->where(
                function ($q) use ($keyword) {
                    $q->where('step_to_reproduce', 'LIKE', "%$keyword%");
                }
            );
        }
        if ($keyword = request('url')) {
            $records = $records->where(
                function ($q) use ($keyword) {
                    $q->where('url', 'LIKE', "%$keyword%");
                }
            );
        }
        if ($keyword = request('website')) {
            $records = $records->WhereIn('website', $keyword);
        }
        if ($keyword = request('date')) {
            $records = $records->where(
                function ($q) use ($keyword) {
                    $q->whereDate('created_at', $keyword);
                }
            );
        }
        $records = $records->get();

        $records = $records->map(
            function ($bug) {
                $bug->bug_type_id_val = $bug->bug_type_id;
                $bug->website_id_val = $bug->website;
                $bug->bug_type_id = BugType::where('id', $bug->bug_type_id)->value('name');
                $bug->bug_environment_id = BugEnvironment::where('id', $bug->bug_environment_id)->value('name');
                $bug->created_by = User::where('id', $bug->created_by)->value('name');
                $bug->created_at_date = \Carbon\Carbon::parse($bug->created_at)->format('d-m-Y');
                //            $bug->bug_severity_id = BugSeverity::where('id',$bug->bug_severity_id)->value('name');
                $bug->bug_color = BugStatus::where('id', $bug->bug_status_id)->value('bug_color');
                $bug->bug_history = BugTrackerHistory::where('bug_id', $bug->id)->get();
                $bug->website = StoreWebsite::where('id', $bug->website)->value('title');
                $bug->summary_short = Str::limit($bug->summary, 10, '..');
                $bug->step_to_reproduce_short = Str::limit($bug->step_to_reproduce, 60, '..');
                $bug->url_short = Str::limit($bug->url, 5, '..');
                $last_chat_res = ChatMessage::select('message')->where('bug_id', $bug->id)->orderBy('id', 'desc')->limit(1)->get()->toArray();
                $last_chat_message = '';
                if (isset($last_chat_res[0]['message']) && $last_chat_res[0]['message'] != '') {
                    $last_chat_message = $last_chat_res[0]['message'];
                }
                $bug->last_chat_message_short = substr($last_chat_message, 0, 28);
                $bug->last_chat_message_long = $last_chat_message;

                return $bug;
            }
        );

        // return response()->json(['code' => 200, 'data' => $records, 'total' => count($records)]);

        return view(
            'bug-tracking.index-ajax', [
                'title' => $title,
                'bugTypes' => $bugTypes,
                'bugEnvironments' => $bugEnvironments,
                'bugSeveritys' => $bugSeveritys,
                'bugStatuses' => $bugStatuses,
                'filterCategories' => $filterCategories,
                'users' => $users,
                'allUsers' => $users,
                'filterWebsites' => $filterWebsites,
                'data' => $records,
                'total' => count($records),
            ]
        );
    }

    public function create()
    {
        $bugStatuses = BugStatus::get();
        $bugEnvironments = BugEnvironment::get();
        $bugSeveritys = BugSeverity::get();
        $bugTypes = BugType::get();
        $users = User::get();
        $filterCategories = SiteDevelopmentCategory::orderBy('title')->pluck('title')->toArray();
        $filterWebsites = StoreWebsite::orderBy('website')->pluck('website')->toArray();

        return view('bug-tracking.create', compact('bugStatuses', 'bugTypes', 'bugEnvironments', 'bugSeveritys', 'users', 'filterCategories', 'filterWebsites'));
    }

    public function edit($id)
    {
        $bugTracker = BugTracker::findorFail($id);
        $bugStatuses = BugStatus::get();
        $bugEnvironments = BugEnvironment::get();
        $bugSeveritys = BugSeverity::get();
        $bugTypes = BugType::get();
        $users = User::get();
        $filterCategories = SiteDevelopmentCategory::orderBy('title')->pluck('title')->toArray();
        $filterWebsites = StoreWebsite::orderBy('website')->pluck('website')->toArray();
        if ($bugTracker) {
            return response()->json(
                [
                    'code' => 200,
                    'data' => $bugTracker,
                    'bugTypes' => $bugTypes,
                    'bugEnvironments' => $bugEnvironments,
                    'bugSeveritys' => $bugSeveritys,
                    'bugStatuses' => $bugStatuses,
                    'filterCategories' => $filterCategories,
                    'users' => $users,
                    'filterWebsites' => $filterWebsites,
                ]
            );
        }

        return response()->json(
            [
                'code' => 500,
                'error' => 'Wrong bug tracking id!',
            ]
        );
    }

    public function status(Request $request)
    {
        $status = $request->all();
        $validator = Validator::make(
            $status, [
                'name' => 'required|string',
            ]
        );
        if ($validator->fails()) {
            return response()->json(
                [
                    'code' => 500,
                    'error' => 'Name is required',
                ]
            );
        }
        $data = $request->except('_token');
        $records = BugStatus::create($data);

        return response()->json(
            [
                'code' => 200,
                'data' => $records,
            ]
        );
    }

    public function statuscolor(Request $request)
    {
        $status_color = $request->all();
        $data = $request->except('_token');
        foreach ($status_color['color_name'] as $key => $value) {
            $bugstatus = BugStatus::find($key);
            $bugstatus->bug_color = $value;
            $bugstatus->save();
        }

        $records = $bugstatus;

        return response()->json(
            [
                'code' => 200,
                'data' => $records,
            ]
        );
    }

    public function environment(Request $request)
    {
        $environment = $request->all();
        $validator = Validator::make(
            $environment, [
                'name' => 'required|string',
            ]
        );
        if ($validator->fails()) {
            return response()->json(
                [
                    'code' => 500,
                    'error' => 'Name is required',
                ]
            );
        }
        $data = $request->except('_token');
        $records = BugEnvironment::create($data);

        return response()->json(
            [
                'code' => 200,
                'data' => $records,
            ]
        );
    }

    public function type(Request $request)
    {
        $type = $request->all();
        $validator = Validator::make(
            $type, [
                'name' => 'required|string',
            ]
        );
        if ($validator->fails()) {
            return response()->json(
                [
                    'code' => 500,
                    'error' => 'Name is required',
                ]
            );
        }
        $data = $request->except('_token');
        $records = BugType::create($data);

        return response()->json(
            [
                'code' => 200,
                'data' => $records,
            ]
        );
    }

    public function severity(Request $request)
    {
        $severity = $request->all();
        $validator = Validator::make(
            $severity, [
                'name' => 'required|string',
            ]
        );
        if ($validator->fails()) {
            return response()->json(
                [
                    'code' => 500,
                    'error' => 'Name is required',
                ]
            );
        }
        $data = $request->except('_token');
        $records = BugSeverity::create($data);

        return response()->json(
            [
                'code' => 200,
                'data' => $records,
            ]
        );
    }

    public function store(Request $request)
    {
        $bug = $request->all();
        $validator = Validator::make(
            $bug, [
                'summary' => 'required|string',
                'step_to_reproduce' => 'required|string',
                'url' => 'required|string',
                'bug_type_id' => 'required|string',
                'bug_environment_id' => 'required|string',
                'assign_to' => 'required|string',
                'bug_severity_id' => 'required|string',
                'bug_status_id' => 'required|string',
                'module_id' => 'required|string',
                'remark' => 'required|string',
                'website' => 'required|string',

            ]
        );

        if ($validator->fails()) {
            $outputString = '';
            $messages = $validator->errors()->getMessages();
            foreach ($messages as $k => $errr) {
                foreach ($errr as $er) {
                    $outputString .= "$k : ".$er.'<br>';
                }
            }

            return redirect()->back()->with('error', $outputString);
        }

        $id = $request->get('id', 0);

        $records = BugTracker::find($id);

        if (! $records) {
            $records = new BugTracker();
        }
        $bug['created_by'] = \Auth::user()->id;
        $bug['summary'] = str_replace("\n", '<br/>', $bug['summary']);
        $bug['step_to_reproduce'] = str_replace("\n", '<br/>', $bug['step_to_reproduce']);
        $records->fill($bug);

        $records->save();
        $params = ChatMessage::create(
            [
                'user_id' => \Auth::user()->id,
                'bug_id' => $records->id,
                'sent_to_user_id' => ($records->assign_to != \Auth::user()->id) ? $records->assign_to : \Auth::user()->id,
                'approved' => '1',
                'status' => '2',
                'message' => $records->remark,
            ]
        );
        $bug['bug_id'] = $records->id;
        $bug['updated_by'] = \Auth::user()->id;
        $userHistory = [
            'bug_id' => $records->id,
            'new_user' => $request->assign_to,
            'updated_by' => \Auth::user()->id,
        ];
        $statusHistory = [
            'bug_id' => $records->id,
            'new_status' => $request->assign_to,
            'updated_by' => \Auth::user()->id,
        ];

        BugUserHistory::create($userHistory);
        BugStatusHistory::create($statusHistory);

        $severity_his = [
            'assign_to' => $request->assign_to,
            'severity_id' => $request->bug_severity_id,
            'bug_id' => $records->id,
            'updated_by' => \Auth::user()->id,
        ];
        BugSeveritiesHistory::create($severity_his);

        $bugTrackerHistory = BugTrackerHistory::create($bug);

        return redirect()->back();
    }

    public function destroy(BugTracker $bugTracker, Request $request)
    {
        try {
            $bug = BugTracker::where('id', '=', $request->id)->delete();
            $bugTrackerHistory = BugTrackerHistory::where('bug_id', '=', $request->id)->delete();

            return response()->json(
                [
                    'code' => 200,
                    'data' => $bug,
                    'message' => 'Deleted successfully!!!',
                ]
            );
        } catch(\Exception $e) {
            $msg = $e->getMessage();
            \Log::error('Bug Tracker Request Delete Error => '.json_decode($e).' #id #'.$request->id ?? '');
            $this->BugErrorLog($request->id ?? '', 'Bug Tracker Request Delete Error', $msg, 'bug_tracker');

            return response()->json(
                [
                    'code' => 500,
                    'message' => $msg,
                ]
            );
        }
    }

    public function update(Request $request)
    {
        $this->validate(
            $request, [
                'summary' => 'required|string',
                'step_to_reproduce' => 'required|string',
                'url' => 'required|string',
                'bug_type_id' => 'required|string',
                'bug_environment_id' => 'required|string',
                'assign_to' => 'required|string',
                'bug_severity_id' => 'required|string',
                'bug_status_id' => 'required|string',
                'module_id' => 'required|string',
                'remark' => 'required|string',
                'website' => 'required|string',

            ]
        );

        $data = $request->except('_token', 'id');
        $bug = BugTracker::where('id', $request->id)->first();

        $old_severity_id = $bug->bug_severity_id;

        $data['created_by'] = \Auth::user()->id;
        $bug['updated_by'] = \Auth::user()->id;
        $userHistory['old_user'] = $bug->assign_to;
        $statusHistory['old_status'] = $bug->bug_status_id;

        $params = ChatMessage::create(
            [
                'user_id' => \Auth::user()->id,
                'bug_id' => $bug->id,
                'sent_to_user_id' => ($request->assign_to != \Auth::user()->id) ? $request->assign_to : \Auth::user()->id,
                'approved' => '1',
                'status' => '2',
                'message' => $request->remark,
            ]
        );
        $data['summary'] = str_replace("\n", '<br/>', $request->summary);
        $data['step_to_reproduce'] = str_replace("\n", '<br/>', $request->step_to_reproduce);

        if ($bug->test_case_id) {
            $testCase = new TestCase;
            $testCase->module_id = $request->module_id;
            $testCase->step_to_reproduce = $data['step_to_reproduce'];
            $testCase->expected_result = $request->expected_result;
            $testCase->website = $request->website;
            $testCase->assign_to = $request->assign_to;
            $testCase->created_by = $data['created_by'];
            $testCase->bug_id = $request->id;
            $testCase->save();

            $testCaseHistory = new TestCaseHistory();
            $testCaseHistory->module_id = $request->module_id;
            $testCaseHistory->step_to_reproduce = $data['step_to_reproduce'];
            $testCaseHistory->expected_result = $request->expected_result;
            $testCaseHistory->website = $request->website;
            $testCaseHistory->assign_to = $request->assign_to;
            $testCaseHistory->created_by = $data['created_by'];
            $testCaseHistory->bug_id = $request->id;
            $testCaseHistory->save();
        }

        if ($bug->bug_status_id == '7') {
            $data['assign_to'] = $bug->created_by;
        }

        $bug->update($data);
        $data['bug_id'] = $request->id;
        BugTrackerHistory::create($data);
        $userHistory = [
            'bug_id' => $request->id,
            'new_user' => $request->assign_to,
            'updated_by' => \Auth::user()->id,
        ];
        $statusHistory = [
            'bug_id' => $request->id,
            'new_status' => $request->bug_status_id,
            'updated_by' => \Auth::user()->id,
        ];
        BugUserHistory::create($userHistory);
        BugStatusHistory::create($statusHistory);

        $severity_his = [
            'assign_to' => $request->assign_to,
            'old_severity_id' => $old_severity_id,
            'severity_id' => $request->bug_severity_id,
            'bug_id' => $request->id,
            'updated_by' => \Auth::user()->id,
        ];
        BugSeveritiesHistory::create($severity_his);

        if ($request->bug_status_id == 3 || $request->bug_status_id == 7) {
            $Task = Task::where('task_bug_ids', $request->id)->first();
            if (count((array) $Task) > 0) {
                if ($request->bug_status_id == 3) {
                    $Task->status = 22;
                } elseif ($request->bug_status_id == 7) {
                    $Task->status = 15;
                }
                $Task->save();
            }
        }

        return redirect()->route('bug-tracking.index')->with('success', 'You have successfully updated a Bug Tracker!');
    }

    public function bugHistory($id)
    {
        $bugHistory = BugTrackerHistory::where('bug_id', $id)->get();
        $bugHistory = $bugHistory->map(
            function ($bug) {

                $bug_type_id =  BugType::where('id', $bug->bug_type_id)->value('name');
                if(isset($bug_type_id) && $bug_type_id =='null') {
                    $bug_type_id = '-';
                }                
                $bug_environment_id = BugEnvironment::where('id', $bug->bug_environment_id)->value('name').' '.$bug->bug_environment_ver;
                if(!isset($bug_environment_id) ) {
                    $bug_environment_id = '-';
                }
                $assign_to = User::where('id', $bug->assign_to)->value('name');
                if(!isset($assign_to)) {
                    $assign_to = '-';
                }
                $updated_by = User::where('id', $bug->updated_by)->value('name');
                if(!isset($updated_by)) {
                    $updated_by = '-';
                }
                $bug_severity_id = BugSeverity::where('id', $bug->bug_severity_id)->value('name');
                if(!isset($bug_severity_id)) {
                    $bug_severity_id = '-';
                }
                $bug_status_id = BugStatus::where('id', $bug->bug_status_id)->value('name');
                if(!isset($bug_status_id)) {
                    $bug_status_id = '-';
                }
                $bug_history = BugTrackerHistory::where('bug_id', $bug->id)->get();
                if(!isset($bug_history)) {
                    $bug_history = '-';
                }


                $bug->bug_type_id = $bug_type_id;
                $bug->bug_environment_id = $bug_environment_id;
                $bug->assign_to = $assign_to;
                $bug->updated_by = $updated_by;
                $bug->bug_severity_id = $bug_severity_id;
                $bug->bug_status_id = $bug_status_id;
                $bug->bug_history = $bug_history;

                return $bug;
            }
        );

        return response()->json(
            [
                'code' => 200,
                'data' => $bugHistory,
            ]
        );
    }

    public function userHistory($id)
    {
        $bugUsers = BugUserHistory::where('bug_id', $id)->orderBy('id', 'desc')->get();
        //        dd($bugUsers);
        $bugUsers = $bugUsers->map(
            function ($bug) {
                $bug->new_user = User::where('id', $bug->new_user)->value('name');
                $bug->old_user = User::where('id', $bug->old_user)->value('name');
                $bug->updated_by = User::where('id', $bug->updated_by)->value('name');
                $bug->created_at_date = $bug->created_at;

                return $bug;
            }
        );

        return response()->json(
            [
                'code' => 200,
                'data' => $bugUsers,
            ]
        );
    }

    public function statusHistory($id)
    {
        $bugStatuses = BugStatusHistory::where('bug_id', $id)->orderBy('id', 'desc')->get();

        $bugStatuses = $bugStatuses->map(
            function ($bug) {
                $bug->new_status = BugStatus::where('id', $bug->new_status)->value('name');
                $bug->old_status = BugStatus::where('id', $bug->old_status)->value('name');
                $bug->updated_by = User::where('id', $bug->updated_by)->value('name');

                return $bug;
            }
        );

        return response()->json(
            [
                'code' => 200,
                'data' => $bugStatuses,
            ]
        );
    }

    public function severityHistory($id)
    {
        $bugStatuses = BugSeveritiesHistory::where('bug_id', $id)->orderBy('id', 'desc')->get();

        $bugStatuses = $bugStatuses->map(
            function ($bug) {
                $bug->old_severity_id = BugSeverity::where('id', $bug->old_severity_id)->value('name');
                $bug->assign_to = User::where('id', $bug->assign_to)->value('name');
                $bug->severity_id = BugSeverity::where('id', $bug->severity_id)->value('name');
                $bug->updated_by = User::where('id', $bug->updated_by)->value('name');

                return $bug;
            }
        );

        return response()->json(
            [
                'code' => 200,
                'data' => $bugStatuses,
            ]
        );
    }

    public function assignUser(Request $request)
    {
        $bugTracker = BugTracker::where('id', $request->id)->first();
        $record = [
            'old_user' => $bugTracker->assign_to,
            'new_user' => $request->user_id,
            'bug_id' => $bugTracker->id,
            'updated_by' => \Auth::user()->id,
        ];
        $bugTracker->assign_to = $request->user_id;
        $bugTracker->save();
        $data = [
            'assign_to' => $bugTracker->assign_to,
            'bug_id' => $bugTracker->id,
            'updated_by' => \Auth::user()->id,
        ];

       DB::table('tasks')->where('task_bug_ids', $request->id)->update(['assign_to' => $request->user_id]);

        BugTrackerHistory::create($data);
        BugUserHistory::create($record);

        return response()->json(
            [
                'code' => 200,
                'data' => $data,
            ]
        );
    }

    public function assignUserBulk(Request $request)
    {

        $data = array();
        if(count($request->id)>0) {
            for($i=0;$i<count($request->id);$i++) {
                $chosen_bug_id = $request->id[$i];
                $bugTracker = BugTracker::where('id', $chosen_bug_id)->first();
                $record = [
                    'old_user' => $bugTracker->assign_to,
                    'new_user' => $request->user_id,
                    'bug_id' => $bugTracker->id,
                    'updated_by' => \Auth::user()->id,
                ];
                $bugTracker->assign_to = $request->user_id;
                $bugTracker->save();
              
               DB::table('tasks')->where('task_bug_ids', $chosen_bug_id)->update(['assign_to' => $request->user_id]);

                $data = [
                    'assign_to' => $bugTracker->assign_to,
                    'bug_id' => $bugTracker->id,
                    'updated_by' => \Auth::user()->id,
                ];
                BugTrackerHistory::create($data);
                BugUserHistory::create($record);
    
            }
        }

        return response()->json(
            [
                'code' => 200,
                'data' => $data,
            ]
        );
    }

    public function severityUser(Request $request)
    {
        
        $bugTracker = BugTracker::where('id',  $request->id)->first();
        $old_severity_id = $bugTracker->bug_severity_id;
        $bugTracker->bug_severity_id = $request->severity_id;
        if ($request->status_id == 8) {
            $created_by = $bugTracker->created_by;
            $bugTracker->assign_to = $created_by;
        }

        $bugTracker->save();
        $data = [
            'bug_severity_id' => $bugTracker->bug_severity_id,
            'bug_id' => $bugTracker->id,
            'updated_by' => \Auth::user()->id,
        ];
        BugTrackerHistory::create($data);

        $record = [
            'assign_to' => $bugTracker->assign_to,
            'old_severity_id' => $old_severity_id,
            'severity_id' => $request->severity_id,
            'bug_id' => $bugTracker->id,
            'updated_by' => \Auth::user()->id,
        ];
        BugSeveritiesHistory::create($record);

        return response()->json(
            [
                'code' => 200,
                'data' => $data,
            ]
        );
    }

    public function severityUserBulk(Request $request)
    {
        if(count($request->id)>0) {
            for($i=0;$i<count($request->id);$i++) {
                $chosen_bug_id = $request->id[$i];
                $bugTracker = BugTracker::where('id',  $chosen_bug_id)->first();
                $old_severity_id = $bugTracker->bug_severity_id;
                $bugTracker->bug_severity_id = $request->severity_id;
                if ($request->status_id == 8) {
                    $created_by = $bugTracker->created_by;
                    $bugTracker->assign_to = $created_by;
                }

                $bugTracker->save();
                $data = [
                    'bug_severity_id' => $bugTracker->bug_severity_id,
                    'bug_id' => $bugTracker->id,
                    'updated_by' => \Auth::user()->id,
                ];
                BugTrackerHistory::create($data);

                $record = [
                    'assign_to' => $bugTracker->assign_to,
                    'old_severity_id' => $old_severity_id,
                    'severity_id' => $request->severity_id,
                    'bug_id' => $bugTracker->id,
                    'updated_by' => \Auth::user()->id,
                ];
                BugSeveritiesHistory::create($record);

            }

        }
        return response()->json(
            [
                'code' => 200,
                'data' => $data,
            ]
        );
    }


    public function statusUser(Request $request)
    {
       
        $bugTracker = BugTracker::where('id',$request->id)->first();
        $record = [
            'old_status' => $bugTracker->bug_status_id,
            'new_status' => $request->status_id,
            'bug_id' => $bugTracker->id,
            'updated_by' => \Auth::user()->id,
        ];
        if ($request->status_id == 7) {
            $prev_created_by = $bugTracker->created_by;
            $bugTracker->assign_to = $prev_created_by;
        }

        $bugTracker->bug_status_id = $request->status_id;
        $bugTracker->save();

        if ($request->status_id == 3 || $request->status_id == 7) {
            $Task = Task::where('task_bug_ids', $request->id)->first();
            if (count((array) $Task) > 0) {
                if ($request->status_id == 3) {
                    $Task->status = 22;
                } elseif ($request->status_id == 7) {
                    $Task->status = 15;
                }
                $Task->save();
            }
        }

        $data = [
            'bug_status_id' => $bugTracker->bug_status_id,
            'bug_id' => $bugTracker->id,
            'updated_by' => \Auth::user()->id,
        ];
        BugTrackerHistory::create($data);
        BugStatusHistory::create($record);

        return response()->json(
            [
                'code' => 200,
                'data' => $data,
            ]
        );
    }

    public function statusUserBulk(Request $request)
    {
        if(count($request->id)>0) {
            for($i=0;$i<count($request->id);$i++) {
                $chosen_bug_id = $request->id[$i];
                $bugTracker = BugTracker::where('id',$chosen_bug_id)->first();
                $record = [
                    'old_status' => $bugTracker->bug_status_id,
                    'new_status' => $request->status_id,
                    'bug_id' => $bugTracker->id,
                    'updated_by' => \Auth::user()->id,
                ];
                if ($request->status_id == 7) {
                    $prev_created_by = $bugTracker->created_by;
                    $bugTracker->assign_to = $prev_created_by;
                }

                $bugTracker->bug_status_id = $request->status_id;
                $bugTracker->save();

                if ($request->status_id == 3 || $request->status_id == 7) {
                    $Task = Task::where('task_bug_ids', $chosen_bug_id)->first();
                    if (count((array) $Task) > 0) {
                        if ($request->status_id == 3) {
                            $Task->status = 22;
                        } elseif ($request->status_id == 7) {
                            $Task->status = 15;
                        }
                        $Task->save();
                    }
                }

                $data = [
                    'bug_status_id' => $bugTracker->bug_status_id,
                    'bug_id' => $bugTracker->id,
                    'updated_by' => \Auth::user()->id,
                ];
                BugTrackerHistory::create($data);
                BugStatusHistory::create($record);

            }

        }

        return response()->json(
            [
                'code' => 200,
                'data' => $data,
            ]
        );
    }

    public function sendMessage(Request $request)
    {
        $id = $request->id;
        $user = Auth::user();
        $task = BugTracker::find($request->id);

        $taskdata = $request->message;

        $userid = Auth::id();

        if ($user) {
            $params = ChatMessage::create(
                [
                    //                  'id'      => $id,
                    'user_id' => $userid,
                    'erp_user' => $userid,
                    'bug_id' => $task->id,
                    'sent_to_user_id' => ($task->assign_to != $user->id) ? $task->assign_to : $task->created_by,
                    'sent_to_user_id' => ($task->assign_to != $user->id) ? $task->assign_to : $task->created_by,
                    'approved' => '1',
                    'status' => '2',
                    'message' => $taskdata,
                ]
            );

            if ($params) {
                return response()->json(
                    [
                        'code' => 200,
                        'message' => 'Successfully Send File',
                    ]
                );
            }

            return response()->json(
                [
                    'message' => 'Something Was Wrong',
                ], 500
            );

            return response()->json(['message' => 'Sorry required fields is missing like id , userid'], 500);
        }
    }

    public function communicationData($id)
    {
        $messages = ChatMessage::where('bug_id', $id)->orderBy('id', 'desc')->get();
        $messages = $messages->map(
            function ($message) {
                $message->user_name = 'From '.User::where('id', $message->user_id)->value('name').' to '.User::where('id', $message->send_to_user_id)->value('name').' '.\Carbon\Carbon::parse($message->created_at)->format('Y-m-d H:i A');

                return $message;
            }
        );

        return response()->json(
            [
                'code' => 200,
                'data' => $messages,
            ]
        );
    }

    public function checkbug(Request $request)
    {
        $bug_type_id = $request->bug_type_id;
        $bug_id = $request->bug_id;
        $module_id = $request->module_id;
        $website_id = $request->website_id;
        // $bug_tracker = Task::where("FIND_IN_SET($bug_id,task_bug_ids)")->get()->asArray();
        $bug_tracker = \DB::table('tasks')->select('*')->whereRaw("find_in_set($bug_id,task_bug_ids)")->get();

        return response()->json(
            [
                'code' => 200,
                'data' => count($bug_tracker),
            ]
        );
    }

    public function getWebsiteList(Request $request)
    {
        $bug_type_id = $request->bug_type_id;
        $module_id = $request->module_id;
        $website_id = $request->website_id;
        $bug_tracker = BugTracker::where('bug_type_id', $bug_type_id)->where('module_id', $module_id)->where('website', $website_id)->whereIn(
            'bug_status_id', [
                '1',
                '2',
                '4',
                '5',
                '6',
                '8',
                '9',
                '10',
            ]
        )->get();
        $bug_list = $bug_tracker->toArray();
        $bug_ids = [];
        $website_ids = [];
        $bugs_html = '<table cellpadding="2" cellspacing="2" border="1" style="width:100%"><tr><td style="text-align:center"><b>Action</b></td><td  style="text-align:center"><b>Bug Id</b></td  style="text-align:center"><td  style="text-align:center;"><b>Summary</b></td><td  style="text-align:center;"><b>Assign To</b></td></tr>';
        if (count($bug_list) > 0) {
            for ($i = 0; $i < count($bug_list); $i++) {
                $bug_ids[] = $bug_list[$i]['id'];
                $website_ids[] = $bug_list[$i]['website'];
                $bug_id = $bug_list[$i]['id'];
                $assign_to = $bug_list[$i]['assign_to'];
                $userData = User::where('id', $assign_to)->get()->toArray();
                $name = '-';
                if (count($userData) > 0 && isset($userData[0]['name'])) {
                    $name = $userData[0]['name'];
                }
                $bugs_html .= '<tr><td  style="text-align:center"><input style="height:13px;" type="checkbox" class="cls-checkbox-bugsids" name="chkBugId[]" value="'.$bug_id.'" id="name="chkBugId'.$bug_id.'"  /></td><td  style="text-align:center">'.$bug_id.'</td><td>&nbsp;&nbsp;&nbsp;'.$bug_list[$i]['summary'].'</td><td>&nbsp;&nbsp;&nbsp;'.$name.'</td></tr>';
            }
        }

        $bugs_html .= '</table>';

        $website_ids_val = implode(',', $website_ids);

        $websiteData = StoreWebsite::whereIn('id', $website_ids)->get();

        $websiteCheckbox = '';
        foreach ($websiteData as $website) {
            $websiteCheckbox .= '<div class="col-4 py-1"><div style="float: left;height: auto;margin-right: 6px;"><input style="height:13px;" type="checkbox" name="website_name['.$website->id.']" value="'.$website->title.' - '.$request->cat_title.'"/></div> <div class=""  style="float: left;height: auto;margin-right: 6px;overflow-wrap: anywhere;width: 80%;">'.$website->website.'</div></div>';
        }

        $data['websiteCheckbox'] = $websiteCheckbox;
        $data['bug_ids'] = implode(',', $bug_ids);
        $data['bug_html'] = $bugs_html;

        return response()->json(
            [
                'code' => 200,
                'data' => $data,
                'message' => 'List of website!',
            ]
        );
    }

    public function taskCount($bug_id)
    {
        $model_site_development = SiteDevelopment::where('bug_id', $bug_id)->get()->toArray();

        $site_developement_id = 0;
        if (count($model_site_development) > 0) {
            $site_developement_id = $model_site_development[0]['id'];
        }

        $taskStatistics['Devtask'] = DeveloperTask::where('site_developement_id', $site_developement_id)->where('status', '!=', 'Done')->select();

        $query = DeveloperTask::join('users', 'users.id', 'developer_tasks.assigned_to')->where('site_developement_id', $site_developement_id)->where('status', '!=', 'Done')->select('developer_tasks.id', 'developer_tasks.task as subject', 'developer_tasks.status', 'users.name as assigned_to_name');
        $query = $query->addSelect(DB::raw("'Devtask' as task_type,'developer_task' as message_type"));
        $taskStatistics = $query->get();
        //print_r($taskStatistics);
        $othertask = Task::where('site_developement_id', $site_developement_id)->whereNull('is_completed')->select();
        $query1 = Task::join('users', 'users.id', 'tasks.assign_to')->where('site_developement_id', $site_developement_id)->whereNull('is_completed')->select('tasks.id', 'tasks.task_subject as subject', 'tasks.assign_status', 'users.name as assigned_to_name');
        $query1 = $query1->addSelect(DB::raw("'Othertask' as task_type,'task' as message_type"));
        $othertaskStatistics = $query1->get();
        $merged = $othertaskStatistics->merge($taskStatistics);

        return response()->json(
            [
                'code' => 200,
                'taskStatistics' => $merged,
            ]
        );
    }

    public function website(Request $request)
    {
        $title = 'Bug Tracking Websites';

        $bug_tracker = DB::select(DB::raw("SELECT bug_trackers.website as website_id, store_websites.website as website, store_websites.title as title, count(bug_trackers.id) as bug_count, GROUP_CONCAT(concat(bug_trackers.id,'-',bug_trackers.bug_severity_id)) as bug_ids  FROM bug_trackers left join store_websites on bug_trackers.website = store_websites.id where bug_trackers.website>0 AND bug_trackers.bug_status_id !=3 group by bug_trackers.website"));

        $arr = json_decode(json_encode($bug_tracker), true);

        if (count($arr) > 0) {
            for ($i = 0; $i < count($arr); $i++) {
                $bug_ids = $arr[$i]['bug_ids'];
                $bug_ids_arrays = explode(',', $bug_ids);

                $critical_array = [];
                $high_array = [];
                $medium_array = [];
                $low_array = [];

                if (count($bug_ids_arrays) > 0) {
                    for ($j = 0; $j < count($bug_ids_arrays); $j++) {
                        $single_bug_array = explode('-', $bug_ids_arrays[$j]);

                        if (count($single_bug_array) == 2) {
                            if ($single_bug_array[1] == '1') {
                                $critical_array[] = $single_bug_array[0];
                            } elseif ($single_bug_array[1] == '2') {
                                $high_array[] = $single_bug_array[0];
                            } elseif ($single_bug_array[1] == '3') {
                                $medium_array[] = $single_bug_array[0];
                            } elseif ($single_bug_array[1] == '4') {
                                $low_array[] = $single_bug_array[0];
                            }
                        }
                    }
                }

                $arr[$i]['critical'] = count($critical_array);
                $arr[$i]['high'] = count($high_array);
                $arr[$i]['medium'] = count($medium_array);
                $arr[$i]['low'] = count($low_array);
            }
        }

        return view(
            'bug-tracking.website', [
                'title' => $title,
                'bug_tracker' => $arr,

            ]
        );
    }

    public function websiteHistory(Request $request)
    {
        $serverity_id = 0;
        $bug_id = 0;
        if ($request->servid) {
            $serverity_id = $request->servid;
        }
        if ($request->id) {
            $bug_id = $request->id;
        }
        $bugTracker = BugTracker::where('website', $bug_id)->where('bug_severity_id', $serverity_id)->where('bug_status_id', '!=', 3)->orderBy('id', 'desc')->get();

        $bugTracker = $bugTracker->map(
            function ($bug) {
                $bug->bug_type_id = BugType::where('id', $bug->bug_type_id)->value('name');
                $bug->created_at_date = \Carbon\Carbon::parse($bug->created_at)->format('d-m-Y');
                $bug->summary_short = Str::limit($bug->summary, 40, '..');

                return $bug;
            }
        );

        return response()->json(
            [
                'code' => 200,
                'data' => $bugTracker,
            ]
        );
    }
}
