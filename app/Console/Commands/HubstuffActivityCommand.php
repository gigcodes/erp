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
            $res = $tasks_controller->getActivityUsers($req, $req);
            $path = null;
            
            $data["email"] = $user->email;
            $data["title"] = "Hubstuff Activities Report";

            if($payment_frequency == "weekly"){
                if ($diff_in_days == 7) {
    
                    $res = $tasks_controller->getActivityUsers($req, $req);
                    $z = (array) $res;

                    foreach($z as $zz){
                        if($path == null){
                            $path = $zz->getRealPath();
                        }
                    }
                }
            }

            if($payment_frequency == "biweekkly"){
                if ($diff_in_days == 14) {
    
                    $res = $tasks_controller->getActivityUsers($req, $req);
                    $z = (array) $res;

                    foreach($z as $zz){
                        if($path == null){
                            $path = $zz->getRealPath();
                        }
                    }
                }
            }

            if($payment_frequency == "fornightly"){
                if ($diff_in_days == 15) {

                    $res = $tasks_controller->getActivityUsers($req, $req);
                    $z = (array) $res;

                    foreach($z as $zz){
                        if($path == null){
                            $path = $zz->getRealPath();
                        }
                    }
                }
            }

            if($payment_frequency == "monthly"){
                if ($diff_in_days == 30) {

                    $res = $tasks_controller->getActivityUsers($req, $req);
                    $z = (array) $res;

                    foreach($z as $zz){
                        if($path == null){
                            $path = $zz->getRealPath();
                        }
                    }
                }
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
            }
        }
    }
}
