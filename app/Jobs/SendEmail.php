<?php

namespace App\Jobs;

use App\Email;
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

            \MultiMail::to($email->to)->from($email->from)->send(new DefaultSendEmail($email));
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
            \Log::info("Issue fom SendEmail ".$e->getMessage());
        }

        $email->save();

        return true;
    }
}
