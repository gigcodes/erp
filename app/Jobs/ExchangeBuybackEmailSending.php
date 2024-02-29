<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ExchangeBuybackEmailSending implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;

    public $backoff = 5;

    /**
     * Create a new job instance.
     *
     * @param protected $to
     * @param protected $success
     * @param protected $emailObject
     *
     * @return void
     */
    public function __construct(protected $to, protected $success, protected $emailObject)
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $emailObject = $this->emailObject;

        try {
            \MultiMail::to($this->to)->send(new \App\Mails\Manual\InitializeRefundRequest($this->success));
            $emailObject->is_draft = 0;
        } catch (\Throwable $th) {
            $emailObject->error_message = $th->getMessage();
            throw new \Exception($th->getMessage());
        }

        $emailObject->save();
    }

    public function tags()
    {
        return [$this->success, $this->to];
    }
}
