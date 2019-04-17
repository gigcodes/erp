<?php

namespace App\Http\Controllers;

use App\PushNotification;
use App\SatutoryTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Helpers;
use App\User;
use App\Task;
use App\Setting;
use App\Remark;
use App\DeveloperTask;
use App\NotificationQueue;

class TaskModuleController extends Controller {

	public function __construct() {

	}

	public function index( Request $request ) {

		if ( $request->input( 'selected_user' ) == '' ) {
			$userid = Auth::id();
		} else {
			$userid = $request->input( 'selected_user' );
		}

		$data['task'] = [];

		$data['task']['pending']      = Task::with('remarks')->where( 'is_statutory', '=', 0 )
		                               ->where( 'is_completed', '=', null )
										->where( function ($query ) use ($userid) {
											return $query->orWhere( 'assign_from', '=', $userid )
											             ->orWhere( 'assign_to', '=', $userid );
										})
		                               ->get()->toArray();

		$data['task']['completed']  = Task::where( 'is_statutory', '=', 0 )
		                                    ->whereNotNull( 'is_completed'  )
											->where( function ($query ) use ($userid) {
												return $query->orWhere( 'assign_from', '=', $userid )
												             ->orWhere( 'assign_to', '=', $userid );
											})
		                                    ->get()->toArray();



		$satutory_tasks = SatutoryTask::latest()
		                                         ->orWhere( 'assign_from', '=', $userid )
												 ->orWhere( 'assign_to', '=', $userid )->whereNotNull('completion_date')
		                                         ->get();

		foreach ($satutory_tasks as $task) {
			switch ($task->recurring_type) {
				case 'EveryDay':
					if (Carbon::parse($task->completion_date)->format('Y-m-d') < date('Y-m-d')) {
						$task->completion_date = null;
						$task->save();
					}
					break;
				case 'EveryWeek':
					if (Carbon::parse($task->completion_date)->addWeek()->format('Y-m-d') < date('Y-m-d')) {
						$task->completion_date = null;
						$task->save();
					}
					break;
				case 'EveryMonth':
					if (Carbon::parse($task->completion_date)->addMonth()->format('Y-m-d') < date('Y-m-d')) {
						$task->completion_date = null;
						$task->save();
					}
					break;
				case 'EveryYear':
					if (Carbon::parse($task->completion_date)->addYear()->format('Y-m-d') < date('Y-m-d')) {
						$task->completion_date = null;
						$task->save();
					}
					break;
				default:

			}
		}

		$data['task']['statutory'] = SatutoryTask::latest()
		                                         ->orWhere( 'assign_from', '=', $userid )
												 ->orWhere( 'assign_to', '=', $userid )
		                                         ->get()->toArray();

		$data['task']['statutory_completed'] = Task::latest()->where( 'is_statutory', '=', 1 )
		                                   ->whereNotNull( 'is_completed'  )
		                                   ->where( function ($query ) use ($userid) {
			                                   return $query->orWhere( 'assign_from', '=', $userid )
			                                                ->orWhere( 'assign_to', '=', $userid );
		                                   })
		                                   ->get()->toArray();

		$data['task']['statutory_today'] = Task::latest()->where( 'is_statutory', '=', 1 )
		                                           ->where( 'is_completed', '=',  null  )
		                                           ->where( function ($query ) use ($userid) {
			                                           return $query->orWhere( 'assign_from', '=', $userid )
			                                                        ->orWhere( 'assign_to', '=', $userid );
		                                           })
		                                           ->get()->toArray();

//		$data['task']['statutory_completed_ids'] = [];
//		foreach ($data['task']['statutory_completed'] as $item)
//			$data['task']['statutory_completed_ids'][] =  $item['statutory_id'];


		$data['task']['deleted']   = Task::onlyTrashed()
		                                ->where( 'is_statutory', '=', 0 )
										->where( function ($query ) use ($userid) {
											return $query->orWhere( 'assign_from', '=', $userid )
											             ->orWhere( 'assign_to', '=', $userid );
										})
		                               ->get()->toArray();

																	//  $tasks_query = Task::where('is_statutory', 0)
															 		// 												->where('assign_to', Auth::id());
																	//
															 		// $pending_tasks_count = Task::where('is_statutory', 0)->where('assign_to', Auth::id())->whereNull('is_completed')->get();
															 		// $completed_tasks_count = $tasks_query->whereNull('is_completed')->count();
																	// dd($pending_tasks_count);

																	// $tasks_query = Task::where('is_statutory', 0)->where('assign_to', Auth::id())->whereNull('is_completed')->count();
																	//
																	// dd($tasks_query);


		$users                     = User::oldest()->get()->toArray();
		$data['users']             = $users;
		$data['daily_activity_date'] = $request->daily_activity_date ? $request->daily_activity_date : date('Y-m-d');

		$category = '';

		//My code start
		$selected_user = $request->input( 'selected_user' );
		$users         = Helpers::getUserArray( User::all() );
		if ( ! empty( $selected_user ) && ! Helpers::getadminorsupervisor() ) {
			return response()->json( [ 'user not allowed' ], 405 );
		}
		//My code end

		return view( 'task-module.show', compact( 'data', 'users', 'selected_user','category' ) );
	}

