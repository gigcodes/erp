<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\PaymentReceipt;
use App\User;
use App\Hubstaff\HubstaffActivity;
use App\UserRate;
use DB;
class UserPayment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:payment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make payment request for users';

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
        DB::beginTransaction();
        $users = User::where('fixed_price_user_or_job',2)->get();
        $firstEntryInActivity = HubstaffActivity::orderBy('starts_at')->first();
        if($firstEntryInActivity) {
            $bigining = date('Y-m-d',strtotime($firstEntryInActivity->starts_at));
        }else {
            $bigining = date('Y-m-d');
        }
        foreach($users as $user) {
            //$lastPayment = PaymentReceipt::where('user_id',$user->id)->orderBy('date','DESC')->first();
            $start =  $bigining;
            $end =  date('Y-m-d');
            //if($lastPayment) {
                //$start = date('Y-m-d',strtotime($lastPayment->date));
                //$end =  $start;
            //}
            /*if($user->payment_frequency == 'fornightly') {
                
            }
            else if($user->payment_frequency == 'weekly') {
                if($lastPayment) {
                    $start = date('Y-m-d',strtotime($lastPayment->date . "+1 days"));
                    $end =  date('Y-m-d',strtotime($lastPayment->date . "+7 days"));
                }
            }
            else if($user->payment_frequency == 'biweekly') {
                if($lastPayment) {
                    $start = date('Y-m-d',strtotime($lastPayment->date . "+1 days"));
                    $end =  date('Y-m-d',strtotime($lastPayment->date . "+14 days"));
                }
            }
            else if($user->payment_frequency == 'monthly') {
                if($lastPayment) {
                    $start = date('Y-m-d',strtotime($lastPayment->date . "+1 days"));
                    $end =  date('Y-m-d',strtotime($lastPayment->date . "+30 days"));
                }
            }*/
            $yesterday = date('Y-m-d',strtotime("-1 days"));

            echo PHP_EOL . "=====Checking $start - $end for $user->id ====" . PHP_EOL;

            $activityrecords  = HubstaffActivity::getTrackedActivitiesBetween($start, $end, $user->id);

            echo PHP_EOL . "===== Result found ".count($activityrecords)." ====" . PHP_EOL;

            $total = 0;
            $minutes = 0;
            foreach($activityrecords as $record) {
                $latestRatesOnDate = UserRate::latestRatesOnDate($record->starts_at,$user->id);
                if($record->tracked > 0 && $latestRatesOnDate && $latestRatesOnDate->hourly_rate > 0) {
                    $total = $total + ($record->tracked/60)/60 * $latestRatesOnDate->hourly_rate;
                    $minutes = $minutes + $record->tracked/60;
                    $record->paid = 1;
                    $record->save(); 
                }
            }
            if($total > 0) {
                $total = number_format($total,2);
                $paymentReceipt = new PaymentReceipt;
                $paymentReceipt->worked_minutes = $minutes;
                $paymentReceipt->status = 'Pending';
                $paymentReceipt->rate_estimated = $total;
                $paymentReceipt->date = $end;
                $paymentReceipt->user_id = $user->id;
                $paymentReceipt->billing_start_date = $start;
                $paymentReceipt->billing_end_date = $end;
                $paymentReceipt->currency = ''; //we need to change this.
                $paymentReceipt->save();
            }
        }
        DB::commit();
        echo PHP_EOL . "=====DONE====" . PHP_EOL;
        } catch (Exception $e) {
            echo $e->getMessage();
            DB::rollBack();
            echo PHP_EOL . "=====FAILED====" . PHP_EOL;
        }
    }
}
