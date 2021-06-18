<?php

namespace App\Observers;

use App\Email;
use App\GmailData;

class EmailObserver
{
    /**
     * Handle the email "created" event.
     *
     * @param  \App\Email  $email
     * @return void
     */
    public function created(Email $email)
    {
        return $this->gmailData($email);
    }

    /**
     * Handle the email "updated" event.
     *
     * @param  \App\Email  $email
     * @return void
     */
    public function updated(Email $email)
    {
        //
    }

    /**
     * Handle the email "deleted" event.
     *
     * @param  \App\Email  $email
     * @return void
     */
    public function deleted(Email $email)
    {
        //
    }

    /**
     * Handle the email "restored" event.
     *
     * @param  \App\Email  $email
     * @return void
     */
    public function restored(Email $email)
    {
        //
    }

    /**
     * Handle the email "force deleted" event.
     *
     * @param  \App\Email  $email
     * @return void
     */
    public function forceDeleted(Email $email)
    {
        //
    }

    public function gmailData(Email $email)
    {
        $a = preg_match_all('/<a[^>]+href=([\'"])(?<href>.+?)\1[^>]*>/i', $email->message, $aTags);
        $img = preg_match_all('/<img[^>]+src=([\'"])(?<src>.+?)\1[^>]*>/i', $email->message, $imgTags);

        if ($a > 0) {
            for ($i = 0; $i < count($imgTags[0]); $i++) {
                $gmail = new GmailData;
                $gmail->sender = $email->from;
                $gmail->page_url = $aTags['href'][$i];
                $gmail->images = $imgTags['src'][$i];
                $gmail->received_at = $email->created_at->format('m/d/Y');
                $gmail->save();
            }
        }else{
            $gmail = new GmailData;
            $gmail->sender = $email->from;
            $gmail->received_at = $email->created_at->format('m/d/Y');
            $gmail->save();
        }
    }
}
