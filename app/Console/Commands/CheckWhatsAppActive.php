<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Marketing\WhatsappConfig;
use Carbon\Carbon;

class CheckWhatsAppActive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whatsapp:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'It is used to check if the whatsappnumber is active , if not active will send whatsapp message';

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
        $numbers = WhatsappConfig::where('is_customer_support','!=',1)->where('status',1)->get();
        
        $time = Carbon::now();
        $morning = Carbon::create($time->year, $time->month, $time->day, 8, 0, 0);
        $evening = Carbon::create($time->year, $time->month, $time->day, 17, 00, 0);
        if ($time->between($morning, $evening, true)) {
        foreach ($numbers as $number) {
                //Checking if device was active from last 15 mins
                if($number->last_online > Carbon::now()->subMinutes(15)->toDateTimeString()){
                    continue;
                }
                $phones = ['+919004780634','+31629987287'];
                $message = $number->number.'Username : '.$number->username.' Phone Number is not working Please Check It';
                
                foreach ($phones as $phone) {
                    app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi($phone, '',$message, '', '');
                }
                            
            }
        }else{
            dump('Not Proper Time To Check');
        }
    }
}