	public function store( Request $request ) {
		$this->validate($request, [
			'task_subject'	=> 'required',
			'task_details'	=> 'required'
		]);

		$data                = $request->except( '_token' );
		$data['assign_from'] = Auth::id();

		if ($request->task_type == 'quick_task') {
			$data['is_statutory'] = 0;
			$data['category'] = 6;
			$data['model_type'] = $request->model_type;
			$data['model_id'] = $request->model_id;
		}

		foreach ($request->assign_to as $assign_to) {
			$data['assign_to'] = $assign_to;

			if($data['is_statutory'] == 0) {
				$task = Task::create( $data );

				// PushNotification::create( [
				// 	'type'       => 'button',
				// 	'message'    => 'Task Details: ' . $data['task_details'],
				// 	'model_type' => Task::class,
				// 	'model_id'   => $task->id,
				// 	'user_id'    => Auth::id(),
				// 	'sent_to'    => $assign_to,
				// 	'role'       => '',
				// ] );
				//
				// PushNotification::create( [
				// 	'message'    => 'Task Created: ' . $data['task_details'] . ' for ' . Helpers::getUserNameById($assign_to),
				// 	'model_type' => Task::class,
				// 	'model_id'   => $task->id,
				// 	'user_id'    => Auth::id(),
				// 	'sent_to'    => '',
				// 	'role'       => 'Admin',
				// ] );

				// NotificationQueueController::createNewNotification( [
				// 	'message'    => 'Reminder for Task : ' . $data['task_details'],
				// 	'timestamps' => [ '+5 minutes',  '+10 minutes',  '+15 minutes',  '+20 minutes',  '+25 minutes',  '+30 minutes',  '+35 minutes',  '+40 minutes',  '+45 minutes',  '+50 minutes',  '+55 minutes',  '+60 minutes',  '+65 minutes',  '+70 minutes',  '+75 minutes',  '+80 minutes',  '+85 minutes',  '+90 minutes',  '+95 minutes',  '+100 minutes'],
				// 	'reminder'	 => 1,
				// 	'model_type' => Task::class,
				// 	'model_id'   => $task->id,
				// 	'user_id'    => \Auth::id(),
				// 	'sent_to'    => $request->input( 'assign_to' ),
				// 	'role'       => '',
				// ] );

				// $diff = Carbon::parse($request->completion_date)->diffInMinutes(Carbon::now());

				// NotificationQueueController::createNewNotification( [
				// 	'message'    => 'Reminder for Task : ' . $data['task_details'],
				// 	// 'timestamps' => ['+'.$diff.'minutes', '+'. $diff + 60 .'minutes', '+'. $diff + 120 .'minutes', '+'.$diff + 180 .'minutes', '+'.$diff + 240 .'minutes', ],
				// 	// 'reminder'	 => 1,
				// 	'timestamps' => ['+0 minutes'],
				// 	'model_type' => Task::class,
				// 	'model_id'   => $task->id,
				// 	'user_id'    => \Auth::id(),
				// 	'sent_to'    => $assign_to,
				// 	'role'       => '',
				// ] );
			}
			else {
				$task = SatutoryTask::create($data);

				// PushNotification::create( [
				// 	'message'    => 'Recurring Task Assigned: ' . $data['task_details'],
				// 	'model_type' => Task::class,
				// 	'model_id'   => $task->id,
				// 	'user_id'    => Auth::id(),
				// 	'sent_to'    => $assign_to,
				// 	'role'       => '',
				// ] );
				//
				// PushNotification::create( [
				// 	'message'    => 'Recurring Task Created: ' . $data['task_details'] . ' for ' . Helpers::getUserNameById($assign_to),
				// 	'model_type' => Task::class,
				// 	'model_id'   => $task->id,
				// 	'user_id'    => Auth::id(),
				// 	'sent_to'    => '',
				// 	'role'       => 'Admin',
				// ] );

				// NotificationQueueController::createNewNotification( [
				// 	'message'    => 'Reminder for Recurring Task : ' . $data['task_details'],
				// 	'timestamps' => [ '+5 minutes',  '+10 minutes',  '+15 minutes',  '+20 minutes',  '+25 minutes',  '+30 minutes',  '+35 minutes',  '+40 minutes',  '+45 minutes',  '+50 minutes',  '+55 minutes',  '+60 minutes',  '+65 minutes',  '+70 minutes',  '+75 minutes',  '+80 minutes',  '+85 minutes',  '+90 minutes',  '+95 minutes',  '+100 minutes'],
				// 	'reminder'	 => 1,
				// 	'model_type' => Task::class,
				// 	'model_id'   => $task->id,
				// 	'user_id'    => \Auth::id(),
				// 	'sent_to'    => $request->input( 'assign_to' ),
				// 	'role'       => '',
				// ] );

				// $diff = Carbon::parse($request->completion_date)->diffInMinutes(Carbon::now());

				// NotificationQueueController::createNewNotification( [
				// 	'message'    => 'Reminder for Task : ' . $data['task_details'],
				// 	// 'timestamps' => ['+'.$diff.'minutes', '+'. $diff + 60 .'minutes', '+'. $diff + 120 .'minutes', '+'.$diff + 180 .'minutes', '+'.$diff + 240 .'minutes', ],
				// 	// 'reminder'	 => 1,
				// 	'timestamps' => ['+0 minutes'],
				// 	'model_type' => Task::class,
				// 	'model_id'   => $task->id,
				// 	'user_id'    => \Auth::id(),
				// 	'sent_to'    => $assign_to,
				// 	'role'       => '',
				// ] );
			}
		}


		return redirect()->back()
		                 ->with( 'success', 'Task created successfully.' );
	}

