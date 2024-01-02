<?php

namespace App\Console\Commands;

use App\Agent;
use App\Supplier;
use Carbon\Carbon;
use App\CronJobReport;
use App\Helpers\LogHelper;
use Illuminate\Console\Command;
use Webklex\PHPIMAP\ClientManager;

class CheckEmailsErrors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:emails-errors';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Cron was started to run']);

            $report = CronJobReport::create([
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);
            $cm = new ClientManager();
            $imap = $cm->make([
                'host' => env('IMAP_HOST_PURCHASE'),
                'port' => env('IMAP_PORT_PURCHASE'),
                'encryption' => env('IMAP_ENCRYPTION_PURCHASE'),
                'validate_cert' => env('IMAP_VALIDATE_CERT_PURCHASE'),
                'username' => env('IMAP_USERNAME_PURCHASE'),
                'password' => env('IMAP_PASSWORD_PURCHASE'),
                'protocol' => env('IMAP_PROTOCOL_PURCHASE'),
            ]);

            $imap->connect();

            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Connecting to IMAMP']);

            $inbox = $imap->getFolder('INBOX');

            $email_addresses = config('app.failed_email_addresses');

            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Get email addresses from config app.failed_email_addresses file.']);

            foreach ($email_addresses as $address) {
                $emails = $inbox->messages()->where('from', $address);
                $emails = $emails->leaveUnread()->get();

                foreach ($emails as $email) {
                    dump('Error Email');

                    if ($email->hasHTMLBody()) {
                        $content = $email->getHTMLBody();
                    } else {
                        $content = $email->getTextBody();
                    }
                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'Getting html body of the email ID:' . $email->id]);

                    if (preg_match_all("/failed: ([\a-zA-Z0-9_.-@]+) host/i", preg_replace('/\s+/', ' ', $content), $match)) {
                        dump('Found address ' . $match[1][0]);

                        $suppliers = Supplier::where('email', $match[1][0])->get();
                        LogHelper::createCustomLogForCron($this->signature, ['message' => 'Supplier model query was finished.']);

                        $agents = Agent::where('email', $match[1][0])->get();
                        LogHelper::createCustomLogForCron($this->signature, ['message' => 'Agent model query was finished.']);

                        foreach ($agents as $agent) {
                            dump('Found agent email');

                            $agent->supplier->has_error = 1;
                            $agent->supplier->save();
                        }

                        foreach ($suppliers as $supplier) {
                            dump('Found supplier email');

                            $supplier->has_error = 1;
                            $supplier->save();
                        }
                    }

                    dump('__________');
                }
            }

            $report->update(['end_time' => Carbon::now()]);
        } catch (\Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
