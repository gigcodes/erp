<?php

namespace App\Jobs;

use App\Email;
use Illuminate\Bus\Queueable;
use App\Mails\Manual\DefaultSendEmail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $email;

    public $emailNewData;

    public $emailOldData;

    public $tries = 3;

    public $backoff = 5;

    /**
     * Create a new job instance.
     *
     * @param  object  $email
     * @param  array  $emaildetails
     * @return void
     */
    public function __construct(Email $email, $emaildetails = [])
    {
        //
        $this->email = $email;
        $this->emailOldData = $email;
        $this->emailNewData = $emaildetails;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Used to set customer email's data to send email to customer if question has auto approve flag is yes
        if (! empty($this->emailNewData)) {
            $updatedEmail = $this->emailNewData;

            $email = $this->email;
            $email->to = $updatedEmail['to'];
            $email->from = $updatedEmail['from'];
            $email->message = $updatedEmail['message'];
        } else {
            $email = $this->email;
        }

        try {
            $multimail = \MultiMail::to($email->to);

            \App\EmailLog::create([
                'email_id' => $email->id,
                'email_log' => 'To Email set from SendEmail',
                'message' => $email->to,
            ]);

            if (! empty($email->cc)) {
                $multimail->cc($email->cc);

                \App\EmailLog::create([
                    'email_id' => $email->id,
                    'email_log' => 'CC Email set from SendEmail',
                    'message' => $email->CC,
                ]);
            }
            if (! empty($email->bcc)) {
                $multimail->bcc($email->bcc);

                \App\EmailLog::create([
                    'email_id' => $email->id,
                    'email_log' => 'BCC Email set from SendEmail',
                    'message' => $email->bcc,
                ]);
            }

            $data = json_decode($email->additional_data, true);

            $attchments = [];

            if (! empty($data['attachment'])) {
                $attchments = $data['attachment'];

                \App\EmailLog::create([
                    'email_id' => $email->id,
                    'email_log' => 'Email Attachment set from SendEmail',
                    'message' => $data['attachment'],
                ]);
            }
            \App\EmailLog::create([
                'email_id' => $email->id,
                'email_log' => 'Sending On DefaultSendEmail from SendEmail',
                'message' => '',
            ]);
            $multimail->from($email->from)->send(new DefaultSendEmail($email, $attchments));

            \App\EmailLog::create([
                'email_id' => $email->id,
                'email_log' => 'Message Sent Successfully from SendEmail',
                'message' => '',
            ]);
            \App\CommunicationHistory::create([
                'model_id' => $email->model_id,
                'model_type' => $email->model_type,
                'type' => $email->template,
                'refer_id' => $email->id,
                'method' => 'email',
            ]);
            if (! empty($this->emailNewData)) {
                $emailOld = $this->emailOldData;
                $email->to = $emailOld['to'];
                $email->from = $emailOld['from'];
                $email->message = $emailOld['message'];
            }
            $email->is_draft = 0;
            $email->status = 'send';
        } catch (\Exception $e) {
            $email->is_draft = 0;
            $email->error_message = $e->getMessage();
            $email->save();

            \Log::info('Issue fom SendEmail ' . $e->getMessage());
            \App\EmailLog::create([
                'email_id' => $email->id,
                'email_log' => 'Error in Sending Email',
                'message' => $e->getMessage(),
            ]);
            throw new \Exception($e->getMessage());
        }

        $email->save();

        return true;
    }

    public function tags()
    {
        return ['SendEmail', $this->email->id];
    }
}
