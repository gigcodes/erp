<?php

namespace App\Http\Controllers;

use App\BugEnvironment;
use App\BugSeverity;
use App\BugStatus;
use App\BugTracker;
use App\BugTrackerHistory;
use App\BugType;
use App\PostmanRequestCreate;
use App\SiteDevelopmentCategory;
use App\User;
use Illuminate\Http\Request;

class BugTrackingController extends Controller
{
    public function index(){
        $bugTrackings = BugTracker::paginate(20);
        return view('bug-tracking.index',compact('bugTrackings'));
    }
    public function create(){
        $bugStatuses = BugStatus::get();
        $bugEnvironments = BugEnvironment::get();
        $bugSeveritys = BugSeverity::get();
        $bugTypes = BugType::get();
        $users = User::get();
        $filterCategories = SiteDevelopmentCategory::orderBy('title')->pluck('title')->toArray();
        return view('bug-tracking.create',compact('bugStatuses','bugTypes','bugEnvironments','bugSeveritys','users','filterCategories'));
    }
    public function edit($id){
        $bugTracker = BugTracker::findorFail($id);
        $bugStatuses = BugStatus::get();
        $bugEnvironments = BugEnvironment::get();
        $bugSeveritys = BugSeverity::get();
        $bugTypes = BugType::get();
        $users = User::get();
        $filterCategories = SiteDevelopmentCategory::orderBy('title')->pluck('title')->toArray();
        return view('bug-tracking.edit',compact('bugStatuses','bugTypes','bugEnvironments','bugSeveritys','users','filterCategories','bugTracker'));
    }

    public function status(Request $request){
        $this->validate($request, [
            'name' => 'required|string'
        ]);
        $data = $request->except('_token');
        BugStatus::create($data);
        return redirect()->back()->with('success', 'You have successfully created a status!');
    }

   public function environment(Request $request){
        $this->validate($request, [
            'name' => 'required|string'
        ]);
        $data = $request->except('_token');
        BugEnvironment::create($data);
        return redirect()->back()->with('success', 'You have successfully created a environment!');
    }

    public function type(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string'
        ]);
        $data = $request->except('_token');
        BugType::create($data);
        return redirect()->back()->with('success', 'You have successfully created a type!');

    }
    public function severity(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string'
        ]);
        $data = $request->except('_token');
        BugSeverity::create($data);
        return redirect()->back()->with('success', 'You have successfully created a severity!');

    }

    public function store(Request $request){
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

        ]);
        $data = $request->except('_token');
        $bugTracker = BugTracker::create($data);
        $data['bug_id']= $bugTracker->id;
        BugTrackerHistory::create($data);
        return redirect()->back()->with('success', 'You have successfully created a Bug Tracker!');

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

        ]);
        $data = $request->except('_token');
        BugTracker::where('id',$id)->update($data);
        $data['bug_id']= $id;
        BugTrackerHistory::create($data);
        return redirect()->back()->with('success', 'You have successfully updated a Bug Tracker!');
    }

}
