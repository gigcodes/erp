<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Email;
use App\ReturnExchange;
use App\CronJobReport;
use Carbon\Carbon;

class ExchangeBuybackEmailSending extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ExchangeBuybackEmailSending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Return exchange buyback cards mail sending';

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
        dump('Cron start');

        try {
            // Save cron report
             $report = CronJobReport::create([
                'signature'  => $this->signature,
                'start_time' => Carbon::now(),
            ]);

            $DraftEmailList = Email::where('is_draft', 1)->get();

            foreach ( $DraftEmailList as $emailObject ) {

                if( !empty( $emailObject->model_id ) && !empty( $emailObject->to ) ){

                    $success = ReturnExchange::where('id', $emailObject->model_id )->first();
                    
                    try {
        
                        \MultiMail::to( $emailObject->to )->send( new \App\Mails\Manual\InitializeRefundRequest( $success ) );
                        $emailObject->is_draft = 0;
                    } catch (\Throwable $th) {
                        $emailObject->error_message = $th->getMessage();
                    }
                    $emailObject->save();
                }
                dump( 'Model id -> '.$emailObject->model_id );
            }

            // Cron report update
            $report->update(['end_time' => Carbon::now() ]);
        } catch (\Throwable $th) {
            dump( $th->getMessage() );
        }

    }
}
