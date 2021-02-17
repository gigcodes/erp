<?php
namespace App\Http\Controllers\GoogleAddWord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\PushNotification;
use App\SatutoryTask;
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
use App\DocumentRemark;
use App\DeveloperTask;
use App\NotificationQueue;
use App\ChatMessage;
use App\DeveloperTaskHistory;
use App\ScheduledMessage;
use App\WhatsAppGroup;
use App\WhatsAppGroupNumber;
use App\PaymentReceipt;
use App\ChatMessagesQuickData;
use App\Hubstaff\HubstaffMember;
use App\Hubstaff\HubstaffTask;
use Illuminate\Pagination\LengthAwarePaginator;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Storage;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;
use App\Helpers\HubstaffTrait;

use Google\AdsApi\AdWords\AdWordsServices;
use Google\AdsApi\AdWords\AdWordsSession;
use Google\AdsApi\AdWords\AdWordsSessionBuilder;
use Google\AdsApi\AdWords\v201809\cm\Ad;
use Google\AdsApi\AdWords\v201809\cm\AdGroup;
use Google\AdsApi\AdWords\v201809\cm\AdGroupAd;
use Google\AdsApi\AdWords\v201809\cm\AdGroupAdOperation;
use Google\AdsApi\AdWords\v201809\cm\AdGroupAdService;
use Google\AdsApi\AdWords\v201809\cm\AdGroupAdStatus;
use Google\AdsApi\AdWords\v201809\cm\AdType;
use Google\AdsApi\AdWords\v201809\cm\ExpandedTextAd;
use Google\AdsApi\AdWords\v201809\cm\Operator;
use Google\AdsApi\AdWords\v201809\cm\OrderBy;
use Google\AdsApi\AdWords\v201809\cm\Paging;
use Google\AdsApi\AdWords\v201809\cm\Predicate;
use Google\AdsApi\AdWords\v201809\cm\PredicateOperator;
use Google\AdsApi\AdWords\v201809\cm\Selector;
use Google\AdsApi\AdWords\v201809\cm\SortOrder;
use Google\AdsApi\AdWords\v201809\cm\AdWordsServicesIntegrationTestProvider;
use Google\AdsApi\Common\OAuth2TokenBuilder;

use Google\AdsApi\AdWords\v201809\cm\Keyword;
use Google\AdsApi\AdWords\v201809\cm\KeywordMatchType;
use Google\AdsApi\AdWords\v201809\cm\Money;
use Google\AdsApi\AdWords\v201809\o\KeywordEstimateRequest;
use Google\AdsApi\AdWords\v201809\o\AdGroupEstimateRequest;
use Google\AdsApi\AdWords\v201809\o\setKeywordEstimateRequests;
use Google\AdsApi\AdWords\v201809\o\TargetingIdeaSelector;
use Google\AdsApi\AdWords\v201809\o\RequestType;
use Google\AdsApi\AdWords\v201809\o\IdeaType;
use Google\AdsApi\AdWords\v201809\o\setRequestedAttributeTypes;
use Google\AdsApi\AdWords\v201809\o\AttributeType;
use Google\AdsApi\AdWords\v201809\o\RelatedToQuerySearchParameter;
use Google\AdsApi\AdWords\v201809\o\TargetingIdeaService;
use Google\AdsApi\AdWords\v201809\cm\CampaignService;
use App\Http\Controllers\GoogleAdsController;
use Google\AdsApi\Common\AdsSession;

