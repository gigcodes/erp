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
use App\TaskCategory;
use App\Contact;
use App\Setting;
use App\Remark;
use App\DeveloperTask;
use App\NotificationQueue;
use App\ChatMessage;
use App\ScheduledMessage;
use Illuminate\Pagination\LengthAwarePaginator;

class TaskModuleController extends Controller {

	public function __construct() {

	}

	public function index( Request $request ) {

		if ( $request->input( 'selected_user' ) == '' ) {
			$userid = Auth::id();
		} else {
			$userid = $request->input( 'selected_user' );
		}

		$categoryWhereClause = '';
		$category = '';
		if ($request->category != '' && $request->category != 1) {
			$categoryWhereClause = "AND category = $request->category";

			$category = $request->category;
		}

		// dd($request->all());
		$term = $request->term ?? "";
		$searchWhereClause = '';

		if ($request->term != '') {
			$searchWhereClause = ' AND (id LIKE "%' . $term . '%" OR category IN (SELECT id FROM task_categories WHERE title LIKE "%' . $term . '%") OR task_subject LIKE "%' . $term . '%" OR task_details LIKE "%' . $term . '%" OR assign_from IN (SELECT id FROM users WHERE name LIKE "%' . $term . '%"))';
		}

		$data['task'] = [];

		// $data['task']['pending']      = Task::with('remarks')->where( 'is_statutory', '=', 0 )
		//                                ->where( 'is_completed', '=', null )
		// 								->where( function ($query ) use ($userid) {
		// 									return $query->orWhere( 'assign_from', '=', $userid )
		// 									             ->orWhere( 'assign_to', '=', $userid );
		// 								})
		//                                ->get()->toArray();

	 $data['task']['pending'] = DB::select('
               SELECT *,
							 (SELECT mm5.remark FROM remarks mm5 WHERE mm5.id = remark_id) AS remark,
							 (SELECT mm3.id FROM chat_messages mm3 WHERE mm3.id = message_id) AS message_id,
               (SELECT mm1.message FROM chat_messages mm1 WHERE mm1.id = message_id) as message,
               (SELECT mm2.status FROM chat_messages mm2 WHERE mm2.id = message_id) AS message_status,
               (SELECT mm4.sent FROM chat_messages mm4 WHERE mm4.id = message_id) AS message_type,
							 (SELECT mm6.is_reminder FROM chat_messages mm6 WHERE mm6.id = message_id) as message_is_reminder,
							 (SELECT mm7.user_id FROM chat_messages mm7 WHERE mm7.id = message_id) as message_user_id,
               (SELECT mm2.created_at FROM chat_messages mm2 WHERE mm2.id = message_id) as last_communicated_at

               FROM (
                 SELECT * FROM tasks

                 LEFT JOIN (
                   SELECT MAX(id) as remark_id, taskid
                   FROM remarks
									 WHERE module_type = "task"
                   GROUP BY taskid
                 ) AS remarks
                 ON tasks.id = remarks.taskid

                 LEFT JOIN (SELECT MAX(id) as message_id, task_id, message, MAX(created_at) as message_created_At FROM chat_messages WHERE chat_messages.status != 7 AND chat_messages.status != 8 AND chat_messages.status != 9 GROUP BY task_id ORDER BY chat_messages.created_at DESC) AS chat_messages
                 ON tasks.id = chat_messages.task_id

               ) AS tasks
               WHERE (deleted_at IS NULL) AND (id IS NOT NULL) AND is_statutory != 1 AND is_verified IS NULL AND (assign_from = ' . $userid . ' OR id IN (SELECT task_id FROM task_users WHERE user_id = ' . $userid . ' AND type LIKE "%User%")) ' . $categoryWhereClause . $searchWhereClause . '
               ORDER BY is_flagged DESC, last_communicated_at DESC;
						');

		// $currentPage = LengthAwarePaginator::resolveCurrentPage();
		// $perPage = Setting::get('pagination');
		// $currentItems = array_slice($data['task']['pending'], $perPage * ($currentPage - 1), $perPage);
		//
		// $data['task']['pending'] = new LengthAwarePaginator($currentItems, count($data['task']['pending']), $perPage, $currentPage, [
		// 	'path'	=> LengthAwarePaginator::resolveCurrentPath()
		// ]);

						// dd($data['task']['pending']);

						// $tasks = Task::all();
						//
						// foreach($tasks as $task) {
						// 	if ($task->assign_to != 0) {
						// 		$user = $task->assign_to;
						// 		$task->users()->syncWithoutDetaching($user);
						// 	}
						// }

		// $data['task']['completed']  = Task::where( 'is_statutory', '=', 0 )
		//                                     ->whereNotNull( 'is_completed'  )
		// 									->where( function ($query ) use ($userid) {
		// 										return $query->orWhere( 'assign_from', '=', $userid )
		// 										             ->orWhere( 'assign_to', '=', $userid );
		// 									});
		// if ($request->category != '') {
		// 	$data['task']['completed'] = $data['task']['completed']->where('category', $request->category);
		// }
		//
		// if ($request->term != '') {
		// 	$data['task']['completed'] = $data['task']['completed']->where(function ($query) use ($term) {
		// 		$query->whereRaw('category IN (SELECT id FROM task_categories WHERE name LIKE "%' . $term . '%") OR assign_from IN (SELECT id FROM users WHERE name LIKE "%' . $term . '%")')
		// 					->orWhere('id', 'LIKE', "%$term%")
		// 					->orWhere('task_details', 'LIKE', "%$term%")->orWhere('task_subject', 'LIKE', "%$term%");
		// 	});
		// }
		//
		// $data['task']['completed'] = $data['task']['completed']->get()->toArray();

		$data['task']['completed'] = DB::select('
                SELECT *,
 							 (SELECT mm5.remark FROM remarks mm5 WHERE mm5.id = remark_id) AS remark,
 							 (SELECT mm3.id FROM chat_messages mm3 WHERE mm3.id = message_id) AS message_id,
                (SELECT mm1.message FROM chat_messages mm1 WHERE mm1.id = message_id) as message,
                (SELECT mm2.status FROM chat_messages mm2 WHERE mm2.id = message_id) AS message_status,
                (SELECT mm4.sent FROM chat_messages mm4 WHERE mm4.id = message_id) AS message_type,
                (SELECT mm2.created_at FROM chat_messages mm2 WHERE mm2.id = message_id) as last_communicated_at

                FROM (
                  SELECT * FROM tasks

                  LEFT JOIN (
                    SELECT MAX(id) as remark_id, taskid
                    FROM remarks
 									 WHERE module_type = "task"
                    GROUP BY taskid
                  ) AS remarks
                  ON tasks.id = remarks.taskid

                  LEFT JOIN (SELECT MAX(id) as message_id, task_id, message, MAX(created_at) as message_created_At FROM chat_messages WHERE chat_messages.status != 7 AND chat_messages.status != 8 AND chat_messages.status != 9 GROUP BY task_id ORDER BY chat_messages.created_at DESC) AS chat_messages
                  ON tasks.id = chat_messages.task_id

                ) AS tasks
                WHERE (deleted_at IS NULL) AND (id IS NOT NULL) AND is_statutory != 1 AND is_verified IS NOT NULL AND (assign_from = ' . $userid . ' OR id IN (SELECT task_id FROM task_users WHERE user_id = ' . $userid . ' AND type LIKE "%User%")) ' . $categoryWhereClause . $searchWhereClause . '
                ORDER BY last_communicated_at DESC;
 						');


		// $satutory_tasks = SatutoryTask::latest()
		//                                          ->orWhere( 'assign_from', '=', $userid )
		// 										 ->orWhere( 'assign_to', '=', $userid )->whereNotNull('completion_date')
		//                                          ->get();
		//
		// foreach ($satutory_tasks as $task) {
		// 	switch ($task->recurring_type) {
		// 		case 'EveryDay':
		// 			if (Carbon::parse($task->completion_date)->format('Y-m-d') < date('Y-m-d')) {
		// 				$task->completion_date = null;
		// 				$task->save();
		// 			}
		// 			break;
		// 		case 'EveryWeek':
		// 			if (Carbon::parse($task->completion_date)->addWeek()->format('Y-m-d') < date('Y-m-d')) {
		// 				$task->completion_date = null;
		// 				$task->save();
		// 			}
		// 			break;
		// 		case 'EveryMonth':
		// 			if (Carbon::parse($task->completion_date)->addMonth()->format('Y-m-d') < date('Y-m-d')) {
		// 				$task->completion_date = null;
		// 				$task->save();
		// 			}
		// 			break;
		// 		case 'EveryYear':
		// 			if (Carbon::parse($task->completion_date)->addYear()->format('Y-m-d') < date('Y-m-d')) {
		// 				$task->completion_date = null;
		// 				$task->save();
		// 			}
		// 			break;
		// 		default:
		//
		// 	}
		// }

		// $data['task']['statutory'] = SatutoryTask::latest()->where(function ($query) use ($userid) {
		// 	$query->where('assign_from', $userid)
		//  				->orWhere('assign_to', $userid);
		// });
	 //
	 //
		// if ($request->category != '') {
		// 	$data['task']['statutory'] = $data['task']['statutory']->where('category', $request->category);
		// }
	 //
		// if ($request->term != '') {
		// 	$data['task']['statutory'] = $data['task']['statutory']->where(function ($query) use ($term) {
		// 		$query->whereRaw('category IN (SELECT id FROM task_categories WHERE name LIKE "%' . $term . '%") OR assign_from IN (SELECT id FROM users WHERE name LIKE "%' . $term . '%")')
		// 					->orWhere('id', 'LIKE', "%$term%")
		// 					->orWhere('task_details', 'LIKE', "%$term%")->orWhere('task_subject', 'LIKE', "%$term%");
		// 	});
		// }
	 //
   // $data['task']['statutory'] = $data['task']['statutory']->get()->toArray();

		// $data['task']['statutory_completed'] = Task::latest()->where( 'is_statutory', '=', 1 )
		//                                    ->whereNotNull( 'is_completed'  )
		//                                    ->where( function ($query ) use ($userid) {
		// 	                                   return $query->orWhere('assign_from', '=', $userid)
		// 	                                                ->orWhere('assign_to', '=', $userid);
		//                                    })
		//                                    ->get()->toArray();

		 $data['task']['statutory_not_completed'] = DB::select('
	               SELECT *,
								 (SELECT mm5.remark FROM remarks mm5 WHERE mm5.id = remark_id) AS remark,
								 (SELECT mm3.id FROM chat_messages mm3 WHERE mm3.id = message_id) AS message_id,
	               (SELECT mm1.message FROM chat_messages mm1 WHERE mm1.id = message_id) as message,
	               (SELECT mm2.status FROM chat_messages mm2 WHERE mm2.id = message_id) AS message_status,
	               (SELECT mm4.sent FROM chat_messages mm4 WHERE mm4.id = message_id) AS message_type,
	               (SELECT mm2.created_at FROM chat_messages mm2 WHERE mm2.id = message_id) as last_communicated_at

	               FROM (
	                 SELECT * FROM tasks

	                 LEFT JOIN (
	                   SELECT MAX(id) as remark_id, taskid
	                   FROM remarks
										 WHERE module_type = "task"
	                   GROUP BY taskid
	                 ) AS remarks
	                 ON tasks.id = remarks.taskid

	                 LEFT JOIN (SELECT MAX(id) as message_id, task_id, message, MAX(created_at) as message_created_At FROM chat_messages WHERE chat_messages.status != 7 AND chat_messages.status != 8 AND chat_messages.status != 9 GROUP BY task_id ORDER BY chat_messages.created_at DESC) AS chat_messages
	                 ON tasks.id = chat_messages.task_id

	               ) AS tasks
	               WHERE (deleted_at IS NULL) AND (id IS NOT NULL) AND is_statutory = 1 AND is_completed IS NULL AND (assign_from = ' . $userid . ' OR id IN (SELECT task_id FROM task_users WHERE user_id = ' . $userid . ')) ' . $categoryWhereClause . $searchWhereClause . '
	               ORDER BY last_communicated_at DESC;
							');
							// dd($data['task']['statutory_completed']);

							// foreach ($data['task']['statutory_completed'] as $task) {
							// 	dump($task->id);
							// }
							//
							// dd('stap');

		// $data['task']['statutory_today'] = Task::latest()->where( 'is_statutory', '=', 1 )
		//                                            ->where( 'is_completed', '=',  null  )
		//                                            ->where( function ($query ) use ($userid) {
		// 	                                           return $query->orWhere( 'assign_from', '=', $userid )
		// 	                                                        ->orWhere( 'assign_to', '=', $userid );
		//                                            });
		//
		// if ($request->category != '') {
		// 	$data['task']['statutory_today'] = $data['task']['statutory_today']->where('category', $request->category);
		// }
		//
		// if ($request->term != '') {
		// 	$data['task']['statutory_today'] = $data['task']['statutory_today']->where(function ($query) use ($term) {
		// 		$query->whereRaw('category IN (SELECT id FROM task_categories WHERE name LIKE "%' . $term . '%") OR assign_from IN (SELECT id FROM users WHERE name LIKE "%' . $term . '%")')
		// 					->orWhere('id', 'LIKE', "%$term%")
		// 					->orWhere('task_details', 'LIKE', "%$term%")->orWhere('task_subject', 'LIKE', "%$term%");
		// 	});
		// }
		//
    //  $data['task']['statutory_today'] = $data['task']['statutory_today']->get()->toArray();

//		$data['task']['statutory_completed_ids'] = [];
//		foreach ($data['task']['statutory_completed'] as $item)
//			$data['task']['statutory_completed_ids'][] =  $item['statutory_id'];


		// $data['task']['deleted']   = Task::onlyTrashed()
		//                                 ->where( 'is_statutory', '=', 0 )
		// 								->where( function ($query ) use ($userid) {
		// 									return $query->orWhere( 'assign_from', '=', $userid )
		// 									             ->orWhere( 'assign_to', '=', $userid );
		// 								});
	 //
		// if ($request->category != '') {
		// 	$data['task']['deleted'] = $data['task']['deleted']->where('category', $request->category);
		// }
	 //
		// if ($request->term != '') {
		// 	$data['task']['deleted'] = $data['task']['deleted']->where(function ($query) use ($term) {
		// 		$query->whereRaw('category IN (SELECT id FROM task_categories WHERE name LIKE "%' . $term . '%") OR assign_from IN (SELECT id FROM users WHERE name LIKE "%' . $term . '%")')
		// 					->orWhere('id', 'LIKE', "%$term%")
		// 					->orWhere('task_details', 'LIKE', "%$term%")->orWhere('task_subject', 'LIKE', "%$term%");
		// 	});
		// }
	 //
   // $data['task']['deleted'] = $data['task']['deleted']->get()->toArray();

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

		$search_suggestions = [];
		foreach ($data['task']['pending'] as $task) {
			$search_suggestions[] = "#" . $task->id . " " . $task->task_subject . ' ' . $task->task_details;
		}

		// $category = '';

		//My code start
		$selected_user = $request->input( 'selected_user' );
		$users         = Helpers::getUserArray( User::all() );
		$task_categories = TaskCategory::where('parent_id', 0)->get();
		$task_categories_dropdown = TaskCategory::attr(['name' => 'category','class' => 'form-control input-sm', 'placeholder' => 'Select a Category'])
		                                        ->renderAsDropdown();

		$categories = [];
		foreach (TaskCategory::all() as $category) {
			$categories[$category->id] = $category->title;
		}

		if ( ! empty( $selected_user ) && ! Helpers::getadminorsupervisor() ) {
			return response()->json( [ 'user not allowed' ], 405 );
		}
		//My code end

		$tasks_view = [];

		return view( 'task-module.show', compact('data', 'users', 'selected_user','category', 'term', 'search_suggestions', 'tasks_view', 'categories', 'task_categories', 'task_categories_dropdown'));
	}

	public function store( Request $request ) {
		$this->validate($request, [
			'task_subject'	=> 'required',
			'task_details'	=> 'required',
			'assign_to'			=> 'required_without:assign_to_contacts'
		]);

		$data                = $request->except( '_token' );
		$data['assign_from'] = Auth::id();

		if ($request->task_type == 'quick_task') {
			$data['is_statutory'] = 0;
			$data['category'] = 6;
			$data['model_type'] = $request->model_type;
			$data['model_id'] = $request->model_id;
		}

		if ($request->task_type == 'note-task') {
			$main_task = Task::find($request->task_id);


		} else {
			if ($request->assign_to) {
				$data['assign_to'] = $request->assign_to[0];
			} else {
				$data['assign_to'] = $request->assign_to_contacts[0];
			}
		}

			$task = Task::create($data);
			// dd($request->all());
			if ($request->is_statutory == 3) {
				foreach ($request->note as $note) {
					if ($note != null) {
						Remark::create([
							'taskid'	=> $task->id,
							'remark'	=> $note,
							'module_type'	=> 'task-note'
						]);
					}
				}
			}

			if ($request->task_type != 'note-task') {
				if ($request->assign_to) {
					foreach ($request->assign_to as $user_id) {
						$task->users()->attach([$user_id => ['type' => User::class]]);
					}
				}

				if ($request->assign_to_contacts) {
					foreach ($request->assign_to_contacts as $contact_id) {
						$task->users()->attach([$contact_id => ['type' => Contact::class]]);
					}
				}
			}

			if ($task->is_statutory != 1) {
				$message = "#" . $task->id . ". " . $task->task_subject . ". " . $task->task_details;
			} else {
				$message = $task->task_subject . ". " . $task->task_details;
			}

			$params = [
			 'number'       => NULL,
			 'user_id'      => Auth::id(),
			 'approved'     => 1,
			 'status'       => 2,
			 'task_id'			=> $task->id,
			 'message'      => $message
		 ];

		 if (count($task->users) > 0) {
			 if ($task->assign_from == Auth::id()) {
				 foreach ($task->users as $key => $user) {
					 if ($key == 0) {
						 $params['erp_user'] = $user->id;
					 } else {
						 app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi($user->phone, $user->whatsapp_number, $params['message']);
					 }
				 }
			 } else {
				 foreach ($task->users as $key => $user) {
					 if ($key == 0) {
						 $params['erp_user'] = $task->assign_from;
					 } else {
						 if ($user->id != Auth::id()) {
							 app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi($user->phone, $user->whatsapp_number, $params['message']);
						 }
					 }
				 }
			 }
		 }

		 if (count($task->contacts) > 0) {
			 foreach ($task->contacts as $key => $contact) {
				 if ($key == 0) {
					 $params['contact_id'] = $task->assign_to;
				 } else {
					 app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi($contact->phone, NULL, $params['message']);
				 }
			 }
		 }

			$chat_message = ChatMessage::create($params);

			$myRequest = new Request();
      $myRequest->setMethod('POST');
      $myRequest->request->add(['messageId' => $chat_message->id]);

      app('App\Http\Controllers\WhatsAppController')->approveMessage('task', $myRequest);


		if ($request->ajax()) {
			return response('success');
		}


		return redirect()->back()
		                 ->with( 'success', 'Task created successfully.' );
	}

	public function flag(Request $request)
	{
		$task = Task::find($request->task_id);

		if ($task->is_flagged == 0) {
			$task->is_flagged = 1;
		} else {
			$task->is_flagged = 0;
		}

		$task->save();

		return response()->json(['is_flagged' => $task->is_flagged]);
	}

	public function loadView(Request $request)
	{
		$tasks = Task::whereIn('id', $request->selected_tasks)->get();
		$users = Helpers::getUserArray(User::all());
		$view = view('task-module.partials.task-view', [
			'tasks_view' => $tasks,
			'users'			=> $users
			])->render();

		return response()->json([
			'view'	=> $view
		]);
	}

	public function assignMessages(Request $request)
	{
		$messages_ids = json_decode($request->selected_messages, true);

		foreach ($messages_ids as $message_id) {
			$message = ChatMessage::find($message_id);
			$message->task_id = $request->task_id;
			$message->save();
		}

		return redirect()->back()->withSuccess('You have successfully assign messages');
	}

	public function messageReminder(Request $request)
	{
		$this->validate($request, [
			'message_id'		=> 'required|numeric',
			'reminder_date'	=> 'required'
		]);

		$message = ChatMessage::find($request->message_id);

		$additional_params = [
			'user_id'	=> $message->user_id,
			'task_id'	=> $message->task_id,
			'erp_user'	=> $message->erp_user,
			'contact_id'	=> $message->contact_id,
		];

		$params = [
			'user_id'       => Auth::id(),
			'message'       => "Reminder - " . $message->message,
			'type'					=> 'task',
			'data'					=> json_encode($additional_params),
			'sending_time'  => $request->reminder_date
		];

		ScheduledMessage::create($params);

		return redirect()->back()->withSuccess('You have successfully set a reminder!');
	}

	public function convertTask(Request $request, $id)
	{
		$task = Task::find($id);

		$task->is_statutory = 3;
		$task->save();

		return response('success', 200);
	}

	public function updateSubject(Request $request, $id)
	{
		$task = Task::find($id);
		$task->task_subject = $request->subject;
		$task->save();

		return response('success', 200);
	}

	public function addNote(Request $request, $id)
	{
		Remark::create([
			'taskid'	=> $id,
			'remark'	=> $request->note,
			'module_type'	=> 'task-note'
		]);

		return response('success', 200);
	}

	public function addSubnote(Request $request, $id)
	{
		Remark::create([
			'taskid'	=> $id,
			'remark'	=> $request->note,
			'module_type'	=> 'task-note-subnote'
		]);

		return response('success', 200);
	}

	public function updateCategory(Request $request, $id)
	{
		$task = Task::find($id);
		$task->category = $request->category;
		$task->save();

		return response('success', 200);
	}

	public function show($id)
	{
		$task = Task::find($id);

		if ((!$task->users->contains(Auth::id()) && $task->is_private == 1) && ($task->assign_from != Auth::id() && $task->contacts()->count() > 0)) {
			return redirect()->back()->withErrors("This task is private!");
		}

		$users = User::all();
		$users_array = Helpers::getUserArray(User::all());
		$categories = TaskCategory::attr(['title' => 'category','class' => 'form-control input-sm', 'placeholder' => 'Select a Category', 'id' => 'task_category'])
																						->selected($task->category)
		                                        ->renderAsDropdown();

		return view('task-module.task-show', [
			'task'	=> $task,
			'users'	=> $users,
			'users_array'	=> $users_array,
			'categories'	=> $categories,
		]);
	}

	public function update(Request $request, $id) {
		$this->validate($request, [
			'assign_to.*'	=> 'required_without:assign_to_contacts'
		]);

		$task = Task::find($id);
		$task->users()->detach();
		$task->contacts()->detach();

		if ($request->assign_to) {
			foreach ($request->assign_to as $user_id) {
				$task->users()->attach([$user_id => ['type' => User::class]]);
			}

			$task->assign_to = $request->assign_to[0];
			$task->save();
		}

		if ($request->assign_to_contacts) {
			foreach ($request->assign_to_contacts as $contact_id) {
				$task->users()->attach([$contact_id => ['type' => Contact::class]]);
			}

			$task->assign_to = $request->assign_to_contacts[0];
			$task->save();
		}

		return redirect()->route('task.show', $id)->withSuccess('You have successfully reassigned users!');
	}

	public function makePrivate(Request $request, $id)
	{
		$task = Task::find($id);

		if ($task->is_private == 1) {
			$task->is_private = 0;
		} else {
			$task->is_private = 1;
		}

		$task->save();

		return response()->json([
			'task'	=> $task
		]);
	}

	public function isWatched(Request $request, $id)
	{
		$task = Task::find($id);

		if ($task->is_watched == 1) {
			$task->is_watched = 0;
		} else {
			$task->is_watched = 1;
		}

		$task->save();

		return response()->json([
			'task'	=> $task
		]);
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
			if ($request->type == 'complete') {
				if ($item->is_completed == '') {
					$item->is_completed = date( 'Y-m-d H:i:s' );
				} else if ($item->is_verified == '') {
					$item->is_verified = date( 'Y-m-d H:i:s' );
				}
			} else if ($request->type == 'clear') {
				$item->is_completed = NULL;
				$item->is_verified = NULL;
			}

			$item->save();
		}

		if ($request->type == 'complete') {
			if ($task->is_completed == '') {
				$task->is_completed = date( 'Y-m-d H:i:s' );
			} else if ($task->is_verified == '') {
				$task->is_verified = date( 'Y-m-d H:i:s' );
			}
		} else if ($request->type == 'clear') {
			$item->is_completed = NULL;
			$item->is_verified = NULL;
		}

		$task->save();

		// if($task->is_statutory == 0)
		// 	$message = 'Task Completed: ' . $task->task_details;
		// else
		// 	$message = 'Recurring Task Completed: ' . $task->task_details;

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
			return response()->json([
				'task'	=> $task
			]);
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

		if ($request->module_type == 'task-discussion') {
			// NotificationQueueController::createNewNotification([
			// 	'message' => 'Remark for Developer Task',
			// 	'timestamps' => ['+0 minutes'],
			// 	'model_type' => DeveloperTask::class,
			// 	'model_id' =>  $id,
			// 	'user_id' => Auth::id(),
			// 	'sent_to' => $request->user == Auth::id() ? 6 : $request->user,
			// 	'role' => '',
			// ]);

			// NotificationQueueController::createNewNotification([
			// 	'message' => 'Remark for Developer Task',
			// 	'timestamps' => ['+0 minutes'],
			// 	'model_type' => DeveloperTask::class,
			// 	'model_id' =>  $id,
			// 	'user_id' => Auth::id(),
			// 	'sent_to' => 56,
			// 	'role' => '',
			// ]);
		}

		// if ($request->module_type == 'developer') {
		// 	$task = DeveloperTask::find($id);
		//
		// 	if ($task->user->id == Auth::id()) {
		// 		NotificationQueueController::createNewNotification([
		// 			'message' => 'New Task Remark',
		// 			'timestamps' => ['+0 minutes'],
		// 			'model_type' => DeveloperTask::class,
		// 			'model_id' =>  $task->id,
		// 			'user_id' => Auth::id(),
		// 			'sent_to' => 6,
		// 			'role' => '',
		// 		]);
		//
		// 		NotificationQueueController::createNewNotification([
		// 			'message' => 'New Task Remark',
		// 			'timestamps' => ['+0 minutes'],
		// 			'model_type' => DeveloperTask::class,
		// 			'model_id' =>  $task->id,
		// 			'user_id' => Auth::id(),
		// 			'sent_to' => 56,
		// 			'role' => '',
		// 		]);
		// 	} else {
		// 		NotificationQueueController::createNewNotification([
		// 			'message' => 'New Task Remark',
		// 			'timestamps' => ['+0 minutes'],
		// 			'model_type' => DeveloperTask::class,
		// 			'model_id' =>  $task->id,
		// 			'user_id' => Auth::id(),
		// 			'sent_to' => $task->user_id,
		// 			'role' => '',
		// 		]);
		// 	}
		// }
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
		// dump($task);
		$classes .= ' '. ( (empty($task) && $task->assign_from == Auth::user()->id) ? 'mytask' : '' ) . ' ';
		$classes .= ' '.( (empty($task) && time() > strtotime( $task->completion_date. ' 23:59:59'  ))  ? 'isOverdue' : '').' ';


		$task_status = empty($task) ? Helpers::statusClass($task->assign_status) : '';

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
