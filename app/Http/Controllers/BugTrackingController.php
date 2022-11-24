<?php

namespace App\Http\Controllers;

use App\AssetsManager;
use App\BugEnvironment;
use App\BugSeverity;
use App\BugStatus;
use App\BugTracker;
use App\BugTrackerHistory;
use App\BugType;
use App\SiteDevelopmentCategory;
use App\StoreWebsite;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BugTrackingController extends Controller
{
    public function index(Request $request){
        $title = "Bug Tracking";

        $bugStatuses = BugStatus::get();
        $bugEnvironments = BugEnvironment::get();
        $bugSeveritys = BugSeverity::get();
        $bugTypes = BugType::get();
        $users = User::get();
        $filterCategories = SiteDevelopmentCategory::orderBy('title')->pluck('title')->toArray();
        $filterWebsites = StoreWebsite::orderBy('website')->pluck('website')->toArray();
        return view('bug-tracking.index',[
            'title'=>$title,
            'bugTypes' => $bugTypes,
            'bugEnvironments' => $bugEnvironments,
            'bugSeveritys' => $bugSeveritys,
            'bugStatuses' => $bugStatuses,
            'filterCategories' => $filterCategories,
            'users'=>$users,
            'filterWebsites' =>$filterWebsites
        ]);

    }

    public function records(Request $request)
    {
        $records = BugTracker::orderBy('id','desc');

        $keyword = request("keyword");
//        if (!empty($keyword)) {
//            $records = $records->where(function ($q) use ($keyword) {
//                $q->where("website", "LIKE", "%$keyword%")
//                    ->orWhere("title", "LIKE", "%$keyword%")
//                    ->orWhere("description", "LIKE", "%$keyword%");
//            });
//        }
        $records = $records->get();
        $records = $records->map(function ($bug){
            $bug->bug_type_id= BugType::where('id',$bug->bug_type_id)->value('name');
            $bug->bug_environment_id= BugEnvironment::where('id',$bug->bug_environment_id)->value('name');
            $bug->assign_to= User::where('id',$bug->assign_to)->value('name');
            $bug->bug_severity_id= BugSeverity::where('id',$bug->bug_severity_id)->value('name');
            $bug->bug_status_id= BugStatus::where('id',$bug->bug_status_id)->value('name');
            return $bug;
        });

        return response()->json(["code" => 200, "data" => $records, "total" => count($records)]);
    }

    public function create(){
        $bugStatuses = BugStatus::get();
        $bugEnvironments = BugEnvironment::get();
        $bugSeveritys = BugSeverity::get();
        $bugTypes = BugType::get();
        $users = User::get();
        $filterCategories = SiteDevelopmentCategory::orderBy('title')->pluck('title')->toArray();
        $filterWebsites = StoreWebsite::orderBy('website')->pluck('website')->toArray();
        return view('bug-tracking.create',compact('bugStatuses','bugTypes','bugEnvironments','bugSeveritys','users','filterCategories','filterWebsites'));
    }

    public function edit($id){
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
                    "code" => 200,
                    "data" => $bugTracker,
                'bugTypes' => $bugTypes,
                'bugEnvironments' => $bugEnvironments,
                'bugSeveritys' => $bugSeveritys,
                'bugStatuses' => $bugStatuses,
                'filterCategories' => $filterCategories,
                'users'=> $users,
                'filterWebsites' => $filterWebsites]
            );
        }

        return response()->json(["code" => 500, "error" => "Wrong bug tracking id!"]);
    }

    public function status(Request $request){
        $status = $request->all();
        $validator = Validator::make($status, [
            'name' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(["code" => 500, "error" => 'Name is required']);
        }
        $data = $request->except('_token');
        $records = BugStatus::create($data);
        return response()->json(["code" => 200, "data" => $records]);
    }

   public function environment(Request $request){

       $environment = $request->all();
       $validator = Validator::make($environment, [
           'name' => 'required|string',
       ]);
       if ($validator->fails()) {
           return response()->json(["code" => 500, "error" => 'Name is required']);
       }
        $data = $request->except('_token');
       $records = BugEnvironment::create($data);
       return response()->json(["code" => 200, "data" => $records]);
    }

    public function type(Request $request)
    {
        $type = $request->all();
        $validator = Validator::make($type, [
            'name' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(["code" => 500, "error" => 'Name is required']);
        }
        $data = $request->except('_token');
        $records = BugType::create($data);
        return response()->json(["code" => 200, "data" => $records]);

    }
    public function severity(Request $request)
    {
        $severity = $request->all();
        $validator = Validator::make($severity, [
            'name' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(["code" => 500, "error" => 'Name is required']);
        }
        $data = $request->except('_token');
        $records = BugSeverity::create($data);
        return response()->json(["code" => 200, "data" => $records]);

    }

    public function store(Request $request){
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
            $outputString = "";
            $messages     = $validator->errors()->getMessages();
            foreach ($messages as $k => $errr) {
                foreach ($errr as $er) {
                    $outputString .= "$k : " . $er . "<br>";
                }
            }
            return response()->json(["code" => 500, "error" => $outputString]);
        }

        $id = $request->get("id", 0);

        $records = BugTracker::find($id);

        if (!$records) {
            $records = new BugTracker();
        }



        $records->fill($bug);

        $records->save();

        return response()->json(["code" => 200, "data" => $records]);


    }
    public function destroy(BugTracker $bugTracker, Request $request) {
        try {
            $bug = BugTracker::where('id', '=', $request->id)->delete();
            $bugTrackerHistory = BugTrackerHistory::where('bug_id', '=', $request->id)->delete();
            return response()->json(['code' => 200, 'data' => $bug, 'message' => 'Deleted successfully!!!']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            \Log::error("Bug Tracker Request Delete Error => ". json_decode($e). " #id #".$request->id  ?? '');
            $this->BugErrorLog($request->id ?? '', 'Bug Tracker Request Delete Error', $msg, 'bug_tracker');
            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function update(Request $request,$id){

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
        $data = $request->except('_token');
        BugTracker::where('id',$id)->update($data);
        $data['bug_id']= $id;
        BugTrackerHistory::create($data);
        return redirect()->route('bug-tracking.index')->with('success', 'You have successfully updated a Bug Tracker!');
    }

}
