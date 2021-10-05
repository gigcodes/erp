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
		$flows =FlowAction::join('flow_paths', 'flow_actions.path_id', '=', 'flow_paths.id')
			->join('flows', 'flow_paths.flow_id', '=', 'flows.id')
			->join('flow_types', 'flow_types.id', '=', 'flow_actions.type_id')
            ->select('flow_actions.id as action_id','flow_actions.time_delay','flow_types.type','flow_actions.time_delay_type', 'flows.flow_name')
			->where('flows.flow_name', '=', 'add to cart')->orderBy('flow_actions.rank', 'asc')
			->get();
			$i = 0;
		if($flows != null) {
			$created_date = Carbon::now();
			foreach($flows as $key=>$flow) {
				//if($key == 0) {
					if($flow['type'] == 'Time Delay') {
						if($flow['time_delay_type'] =='days'){
							$created_date = $created_date->addDays($flow['time_delay']);
						} elseif($flow['time_delay_type'] =='hours') {
							$created_date = $created_date->addHours($flow['time_delay']);
						} elseif($flow['time_delay_type'] =='minutes') {
							$created_date = $created_date->addMinutes($flow['time_delay']);
						}
					}
				//}
				if($key == 0) {
					$leads = ErpLeads::select('erp_leads.id','erp_leads.customer_id','erp_leads.created_at as order_date',
					'users.name as customer_name','users.email as customer_email')
                            ->leftJoin('users','erp_leads.customer_id','=','users.id')
                           ->where('erp_leads.created_at', 'like', Carbon::now()->format('Y-m-d').'%')
						   ->whereNotNull('users.email')
							//->where('erp_leads.created_at', '>=', $created_date)
							->get(); 
							$i = 1;
				} 
				if($flow['type'] == 'Send Message') {
					$message = FlowMessage::where('action_id', $flow['action_id'])->first();
					if($message != null){
						foreach($leads as $lead) {
							$params = [
								'model_id'        => $lead->id,
								'model_type'      => ErpLeads::class,
								'type'            => 'outgoing',
								'seen'            => 0,
								'from'            => $message['sender_email_address'],
								'to'              => $lead['customer_email'],
								'subject'         => $message['subject'],
								'message'         => $message['html_content'],
								'template'        => 'customer-simple',
								'schedule_at'     => $created_date,
								'is_draft'     => 1,
							];

							Email::create($params);
						}
					}
				}
				
			}
		}
		
    }
}
