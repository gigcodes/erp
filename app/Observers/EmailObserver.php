<?php

namespace App\Observers;

use App\Email;
use App\EmailAddress;
use App\GmailDataList;
use App\GmailDataMedia;
use App\ContentManageentEmail;
use App\Events\EmailReceivedAlert;

class EmailObserver
{
    /**
     * Handle the email "created" event.
     *
     * @return void
     */
    public function created(Email $email)
    {
        $this->checkEmailAlert($email);
        return $this->gmailData($email);
    }

    /**
     * Handle the email "updated" event.
     *
     * @return void
     */
    public function updated(Email $email)
    {
        //
    }

    /**
     * Handle the email "deleted" event.
     *
     * @return void
     */
    public function deleted(Email $email)
    {
        //
    }

    /**
     * Handle the email "restored" event.
     *
     * @return void
     */
    public function restored(Email $email)
    {
        //
    }

    /**
     * Handle the email "force deleted" event.
     *
     * @return void
     */
    public function forceDeleted(Email $email)
    {
        //
    }

    public function gmailData(Email $email)
    {
        $receiver_email = $email->to;
        $content_management_email = ContentManageentEmail::first();
        if ($content_management_email) {
            if ($receiver_email == $content_management_email->email) {
                $a = preg_match_all('/<a[^>]+href=([\'"])(?<href>.+?)\1[^>]*>/i', $email->message, $aTags);
                $img = preg_match_all('/<img[^>]+src=([\'"])(?<src>.+?)\1[^>]*>/i', $email->message, $imgTags);

                if ($a > 0) {
                    $gmail = new GmailDataList;
                    $gmail->sender = $email->from;
                    $gmail->domain = substr($email->from, strpos($email->from, '@') + 1);
                    $gmail->received_at = $email->created_at->format('m/d/Y');
                    $gmail->save();

                    for ($i = 0; $i < count($imgTags[0]); $i++) {
                        if (file_get_contents($imgTags['src'][$i]) != '') {
                            $gmail_media = new GmailDataMedia;
                            $gmail_media->gmail_data_list_id = $gmail->id;
                            $gmail_media->page_url = $aTags['href'][$i];
                            $gmail_media->images = $imgTags['src'][$i];
                            $gmail_media->save();
                        }
                    }
                }
            }
        }
    }

    private function checkEmailAlert(Email $email)
    {
        try {
            //Email Alerts
            $enabledAlertEmails = EmailAddress::where('email_alert',1)->pluck('from_address')->toArray();
            if(count($enabledAlertEmails) && in_array($email->to, $enabledAlertEmails)) {
                EmailReceivedAlert::dispatch($email);
            }
           
        } catch (\Throwable $th) {
            //throw $th;
        }

    }
}
