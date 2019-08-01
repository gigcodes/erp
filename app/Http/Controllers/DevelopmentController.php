<?php

namespace App\Http\Controllers;

use App\DeveloperMessagesAlertSchedules;
use Illuminate\Http\Request;
use App\DeveloperTask;
use App\DeveloperModule;
use App\DeveloperComment;
use App\DeveloperCost;
use App\PushNotification;
use App\User;
use App\Helpers;
use App\Issue;
use Auth;
use Carbon\Carbon;
use Plank\Mediable\Media;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;

class DevelopmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function __construct() {
     		$this->middleware('permission:developer-tasks', ['except' => ['issueCreate', 'issueStore', 'moduleStore']]);
     	}

    public function index(Request $request)
    {
      $user = $request->user ?? Auth::id();
      $start = $request->range_start ? "$request->range_start 00:00" : Carbon::now()->startOfWeek();
      $end = $request->range_end ? "$request->range_end 23:59" : Carbon::now()->endOfWeek();
      $id = null;

      $progressTasks = new DeveloperTask();
      $plannedTasks = new DeveloperTask();
      $completedTasks = new DeveloperTask();

      if (!Auth::user()->hasRole('Admin')) {
          $progressTasks = DeveloperTask::where('user_id', $user);
          $plannedTasks = DeveloperTask::where('user_id', $user);
          $completedTasks = DeveloperTask::where('user_id', $user);
      }

      if ($request->get('range_start') != '') {
        $progressTasks = $progressTasks->whereBetween('created_at', [$start, $end]);
        $plannedTasks = $plannedTasks->whereBetween('created_at', [$start, $end]);
        $completedTasks = $completedTasks->whereBetween('created_at', [$start, $end]);
      }

      if ($request->get('id')) {
          $progressTasks = $progressTasks->where(function($query) use ($request) {
              $id = $request->get('id');
              $query->where('id', $id)->orWhere('subject', 'LIKE', "%$id%");
          });
          $plannedTasks = $plannedTasks->where(function($query) use ($request) {
              $id = $request->get('id');
              $query->where('id', $id)->orWhere('subject', 'LIKE', "%$id%");
          });
          $completedTasks = $completedTasks->where(function($query) use ($request) {
              $id = $request->get('id');
              $query->where('id', $id)->orWhere('subject', 'LIKE', "%$id%");
          });
      }

      $plannedTasks = $plannedTasks->where('status', 'Planned')->orderBy('created_at')->with(['user', 'messages'])->get();
      $completedTasks = $completedTasks->where('status', 'Done')->orderBy('created_at')->with(['user', 'messages'])->get();
      $progressTasks = $progressTasks->where('status', 'In Progress')->orderBy('created_at')->with(['user', 'messages'])->get();

      $modules = DeveloperModule::all();
      $users = Helpers::getUserArray(User::role('Developer')->get());
      $module_names = [];

      foreach ($modules as $module) {
        $module_names[$module->id] = $module->name;
      }

      $times = [];
      return view('development.index', [
        'times' => $times,
        'users' => $users,
        'modules' => $modules,
        'user'  => $user,
        'start'  => $start,
        'end'  => $end,
        'module_names'  => $module_names,
        'completedTasks' => $completedTasks,
        'plannedTasks' => $plannedTasks,
        'progressTasks' => $progressTasks
      ]);
    }

    public function moveTaskToProgress(Request $request) {
        $task = DeveloperTask::find($request->get('task_id'));
        $date = $request->get('date');
        $task->status = 'In Progress';
        $hour = $request->get('hour') ?? '00';
        $minutes = $request->get('mimutes') ?? '00';
        $task->estimate_time = $date . ' ' . "$hour:$minutes:00 ";
        $task->start_time = Carbon::now()->toDateTimeString();
        $task->save();

        return response()->json([
             'status' => 'success'
         ]);
    }

    public function completeTask(Request $request) {
        $task = DeveloperTask::find($request->get('task_id'));
        $task->status = 'Done';
        $task->end_time = Carbon::now()->toDateTimeString();
        $task->save();

        return response()->json([
            'status' => 'success'
        ]);
    }

    public function relistTask(Request $request) {
        $task = DeveloperTask::find($request->get('task_id'));
        $task->status = 'Planned';
        $task->end_time = null;
        $task->start_time = null;
        $task->estimate_time = null;
        $task->save();

        return response()->json([
            'status' => 'success'
        ]);
    }

    public function issueIndex(Request $request)
    {
        $issues = new Issue;

        if ($request->get('submitted_by') > 0) {
            $issues = $issues->where('submitted_by', $request->get('submitted_by'));
        }
        if ($request->get('responsible_user') > 0) {
            $issues = $issues->where('responsible_user_id', $request->get('responsible_user'));
        }

        if ($request->get('corrected_by') > 0) {
            $issues = $issues->where('user_id', $request->get('corrected_by'));
        }

        if ($request->get('subject') != '') {
            $issues = $issues->where(function($query) use ($request) {
                $subject = $request->get('subject');
                $query->where('id', 'LIKE', "%$subject%")->orWhere('subject', 'LIKE', "%$subject%");
            });
        }

        $modules = DeveloperModule::all();
        $users = Helpers::getUserArray(User::all());
        $issues = $issues->orderBy('priority', 'ASC')->orderBy('created_at', 'DESC')->with('communications')->get();

        return view('development.issue', [
        'issues'  => $issues,
        'users'   => $users,
        'modules' => $modules,
        'request' => $request
      ]);
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

    public function issueCreate()
    {
      return view('development.issue-create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $this->validate($request, [
        'priority'  => 'required|integer',
        'subject'   => 'sometimes|nullable|string',
        'task'      => 'required|string|min:3',
        'cost'      => 'sometimes|nullable|integer',
        'status'    => 'required'
      ]);

      $data = $request->except('_token');
      $data['user_id'] = $request->user_id ? $request->user_id : Auth::id();

      $module = $request->get('module_id');

      $module = DeveloperModule::find($module);
      if (!$module) {
          $module = new DeveloperModule();
          $module->name = $request->get('module_id');
          $module->save();
          $data['module_id'] = $module->id;
      }

      $task = DeveloperTask::create($data);

      if ($request->hasfile('images')) {
        foreach ($request->file('images') as $image) {
          $media = MediaUploader::fromSource($image)->upload();
          $task->attachMedia($media,config('constants.media_tags'));
        }
      }

      // if ($task->status == 'Done') {
      //   NotificationQueueController::createNewNotification([
      //     'message' => 'New Task to Verify',
      //     'timestamps' => ['+0 minutes'],
      //     'model_type' => DeveloperTask::class,
      //     'model_id' =>  $task->id,
      //     'user_id' => Auth::id(),
      //     'sent_to' => 6,
      //     'role' => '',
      //   ]);
      //
      //   NotificationQueueController::createNewNotification([
      //     'message' => 'New Task to Verify',
      //     'timestamps' => ['+0 minutes'],
      //     'model_type' => DeveloperTask::class,
      //     'model_id' =>  $task->id,
      //     'user_id' => Auth::id(),
      //     'sent_to' => 56,
      //     'role' => '',
      //   ]);
      // }

      if ($request->ajax()) {
        return response()->json(['task' => $task]);
      }

      return redirect()->route('development.index')->with('success', 'You have successfully added task!');
    }

    public function issueStore(Request $request)
    {
      $this->validate($request, [
        'priority'  => 'required|integer',
        'issue'     => 'required|min:3'
      ]);

      $data = $request->except('_token');

      $module = $request->get('module');

      $module = DeveloperModule::find($module);
      if (!$module) {
          $module = new DeveloperModule();
          $module->name = $request->get('module');
          $module->save();
          $data['module'] = $module->id;
      }

      $issue = Issue::create($data);

      $issue->submitted_by = Auth::user()->id;
      $issue->save();

      if ($request->hasfile('images')) {
        foreach ($request->file('images') as $image) {
          $media = MediaUploader::fromSource($image)->upload();
          $issue->attachMedia($media,config('constants.media_tags'));
        }
      }

      return redirect()->back()->with('success', 'You have successfully submitted an issue!');
    }

    public function moduleStore(Request $request)
    {
      $this->validate($request, [
        'name'  => 'required|string|min:3'
      ]);

      $data = $request->except('_token');

      DeveloperModule::create($data);

      return redirect()->back()->with('success', 'You have successfully submitted an issue!');
    }

    public function commentStore(Request $request)
    {
      $this->validate($request, [
        'message'  => 'required|string|min:3'
      ]);

      $data = $request->except('_token');
      $data['user_id'] = Auth::id();

      DeveloperComment::create($data);

      return redirect()->back()->with('success', 'You have successfully wrote a comment!');
    }

    public function costStore(Request $request)
    {
      $this->validate($request, [
        'amount'      => 'required|numeric',
        'paid_date'   => 'required'
      ]);

      $data = $request->except('_token');

      DeveloperCost::create($data);

      return redirect()->back()->with('success', 'You have successfully added payment!');
    }

    public function awaitingResponse(Request $request, $id)
    {
      $comment = DeveloperComment::find($id);
      $comment->status = 1;
      $comment->save();

      return response('success');
    }

    public function issueAssign(Request $request, $id)
    {
      $this->validate($request, [
        'user_id' => 'required|integer'
      ]);

      $issue = Issue::find($id);
      $task = new DeveloperTask;

      $task->priority = $issue->priority;
      $task->task = $issue->issue;
      $task->user_id = $request->user_id;
      $task->status = 'Planned';

      $task->save();

      foreach ($issue->getMedia(config('constants.media_tags')) as $image) {
        $task->attachMedia($image, config('constants.media_tags'));
      }

      $issue->user_id = $request->user_id;
      $issue->save();
      $issue->delete();

      return redirect()->back()->with('success', 'You have successfully assigned the issue!');
    }

    public function moduleAssign(Request $request, $id)
    {
      $this->validate($request, [
        'user_id' => 'required|integer'
      ]);

      $module = DeveloperTask::find($id);

      $module->user_id = $request->user_id;
      $module->module = 0;

      $module->save();

      return redirect()->route('development.index')->with('success', 'You have successfully assigned the module!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
      $this->validate($request, [
        'priority'  => 'required|integer',
        'task'      => 'required|string|min:3',
        'cost'      => 'sometimes||nullable|integer',
        'status'    => 'required'
      ]);

      $data = $request->except('_token');
      $data['user_id'] = $request->user_id ? $request->user_id : Auth::id();

      $task = DeveloperTask::find($id);
      $task->update($data);

      if ($request->hasfile('images')) {
        foreach ($request->file('images') as $image) {
          $media = MediaUploader::fromSource($image)->upload();
          $task->attachMedia($media,config('constants.media_tags'));
        }
      }

      return redirect()->route('development.index')->with('success', 'You have successfully updated task!');
    }

    public function updateCost(Request $request, $id)
    {
      $task = DeveloperTask::find($id);

      if ($task->user_id == Auth::id()) {
        $task->cost = $request->cost;
        $task->save();
      }

      return response('success');
    }

    public function updateStatus(Request $request, $id)
    {
      $task = DeveloperTask::find($id);
      $task->status = $request->status;

      if ($request->status == 'In Progress') {
        $task->start_time = Carbon::now();
      }

      if ($request->status == 'Done') {
        $task->end_time = Carbon::now();
      }

      $task->save();

      // if ($task->status == 'Done' && $task->completed == 0) {
      //   NotificationQueueController::createNewNotification([
      //     'message' => 'New Task to Verify',
      //     'timestamps' => ['+0 minutes'],
      //     'model_type' => DeveloperTask::class,
      //     'model_id' =>  $task->id,
      //     'user_id' => Auth::id(),
      //     'sent_to' => 6,
      //     'role' => '',
      //   ]);
      //
      //   NotificationQueueController::createNewNotification([
      //     'message' => 'New Task to Verify',
      //     'timestamps' => ['+0 minutes'],
      //     'model_type' => DeveloperTask::class,
      //     'model_id' =>  $task->id,
      //     'user_id' => Auth::id(),
      //     'sent_to' => 56,
      //     'role' => '',
      //   ]);
      // }

      return response('success');
    }

    public function updateTask(Request $request, $id)
    {
      $task = DeveloperTask::find($id);
      $task->task = $request->task;
      $task->save();

      return response('success');
    }

    public function updatePriority(Request $request, $id)
    {
      $task = DeveloperTask::find($id);
      $task->priority = $request->priority;
      $task->save();

      return response()->json([
        'priority'  => $task->priority
      ]);
    }

    public function verify(Request $request, $id)
    {
      $task = DeveloperTask::find($id);
      $task->completed = 1;
      $task->save();

      $notifications = PushNotification::where('model_type', 'App\DeveloperTask')->where('model_id', $task->id)->where('isread', 0)->get();

      foreach ($notifications as $notification) {
        $notification->isread = 1;
        $notification->save();
      }

      if ($request->ajax()) {
        return response('success');
      }

      return redirect()->route('development.index')->with('success', 'You have successfully verified the task!');
    }

    public function verifyView(Request $request)
    {
      $task = DeveloperTask::find($request->id);

      PushNotification::where('model_type', 'App\DeveloperTask')->where('model_id', $request->id)->delete();

      if ($request->tab) {
        $message = 'New Task to Verify';

        // NotificationQueueController::createNewNotification([
        //   'message' => $message,
        //   'timestamps' => ['+10 minutes'],
        //   'model_type' => DeveloperTask::class,
        //   'model_id' =>  $task->id,
        //   'user_id' => Auth::id(),
        //   'sent_to' => 6,
        //   'role' => '',
        // ]);
        //
        // NotificationQueueController::createNewNotification([
        //   'message' => $message,
        //   'timestamps' => ['+10 minutes'],
        //   'model_type' => DeveloperTask::class,
        //   'model_id' =>  $task->id,
        //   'user_id' => Auth::id(),
        //   'sent_to' => 56,
        //   'role' => '',
        // ]);

        return redirect(url("/development#task_$request->id"));
      } else {
        $message = 'New Task Remark';

        if ($request->user == Auth::id()) {
          // NotificationQueueController::createNewNotification([
          //   'message' => $message,
          //   'timestamps' => ['+10 minutes'],
          //   'model_type' => DeveloperTask::class,
          //   'model_id' =>  $task->id,
          //   'user_id' => Auth::id(),
          //   'sent_to' => 6,
          //   'role' => '',
          // ]);
          //
          // NotificationQueueController::createNewNotification([
          //   'message' => $message,
          //   'timestamps' => ['+10 minutes'],
          //   'model_type' => DeveloperTask::class,
          //   'model_id' =>  $task->id,
          //   'user_id' => Auth::id(),
          //   'sent_to' => 56,
          //   'role' => '',
          // ]);
        } else {
          // NotificationQueueController::createNewNotification([
          //   'message' => $message,
          //   'timestamps' => ['+10 minutes'],
          //   'model_type' => DeveloperTask::class,
          //   'model_id' =>  $task->id,
          //   'user_id' => Auth::id(),
          //   'sent_to' => $request->user,
          //   'role' => '',
          // ]);
        }

        return redirect(url("/development?user=$request->user#task_$task->id"));

        // if ($task->status == 'Done' && $task->completed == 1) {
        // } elseif ($task->status == 'Done' && $task->completed == 0) {
        //   return redirect(url("/development#task_$request->id"));
        // } else {
        //   return redirect(url("/development#task_$task->id"));
        // }
      }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
      $task = DeveloperTask::find($id);
      $task->development_details()->delete();
      $task->delete();

      if ($request->ajax()) {
        return response('success');
      }

      return redirect()->route('development.index')->with('success', 'You have successfully archived the task!');
    }

    public function issueDestroy($id)
    {
      Issue::find($id)->delete();

      return redirect()->route('development.issue.index')->with('success', 'You have successfully archived the issue!');
    }

    public function moduleDestroy($id)
    {
      $module = DeveloperModule::find($id);

      foreach ($module->tasks as $task) {
        $task->module_id = '';
        $task->save();
      }

      $module->delete();

      return redirect()->route('development.index')->with('success', 'You have successfully archived the module!');
    }

    public function assignUser(Request $request) {
        $issue = Issue::find($request->get('issue_id'));
        $issue->user_id = $request->get('user_id');
        $issue->save();

        return response()->json([
            'status' => 'success'
        ]);
    }

    public function assignResponsibleUser(Request $request) {
        $issue = Issue::find($request->get('issue_id'));
        $issue->responsible_user_id = $request->get('responsible_user_id');
        $issue->save();

        return response()->json([
            'status' => 'success'
        ]);
    }

    public function saveAmount(Request $request) {
        $issue = Issue::find($request->get('issue_id'));
        $issue->cost = $request->get('cost');
        $issue->save();

        return response()->json([
            'status' => 'success'
        ]);
    }

    public function resolveIssue(Request $request) {
        $issue = Issue::find($request->get('issue_id'));
        $issue->is_resolved = $request->get('is_resolved');
        $issue->save();

        return response()->json([
            'status' => 'success'
        ]);
    }

    public function saveEstimateTime(Request $request) {
        $issue = Issue::find($request->get('issue_id'));
        $issue->estimate_time = $request->get('estimate_time');
        $issue->save();

        return response()->json([
            'status' => 'success'
        ]);
    }

    public function updateValues(Request $request) {
        $task = DeveloperTask::find($request->get('id'));
        $type = $request->get('type');
        $value = $request->get('value');
        if ($type == 'start_date') {
            $task->start_date = $request->get('value');
        } else if ($type == 'end_date') {
            $task->end_date = $request->get('value');
        } else if ($type == 'estimate_date') {
            $task->estimate_date = $request->get('value');
        } else if ($type == 'cost') {
            $task->cost = $request->get('value');
        } else if ($type=='module') {
            $task->module_id = $request->get('value');
        }
        $task->save();

        return response()->json([
            'status' => 'success'
        ]);

    }
}
