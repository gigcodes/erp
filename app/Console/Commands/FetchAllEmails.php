<?php

namespace App\Console\Commands;

use App\CashFlow;
use App\CronJobReport;
use App\Email;
use App\EmailAddress;
use App\EmailRunHistories;
use App\Supplier;
use Carbon\Carbon;
use EmailReplyParser\Parser\EmailParser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Webklex\IMAP\Client;
use App\Jobs\FetchEmail;

/**
 * @author Sukhwinder <sukhwinder@sifars.com>
 * This command takes care of receiving all the emails from the smtp set in the environment
 *
 * All fetched emails will go inside emails table
 */
class FetchAllEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:all_emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetches all emails from the configured SMTP settings';

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
        $report = CronJobReport::create([
            'signature'  => $this->signature,
            'start_time' => Carbon::now(),
        ]);

        $emailAddresses = EmailAddress::orderBy('id', 'asc')->get();

        foreach ($emailAddresses as $emailAddress) {
            FetchEmail::dispatch($emailAddress)->onQueue('fetch_email');
        } 
    }

    /**
     * Check all the emails in the DB and extract the model type from there
     *
     * @param [type] $email
     * @param [type] $email_list
     * @return array(model_id,miodel_type)
     */
   

}