	public function update() {

	}

	public function complete(Request $request, $taskid ) {

		$task               = Task::find( $taskid );
		// $task->is_completed = date( 'Y-m-d H:i:s' );
//		$task->deleted_at = null;

		// if ( $task->assign_to == Auth::id() ) {
		// 	$task->save();
		// }

		$tasks = Task::where('category', $task->category)->where('assign_from', $task->assign_from)->where('is_statutory', $task->is_statutory)->where('task_details', $task->task_details)->where('task_subject', $task->task_subject)->get();

		foreach ($tasks as $item) {
			$item->is_completed = date( 'Y-m-d H:i:s' );
			$item->save();
		}

		if($task->is_statutory == 0)
			$message = 'Task Completed: ' . $task->task_details;
		else
			$message = 'Recurring Task Completed: ' . $task->task_details;

		// PushNotification::create( [
		// 	'message'    => $message,
		// 	'model_type' => Task::class,
		// 	'model_id'   => $task->id,
		// 	'user_id'    => Auth::id(),
		// 	'sent_to'    => $task->assign_from,
		// 	'role'       => '',
		// ] );
		//
		// PushNotification::create( [
		// 	'message'    => $message,
		// 	'model_type' => Task::class,
		// 	'model_id'   => $task->id,
		// 	'user_id'    => Auth::id(),
		// 	'sent_to'    => '',
		// 	'role'       => 'Admin',
		// ] );

		// $notification_queues = NotificationQueue::where('model_id', $task->id)->where('model_type', 'App\Task')->delete();

		if ($request->ajax()) {
			return response('success');
		}

		return redirect()->back()
		                 ->with( 'success', 'Task marked as completed.' );
	}

	public function statutoryComplete( $taskid ) {

		$task               = SatutoryTask::find( $taskid );
		$task->completion_date = date( 'Y-m-d H:i:s' );
//		$task->deleted_at = null;

		if ( $task->assign_to == Auth::id() ) {
			$task->save();
		}

		$message = 'Statutory Task Completed: ' . $task->task_details;

		// $notification_queues = NotificationQueue::where('model_id', $task->id)->where('model_type', 'App\StatutoryTask')->delete();

		// PushNotification::create( [
		// 	'message'    => $message,
		// 	'model_type' => SatutoryTask::class,
		// 	'model_id'   => $task->id,
		// 	'user_id'    => Auth::id(),
		// 	'sent_to'    => $task->assign_from,
		// 	'role'       => '',
		// ] );

		return redirect()->back()
		                 ->with( 'success', 'Statutory Task marked as completed.' );
	}

