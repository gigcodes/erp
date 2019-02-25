<?php

namespace App\Http\Controllers;

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
      $user = $request->user ? $request->user : Auth::id();
      $start = $request->range_start ? $request->range_start : Carbon::now()->startOfWeek();
      $end = $request->range_end ? $request->range_end : Carbon::now()->endOfWeek();
      $tab = $request->tab ? $request->tab : NULL;

      $tasks = DeveloperTask::where('user_id', $user)->where('module', 0)->where('status', '!=', 'Done')->orderBy('priority')->get()->groupBy('module_id');
      $review_tasks = DeveloperTask::where('user_id', $user)->where('module', 0)->where('status', 'Done')->where('completed', 0)->orderBy('priority')->get()->groupBy('module_id');
      $completed_tasks = DeveloperTask::where('user_id', $user)->where('module', 0)->where('status', 'Done')->where('completed', 1)->whereBetween('start_time', [$start, $end])->orderBy('priority')->get()->groupBy('module_id');

      $total_tasks = DeveloperTask::where('user_id', $user)->where('module', 0)->where('status', 'Done')->get();

      $all_time_cost = 0;
      foreach ($total_tasks as $task) {
        $all_time_cost += $task->cost;
      }

      $modules = DeveloperModule::all();
      $users = Helpers::getUserArray(User::all());
      $comments = DeveloperComment::where('send_to', $user)->latest()->get();
      $amounts = DeveloperCost::where('user_id', $user)->orderBy('paid_date')->get();
      $module_names = [];

      foreach ($modules as $module) {
        $module_names[$module->id] = $module->name;
      }

      return view('development.index', [
        'tasks' => $tasks,
        'review_tasks' => $review_tasks,
        'completed_tasks' => $completed_tasks,
        'users' => $users,
        'modules' => $modules,
        'user'  => $user,
        'module_names'  => $module_names,
        'comments'  => $comments,
        'amounts'  => $amounts,
        'all_time_cost' => $all_time_cost,
        'tab' => $tab,
      ]);
    }

    public function issueIndex()
    {
      $issues = Issue::all();
      $users = Helpers::getUserArray(User::all());

      return view('development.issue', [
        'issues'  => $issues,
        'users'   => $users
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
        'task'      => 'required|string|min:3',
        'cost'      => 'sometimes||nullable|integer',
        'status'    => 'required'
      ]);

      $data = $request->except('_token');
      $data['user_id'] = $request->user_id ? $request->user_id : Auth::id();

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

      return redirect()->route('development.index')->with('success', 'You have successfully added task!');
    }

    public function issueStore(Request $request)
    {
      $this->validate($request, [
        'priority'  => 'required|integer',
        'issue'     => 'required|min:3'
      ]);

      $data = $request->except('_token');

      $issue = Issue::create($data);

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

      return redirect()->route('development.index')->with('success', 'You have successfully updated task!');
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

        return redirect(url("/development?tab=review#review_task_$request->id"));
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

        if ($task->status == 'Done' && $task->completed == 1) {
          return redirect(url("/development?tab=3#completed_task_$task->id"));
        } elseif ($task->status == 'Done' && $task->completed == 0) {
          return redirect(url("/development?tab=review#review_task_$request->id"));
        } else {
          return redirect(url("/development?tab=1#task_$task->id"));
        }
      }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      DeveloperTask::find($id)->delete();

      return redirect()->route('development.index')->with('success', 'You have successfully archived the task!');
    }

    public function issueDestroy($id)
    {
      Issue::find($id)->delete();

      return redirect()->route('development.issue.index')->with('success', 'You have successfully archived the issue!');
    }

    public function moduleDestroy($id)
    {
      DeveloperModule::find($id)->delete();

      return redirect()->route('development.index')->with('success', 'You have successfully archived the module!');
    }
}
