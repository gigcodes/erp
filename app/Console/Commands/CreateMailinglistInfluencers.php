<?php

namespace App\Console\Commands;

use App\Customer;
use App\EmailEvent;
use Illuminate\Console\Command;

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

        $websites = \App\StoreWebsite::select('id', 'title')->where("website_source", "magento")->orderBy('id', 'desc')->get();

        /*foreach ($influencers as $influencer) {
        $email_list[] = ['email' => $influencer->email, 'name' => $influencer->name, 'platform' => $influencer->platform];
        }*/

        foreach ($websites as $website) {
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
						]);
						
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
							if (isset($response->id)) {
								$mailList->remote_id = $res->list_uid;
								$mailList->save();
								$this->mailList[] = $mailList;
							}
							
							return response()->json(true);
						}   
					}
				}else{
					$this->mailList[] = $mailingList;
				}
            }
        }
        
        if (!empty($influencers) && !empty($this->mailList)) {

            $listIds = [];
            if (!empty($this->mailList)) {
                foreach ($this->mailList as $mllist) {
                    $listIds[] = intval($mllist->remote_id);
                }
            }
				
            foreach ($influencers as $list) {
                if (strpos(strtolower($service->name), strtolower('SendInBlue')) !== false) { 
					$response = $this->callApi("https://api.sendinblue.com/v3/contacts", "POST", [
						"email"      => $list->email,
						"listIds"    => $listIds,
						"attributes" => ['firstname' => $list->name],
						"updateEnabled" => true
					]);
				} else if(strpos($service->name, 'AcelleMail') !== false) {
					//Assign Customer to list

					$curl = curl_init();

					curl_setopt_array($curl, array(
					CURLOPT_URL => "https://acelle.theluxuryunlimited.com/api/v1/lists/".$listIds."/subscribers/store?api_token=".config('env.ACELLE_MAIL_API_TOKEN'),
					  CURLOPT_RETURNTRANSFER => true,
					  CURLOPT_ENCODING => "",
					  CURLOPT_MAXREDIRS => 10,
					  CURLOPT_TIMEOUT => 0,
					  CURLOPT_FOLLOWLOCATION => true,
					  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					  CURLOPT_CUSTOMREQUEST => "POST",
					  CURLOPT_POSTFIELDS => array('EMAIL' => $list->email,'name' => ' '),
					));

					$response = curl_exec($curl);

					$response = json_decode($response);
					$url =  "https://acelle.theluxuryunlimited.com/api/v1/lists/".$listIds."/subscribers/".$response->subscriber_uid."/subscribe?api_token=".config('env.ACELLE_MAIL_API_TOKEN');
					$headers = array('Content-Type: application/json');
					$curl = curl_init();
					curl_setopt($curl, CURLOPT_URL, $url);
					curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
					curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
					$response = curl_exec($curl);
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

    public function callApi($url, $method, $data = [])
    {
        $curl = curl_init();

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
                "api-key: " . getenv('SEND_IN_BLUE_API'),
                "Content-Type: application/json",
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        \Log::info($response);
        return json_decode($response);
    }
	
	
}
