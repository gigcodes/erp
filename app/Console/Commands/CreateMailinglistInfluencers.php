<?php

namespace App\Console\Commands;

use App\Customer;
use App\EmailEvent;
use Illuminate\Console\Command;
use App\Service;
use App\Mailinglist;
use App\MailinglistTemplate;
use  App\Loggers\MailinglistIinfluencersLogs;
class CreateMailinglistInfluencers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create-mailinglist-influencers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is using for create mailing list from influencers ';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public $mailList = [];

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
		
        $influencers = \App\ScrapInfluencer::where(function($q) {
            $q->orWhere("read_status","!=",1)->orWhereNull('read_status');
        })->where('email', '!=', "")->get();

	//	$influencers = \App\ScrapInfluencer::where('email', '!=', "")->limit(10)->get();
//		dd($influencers);
		$send_in_blue_apis=[];		
        $websites = \App\StoreWebsite::select('id', 'title', 'mailing_service_id','send_in_blue_api','send_in_blue_account')->where("website_source", "magento")->whereNotNull('mailing_service_id')->where('mailing_service_id', '>', 0)->orderBy('id', 'desc')->get();

        /*foreach ($influencers as $influencer) {
        $email_list[] = ['email' => $influencer->email, 'name' => $influencer->name, 'platform' => $influencer->platform];
        }*/
	
        foreach ($websites as $website) { 
			$send_in_blue_apis[$website->id]=$website->send_in_blue_api;
			$service = Service::find($website->mailing_service_id);
			if($service){
				$name = $website->title;
				if ($name != '') {
					$name = $name . "_" . date("d_m_Y");
				} else {
					$name = 'WELCOME_LIST_' . date("d_m_Y");
				}

				$mailingList = \App\Mailinglist::where('name', $name)->where('service_id', $website->mailing_service_id)->where("website_id", $website->id)->where('remote_id', ">", 0)->first();
		
				if (!$mailingList) {
					
					$mailList = \App\Mailinglist::create([
						'name'       => $name,
						'website_id' => $website->id,
						'service_id' => $website->mailing_service_id,
					]);
				
					if (strpos(strtolower($service->name), strtolower('SendInBlue')) !== false) { 
						$response = $this->callApi("https://api.sendinblue.com/v3/contacts/lists", "POST", $data = [
							"folderId" => 1,
							"name"     => $name,
						],$website->send_in_blue_api);
						
						if (isset($response->id)) {
							$mailList->remote_id = $response->id;
							$mailList->save();
							$this->mailList[] = $mailList;
						}
					} else if(strpos($service->name, 'AcelleMail') !== false) {
						$curl = curl_init();

						curl_setopt_array($curl, array(
						CURLOPT_URL => "https://acelle.theluxuryunlimited.com/api/v1/lists?api_token=".config('env.ACELLE_MAIL_API_TOKEN'),
						  CURLOPT_RETURNTRANSFER => true,
						  CURLOPT_ENCODING => "",
						  CURLOPT_MAXREDIRS => 10,
						  CURLOPT_TIMEOUT => 0,
						  CURLOPT_FOLLOWLOCATION => true,
						  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
						  CURLOPT_CUSTOMREQUEST => "POST",
						  CURLOPT_POSTFIELDS => array('contact[company]' => '.','contact[state]' => 'afdf','name' =>  $name,'default_subject'=> $name,'from_email' => 'welcome@test.com','from_name' => 'dsfsd','contact[address_1]' => 'af','contact[country_id]' => '219','contact[city]' => 'sdf','contact[zip]' => 'd','contact[phone]' => 'd','contact[email]' => 'welcome@test.com'),
						));

						$response = curl_exec($curl);
						 
						curl_close($curl); 
						$res = json_decode($response); 
						if($res->status == 1){
							//getting last id
							$list = Mailinglist::orderBy('id','desc')->first();
							if($list){
								$id = ($list->id + 1);
							}else{
								$id = 1;
							}
							if (isset($res->list_uid)) {
								$mailList->remote_id = $res->list_uid;
								$mailList->save();
								$this->mailList[] = $mailList;
							}
							
						}   
					}
					//$this->mailList[] = $mailList;
				}else{
					$this->mailList[] = $mailingList;
				}
            }
        }
   
        if (!empty($influencers) && !empty($this->mailList)) {  
            $webListIds=$listIds = [];
            if (!empty($this->mailList)) {
                foreach ($this->mailList as $mllist) {
                    $listIds[] = $mllist->remote_id;
                 //   $listIds[] = intval($mllist->remote_id);
					$webListIds[$mllist->website_id][] = $mllist->remote_id;
                }
            }
			
            foreach ($influencers as $list) {
                if (strpos(strtolower($service->name), strtolower('SendInBlue')) !== false) { 
					foreach ($webListIds as $key=>$listIdsData){
						$api_key=isset($send_in_blue_apis[$key])?$send_in_blue_apis[$key]:'';
						$reqData=[
							"email"      => $list->email,
							"listIds"    => $listIdsData,
							"attributes" => ['firstname' => $list->name],
							"updateEnabled" => true
						];
						$url="https://api.sendinblue.com/v3/contacts";
						$response = $this->callApi($url, "POST", $reqData,$api_key );
						MailinglistIinfluencersLogs::log([
							"service"=>$service->name,
							"maillist_id"=>json_encode($listIdsData),
							"email"=>$list->email,
							"name"=> $list->name,
							"url"=>$url,
							"request_data"=>json_encode($reqData),
							"response_data"=>json_encode($response),

						]);
					}
				} else if(strpos($service->name, 'AcelleMail') !== false) {
					//Assign Customer to list
					foreach($listIds as $listId) {
						$curl = curl_init();

						$ch = curl_init();
						$url='https://acelle.theluxuryunlimited.com/api/v1/subscribers?list_uid='.$listId;
						curl_setopt($ch, CURLOPT_URL, $url);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($ch, CURLOPT_POST, 1);
						curl_setopt($ch, CURLOPT_POSTFIELDS, "api_token=".config('env.ACELLE_MAIL_API_TOKEN')."&EMAIL=".$list->email);

						$headers = array();
						$headers[] = 'Accept: application/json';
						$headers[] = 'Content-Type: application/x-www-form-urlencoded';
						curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

						$response = curl_exec($ch);
						MailinglistIinfluencersLogs::log([
							"service"=>$service->name,
							"maillist_id"=>json_encode($listId),
							"email"=>$list->email,
							"name"=> $list->name,
							"url"=>$url,
							"request_data"=>"api_token=".config('env.ACELLE_MAIL_API_TOKEN')."&EMAIL=".$list->email,
							"response_data"=>json_encode($response),

						]);
						if (curl_errno($ch)) {
							echo 'Error:' . curl_error($ch);
						}
						curl_close($ch);
						
					}
				}

                $customer = Customer::where('email', $list->email)->first();
                if (!$customer) {
                    $customer = Customer::create([
                        "name"   => $list->name,
                        "email"  => $list->email,
                        "source" => "scrap_influencer",
                    ]);
                }
				
				
				
                $mailing_item = MailinglistTemplate::where('auto_send', 1)->where('duration', 0)->first();
				if (!empty($this->mailList)) {
                    foreach ($this->mailList as $mllist) {
                        $mllist->listCustomers()->attach($customer->id);
						$list_contact_id = \DB::table('list_contacts')->where(['list_id'=>$mllist->id, "customer_id"=>$customer->id])->pluck('id')->first();					
							    
						/*** send welcome email to mailing list customers start**/
						if($mailing_item != null and !empty($mailing_item['static_template'])) { 
							$mllist['email'] = $list->email;
							$mllist['name'] = $list->name;
							(new Mailinglist)->sendAutoEmails($mllist, $mailing_item, $service);
						}	
						/*** send welcome email to mailing list customers end**/						
                    }
                }

                $list->read_status = 1;
                $list->save();
            }
			
        }
    }

    public function callApi($url, $method, $data = [],$send_in_blue_api="")
    {
        $curl = curl_init();
		$api_key=($send_in_blue_api=="")? getenv('SEND_IN_BLUE_API'):$send_in_blue_api;
        curl_setopt_array($curl, array(
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_POSTFIELDS     => json_encode($data),
            CURLOPT_HTTPHEADER     => array(
                "api-key: " . $send_in_blue_api,
                "Content-Type: application/json",
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        \Log::info($response);
        return json_decode($response);
    }
	
	
}
