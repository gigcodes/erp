<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;

use App\User;
use App\HubstaffActivityByPaymentFrequency;
use App\Http\Controllers\HubstaffActivitiesController;
use App\Mails\Manual\HubstuffActivitySendMail;

use Carbon\Carbon;
use Mail;
use Auth;
use Illuminate\Support\Facades\Log;



class HubstuffActivityCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'HubstuffActivity:Command';

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
        $tasks_controller = new HubstaffActivitiesController;
       
        $users = User::where('payment_frequency', '!=' ,'')->get();
        $today = Carbon::now()->toDateTimeString();

        foreach ($users as $key => $user) {
            
            
            $payment_frequency = $user->payment_frequency;
            $last_mail_sent = $user->last_mail_sent_payment;

            $to = Carbon::now()->startOfMonth();

            if ($last_mail_sent) {
                $to = Carbon::createFromFormat('Y-m-d H:s:i', $last_mail_sent);
            }
            $from = Carbon::createFromFormat('Y-m-d H:s:i', $today);

            // $today_week = new Carbon();
            // if($today_week->dayOfWeek == Carbon::FRIDAY)
            //     dd($today_week);
            // else
            //     dd("555");
           
            $diff_in_days = $to->diffInDays($from);

            $req = new Request;  
            $req->request->add(["activity_command" => true]);
            $req->request->add(["user" => $user]);
            $req->request->add(["user_id" => $user->id]);
            $req->request->add(["developer_task_id" => null]);
            $req->request->add(["task_id" => null]);
            $req->request->add(["task_status" => null]);
            $req->request->add(["start_date" => $to]);
            $req->request->add(["end_date" => $from]);
            $req->request->add(["status" => null]);
            $req->request->add(["submit" => "report_download"]);


            // $res = $tasks_controller->getActivityUsers($req, $req);




            $path = null;

            $data["email"] = 'g62@gopanear.com';//$user->email;
            $data["title"] = "Hubstuff Activities Report";

            if($payment_frequency == "weekly" ){
                $today_week = new Carbon();
                // dump('weekly => '.$user->name.', Current Day => '.$diff_in_days);
                dump('weekly => '.$user->name.', Day =>'.$today_week->dayOfWeek.', Least Mail Date => '.($last_mail_sent ?? 'No').', Start Date => '.$to.', End Date => '.$from);

               
                if($today_week->dayOfWeek == Carbon::MONDAY){
                    dump('Get Report ......');
                    // if ($diff_in_days == 7 ) {
                    
                        $res = $tasks_controller->getActivityUsers(new Request(), $req, 'HubstuffActivityCommand');
                        $z = (array) $res;
                        foreach($z as $zz){
                            if($path == null){

                                $path = $zz->getRealPath();

                            }
                        }
                    // }
                }

                // if ($diff_in_days == 7 ) {

                //     $res = $tasks_controller->getActivityUsers($req, $req);

                //     $z = (array) $res;

                //     foreach($z as $zz){
                //         if($path == null){

                //             $path = $zz->getRealPath();

                //         }
                //     }
                // }
            }

            if($payment_frequency == "biweekly"){

                $today_week = new Carbon();

                dump('biweekly => '.$user->name.', Day =>'.$today_week->dayOfWeek.', Least Mail Date => '.($last_mail_sent ?? 'No').', Start Date => '.$to.', End Date => '.$from);

                if($today_week->dayOfWeek == Carbon::MONDAY || $today_week->dayOfWeek == Carbon::THURSDAY){
                    dump('Get Report ......');
                    $res = $tasks_controller->getActivityUsers(new Request(), $req, 'HubstuffActivityCommand');
                    $z = (array) $res;
                    foreach($z as $zz){
                        if($path == null){

                            $path = $zz->getRealPath();

                        }
                    }
                }

                // if ($diff_in_days == 14) {

                //     $res = $tasks_controller->getActivityUsers($req, $req);

                //     $z = (array) $res;

                //     foreach($z as $zz){

                //         if($path == null){

                //             $path = $zz->getRealPath();
                //     }
                // }
                // }
            }

            if($payment_frequency == "fornightly"){
                $date_fornightly = Carbon::now()->format('d');

                // dump('fornightly => '.$user->name.', Current Day => '.$diff_in_days);
                dump('fornightly => '.$user->name.', Today Date =>'.$date_fornightly.', Least Mail Date => '.($last_mail_sent ?? 'No').', Start Date => '.$to.', End Date => '.$from);

                if($date_fornightly == 15){
                    dump('Get Report ......');
                    $res = $tasks_controller->getActivityUsers(new Request(), $req, 'HubstuffActivityCommand');
                    $z = (array) $res;
                    foreach($z as $zz){
                        if($path == null){

                            $path = $zz->getRealPath();

                        }
                    }
                }

                // if ($diff_in_days == 15) {

                //     $res = $tasks_controller->getActivityUsers($req, $req);

                //     $z = (array) $res;

                //     foreach($z as $zz){

                //         if($path == null){

                //             $path = $zz->getRealPath();
                //         }
                //     }
                // }
            }

            if($payment_frequency == "monthly"){
                $date_monthly = Carbon::now()->format('d');

                // dump('monthly => '.$user->name.', Current Day => '.$diff_in_days);


                $last_month_first_date = new Carbon('first day of last month');
                $last_month_last_date = new Carbon('last day of last month');
                $from = Carbon::createFromFormat('Y-m-d H:s:i', $last_month_first_date);
                $to = Carbon::createFromFormat('Y-m-d H:s:i', $last_month_last_date);

                $req->request->add(["start_date" => $from]);
                $req->request->add(["end_date" => $to]);

                dump('monthly => '.$user->name.', Today Date =>'.$date_monthly.', Least Mail Date => '.($last_mail_sent ?? 'No').', Start Date => '.$from.', End Date => '.$to);

                if($date_monthly == 1){

                    dump('Get Report ......');
                    
                    $res = $tasks_controller->getActivityUsers(new Request(), $req, 'HubstuffActivityCommand');
                    $z = (array) $res;
                    foreach($z as $zz){
                        if($path == null){

                            $path = $zz->getRealPath();

                        }
                    }
                }

                // if ($diff_in_days == 30) {

                //     $res = $tasks_controller->getActivityUsers($req, $req);

                //     $z = (array) $res;

                //     foreach($z as $zz){

                //         if($path == null){

                //             $path = $zz->getRealPath();
                //         }
                //     }
                // }
            }

            if ($path) {

                Auth::logout($user);

                Mail::send('hubstaff.hubstaff-activities-mail', $data, function($message)use($data, $path) {
                    $message->to($data["email"], $data["email"])
                            ->subject($data["title"])->attach($path);
                });

                $user->last_mail_sent_payment = $today;
                $user->save();

                $storage_path = substr($path, strpos($path, 'framework'));
                    
                $hubstaff_activity = new HubstaffActivityByPaymentFrequency;
                $hubstaff_activity->user_id = $user->id;
                $hubstaff_activity->activity_excel_file = $storage_path;
                $hubstaff_activity->save();

                dump('Mail Sent Successfully => '.$user->name);
                dump('');

            }else{
                dump('Frequency Not Match Of User '.$user->name);
                dump('');
            }

        }
    }
}
