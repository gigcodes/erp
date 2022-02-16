<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Flow;
use App\FlowType;
use App\FlowPath;
use App\FlowAction;
use App\FlowMessage;
use App\Email;
use App\DeveloperTask;
use App\ScrapLog;
use App\TaskCategory;
use App\Task;
use App\ErpLeads;
use Carbon\Carbon;
use App\Mail\ScheduledEmail;
use DB;
use App\Loggers\FlowLog;
use App\Loggers\FlowLogMessages;

class ScheduleEmails extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'command:schedule_emails';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Schedule Emails';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	public $log;
	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		//dd("test");
		$created_date = Carbon::now();
		$modalType = "";
		$leads = [];

		//$flows = Flow::select('id', 'flow_name as name')->get();
		$flows = Flow::whereIn('flow_name', ['task_pr'])->select('id', 'flow_name as name')->get();  

		FlowLog::log(["flow_id" => 0, "messages" => "Flow action started to check and found total flows : " . $flows->count()]);

		//$this->log[]="Flow action started to check and found total flows : ".$flows->count();
		foreach ($flows as $flow) {

			$flowActions = FlowAction::join('flow_paths', 'flow_actions.path_id', '=', 'flow_paths.id')
				->join('flows', 'flow_paths.flow_id', '=', 'flows.id')
				->join('flow_types', 'flow_types.id', '=', 'flow_actions.type_id')
				->select('flows.store_website_id', 'flow_actions.id as action_id', 'flow_actions.time_delay', 'flow_actions.message_title', 'flow_actions.condition', 'flow_types.type', 'flow_actions.time_delay_type', 'flows.flow_name')
				->where('flows.id', '=', $flow['id'])->whereNull('flow_paths.parent_action_id')->orderBy('flow_actions.rank', 'asc')
				->get();

			$flowlog = FlowLog::log(["flow_id" => $flow['id'], "messages" => $flow["name"] . " has found total Action  : " . $flowActions->count()]);


			if ($flowActions != null) {
				$i = 0;
				$created_date = Carbon::now();
				foreach ($flowActions as $key => $flowAction) {
					if ($flowAction['type'] == 'Time Delay') {
						if ($flowAction['time_delay_type'] == 'days') {
							$created_date = $created_date->addDays($flowAction['time_delay']);
						} elseif ($flowAction['time_delay_type'] == 'hours') {
							$created_date = $created_date->addHours($flowAction['time_delay']);
						} elseif ($flowAction['time_delay_type'] == 'minutes') {
							$created_date = $created_date->addMinutes($flowAction['time_delay']);
						}
					}
					$leadsFlowArray = ['add_to_cart', 'attach_images_for_product', 'dispatch_send_price', 'new_erp_lead', 'out_of_stock_subscribe', 'payment_failed'];
					if ($key == 0 and (in_array($flow['name'], $leadsFlowArray))) {
						$nameInDB = str_replace('_', '-', $flow['name']);
						$leads = ErpLeads::select(
							'erp_leads.id',
							'erp_leads.customer_id',
							'erp_leads.created_at as order_date',
							'customers.name as customer_name',
							'customers.email as customer_email',
							'customers.id as customer_id'
						)
							->leftJoin('customers', 'erp_leads.customer_id', '=', 'customers.id')
							->where('erp_leads.created_at', 'like', Carbon::now()->format('Y-m-d') . '%')
							->where("customers.store_website_id", $flow['store_website_id'])
							->whereIn('type', [$flow['name'], $nameInDB])
							->whereNotNull('customers.email')
							->get();
						$i = 1;
						$modalType =  ErpLeads::class;
					} else if ($key == 0 and $flow['name'] == 'wishlist') {
						$leads = \App\CustomerBasketProduct::join("customer_baskets as cb", "cb.id", "customer_basket_products.customer_basket_id")
							->where("cb.store_website_id", $flow['store_website_id'])
							->where('customer_basket_products.created_at', 'like', Carbon::now()->format('Y-m-d') . '%')
							->select('customer_basket_products.id', 'cb.customer_name', 'cb.customer_email', 'cb.customer_id')
							->get();
						$modalType =  CustomerBasketProduct::class;
					} else if ($key == 0 and $flow['name'] == 'delivered_order') {
						$leads = \App\Order::leftJoin('customers', 'orders.customer_id', '=', 'customers.id')
							->where("customers.store_website_id", $flow['store_website_id'])
							->whereIn('orders.order_status', ['delivered', 'Delivered'])
							->where('orders.date_of_delivery', 'like', Carbon::now()->format('Y-m-d') . '%')
							->select('orders.id', 'customers.name as customer_name', 'customers.email as customer_email', 'customers.id as customer_id')
							->get();
						$modalType =  Orders::class;
					} else if ($key == 0 and $flow['name'] == 'newsletters') {
						$leads = \App\Mailinglist::leftJoin('list_contacts', 'list_contacts.list_id', '=', 'mailinglists.id')
							->leftJoin('customers', 'customers.id', '=', 'list_contacts.customer_id')
							->where('mailinglists.website_id', $flow['store_website_id'])
							->where('list_contacts.created_at', 'like', Carbon::now()->format('Y-m-d') . '%')
							->where('customers.newsletter', 1)
							->select('mailinglists.id', 'customers.email as customer_email', 'customers.id as customer_id')->get();
						$modalType =  Mailinglist::class;
					} else if ($key == 0 and $flow['name'] == 'customer_win_back') {
						$leads = \App\Order::leftJoin('customers', 'orders.customer_id', '=', 'customers.id')
							->where("customers.store_website_id", $flow['store_website_id'])
							->whereIn(\DB::raw('lower(orders.order_status)'), ['Follow up for advance', 'Prepaid'])
							->where('orders.created_at', 'like', Carbon::now()->format('Y-m-d') . '%')
							->select('orders.id', 'customers.name as customer_name', 'customers.email as customer_email', 'customers.id as customer_id')
							->get();
						$modalType =  Orders::class;
					} else if ($key == 0 and $flow['name'] == 'order_reviews') {
						$leads = \App\Order::leftJoin('customers', 'orders.customer_id', '=', 'customers.id')
							->where("customers.store_website_id", $flow['store_website_id'])
							->whereIn('orders.order_status', ['delivered', 'Delivered'])
							->where('orders.date_of_delivery', 'like', Carbon::now()->format('Y-m-d') . '%')
							->select('orders.id', 'customers.name as customer_name', 'customers.email as customer_email', 'customers.id as customer_id')->get();
						$modalType =  Orders::class;
					} else if ($key == 0 and $flow['name'] == 'customer_post_purchase') { //
						$leads = [];
						$modalType =  Orders::class;
					} else if ($key == 0 and $flow['name'] == 'task_pr') { //
						$leads = [];
						$modalType =  DeveloperTask::class;
					} else if ($key == 0 and $flow['name'] == 'site_dev') { //
						$leads = [];
						$modalType =  Task::class;
					}
					$this->doProcess($flowAction, $modalType, $leads, $flow['store_website_id'], $created_date, $flowlog["id"]);
				}
			}
		}
	}

	public function doProcess($flowAction, $modalType, $leads, $store_website_id, $created_date, $flow_log_id, $leadType = 'customer')
	{
		$scraper_id = 0;
		/*FlowLogMessages::log([
			"flow_action" => ($flowAction['type'] == 'Condition') ? $flowAction['type'] . "-" . $flowAction['condition'] : $flowAction['type'],
			"modalType" => $modalType,
			"leads" => "",
			"store_website_id" => $store_website_id,
			"messages" => count($leads) . " founds to send message",
			"flow_log_id" => $flow_log_id,
			"scraper_id" => $scraper_id
		]);*/
		if ($flowAction['type'] == 'Send Message') {
			$message = FlowMessage::where('action_id', $flowAction['action_id'])->first();
			
			if ($message != null) {
				foreach ($leads as $lead) {
					if(isset($lead['scraper_id'])) {
						$scraper_id = $lead['scraper_id'];
					}
					if ($message['mail_tpl'] != '') {
						$bodyText  = @(string)view($message['mail_tpl']);
					} else {
						$arrToReplace = ['{FIRST_NAME}'];
						$valToReplace = [$lead->customer_name];
						$bodyText = str_replace($arrToReplace, $valToReplace, $message['html_content']);
					}
					$emailData['subject'] = $message['subject'];
					$emailData['template'] = $bodyText;
					$emailData['from'] = $message['sender_email_address'];
					$emailClass = (new ScheduledEmail($emailData))->build();

					$params = [
						'model_id'        => $lead->id,
						'model_type'      => $modalType,
						'type'            => 'outgoing',
						'seen'            => 0,
						'from'            =>  $emailClass->sendFrom,
						'to'              => $lead['customer_email'],
						//'to'              => "technodeviser05@gmail.com",
						'subject'         => $message['subject'],
						'message'         =>  $emailClass->render(),
						'template'        => 'flow#' . $flow['id'],
						'schedule_at'     => $created_date,
						'is_draft'     => 1,
					];
					Email::create($params);
					$flowLogMessage = FlowLogMessages::where([
						"flow_action" => $flowAction['type'],
						"modalType" => $modalType,
						"leads" => $lead['customer_email'],
						"store_website_id" => $store_website_id,
						"messages" => $bodyText.' ('.$created_date.')',
						"flow_log_id" => $flow_log_id,
						"scraper_id" => $scraper_id
					])->first();
					
					if($flowLogMessage == null) {
						FlowLogMessages::log([
							"flow_action" => $flowAction['type'],
							"modalType" => $modalType,
							"leads" => $lead['customer_email'],
							"store_website_id" => $store_website_id,
							"messages" => $bodyText.' ('.$created_date.')',
							"flow_log_id" => $flow_log_id,
							"scraper_id" => $scraper_id
						]);
					}
				}
			}
			else{
				FlowLogMessages::log([
					"flow_action" => $flowAction['type'],
					"modalType" => $modalType,
					"leads" => "",
					"store_website_id" => $store_website_id,
					"messages" => "flow Message is not found for email - ".$flowAction['action_id'],
					"flow_log_id" => $flow_log_id,
					"scraper_id" => $scraper_id
				]);
			}
		} else if ($flowAction['type'] == 'Whatsapp' || $flowAction['type'] == 'SMS') {
			$messageApplicationId = '';
			if ($flowAction['type'] == 'SMS') {
				$messageApplicationId = 3;
			} 
			foreach ($leads as $lead) {
				if(isset($lead['scraper_id'])) {
					$scraper_id = $lead['scraper_id'];
				}


				$extraParams = array();
				if($modalType == 'App\ErpLeads')
				{
					$extraParams = ['lead_id' => $lead->id];
				}
				elseif($modalType == 'App\CustomerBasketProduct')
				{
					$extraParams = ['customer_id' => $lead->id];
				}
				elseif($modalType == 'App\Orders')
				{
					$extraParams = ['order_id' => $lead->id];
				}
				elseif($modalType == 'App\DeveloperTask')
				{ 
					$extraParams = ['developer_task_id' => $lead->id];
				}
				elseif($modalType == 'App\Task')
				{
					$extraParams = ['task_id' => $lead->id];
				}
				elseif($modalType == 'App\Mailinglist')
				{
					$extraParams = ['email_id' => $lead->id];
				}
				

				if($leadType == 'customer') {
					$insertParams = [
						"message"                => $flowAction['message_title'],
						"status"                 => 1,
						"is_queue"               => 1,
						"approved"               => 0,
						//"user_id"                => 6,
						"number"                 => null,
						"message_application_id" => $messageApplicationId,
						'scheduled_at'     => $created_date,
						'flow_exit'     => 1,  /* if the message is coming from flow */
					];

					$createParams = array_merge($extraParams,$insertParams);// dd($createParams);

					$chatMessage = \App\ChatMessage::firstOrCreate(["customer_id"=>$lead->customer_id,
						"message"=>$flowAction['message_title'],"status"=>1,"is_queue"=>1,"message_application_id"=>$messageApplicationId], $createParams);
				} else {
					$insertParams = [
						"user_id"            => $lead->customer_id,
						"message"                => $flowAction['message_title'],
						"status"                 => 1,
						"is_queue"               => 1,
						"approved"               => 0,
						"number"                 => null,
						"message_application_id" => $messageApplicationId,
						'scheduled_at'     => $created_date,
						'flow_exit'     => 1,  /* if the message is coming from flow */
					];

					$createParams = array_merge($extraParams,$insertParams);//dd($createParams);

					$chatMessage = \App\ChatMessage::firstOrCreate(["user_id"=> $lead->customer_id,"message"=>$flowAction['message_title'],
						"status"=>1,"is_queue"=>1,"message_application_id"=>$messageApplicationId], $createParams);
				}
				

				
				$flowLogMessage = FlowLogMessages::where([
					"flow_action" => $flowAction['type'],
					"modalType" => $modalType,
					"leads" => $lead->customer_id,
					"store_website_id" => $store_website_id,
					"messages" => $flowAction['message_title'].' ('.$created_date.')',
					"flow_log_id" => $flow_log_id,
					"scraper_id" => $scraper_id
				])->first();
				
				if($flowLogMessage == null) {
					FlowLogMessages::log([
						"flow_action" => $flowAction['type'],
						"modalType" => $modalType,
						"leads" => $lead->customer_id,
						"store_website_id" => $store_website_id,
						"messages" => $flowAction['message_title'].' ('.$created_date.')',
						"flow_log_id" => $flow_log_id,
						"scraper_id" => $scraper_id
					]);
				}
				
			}
		} else if ($flowAction['type'] == 'Condition') {
			if ($flowAction['condition'] == 'customer has ordered before') {
				$flowPathsNew = FlowAction::join('flow_paths', 'flow_actions.path_id', '=', 'flow_paths.id')
					->join('flows', 'flow_paths.flow_id', '=', 'flows.id')
					->join('flow_types', 'flow_types.id', '=', 'flow_actions.type_id')
					->select('flows.store_website_id', 'flow_actions.id as action_id', 'flow_actions.time_delay', 'flow_actions.message_title', 'flow_types.type', 'flow_actions.time_delay_type', 'flows.flow_name')
					->where('flow_paths.parent_action_id', '=', $flowAction['action_id'])->orderBy('flow_actions.rank', 'asc')
					->get()->groupBy('path_for');
				foreach ($flowPathsNew as $path_for => $flowActiosnNew) {
					if ($path_for == 'yes') {
						$leads = \App\Order::leftJoin('customers', 'orders.customer_id', '=', 'customers.id')
							->where("customers.store_website_id", $store_website_id)
							->whereIn('orders.order_status', ['delivered', 'Delivered'])
							->where('orders.date_of_delivery', 'like', Carbon::now()->format('Y-m-d') . '%')
							->select('orders.id', 'orders.customer_id', 'customers.name as customer_name', 'customers.email as customer_email', 'customers.id as customer_id', \DB::raw('count(*) as duplicate'))
							->groupBy('orders.customer_id')->having(DB::raw('count(*)'), '>', 1)->get();
					} else {
						$leads = \App\Order::leftJoin('customers', 'orders.customer_id', '=', 'customers.id')
							->where("customers.store_website_id", $store_website_id)
							->whereIn('orders.order_status', ['delivered', 'Delivered'])
							->where('orders.date_of_delivery', 'like', Carbon::now()->format('Y-m-d') . '%')
							->select('orders.id', 'orders.customer_id', 'customers.name as customer_name', 'customers.email as customer_email', 'customers.id as customer_id', \DB::raw('count(*) as duplicate'))
							->groupBy('orders.customer_id')->having(DB::raw('count(*)'), 1)->get();
					}
					foreach ($flowActiosnNew as $flowActionNew) {
						$this->doProcess($flowActionNew, $modalType, $leads, $store_website_id, $created_date, $flow_log_id);
					}
				}
			} elseif ($flowAction['condition'] == 'check_if_pr_merged') {
				$flowPathsNew = FlowAction::join('flow_paths', 'flow_actions.path_id', '=', 'flow_paths.id')
					->join('flows', 'flow_paths.flow_id', '=', 'flows.id')
					->join('flow_types', 'flow_types.id', '=', 'flow_actions.type_id')
					->select('flows.store_website_id', 'flow_actions.id as action_id', 'flow_actions.time_delay', 'flow_actions.message_title', 'flow_types.type', 'flow_actions.condition', 'flow_actions.time_delay_type', 'flows.flow_name', 'flow_paths.path_for')
					->where('flow_paths.parent_action_id', '=', $flowAction['action_id'])->orderBy('flow_actions.rank', 'asc')
					->get()->groupBy('path_for'); 
				foreach ($flowPathsNew as $path_for => $flowActiosnNew) { 
					if ($path_for == 'yes') { 
						$leads = DeveloperTask::leftJoin('users', 'users.id', '=', 'developer_tasks.assigned_to')
							->whereDate('developer_tasks.created_at', '<=', $created_date)
							->where('scraper_id', '<>', 0)->whereNotNull('scraper_id')
							->where('is_pr_merged', 1)
							->select('developer_tasks.id', 'developer_tasks.scraper_id', 'users.name as customer_name', 'users.email as customer_email', 'users.id as customer_id')->get();
					} else { 
						$leads = DeveloperTask::leftJoin('users', 'users.id', '=', 'developer_tasks.assigned_to')
							->whereDate('developer_tasks.created_at', '<=', $created_date)
							->where('scraper_id', '<>', 0)->whereNotNull('scraper_id')
							->where('is_pr_merged', 0)
							->select('developer_tasks.id', 'developer_tasks.scraper_id', 'users.name as customer_name', 'users.email as customer_email', 'users.id as customer_id')->get();
						//dd($leads[0]);
					}  
					
					foreach ($flowActiosnNew as $flowActionNew) {
						$this->doProcess($flowActionNew, $modalType, $leads, $store_website_id, $created_date, $flow_log_id, 'user');
					}
				}
			} elseif ($flowAction['condition'] == 'check_scrapper_error_logs') {
				$leads = [];
				$flowPathsNew = FlowAction::join('flow_paths', 'flow_actions.path_id', '=', 'flow_paths.id')
					->join('flows', 'flow_paths.flow_id', '=', 'flows.id')
					->join('flow_types', 'flow_types.id', '=', 'flow_actions.type_id')
					->select('flows.store_website_id', 'flow_actions.id as action_id', 'flow_actions.time_delay', 'flow_actions.message_title', 'flow_types.type', 'flow_actions.time_delay_type', 'flows.flow_name')
					->where('flow_paths.parent_action_id', '=', $flowAction['action_id'])->orderBy('flow_actions.rank', 'asc')
					->where('path_for', 'yes')->get()->groupBy('path_for'); 
				foreach ($flowPathsNew as $path_for => $flowActiosnNew) {
					$leads = DeveloperTask::leftJoin('users', 'users.id', '=', 'developer_tasks.assigned_to')
						->whereDate('developer_tasks.created_at', '<=', $created_date)->where('developer_tasks.status', '<>', 'Done')->where('scraper_id', '<>', 0)->whereNotNull('scraper_id')->where('is_pr_merged', 1)
						->select('developer_tasks.id','developer_tasks.scraper_id', 'users.name as customer_name', 'users.email as customer_email', 'users.id as customer_id')->get();

					foreach ($leads as $key => $scrapperTask) {
						$log = ScrapLog::where('scraper_id', $scrapperTask['scraper_id'])->orderBy('id', 'desc')->first();
						if ($log['status'] == 'success') {
							unset($leads[$key]);
							DeveloperTask::where('id', $scrapperTask['id'])->update(['status' => 'Done']);
						}
					}
					foreach ($flowActiosnNew as $flowActionNew) {
						$this->doProcess($flowActionNew, $modalType, $leads, $store_website_id, $created_date, $flow_log_id, 'user');
					}
				}
			} elseif ($flowAction['condition'] == 'check_if_design_task_done') { 
				$leads = [];
				$flowPathsNew = FlowAction::join('flow_paths', 'flow_actions.path_id', '=', 'flow_paths.id')
					->join('flows', 'flow_paths.flow_id', '=', 'flows.id')
					->join('flow_types', 'flow_types.id', '=', 'flow_actions.type_id')
					->select('flows.store_website_id', 'flow_actions.id as action_id', 'flow_actions.time_delay', 'flow_actions.message_title', 'flow_types.type', 'flow_actions.condition', 'flow_actions.time_delay_type', 'flows.flow_name', 'flow_paths.path_for')
					->where('flow_paths.parent_action_id', '=', $flowAction['action_id'])->orderBy('flow_actions.rank', 'asc')
					->get()->groupBy('path_for');
				foreach ($flowPathsNew as $path_for => $flowActiosnNew) { 
					$designCategoryId = TaskCategory::where('title', 'like', 'Design%')->pluck('id')->first();
					if ($path_for == 'yes') { 
						$tasks = Task::leftJoin('users', 'users.id', '=', 'tasks.assign_to')
							    ->whereDate('tasks.created_at', '<=', $created_date)
								->where('category', $designCategoryId)
								->whereNotNull('is_completed')
							    ->select('tasks.id', 'tasks.task_subject', 'tasks.task_details', 'tasks.site_developement_id', 'users.name as customer_name', 'users.email as customer_email', 'users.id as customer_id')->get();
						$devCategoryId = TaskCategory::where('title', 'like', 'Site Devel%')->pluck('id')->first();
						foreach($tasks as $task) { 
							$ifAlreadyCreated = Task::where('parent_task_id', $task['id'])->first();
							if($ifAlreadyCreated == null) {
								$requests = array(
									'task_subject' => $task['task_subject'],
									'task_detail' => $task['task_details'],
									'task_asssigned_to' => 6,
									//'task_asssigned_from' => 10410,
									'category_id'=>$devCategoryId,
									'site_id'=>$task['site_developement_id'],
									'task_type'=>0,
									'repository_id'=>null,
									'cost'=>null,
									'task_id'=>null,
									'customer_id'=>null,
									'parent_task_id'=>$task['id']
								); 
								//app('App\Http\Controllers\TaskModuleController')->createTaskFromSortcut($requests);
								$check = (new Task)->createTaskFromSortcuts($requests);
							}
						} 
					} else {
						$leads = Task::leftJoin('users', 'users.id', '=', 'tasks.assign_to')->where('category', $designCategoryId)
							    ->whereDate('tasks.created_at', '<=', $created_date)->whereNull('is_completed')
							    ->select('tasks.id', 'users.name as customer_name', 'users.email as customer_email', 'users.id as customer_id')->get();
					}   
					foreach ($flowActiosnNew as $flowActionNew) {
						$this->doProcess($flowActionNew, $modalType, $leads, $store_website_id, $created_date, $flow_log_id);
					}
				}
			} elseif ($flowAction['condition'] == 'check_if_development_task_done') { 
				$leads = [];
				$flowPathsNew = FlowAction::join('flow_paths', 'flow_actions.path_id', '=', 'flow_paths.id')
					->join('flows', 'flow_paths.flow_id', '=', 'flows.id')
					->join('flow_types', 'flow_types.id', '=', 'flow_actions.type_id')
					->select('flows.store_website_id', 'flow_actions.id as action_id', 'flow_actions.time_delay', 'flow_actions.message_title', 'flow_types.type', 'flow_actions.condition', 'flow_actions.time_delay_type', 'flows.flow_name', 'flow_paths.path_for')
					->where('flow_paths.parent_action_id', '=', $flowAction['action_id'])->orderBy('flow_actions.rank', 'asc')
					->get()->groupBy('path_for');
				foreach ($flowPathsNew as $path_for => $flowActiosnNew) { 
					$devCategoryId = TaskCategory::where('title', 'like', 'Site Devel%')->pluck('id')->first();
					if ($path_for == 'yes') { 
						$tasks = Task::leftJoin('users', 'users.id', '=', 'tasks.assign_to')
							    ->whereDate('tasks.created_at', '<', $created_date)->where('category', $devCategoryId)->whereNotNull('is_completed')
							    ->select('tasks.id', 'tasks.task_subject', 'tasks.task_details', 'tasks.site_developement_id', 'users.name as customer_name', 'users.email as customer_email', 'users.id as customer_id')->get();
						$qaCatId = TaskCategory::where('title', 'like', 'qa%')->orWhere('title', 'like', '%testing%')->pluck('id')->first();
						if($qaCatId != null) {
							foreach($tasks as $task) { 
								$ifAlreadyCreated = Task::where('parent_task_id', $task['id'])->first();
								if($ifAlreadyCreated == null) {
									$requests = array(
										'task_subject' => $task['task_subject'],
										'task_detail' => $task['task_details'],
										'task_asssigned_to' => 6,
										'task_asssigned_from' => 6,
										'category_id'=>$qaCatId,
										'site_id'=>$task['site_developement_id'],
										'task_type'=>0,
										'repository_id'=>null,
										'cost'=>null,
										'task_id'=>null,
										'customer_id'=>null,
										'parent_task_id'=>$task['id']
									);
									$check = (new Task)->createTaskFromSortcuts($requests);
								}
							} 
						}
					} else {
						$leads = Task::leftJoin('users', 'users.id', '=', 'tasks.assign_to')->where('category', $devCategoryId)
							    ->whereDate('tasks.created_at', '<', $created_date)->whereNull('is_completed')
							    ->select('tasks.id', 'users.name as customer_name', 'users.email as customer_email', 'users.id as customer_id')->get();
					}   
					foreach ($flowActiosnNew as $flowActionNew) {
						$this->doProcess($flowActionNew, $modalType, $leads, $store_website_id, $created_date, $flow_log_id);
					}
				}
			}
		}
	}
}