class googleAddsController extends Controller
{
	public function index( Request $request ) {

		$oAuth2Credential = (new OAuth2TokenBuilder())
            ->fromFile(storage_path('adsapi_php.ini'))
            ->build();

		$session = (new AdWordsSessionBuilder())
               ->fromFile(storage_path('adsapi_php.ini'))
               ->withOAuth2Credential($oAuth2Credential)
               ->build();

		$selector = new TargetingIdeaSelector();
		$selector->setRequestType(RequestType::IDEAS);
		$selector->setIdeaType(IdeaType::KEYWORD);


		$selector->setRequestedAttributeTypes(
		    [
		        AttributeType::KEYWORD_TEXT,
		        AttributeType::SEARCH_VOLUME,
		        AttributeType::AVERAGE_CPC,
		        AttributeType::COMPETITION,
		        AttributeType::CATEGORY_PRODUCTS_AND_SERVICES
		    ]
		);
		$paging = new Paging();
		$paging->setStartIndex(0);
		$paging->setNumberResults(10);
		$selector->setPaging($paging);

		$searchParameters = [];
		// Create related to query search parameter.
		$relatedToQuerySearchParameter = new RelatedToQuerySearchParameter();
		$relatedToQuerySearchParameter->setQueries(
		    [
		        'bakery',
		        'pastries',
		        'birthday cake'
		    ]
		);
		$searchParameters[] = $relatedToQuerySearchParameter;
		$selector->setSearchParameters($searchParameters);
		// Get keyword ideas.
		// $targetingIdeaService = new TargetingIdeaService();
		// $page = $targetingIdeaService->get($selector);


		// $account_id = 1;
		// $result = \App\GoogleAdsAccount::find($account_id);
  //       if (\Storage::disk('adsapi')->exists($account_id . '/' . $result->config_file_path)) {
  //           $storagepath = \Storage::disk('adsapi')->url($account_id . '/' . $result->config_file_path);
  //           $storagepath = storage_path('app/adsapi/' . $account_id . '/' . $result->config_file_path);
  //           /* echo $storagepath; exit;
  //       echo storage_path('adsapi_php.ini'); exit; */
  //           return $storagepath;
  //       } else {
  //           abort(404,"Please add adspai_php.ini file");
  //       }



		if ( $request->input( 'selected_user' ) == '' ) {
			$userid = Auth::id();
		} else {
			$userid = $request->input( 'selected_user' );
		}
		
		if ( !$request->input( 'type' ) || $request->input( 'type' ) == '' ) {
			$type = 'pending';
		} else {
			$type = $request->input( 'type' );
		}
		$activeCategories = TaskCategory::where('is_active',1)->pluck('id')->all();
		$categoryWhereClause = '';
		$category = '';
		$request->category = $request->category ? $request->category : 1;
		if ($request->category != '') {
			if ($request->category != 1) {
				$categoryWhereClause = "AND category = $request->category";
				$category = $request->category;
			} else {
				$category_condition  = implode(',', $activeCategories);
				if ($category_condition != '' || $category_condition != null) {
					$category_condition = '( ' . $category_condition . ' )';
					$categoryWhereClause = "AND category in " . $category_condition;
				} else {
					$categoryWhereClause = "";
				}
			}
		}

		$term = $request->term ?? "";
		$searchWhereClause = '';

		if ($request->term != '') {
			$searchWhereClause = ' AND (id LIKE "%' . $term . '%" OR category IN (SELECT id FROM task_categories WHERE title LIKE "%' . $term . '%") OR task_subject LIKE "%' . $term . '%" OR task_details LIKE "%' . $term . '%" OR assign_from IN (SELECT id FROM users WHERE name LIKE "%' . $term . '%") OR id IN (SELECT task_id FROM task_users WHERE user_id IN (SELECT id FROM users WHERE name LIKE "%' . $term . '%")))';
		}
		if ($request->get('is_statutory_query') != '' && $request->get('is_statutory_query') != null) {
		    $searchWhereClause .= ' AND is_statutory = ' . $request->get('is_statutory_query');
		}
		else {
			$searchWhereClause .= ' AND is_statutory != 3';
		}
		$orderByClause = ' ORDER BY';
		if($request->sort_by == 1) {
			$orderByClause .= ' tasks.created_at desc,';
		}
		else if($request->sort_by == 2) {
			$orderByClause .= ' tasks.created_at asc,';
		}
		$data['task'] = [];

		$search_term_suggestions = [];
		$search_suggestions = [];
		$assign_from_arr = array(0);
		$special_task_arr = array(0);
		$assign_to_arr = array(0);
		$data['task']['pending'] = [];
		$data['task']['statutory_not_completed'] = [];
		$data['task']['completed'] = [];
		if($type == 'pending') {
			$paginate = 50;
    		$page = $request->get('page', 1);
			$offSet = ($page * $paginate) - $paginate; 
			
			$orderByClause .= ' is_flagged DESC, message_created_at DESC';
			$isCompleteWhereClose = ' AND is_verified IS NULL ';

			if(!Auth::user()->isAdmin()) {
				$isCompleteWhereClose = ' AND is_completed IS NULL AND is_verified IS NULL ';
			}
			if($request->filter_by == 1) {
				$isCompleteWhereClose = ' AND is_completed IS NULL ';
			}
			if($request->filter_by == 2) {
				$isCompleteWhereClose = ' AND is_completed IS NOT NULL AND is_verified IS NULL ';
			}

			$data['task']['pending'] = DB::select('
			SELECT tasks.*

			FROM (
			  SELECT * FROM tasks
			  LEFT JOIN (
				  SELECT 
				  chat_messages.id as message_id, 
				  chat_messages.task_id, 
				  chat_messages.message, 
				  chat_messages.status as message_status, 
				  chat_messages.sent as message_type, 
				  chat_messages.created_at as message_created_at, 
				  chat_messages.is_reminder AS message_is_reminder,
				  chat_messages.user_id AS message_user_id
				  FROM chat_messages join chat_messages_quick_datas on chat_messages_quick_datas.last_communicated_message_id = chat_messages.id WHERE chat_messages.status not in(7,8,9) and chat_messages_quick_datas.model="App\\\\Task"
			  ) as chat_messages  ON chat_messages.task_id = tasks.id
			) AS tasks
			WHERE (deleted_at IS NULL) AND (id IS NOT NULL) AND is_statutory != 1 '.$isCompleteWhereClose.' AND (assign_from = ' . $userid . ' OR master_user_id = ' . $userid . ' OR id IN (SELECT task_id FROM task_users WHERE user_id = ' . $userid . ' AND type LIKE "%User%")) ' . $categoryWhereClause . $searchWhereClause .$orderByClause.' limit '.$paginate.' offset '.$offSet.'; ');


			foreach ($data['task']['pending'] as $task) {
				array_push($assign_to_arr, $task->assign_to);
				array_push($assign_from_arr, $task->assign_from);
				array_push($special_task_arr, $task->id);
			}
			
			$user_ids_from = array_unique($assign_from_arr);
			$user_ids_to = array_unique($assign_to_arr);
		
			foreach ($data['task']['pending'] as $task) {
				$search_suggestions[] = "#" . $task->id . " " . $task->task_subject . ' ' . $task->task_details;
				$from_exist = in_array($task->assign_from, $user_ids_from);
				if($from_exist) {
					$from_user = User::find($task->assign_from);
					if($from_user) {
						$search_term_suggestions[] = $from_user->name;
					}
				}

				$to_exist = in_array($task->assign_to, $user_ids_to);
				if($to_exist) {
					$to_user = User::find($task->assign_to);
					if($to_user) {
						$search_term_suggestions[] = $to_user->name;
					}
				}			
				$search_term_suggestions[] = "$task->id";
				$search_term_suggestions[] = $task->task_subject;
				$search_term_suggestions[] = $task->task_details;
			}
		}
		else if($type == 'completed') {
			$paginate = 50;
    		$page = $request->get('page', 1);
			$offSet = ($page * $paginate) - $paginate; 
			$orderByClause .= ' last_communicated_at DESC';
			$data['task']['completed'] = DB::select('
                SELECT *,
 				message_id,
                message,
                message_status,
                message_type,
                message_created_At as last_communicated_at
                FROM (
                  SELECT * FROM tasks
                 LEFT JOIN (
					SELECT 
					chat_messages.id as message_id, 
					chat_messages.task_id, 
					chat_messages.message, 
					chat_messages.status as message_status, 
					chat_messages.sent as message_type, 
					chat_messages.created_at as message_created_at, 
					chat_messages.is_reminder AS message_is_reminder,
					chat_messages.user_id AS message_user_id
					FROM chat_messages join chat_messages_quick_datas on chat_messages_quick_datas.last_communicated_message_id = chat_messages.id WHERE chat_messages.status not in(7,8,9) and chat_messages_quick_datas.model="App\\\\Task"
                 ) AS chat_messages ON chat_messages.task_id = tasks.id
                ) AS tasks
                WHERE (deleted_at IS NULL) AND (id IS NOT NULL) AND is_statutory != 1 AND is_verified IS NOT NULL AND (assign_from = ' . $userid . ' OR master_user_id = ' . $userid . ' OR id IN (SELECT task_id FROM task_users WHERE user_id = ' . $userid . ' AND type LIKE "%User%")) ' . $categoryWhereClause . $searchWhereClause .$orderByClause.' limit '.$paginate.' offset '.$offSet.';');
				

			foreach ($data['task']['completed'] as $task) {
				array_push($assign_to_arr, $task->assign_to);
				array_push($assign_from_arr, $task->assign_from);
				array_push($special_task_arr, $task->id);
			}
			
			$user_ids_from = array_unique($assign_from_arr);
			$user_ids_to = array_unique($assign_to_arr);
		
			foreach ($data['task']['completed'] as $task) {
				$search_suggestions[] = "#" . $task->id . " " . $task->task_subject . ' ' . $task->task_details;
				$from_exist = in_array($task->assign_from, $user_ids_from);
				if($from_exist) {
					$from_user = User::find($task->assign_from);
					if($from_user) {
						$search_term_suggestions[] = $from_user->name;
					}
				}

				$to_exist = in_array($task->assign_to, $user_ids_to);
				if($to_exist) {
					$to_user = User::find($task->assign_to);
					if($to_user) {
						$search_term_suggestions[] = $to_user->name;
					}
				}			
				$search_term_suggestions[] = "$task->id";
				$search_term_suggestions[] = $task->task_subject;
				$search_term_suggestions[] = $task->task_details;
			}
		} else if($type == 'statutory_not_completed') {
			$paginate = 50;
    		$page = $request->get('page', 1);
			$offSet = ($page * $paginate) - $paginate; 
			$orderByClause .= ' last_communicated_at DESC';
			$data['task']['statutory_not_completed'] = DB::select('
               SELECT *,
			   message_id,
               message,
               message_status,
               message_type,
               message_created_At as last_communicated_at

               FROM (
                 SELECT * FROM tasks
                 LEFT JOIN (
						SELECT 
						chat_messages.id as message_id, 
						chat_messages.task_id, 
						chat_messages.message, 
						chat_messages.status as message_status, 
						chat_messages.sent as message_type, 
						chat_messages.created_at as message_created_at, 
						chat_messages.is_reminder AS message_is_reminder,
						chat_messages.user_id AS message_user_id
						FROM chat_messages join chat_messages_quick_datas on chat_messages_quick_datas.last_communicated_message_id = chat_messages.id WHERE chat_messages.status not in(7,8,9) and chat_messages_quick_datas.model="App\\\\Task"
                 ) AS chat_messages ON chat_messages.task_id = tasks.id

               ) AS tasks
			   WHERE (deleted_at IS NULL) AND (id IS NOT NULL) AND is_statutory = 1 AND is_verified IS NULL AND (assign_from = ' . $userid . ' OR master_user_id = ' . $userid . ' OR id IN (SELECT task_id FROM task_users WHERE user_id = ' . $userid . ')) ' . $categoryWhereClause . $orderByClause .' limit '.$paginate.' offset '.$offSet.';');
			   
			   foreach ($data['task']['statutory_not_completed'] as $task) {
				array_push($assign_to_arr, $task->assign_to);
				array_push($assign_from_arr, $task->assign_from);
				array_push($special_task_arr, $task->id);
			}
			
			$user_ids_from = array_unique($assign_from_arr);
			$user_ids_to = array_unique($assign_to_arr);
		
			foreach ($data['task']['statutory_not_completed'] as $task) {
				$search_suggestions[] = "#" . $task->id . " " . $task->task_subject . ' ' . $task->task_details;
				$from_exist = in_array($task->assign_from, $user_ids_from);
				if($from_exist) {
					$from_user = User::find($task->assign_from);
					if($from_user) {
						$search_term_suggestions[] = $from_user->name;
					}
				}

				$to_exist = in_array($task->assign_to, $user_ids_to);
				if($to_exist) {
					$to_user = User::find($task->assign_to);
					if($to_user) {
						$search_term_suggestions[] = $to_user->name;
					}
				}			
				$search_term_suggestions[] = "$task->id";
				$search_term_suggestions[] = $task->task_subject;
				$search_term_suggestions[] = $task->task_details;
			}
		} else {
			return;
		}

		$users                     = User::oldest()->get()->toArray();
		$data['users']             = $users;
		$data['daily_activity_date'] = $request->daily_activity_date ? $request->daily_activity_date : date('Y-m-d');

		//My code start
		$selected_user = $request->input( 'selected_user' );
		$users         = Helpers::getUserArray( User::all() );
		$task_categories = TaskCategory::where('parent_id', 0)->get();
		$task_categories_dropdown = nestable(TaskCategory::where('is_approved', 1)->get()->toArray())->attr(['name' => 'category','class' => 'form-control input-sm'])
		->selected($request->category)
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
		$priority  = \App\ErpPriority::where('model_type', '=', Task::class)->pluck('model_id')->toArray();

		$openTask = \App\Task::join("users as u","u.id","tasks.assign_to")
		->whereNull("tasks.is_completed")
		->groupBy("tasks.assign_to")
		->select(\DB::raw("count(u.id) as total"),"u.name as person")
		->pluck("total","person");

		if($request->is_statutory_query == 3) {
			$title = 'Discussion tasks';
		} else {
			$title = 'Google Keyword Search';
		}

		if ($request->ajax()) {
			if($type == 'pending') {
				return view( 'task-module.partials.pending-row-ajax', compact('data', 'users', 'selected_user','category', 'term', 'search_suggestions', 'search_term_suggestions', 'tasks_view', 'categories', 'task_categories', 'task_categories_dropdown', 'priority','openTask','type','title'));
			}
			else if( $type == 'statutory_not_completed') {
				return view( 'task-module.partials.statutory-row-ajax', compact('data', 'users', 'selected_user','category', 'term', 'search_suggestions', 'search_term_suggestions', 'tasks_view', 'categories', 'task_categories', 'task_categories_dropdown', 'priority','openTask','type','title'));
			}
			else if( $type == 'completed') {
				return view( 'task-module.partials.completed-row-ajax', compact('data', 'users', 'selected_user','category', 'term', 'search_suggestions', 'search_term_suggestions', 'tasks_view', 'categories', 'task_categories', 'task_categories_dropdown', 'priority','openTask','type','title'));
			}
			else {
				return view( 'task-module.partials.pending-row-ajax', compact('data', 'users', 'selected_user','category', 'term', 'search_suggestions', 'search_term_suggestions', 'tasks_view', 'categories', 'task_categories', 'task_categories_dropdown', 'priority','openTask','type','title'));
			}
		}

		// if($request->is_statutory_query == 3) {
		// 	return view( 'task-module.discussion-tasks', compact('data', 'users', 'selected_user','category', 'term', 'search_suggestions', 'search_term_suggestions', 'tasks_view', 'categories', 'task_categories', 'task_categories_dropdown', 'priority','openTask','type','title'));
		// }
		// else {
		// 	return view( 'task-module.show', compact('data', 'users', 'selected_user','category', 'term', 'search_suggestions', 'search_term_suggestions', 'tasks_view', 'categories', 'task_categories', 'task_categories_dropdown', 'priority','openTask','type','title'));
		// }
		return view( 'google.google-adds.index', compact('data', 'users', 'selected_user','category', 'term', 'search_suggestions', 'search_term_suggestions', 'tasks_view', 'categories', 'task_categories', 'task_categories_dropdown', 'priority','openTask','type','title'));
	}
}
