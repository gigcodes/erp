<?php

namespace App\Observers;

use App\Mailinglist;
use App\MailinglistTemplate;
use App\CampaignEvent;

class MailinglistObserver
{
    /**
     * Handle the mailinglist "created" event.
     *
     * @param  \App\Mailinglist  $mailinglist
     * @return void
     */
    public function created(Mailinglist $mailinglist)
    {
		$mailing_item = (new MailinglistTemplate)->getWelcomeTemplate($mailinglist->website_id);
		if(!empty($mailing_item['static_template'])) { 
			$htmlContent = $mailing_item->static_template;
                $data = [
                    "to" => [0=>["email"=>$mailinglist->email]],
                    "sender" => [
                        //"id" => 1,
                        "email" => 'Info@theluxuryunlimited.com'
                    ],
                    "subject" => $mailing_item->subject,
                  //  "htmlContent" => $htmlContent, 
                    "textContent" => $htmlContent, 
					"tags" => [
						"Welcome message"
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
				
			CampaignEvent::create(['email'=>$mailinglist->email, 'event'=>'sent', 'subject'=>$mailinglist->subject, 'date_event'=>Carbon::now()]);
			Mailinglist::where('id', $mailinglist->id)->update(['emails_sent'=>1]);
	}
		
        /*if($mailinglist->remote_id != '') {
			$mailing_item = (new MailinglistTemplate)->getWelcomeTemplate($mailinglist->website_id);
			if(!empty($mailing_item['static_template'])) { 
				$curl = curl_init();
				$data = [
					"sender" => array(
						'name' => 'Luxury Unlimited',
						'id' => 1,
					),
					"htmlContent" => $mailing_item->static_template,
					"name" =>  $mailing_item->subject,
					'subject'=> $mailing_item->subject,
					'recipients'=> array(
						'listIds' => [intval($mailinglist->remote_id)]
					),
				];
						
				curl_setopt_array($curl, array(
					CURLOPT_URL => "https://api.sendinblue.com/v3/emailCampaigns",
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => "",
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 30,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => "POST",
					CURLOPT_POSTFIELDS => json_encode($data),
					CURLOPT_HTTPHEADER => array(
						"api-key: ".config('env.SEND_IN_BLUE_API'),
						"Content-Type: application/json"
					),
				));
				$response = curl_exec($curl);
				$response = json_decode($response);
				if(!empty($response->id)){
					$mailing_item->api_template_id = $response->id;
				}
				curl_close($curl);
			}
		}*/
    }

    /**
     * Handle the mailinglist "updated" event.
     *
     * @param  \App\Mailinglist  $mailinglist
     * @return void
     */
    public function updated(Mailinglist $mailinglist)
    {
        //
    }

    /**
     * Handle the mailinglist "deleted" event.
     *
     * @param  \App\Mailinglist  $mailinglist
     * @return void
     */
    public function deleted(Mailinglist $mailinglist)
    {
        //
    }

    /**
     * Handle the mailinglist "restored" event.
     *
     * @param  \App\Mailinglist  $mailinglist
     * @return void
     */
    public function restored(Mailinglist $mailinglist)
    {
        //
    }

    /**
     * Handle the mailinglist "force deleted" event.
     *
     * @param  \App\Mailinglist  $mailinglist
     * @return void
     */
    public function forceDeleted(Mailinglist $mailinglist)
    {
        //
    }
}
