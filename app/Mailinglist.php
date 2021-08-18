<?php

namespace App;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use App\StoreWebsite;
use Illuminate\Database\Eloquent\Model;

class Mailinglist extends Model
{
    /**
     * @var string
     * @SWG\Property(property="id",type="integer")
     * @SWG\Property(property="name",type="string")
     * @SWG\Property(property="remote_id",type="integer")
     * @SWG\Property(property="service_id",type="integer")
     * @SWG\Property(property="website_id",type="integer")
     * @SWG\Property(property="email",type="string")
     */
    protected $fillable = ['id', 'name', 'remote_id', 'service_id','website_id','email'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function service()
    {
       return $this->belongsTo(Service::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function website()
    {
       return $this->hasOne(StoreWebsite::class,'id','website_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function listCustomers()
    {
        return $this->belongsToMany(Customer::class, 'list_contacts', 'list_id', 'customer_id')->withTimestamps();
    }
	
	public function sendAutoEmails($mailingList, $mailing_item) {
		
			if(!empty($mailing_item['static_template'])) { 
				$emailEvent = EmailEvent::create(["list_contact_id"=>$mailingList->list_contact_id, 'template_id'=> $mailing_item->id]);
				$htmlContent = $mailing_item->static_template;
				$data = [
					"to" => [0=>["email"=>$mailingList->email]],
					"sender" => [
						"email" => 'Info@theluxuryunlimited.com'
					],
					"subject" => $mailing_item->subject,
					"htmlContent" => $htmlContent, 
					"tag" => $emailEvent->id
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
				$response = curl_exec($curl);
				$response = json_decode($response);
				curl_close($curl);
							 dd($response);
				
			}
		
	}

}