	public function addRemark( Request $request ) {

		$remark       = $request->input( 'remark' );
		$id           = $request->input( 'id' );
		$created_at = date('Y-m-d H:i:s');
		$update_at = date('Y-m-d H:i:s');
		$remark_entry = Remark::create([
			'taskid'	=> $id,
			'remark'	=> $remark,
			'module_type'	=> $request->module_type,
			'user_name'	=> $request->user_name ? $request->user_name : Auth::user()->name
		]);

		if ($request->module_type == 'developer') {
			$task = DeveloperTask::find($id);

			if ($task->user->id == Auth::id()) {
				NotificationQueueController::createNewNotification([
					'message' => 'New Task Remark',
					'timestamps' => ['+0 minutes'],
					'model_type' => DeveloperTask::class,
					'model_id' =>  $task->id,
					'user_id' => Auth::id(),
					'sent_to' => 6,
					'role' => '',
				]);

				NotificationQueueController::createNewNotification([
					'message' => 'New Task Remark',
					'timestamps' => ['+0 minutes'],
					'model_type' => DeveloperTask::class,
					'model_id' =>  $task->id,
					'user_id' => Auth::id(),
					'sent_to' => 56,
					'role' => '',
				]);
			} else {
				NotificationQueueController::createNewNotification([
					'message' => 'New Task Remark',
					'timestamps' => ['+0 minutes'],
					'model_type' => DeveloperTask::class,
					'model_id' =>  $task->id,
					'user_id' => Auth::id(),
					'sent_to' => $task->user_id,
					'role' => '',
				]);
			}
		}
		// $remark_entry = DB::insert('insert into remarks (taskid, remark, created_at, updated_at) values (?, ?, ?, ?)', [$id  ,$remark , $created_at, $update_at]);

		// if (is_null($request->module_type)) {
		// 	$task = Task::find($remark_entry->taskid);
		//
		// 	PushNotification::create( [
		// 		'message'    => 'Remark added: ' . $remark,
		// 		'model_type' => Task::class,
		// 		'model_id'   => $task->id,
		// 		'user_id'    => Auth::id(),
		// 		'sent_to'    => $task->assign_from,
		// 		'role'       => '',
		// 	] );
		//
		// 	PushNotification::create( [
		// 		'message'    => 'Remark added: ' . $remark,
		// 		'model_type' => Task::class,
		// 		'model_id'   => $task->id,
		// 		'user_id'    => Auth::id(),
		// 		'sent_to'    => '',
		// 		'role'       => 'Admin',
		// 	] );
		// }


		return response()->json(['remark' => $remark ],200);
	}

	public function list(Request $request)
	{
		$pending_tasks = Task::where('is_statutory', 0)->whereNull('is_completed')->where('assign_from', Auth::id());
		$completed_tasks = Task::where('is_statutory', 0)->whereNotNull('is_completed')->where('assign_from', Auth::id());

		if ($request->user[0] != null) {
			$pending_tasks = $pending_tasks->whereIn('assign_to', $request->user);
			$completed_tasks = $completed_tasks->whereIn('assign_to', $request->user);
		}

		if ($request->date != null) {
			$pending_tasks = $pending_tasks->where('created_at', 'LIKE', "%$request->date%");
			$completed_tasks = $completed_tasks->where('created_at', 'LIKE', "%$request->date%");
		}

		$pending_tasks = $pending_tasks->oldest()->paginate(Setting::get('pagination'));
		$completed_tasks = $completed_tasks->orderBy('is_completed', 'DESC')->paginate(Setting::get('pagination'), ['*'], 'completed-page');

		$users = Helpers::getUserArray(User::all());
		$user = $request->user ?? [];
		$date = $request->date ?? '';

		return view('task-module.list', [
			'pending_tasks'		=> $pending_tasks,
			'completed_tasks'	=> $completed_tasks,
			'users'						=> $users,
			'user'						=> $user,
			'date'						=> $date
		]);
	}

	public function getremark( Request $request ) {

		$id   = $request->input( 'id' );

		$task = Task::find( $id );

		echo $task->remark;
	}


	public function deleteTask(Request $request){

		$id   = $request->input( 'id' );
		$task = Task::find( $id );

		$task->remark = $request->input( 'comment' );
		$task->save();

		$task->delete();
	}

	public function archiveTask($id)
	{
		$task = Task::find($id);

		$task->delete();

		return redirect('/');
	}

	public function deleteStatutoryTask(Request $request){

		$id   = $request->input( 'id' );
		$task = SatutoryTask::find( $id );
		$task->delete();

		return redirect()->back();
	}

