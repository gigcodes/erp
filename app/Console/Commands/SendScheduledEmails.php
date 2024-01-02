<?php

namespace App\Console\Commands;

use App\Email;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendScheduledEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:send_scheduled_emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Scheduled Emails';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $created_date = Carbon::now()->format('Y-m-d');
        $emails = Email::where('schedule_at', 'like', $created_date . '%')->whereNotNull('schedule_at')->where('is_draft', 1)->where('type', 'outgoing')->get();

        foreach ($emails as $email) {
            $result = \App\Jobs\SendEmail::dispatch($email)->onQueue('send_email');
        }
    }
}
