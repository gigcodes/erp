<?php

namespace App\Jobs;

use App\Email;
use App\EmailLog;
use App\Mails\Manual\DefaultSendEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $email;

    public $tries = 3;
    public $backoff = 5;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Email $email)
    {
        //
        $this->email = $email;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $email = $this->email;

        try {

           $multimail = \MultiMail::to($email->to);

           \App\EmailLog::create([
            'email_id'   => $email->id,
            'email_log' => 'To Email set from SendEmail',
            'message'       => $email->to
            ]);
            // $multimail->from($email->from);
            
            if(!empty($email->cc)) {
                $multimail->cc($email->cc);

                \App\EmailLog::create([
                    'email_id'   => $email->id,
                    'email_log' => 'CC Email set from SendEmail',
                    'message'       => $email->CC
                    ]);
            }
            if(!empty($email->bcc)) {
                $multimail->bcc($email->bcc);

                \App\EmailLog::create([
                    'email_id'   => $email->id,
                    'email_log' => 'BCC Email set from SendEmail',
                    'message'       => $email->bcc
                    ]);
            }

            $data = json_decode($email->additional_data,true);

            $attchments = [];

            if(!empty($data['attachment'])) {

                $attchments = $data['attachment'];

                \App\EmailLog::create([
                    'email_id'   => $email->id,
                    'email_log' => 'Email Attachment set from SendEmail',
                    'message'       => $data['attachment']
                    ]);
                // foreach ($data['attachment'] as $file_path) {
                //     $attchments[] = 
                //     $multimail->attachFromStorageDisk('files', $file_path);
                // }
            }
            \App\EmailLog::create([
                'email_id'   => $email->id,
                'email_log' => 'Sending On DefaultSendEmail from SendEmail',
                'message'       => ''
                ]);
            $multimail->from($email->from)->send(new DefaultSendEmail($email, $attchments));

            \App\EmailLog::create([
                'email_id'   => $email->id,
                'email_log' => 'Message Sent Successfully from SendEmail',
                'message'       => ''
                ]);
            \App\CommunicationHistory::create([
                'model_id'   => $email->model_id,
                'model_type' => $email->model_type,
                'type'       => $email->template,
                'refer_id'   => $email->id,
                'method'     => 'email',
            ]);
            $email->is_draft = 0;
            $email->status   = 'send';
        } catch (\Exception $e) {
            
            $email->is_draft = 1;
            $email->error_message = $e->getMessage();
            
            \Log::info("Issue fom SendEmail ".$e->getMessage());
            //\Log::info("Issue fom SendEmail ");
            \App\EmailLog::create([
                'email_id'   => $email->id,
                'email_log' =>  'Error in Sending Email',
                'message'       => $e->getMessage()
                ]);
            throw new \Exception($e->getMessage());
        }

        $email->save();

        return true;
    }
	
	public function tags() 
    {
        return [ 'SendEmail', $this->email->id ];
    }

}