	public function exportTask(Request $request){

		$users = $request->input('selected_user');
		$from = $request->input( 'range_start' ) . " 00:00:00.000000";
		$to   = $request->input( 'range_end' ) . " 23:59:59.000000";

		$tasks = (new Task())->newQuery()->withTrashed()->whereBetween('created_at',[$from,$to])->where('assign_from', '!=', 0)->where('assign_to', '!=', 0);

		if( !empty($users) ){
			$tasks = $tasks->whereIn('assign_to',$users);
		}

		$tasks_list =  $tasks->get()->toArray();
		$tasks_csv = [];
		$userList = Helpers::getUserArray( User::all() );

		for ($i = 0 ; $i < sizeof($tasks_list) ; $i++){

			$task_csv = [];
			$task_csv['id'] = $tasks_list[$i]['id'];
			$task_csv['SrNo'] = $i+1;
			$task_csv['assign_from'] = $userList[$tasks_list[$i]['assign_from']];
			$task_csv['assign_to'] = $userList[$tasks_list[$i]['assign_to']];
			$task_csv['type'] = $tasks_list[$i]['is_statutory'] == 1 ? 'Statutory' : 'Other';
			$task_csv['task_subject'] = $tasks_list[$i]['task_subject'];
			$task_csv['task_details'] = $tasks_list[$i]['task_details'];
			$task_csv['completion_date'] = $tasks_list[$i]['completion_date'];
			$task_csv['remark'] = $tasks_list[$i]['remark'];
			$task_csv['completed_on'] = $tasks_list[$i]['is_completed'];
			$task_csv['created_on'] = $tasks_list[$i]['created_at'];

			array_push($tasks_csv,$task_csv);
		}

		// $this->outputCsv('tasks.csv', $tasks_csv);
		return view('task-module.export')->withTasks($tasks_csv);
	}

	public function outputCsv($fileName, $assocDataArray)
	{
		header('Pragma: public');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Cache-Control: private', false);
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment;filename=' . $fileName);
		if(isset($assocDataArray['0'])){
			$fp = fopen('php://output', 'w');
			fputcsv($fp, array_keys($assocDataArray['0']));
			foreach($assocDataArray AS $values){
				fputcsv($fp, $values);
			}
			fclose($fp);
		}
	}


	public static function getClasses($task){

		$classes = ' ';

		$classes .= ' '. ( $task['assign_from'] == Auth::user()->id ? 'mytask' : '' ) . ' ';
		$classes .= ' '.( time() > strtotime( $task['completion_date']. ' 23:59:59'  )  ? 'isOverdue' : '').' ';


		$task_status = Helpers::statusClass($task['assign_status']);

		$classes .= $task_status;

		return $classes;
	}

	public function recurringTask(){

		$statutory_tasks = SatutoryTask::all()->toArray();

		foreach ($statutory_tasks as $statutory_task){

			switch ( $statutory_task['recurring_type'] ){

				case 'EveryDay':
					self::createTasksFromSatutary($statutory_task);
				break;

				case 'EveryWeek':
					if( $statutory_task['recurring_day'] == date('D') )
					self::createTasksFromSatutary($statutory_task);
				break;

				case 'EveryMonth':
					if( $statutory_task['recurring_day'] == date('d') )
					self::createTasksFromSatutary($statutory_task);
				break;

				case 'EveryYear':
					$dayNdate  = date('d-n',strtotime($statutory_task['recurring_day']));
					if( $dayNdate == date('d-n') )
					self::createTasksFromSatutary($statutory_task);
				break;
			}
		}
	}

	public static function createTasksFromSatutary($statutory_task){

		$statutory_task['is_statutory'] = 1;
		$statutory_task['statutory_id'] = $statutory_task['id'];
		$task = Task::create( $statutory_task );

		// PushNotification::create([
		// 	'message'    => 'Recurring Task: ' . $statutory_task['task_details'],
		// 	'role'       => '',
		// 	'model_type' => Task::class,
		// 	'model_id'   => $task->id,
		// 	'user_id'    => Auth::id(),
		// 	'sent_to'    => $statutory_task['assign_to'],
		// ]);
	}

	public function getTaskRemark(Request $request){

		$id   = $request->input( 'id' );

		if (is_null($request->module_type)) {
			$remark = \App\Task::getremarks($id);
		} else {
			$remark = Remark::where('module_type', $request->module_type)->where('taskid', $id)->get();
		}

		return response()->json($remark,200);
	}

}
