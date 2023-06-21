<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessAllFAQ implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $replyInfo;

    private $user_id;

    public function __construct($replyInfo, $user_id)
    {
        $this->replyInfo = $replyInfo;
        $this->user_id = $user_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $replyInfo = $this->replyInfo;
        $user_id = $this->user_id;

        try {
            //Add the data for queue
            foreach ($replyInfo as $key => $value) {
                if (! empty($value->is_translate)) {   //if FAQ translate is  available then send for FAQ
                    $insertArray = [];
                    $insertArray[] = $value->id;

                    ProceesPushFaq::dispatch($insertArray)->onQueue('faq');
                } else {   //If FAQ transation is not available then first set for translation
                    $insertArray = [];
                    $insertArray[] = $value->id;

                    $replyInformation = \App\Reply::find($value->id);

                    ProcessTranslateReply::dispatch($replyInformation, $user_id)->onQueue('replytranslation');   //set for translation

                    ProceesPushFaq::dispatch($insertArray)->onQueue('faq');
                }
            }
        } catch (Exception $e) {
        }
    }
}
