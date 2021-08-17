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
            $name = $website->title;
            if ($name != '') {
                $name = $name . "_" . date("d_m_Y");
            } else {
                $name = 'WELCOME_LIST_' . date("d_m_Y");
            }

            $mailingList = \App\Mailinglist::where('name', $name)->where("website_id", $website->id)->where('remote_id', ">", 0)->first();
            if (!$mailingList) {
                
                $mailList = \App\Mailinglist::create([
                    'name'       => $name,
                    'website_id' => $website->id,
                    'service_id' => 1,
                ]);

                $response = $this->callApi("https://api.sendinblue.com/v3/contacts/lists", "POST", $data = [
                    "folderId" => 1,
                    "name"     => $name,
                ]);
                
                if (isset($response->id)) {
                    $mailList->remote_id = $response->id;
                    $mailList->save();
                    $this->mailList[] = $mailList;
                }
            }else{
                $this->mailList[] = $mailingList;
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
                
                $response = $this->callApi("https://api.sendinblue.com/v3/contacts", "POST", [
                    "email"      => $list->email,
                    "listIds"    => $listIds,
                    "attributes" => ['firstname' => $list->name],
                    "updateEnabled" => true
                ]);

                $customer = Customer::where('email', $list->email)->first();
                if (!$customer) {
                    $customer = Customer::create([
                        "name"   => $list->name,
                        "email"  => $list->email,
                        "source" => "scrap_influencer",
                    ]);
                }
                $mailing_item = MailinglistTemplate::template("Intro Email 1"); ;
				if (!empty($this->mailList)) {
                    foreach ($this->mailList as $mllist) {
                        $mllist->listCustomers()->attach($customer->id);
						$list_contact_id = \DB::table('list_contact')->where(['list_id'=>$mllist->id, "customer_id"=>$customer->id])->pluck('id')->first();					
							    
						/*** send welcome email to mailing list customers start**/
						if(!empty($mailing_item['static_template'])) { 
							$htmlContent = $mailing_item->static_template;
								$data = [
									"to" => [0=>["email"=>$customer->email]],
									"sender" => [
										//"id" => 1,
										"email" => 'Info@theluxuryunlimited.com'
									],
									"subject" => $mailing_item->subject,
									"htmlContent" => $htmlContent, 
									"tags" => [
										"intro_email"=>1,
										"list_contact_id"=>$list_contact_id
									]
								];
								$curl = curl_init();
								curl_setopt_array($curl, array(
								CURLOPT_URL => "https://api.sendinblue.com/v3/smtp/email",

								CURLOPT_RETURNTRANSFER => true,
								CURLOPT_ENCODING => "",
								CURLOPT_MAXREDIRS => 10,
								CURLOPT_TIMEOUT => 30,
								CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
								CURLOPT_CUSTOMREQUEST => "POST",
								CURLOPT_POSTFIELDS => json_encode($data),
								CURLOPT_HTTPHEADER => array(
									  "api-key:".env('SEND_IN_BLUE_SMTP_EMAIL_API'),
										"Content-Type: application/json"
									),
								));
								curl_close($curl);
								EmailEvent::create(["list_contact_id"=>$list_contact_id,"intro_email"=>1]);
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
