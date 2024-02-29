<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\EMailAcknowledgement;

class SendEmailAcknowledgement extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'acknowledgement:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Acknowledgement Email';

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
        try {
            $currentTime = Carbon::now()->format('Y-m-d H:i:s');

            $EMailAcknowledgement = EMailAcknowledgement::with('email_address_record')->where('end_date', '>=', $currentTime)->get();

            if (! empty($EMailAcknowledgement)) {
                foreach ($EMailAcknowledgement as $key => $value) {
                    $latest_email = \App\Email::where('to', $value->email_address_record->username)->where('is_reply', 0)->where('created_at', '>', $value->start_date)->where('created_at', '<', $value->end_date)->get();

                    if (! empty($latest_email)) {
                        foreach ($latest_email as $key => $email) {
                            \App\Email::where('id', $email->id)->update(['is_reply' => 1]);

                            if ($value->ack_status == 1) {
                                $status   = 'outgoing';
                                $is_draft = 0;
                            } else {
                                $status   = 'pre-send';
                                $is_draft = 1;
                            }

                            $email = \App\Email::create([
                                'model_id'         => $email->id,
                                'model_type'       => \App\Email::class,
                                'from'             => $email->to,
                                'to'               => $email->from,
                                'subject'          => 'Re: ' . $email->subject,
                                'message'          => $value->ack_message,
                                'template'         => 'reply-email',
                                'additional_data'  => '',
                                'type'             => 'outgoing',
                                'status'           => $status,
                                'store_website_id' => null,
                                'is_draft'         => $is_draft,
                            ]);

                            \App\EmailLog::create([
                                'email_id'  => $email->id,
                                'email_log' => 'Email forward initiated',
                                'message'   => $email->to,
                            ]);

                            \App\Jobs\SendEmail::dispatch($email)->onQueue('send_email');
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
