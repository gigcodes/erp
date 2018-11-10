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

		$data['task']['pending']      = Task::where( 'is_statutory', '=', 0 )
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


		$users                     = User::oldest()->get()->toArray();
		$data['users']             = $users;

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

		$data                = $request->except( '_token' );
		$data['assign_from'] = Auth::id();

		if($data['is_statutory'] == 0) {
			$task = Task::create( $data );

			PushNotification::create( [
				'type'       => 'button',
				'message'    => 'Task Details: ' . $data['task_details'],
				'model_type' => Task::class,
				'model_id'   => $task->id,
				'user_id'    => Auth::id(),
				'sent_to'    => $request->input( 'assign_to' ),
				'role'       => '',
			] );
		}
		else {
			$task = SatutoryTask::create($data);

			PushNotification::create( [
				'message'    => 'Recurring Task Assigned: ' . $data['task_details'],
				'model_type' => Task::class,
				'model_id'   => $task->id,
				'user_id'    => Auth::id(),
				'sent_to'    => $request->input( 'assign_to' ),
				'role'       => '',
			] );
		}

		return redirect()->back()
		                 ->with( 'success', 'Task created successfully.' );
	}

	public function update() {

	}

	public function complete( $taskid ) {

		$task               = Task::find( $taskid );
		$task->is_completed = date( 'Y-m-d H:i:s' );
//		$task->deleted_at = null;

		if ( $task->assign_to == Auth::id() ) {
			$task->save();
		}

		if($task->is_statutory == 0)
			$message = 'Task Completed: ' . $task->task_details;
		else
			$message = 'Recurring Task Completed: ' . $task->task_details;

		PushNotification::create( [
			'message'    => $message,
			'model_type' => Task::class,
			'model_id'   => $task->id,
			'user_id'    => Auth::id(),
			'sent_to'    => $task->assign_from,
			'role'       => '',
		] );

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

		PushNotification::create( [
			'message'    => $message,
			'model_type' => SatutoryTask::class,
			'model_id'   => $task->id,
			'user_id'    => Auth::id(),
			'sent_to'    => $task->assign_from,
			'role'       => '',
		] );

		return redirect()->back()
		                 ->with( 'success', 'Statutory Task marked as completed.' );
	}

	public function addRemark( Request $request ) {

		$remark       = $request->input( 'remark' );
		$id           = $request->input( 'id' );
		$created_at = date('Y-m-d H:i:s');
		$update_at = date('Y-m-d H:i:s');
		DB::insert('insert into remarks (taskid, remark, created_at, updated_at) values (?, ?, ?, ?)', [$id  ,$remark , $created_at, $update_at]);
		return response()->json(['remark' => $remark ],200);
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

		$tasks = (new Task())->newQuery()->whereBetween('created_at',[$from,$to]);

		if( !empty($users) ){
			$tasks = $tasks->whereIn('assign_to',$users);
		}

		$tasks_list =  $tasks->get()->toArray();
		$tasks_csv = [];
		$userList = Helpers::getUserArray( User::all() );

		for ($i = 0 ; $i < sizeof($tasks_list) ; $i++){

			$task_csv = [];
			$task_csv['SrNo'] = $i+1;
			$task_csv['assign_from'] = $userList[$tasks_list[$i]['assign_from']];
			$task_csv['assign_to'] = $userList[$tasks_list[$i]['assign_to']];
			$task_csv['type'] = $tasks_list[$i]['is_statutory'] == 1 ? 'Statutory' : 'Other';
			$task_csv['task_details'] = $tasks_list[$i]['task_details'];
			$task_csv['completion_date'] = $tasks_list[$i]['completion_date'];
			$task_csv['remark'] = $tasks_list[$i]['remark'];
			$task_csv['completed_on'] = $tasks_list[$i]['is_completed'];
			$task_csv['created_on'] = $tasks_list[$i]['created_at'];

			array_push($tasks_csv,$task_csv);
		}


		$this->outputCsv('tasks.csv', $tasks_csv);
//		return redirect()->back();
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

		PushNotification::create([
			'message'    => 'Recurring Task: ' . $statutory_task['task_details'],
			'role'       => '',
			'model_type' => Task::class,
			'model_id'   => $task->id,
			'user_id'    => Auth::id(),
			'sent_to'    => $statutory_task['assign_to'],
		]);
	}
}
