<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Flow;
use App\FlowType;
use App\FlowPath;
use App\FlowAction;
use App\FlowMessage;
use App\Email;

use App\ErpLeads;
use Carbon\Carbon;
use App\Mail\ScheduledEmail;

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

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
		$created_date = Carbon::now();
		$flows = Flow::whereIn('flow_name', ['add_to_cart', 'wishlist', 'delivered_order'])->select('id', 'flow_name as name')->get();
		foreach($flows as $flow) {
			$flowActions =FlowAction::join('flow_paths', 'flow_actions.path_id', '=', 'flow_paths.id')
			->join('flows', 'flow_paths.flow_id', '=', 'flows.id')
			->join('flow_types', 'flow_types.id', '=', 'flow_actions.type_id')
            ->select('flow_actions.id as action_id','flow_actions.time_delay','flow_actions.message_title','flow_types.type','flow_actions.time_delay_type', 'flows.flow_name')
			->where('flows.id', '=', $flow['id'])->orderBy('flow_actions.rank', 'asc')
			->get();
			
			if($flowActions != null) { 
				$i = 0;
				$created_date = Carbon::now();
				foreach($flowActions as $key=>$flowAction) {
						if($flowAction['type'] == 'Time Delay') {
							if($flowAction['time_delay_type'] =='days'){
								$created_date = $created_date->addDays($flowAction['time_delay']);
							} elseif($flowAction['time_delay_type'] =='hours') {
								$created_date = $created_date->addHours($flowAction['time_delay']);
							} elseif($flowAction['time_delay_type'] =='minutes') {
								$created_date = $created_date->addMinutes($flowAction['time_delay']);
							}
						}
					
					if($key == 0 and $flow['name'] == 'add_to_cart') {
						$leads = ErpLeads::select('erp_leads.id','erp_leads.customer_id','erp_leads.created_at as order_date',
						'customers.name as customer_name','customers.email as customer_email','customers.id as customer_id')
								->leftJoin('customers','erp_leads.customer_id','=','customers.id')
							    ->where('erp_leads.created_at', 'like', Carbon::now()->format('Y-m-d').'%')
							    ->where("customers.store_website_id",$flow['store_website_id'])
								->whereNotNull('customers.email')
								->get(); 
								$i = 1;
						$modalType =  ErpLeads::class;
					} else if($key == 0 and $flow['name'] == 'wishlist') {
						$leads = \App\CustomerBasketProduct::join("customer_baskets as cb", "cb.id", "customer_basket_products.customer_basket_id")
						->where("cb.store_website_id",$flow['store_website_id'])
						->where('customer_basket_products.created_at', 'like', Carbon::now()->format('Y-m-d').'%')
						->select('customer_basket_products.id', 'cb.customer_name', 'cb.customer_email', 'cb.customer_id')
						->get();
						$modalType =  CustomerBasketProduct::class;
					} else if($key == 0 and $flow['name'] == 'delivered_order') {
						$leads = \App\Order::leftJoin('customers','orders.customer_id','=','customers.id')
						->where("customers.store_website_id",$flow['store_website_id'])
						->whereIn('orders.order_status', ['delivered', 'Delivered'])
						->where('orders.date_of_delivery', 'like', Carbon::now()->format('Y-m-d').'%')
						->select('orders.id', 'customers.name as customer_name','customers.email as customer_email','customers.id as customer_id')
						->get();
						$modalType =  Orders::class;
					} 
					if($flowAction['type'] == 'Send Message') {
						$message = FlowMessage::where('action_id', $flowAction['action_id'])->first();
						if($message != null) {
							foreach($leads as $lead) {
								if($message['mail_tpl'] != '') {
									$bodyText  = @(string)view($message['mail_tpl']);
								}
								else {
									$arrToReplace = ['{FIRST_NAME}'];
									$valToReplace = [$lead->customer_name];
									$bodyText = str_replace($arrToReplace,$valToReplace,$message['html_content']);
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
									'template'        => 'flow#'.$flow['id'],
									'schedule_at'     => $created_date,
									'is_draft'     => 1,
								];
								Email::create($params);
							}
						}
					} else if($flowAction['type'] == 'Whatsapp' || $flowAction['type'] == 'SMS') {	
						foreach($leads as $lead) {
						 $insertParams = [
                                        "customer_id"            => $lead->customer_id,
                                        "message"                => $flowAction['message_title'],
                                        "status"                 => 1,
                                        "is_queue"               => 1,
                                        "approved"               => 1,
                                        "user_id"                => 6,
                                        "number"                 => null,
                                        "message_application_id" => 10001,
										'scheduled_at'     => $created_date,
                                       ];

                                    $chatMessage = \App\ChatMessage::create($insertParams);
						}
					}				
				}
			}
		}
		
    }
}
