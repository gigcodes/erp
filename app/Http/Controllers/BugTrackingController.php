<?php

namespace App\Http\Controllers;

use App\BugEnvironment;
use App\BugSeverity;
use App\BugStatus;
use App\BugTracker;
use App\BugTrackerHistory;
use App\BugType;
use App\ChatMessage;
use App\SiteDevelopmentCategory;
use App\StoreWebsite;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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

        return view('bug-tracking.index', [
            'title' => $title,
            'bugTypes' => $bugTypes,
            'bugEnvironments' => $bugEnvironments,
            'bugSeveritys' => $bugSeveritys,
            'bugStatuses' => $bugStatuses,
            'filterCategories' => $filterCategories,
            'users' => $users,
            'filterWebsites' => $filterWebsites,
        ]);
    }

    public function records(Request $request)
    {
        $records = BugTracker::orderBy('id', 'desc');

        if ($keyword = request('summary')) {
            $records = $records->where(function ($q) use ($keyword) {
                $q->where('summary', 'LIKE', "%$keyword%");
            });
        }
        if ($keyword = request('bug_type')) {
            $records = $records->where(function ($q) use ($keyword) {
                $q->where('bug_type_id', $keyword);
            });
        }
        if ($keyword = request('bug_enviornment')) {
            $records = $records->where(function ($q) use ($keyword) {
                $q->where('bug_environment_id', $keyword);
            });
        }
        if ($keyword = request('bug_severity')) {
            $records = $records->where(function ($q) use ($keyword) {
                $q->where('bug_severity_id', $keyword);
            });
        }
        if ($keyword = request('bug_status')) {
            $records = $records->where(function ($q) use ($keyword) {
                $q->where('bug_status_id', $keyword);
            });
        }
        if ($keyword = request('module_id')) {
            $records = $records->where(function ($q) use ($keyword) {
                $q->where('module_id', 'LIKE', "%$keyword%");
            });
        }
        if ($keyword = request('step_to_reproduce')) {
            $records = $records->where(function ($q) use ($keyword) {
                $q->where('step_to_reproduce', 'LIKE', "%$keyword%");
            });
        }
        if ($keyword = request('url')) {
            $records = $records->where(function ($q) use ($keyword) {
                $q->where('url', 'LIKE', "%$keyword%");
            });
        }
        if ($keyword = request('website')) {
            $records = $records->where(function ($q) use ($keyword) {
                $q->where('website', 'LIKE', "%$keyword%");
            });
        }
        if ($keyword = request('date')) {
            $records = $records->where(function ($q) use ($keyword) {
                $q->whereDate('created_at', $keyword);
            });
        }
        $records = $records->get();
        $records = $records->map(function ($bug) {
            $bug->bug_type_id = BugType::where('id', $bug->bug_type_id)->value('name');
            $bug->bug_environment_id = BugEnvironment::where('id', $bug->bug_environment_id)->value('name');
            $bug->created_by = User::where('id', $bug->created_by)->value('name');
            $bug->created_at_date = \Carbon\Carbon::parse($bug->created_at)->format('d-m-Y  H:i');
//            $bug->bug_severity_id = BugSeverity::where('id',$bug->bug_severity_id)->value('name');
//            $bug->bug_status_id = BugStatus::where('id',$bug->bug_status_id)->value('name');
            $bug->bug_history = BugTrackerHistory::where('bug_id', $bug->id)->get();
            $bug->website = StoreWebsite::where('id', $bug->website)->value('title');
            $bug->summary_short = str_limit($bug->summary, 5, '..');
            $bug->step_to_reproduce_short = str_limit($bug->step_to_reproduce, 5, '..');
            $bug->url_short = str_limit($bug->url, 5, '..');

            return $bug;
        });

        return response()->json(['code' => 200, 'data' => $records, 'total' => count($records)]);
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
            return response()->json([
                'code' => 200,
                'data' => $bugTracker,
                'bugTypes' => $bugTypes,
                'bugEnvironments' => $bugEnvironments,
                'bugSeveritys' => $bugSeveritys,
                'bugStatuses' => $bugStatuses,
                'filterCategories' => $filterCategories,
                'users' => $users,
                'filterWebsites' => $filterWebsites, ]
            );
        }

        return response()->json(['code' => 500, 'error' => 'Wrong bug tracking id!']);
    }

    public function status(Request $request)
    {
        $status = $request->all();
        $validator = Validator::make($status, [
            'name' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['code' => 500, 'error' => 'Name is required']);
        }
        $data = $request->except('_token');
        $records = BugStatus::create($data);

        return response()->json(['code' => 200, 'data' => $records]);
    }

   public function environment(Request $request)
   {
       $environment = $request->all();
       $validator = Validator::make($environment, [
           'name' => 'required|string',
       ]);
       if ($validator->fails()) {
           return response()->json(['code' => 500, 'error' => 'Name is required']);
       }
       $data = $request->except('_token');
       $records = BugEnvironment::create($data);

       return response()->json(['code' => 200, 'data' => $records]);
   }

    public function type(Request $request)
    {
        $type = $request->all();
        $validator = Validator::make($type, [
            'name' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['code' => 500, 'error' => 'Name is required']);
        }
        $data = $request->except('_token');
        $records = BugType::create($data);

        return response()->json(['code' => 200, 'data' => $records]);
    }

    public function severity(Request $request)
    {
        $severity = $request->all();
        $validator = Validator::make($severity, [
            'name' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['code' => 500, 'error' => 'Name is required']);
        }
        $data = $request->except('_token');
        $records = BugSeverity::create($data);

        return response()->json(['code' => 200, 'data' => $records]);
    }

    public function store(Request $request)
    {
        $bug = $request->all();
        $validator = Validator::make($bug, [
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

        ]);

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
		$bug['summary'] = str_replace("\n", "<br/>", $bug['summary']);	
		$bug['step_to_reproduce'] = str_replace("\n", "<br/>", $bug['step_to_reproduce']);	
        $records->fill($bug);

        $records->save();
        $params = ChatMessage::create([
            'user_id' => \Auth::user()->id,
            'bug_id' => $records->id,
            'sent_to_user_id' => ($records->assign_to != \Auth::user()->id) ? $records->assign_to : \Auth::user()->id,
            'approved' => '1',
            'status' => '2',
            'message' => $records->remark,
        ]);
        $bug['bug_id'] = $records->id;
        $bug['updated_by'] = \Auth::user()->id;
        $bugTrackerHistory = BugTrackerHistory::create($bug);

        return redirect()->back();
    }

    public function destroy(BugTracker $bugTracker, Request $request)
    {
        try {
            $bug = BugTracker::where('id', '=', $request->id)->delete();
            $bugTrackerHistory = BugTrackerHistory::where('bug_id', '=', $request->id)->delete();

            return response()->json(['code' => 200, 'data' => $bug, 'message' => 'Deleted successfully!!!']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            \Log::error('Bug Tracker Request Delete Error => '.json_decode($e).' #id #'.$request->id ?? '');
            $this->BugErrorLog($request->id ?? '', 'Bug Tracker Request Delete Error', $msg, 'bug_tracker');

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function update(Request $request)
    {
        $this->validate($request, [
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

        ]);

        $data = $request->except('_token', 'id');
        $bug = BugTracker::where('id', $request->id)->first();

        $data['created_by'] = \Auth::user()->id;
        $data['updated_by'] = \Auth::user()->id;
		
		
        $params = ChatMessage::create([
            'user_id' => \Auth::user()->id,
            'bug_id' => $bug->id,
            'sent_to_user_id' => ($request->assign_to != \Auth::user()->id) ? $request->assign_to : \Auth::user()->id,
            'approved' => '1',
            'status' => '2',
            'message' => $request->remark,
        ]);
		$data['summary'] = str_replace("\n", "<br/>", $request->summary);
        $data['step_to_reproduce'] = str_replace("\n", "<br/>", $request->step_to_reproduce);	 
        $bug->update($data);
        $data['bug_id'] = $request->id;
        BugTrackerHistory::create($data);

        return redirect()->route('bug-tracking.index')->with('success', 'You have successfully updated a Bug Tracker!');
    }

    public function bugHistory($id)
    {
        $bugHistory = BugTrackerHistory::where('bug_id', $id)->get();
        $bugHistory = $bugHistory->map(function ($bug) {
            $bug->bug_type_id = BugType::where('id', $bug->bug_type_id)->value('name');
            $bug->bug_environment_id = BugEnvironment::where('id', $bug->bug_environment_id)->value('name').' '.$bug->bug_environment_ver;
            $bug->assign_to = User::where('id', $bug->assign_to)->value('name');
            $bug->updated_by = User::where('id', $bug->updated_by)->value('name');
            $bug->bug_severity_id = BugSeverity::where('id', $bug->bug_severity_id)->value('name');
            $bug->bug_status_id = BugStatus::where('id', $bug->bug_status_id)->value('name');
            $bug->bug_history = BugTrackerHistory::where('bug_id', $bug->id)->get();

            return $bug;
        });

        return response()->json(['code' => 200, 'data' => $bugHistory]);
    }

    public function assignUser(Request $request)
    {
        $bugTracker = BugTracker::where('id', $request->id)->first();
        $bugTracker->assign_to = $request->user_id;
        $bugTracker->save();
        $data = [
            'bug_type_id' => $bugTracker->bug_type_id,
            'summary' => $bugTracker->summary,
            'step_to_reproduce' => $bugTracker->step_to_reproduce,
            'url' => $bugTracker->url,
            'bug_environment_id' => $bugTracker->bug_environment_id,
            'assign_to' => $bugTracker->assign_to,
            'bug_severity_id' => $bugTracker->bug_severity_id,
            'bug_status_id' => $bugTracker->bug_status_id,
            'module_id' => $bugTracker->module_id,
            'remark' => $bugTracker->remark,
            'website' => $bugTracker->website,
            'bug_id' => $bugTracker->id,
            'updated_by' => \Auth::user()->id,
        ];
        BugTrackerHistory::create($data);

        return response()->json(['code' => 200, 'data' => $data]);
    }

    public function severityUser(Request $request)
    {
        $bugTracker = BugTracker::where('id', $request->id)->first();
        $bugTracker->bug_severity_id = $request->severity_id;
        $bugTracker->save();
        $data = [
            'bug_type_id' => $bugTracker->bug_type_id,
            'step_to_reproduce' => $bugTracker->step_to_reproduce,
            'url' => $bugTracker->url,
            'summary' => $bugTracker->summary,
            'bug_environment_id' => $bugTracker->bug_environment_id,
            'assign_to' => $bugTracker->assign_to,
            'bug_severity_id' => $bugTracker->bug_severity_id,
            'bug_status_id' => $bugTracker->bug_status_id,
            'module_id' => $bugTracker->module_id,
            'remark' => $bugTracker->remark,
            'website' => $bugTracker->website,
            'bug_id' => $bugTracker->id,
            'updated_by' => \Auth::user()->id,
        ];
        BugTrackerHistory::create($data);

        return response()->json(['code' => 200, 'data' => $data]);
    }

    public function statusUser(Request $request)
    {
        $bugTracker = BugTracker::where('id', $request->id)->first();
        $bugTracker->bug_status_id = $request->status_id;
        $bugTracker->save();

        $data = [
            'bug_type_id' => $bugTracker->bug_type_id,
            'summary' => $bugTracker->summary,
            'step_to_reproduce' => $bugTracker->step_to_reproduce,
            'url' => $bugTracker->url,
            'bug_environment_id' => $bugTracker->bug_environment_id,
            'assign_to' => $bugTracker->assign_to,
            'bug_severity_id' => $bugTracker->bug_severity_id,
            'bug_status_id' => $bugTracker->bug_status_id,
            'module_id' => $bugTracker->module_id,
            'remark' => $bugTracker->remark,
            'website' => $bugTracker->website,
            'bug_id' => $bugTracker->id,
            'updated_by' => \Auth::user()->id,
        ];
        BugTrackerHistory::create($data);

        return response()->json(['code' => 200, 'data' => $data]);
    }

    public function sendMessage(Request $request)
    {
        $id = $request->id;
        $user = Auth::user();
        $task = BugTracker::find($request->id);

        $taskdata = $request->message;

        $userid = Auth::id();

        if ($user) {
            $params = ChatMessage::create([
                //                  'id'      => $id,
                'user_id' => $userid,
                'erp_user' => $userid,
                'bug_id' => $task->id,
                'sent_to_user_id' => ($task->assign_to != $user->id) ? $task->assign_to : $task->created_by,
                'sent_to_user_id' => ($task->assign_to != $user->id) ? $task->assign_to : $task->created_by,
                'approved' => '1',
                'status' => '2',
                'message' => $taskdata,
            ]);

            if ($params) {
                return response()->json(['code' => 200, 'message' => 'Successfully Send File']);
            }

            return response()->json([
                'message' => 'Something Was Wrong',
            ], 500);

            return response()->json(['message' => 'Sorry required fields is missing like id , userid'], 500);
        }
    }

    public function communicationData($id)
    {
        $messages = ChatMessage::where('bug_id', $id)->orderBy('id', 'desc')->get();
        $messages = $messages->map(function ($message) {
            $message->user_name = 'From '.User::where('id', $message->user_id)->value('name').' to '.User::where('id', $message->send_to_user_id)->value('name').' '.\Carbon\Carbon::parse($message->created_at)->format('Y-m-d H:i A');

            return $message;
        });

        return response()->json(['code' => 200, 'data' => $messages]);
    }
}
