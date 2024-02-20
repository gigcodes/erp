<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Model;

class Mailinglist extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="id",type="integer")
     * @SWG\Property(property="name",type="string")
     * @SWG\Property(property="remote_id",type="integer")
     * @SWG\Property(property="service_id",type="integer")
     * @SWG\Property(property="website_id",type="integer")
     * @SWG\Property(property="email",type="string")
     */
    protected $fillable = ['id', 'name', 'language', 'remote_id', 'service_id', 'website_id', 'email'];

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
        return $this->hasOne(StoreWebsite::class, 'id', 'website_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function listCustomers()
    {
        return $this->belongsToMany(Customer::class, 'list_contacts', 'list_id', 'customer_id')->withTimestamps();
    }

    public function sendAutoEmails($mailingList, $mailing_item, $service)
    {
        $mailing_item->customer = $mailingList;
        $emailClass = (new \App\Mail\MailingListMails($mailing_item))->build();
        $website = \App\StoreWebsite::where('id', $mailingList->website_id)->first();
        $api_key = (isset($website->send_in_blue_smtp_email_api) && $website->send_in_blue_smtp_email_api != '') ? $website->send_in_blue_smtp_email_api : getenv('SEND_IN_BLUE_SMTP_EMAIL_API');

        if (strpos(strtolower($service->name), strtolower('SendInBlue')) !== false) {
            $emailEvent = EmailEvent::create(['list_contact_id' => $mailingList->list_contact_id, 'template_id' => $mailing_item->id]);
            $htmlContent = $emailClass['template'];
            $data = [
                'to' => [0 => ['email' => $mailingList->email]],
                'sender' => [
                    'email' => $emailClass['from_email'],
                ],
                'subject' => $mailing_item->subject,
                'htmlContent' => $htmlContent,
                'tag' => $emailEvent->id,
            ];

            $response = Http::withHeaders([
                'api-key' => env('SEND_IN_BLUE_SMTP_EMAIL_API'),
                'Content-Type' => 'application/json',
            ])
                ->post('https://api.sendinblue.com/v3/smtp/email', $data)->json();
        } elseif (strpos($service->name, 'AcelleMail') !== false) {
            $htmlContent = $emailClass['template'];

            $url = 'https://acelle.theluxuryunlimited.com/api/v1/campaign/create/' . $mailingList->remote_id . '?api_token=' . config('env.ACELLE_MAIL_API_TOKEN');
            $data = [
                'name' => $mailing_item->subject,
                'subject' => $mailing_item->subject,
                'run_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'template_content' => $htmlContent,
            ];

            $response = Http::post($url, $data)->json();
        }
    }
}
