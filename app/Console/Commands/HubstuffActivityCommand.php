<?php

namespace App\Console\Commands;

use Auth;
use Mail;
use App\User;
use App\CashFlow;
use Carbon\Carbon;
use App\PayentMailData;
use Illuminate\Http\Request;
use Illuminate\Console\Command;
use App\Loggers\HubstuffCommandLog;
use App\Loggers\HubstuffCommandLogMessage;
use App\HubstaffActivityByPaymentFrequency;
use App\Http\Controllers\HubstaffActivitiesController;

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
        $HubstuffCommandLog = new HubstuffCommandLog();
        $tasks_controller = new HubstaffActivitiesController;

        $users = User::where('payment_frequency', '!=', '')->get();
        $HubstuffCommandLog->messages = 'Total user for payment frequency is:' . count($users);
        $today = Carbon::now()->toDateTimeString();
        $HubstuffCommandLog->date = $today;
        $HubstuffCommandLog->day = Carbon::now()->dayOfWeek;
        $HubstuffCommandLog->userCount = count($users);

        $HubstuffCommandLog->save();
        $HubstuffCommandLog_id = $HubstuffCommandLog->id;
        $weekly = $biweekly = $fornightly = $monthly = 0;
        //   dd($users);
        //  $j=0;
        foreach ($users as $key => $user) {
            $HubstuffCommandLogMessage = new HubstuffCommandLogMessage();
            $HubstuffCommandLogMessage->hubstuff_command_log_id = $HubstuffCommandLog_id;
            $HubstuffCommandLogMessage->user_id = $user->id;
            $HubstuffCommandLogMessage->frequency = $user->payment_frequency;
            $HubstuffCommandLogMessage->save();
            $HubstuffCommandLogMessage_id = $HubstuffCommandLogMessage->id;
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
            $req->request->add(['activity_command' => true]);
            $req->request->add(['user' => $user]);
            $req->request->add(['user_id' => $user->id]);
            $req->request->add(['developer_task_id' => null]);
            $req->request->add(['task_id' => null]);
            $req->request->add(['task_status' => null]);
//            $req->request->add(["start_date" => $to]);
            //          $req->request->add(["end_date" => $from]);
            $req->request->add(['status' => null]);
            $req->request->add(['submit' => 'report_download']);
            $req->request->add(['response_type' => 'with_payment_receipt']);
            $req->request->add(['HubstuffCommandLogMessage_id' => $HubstuffCommandLogMessage_id]);
            $get_activity = false;

            // $res = $tasks_controller->getActivityUsers($req, $req);

            $path = null;

            $data['email'] = $user->email;
            $data['title'] = 'Hubstuff Activities Report';
            if ($payment_frequency == 'weekly') {
                $weekly++;
                $HubstuffCommandLogMessage->message = 'Go to weekly condition';
                $today_week = new Carbon();
                dump('weekly => ' . $user->name . ', Day =>' . $today_week->dayOfWeek . ', Least Mail Date => ' . ($last_mail_sent ?? 'No') . ', Start Date => ' . $to . ', End Date => ' . $from);
                $day = Carbon::now();
                //  $weekStartDate = $day->startOfWeek()->format('Y-m-d H:i');
                //$weekEndDate = $day->endOfWeek()->format('Y-m-d H:i');
                $from = date('Y-m-d ', strtotime('last week monday'));
                $to = date('Y-m-d ', strtotime('last week sunday'));

                if ($today_week->dayOfWeek == Carbon::FRIDAY) {
                    $HubstuffCommandLogMessage->message = $HubstuffCommandLogMessage->message . '-->go to monday condition';
                    $get_activity = true;

                    //    $res = $tasks_controller->getActivityUsers(new Request(), $req, 'HubstuffActivityCommand');
                }
            }

            if ($payment_frequency == 'biweekly') {
                $biweekly++;
                $HubstuffCommandLogMessage->message = 'Go to biweekly condition';
                $today_week = new Carbon();
                if ($today_week->dayOfWeek == Carbon::MONDAY) {
                    $from = date('Y-m-d ', strtotime('last week friday'));
                    $to = date('Y-m-d ', strtotime('last week sunday'));
                    $HubstuffCommandLogMessage->message = $HubstuffCommandLogMessage->message . '-->go to MONDAY condition';
                    $get_activity = true;
                    //  $res = $tasks_controller->getActivityUsers(new Request(), $req, 'HubstuffActivityCommand');
                }
                if ($today_week->dayOfWeek == Carbon::FRIDAY) {
                    $from = date('Y-m-d ', strtotime('last week monday'));
                    $to = date('Y-m-d ', strtotime('last week thursday'));
                    $get_activity = true;
                }
            }

            if ($payment_frequency == 'fornightly') {
                $fornightly++;
                $date_fornightly = Carbon::now()->format('d');
                $HubstuffCommandLogMessage->message = 'Go to fornightly condition';
                dump('fornightly => ' . $user->name . ', Today Date =>' . $date_fornightly . ', Least Mail Date => ' . ($last_mail_sent ?? 'No') . ', Start Date => ' . $to . ', End Date => ' . $from);

                if ($date_fornightly == 16) {
                    $last_month_first_date = new Carbon('first day of last month');
                    $last_month_last_date = new Carbon('last day of last month');
                    $from = Carbon::createFromFormat('Y-m-d H:s:i', $last_month_first_date);
                    $to = Carbon::createFromFormat('Y-m-d H:s:i', $last_month_first_date->subdays(-14));
                    $HubstuffCommandLogMessage->message = $HubstuffCommandLogMessage->message . '-->go to date 16 condition';
                    $get_activity = true;

                    //   $res = $tasks_controller->getActivityUsers(new Request(), $req, 'HubstuffActivityCommand');
                }
                if ($date_fornightly == 1) {
                    $last_month_first_date = new Carbon('first day of last month');
                    $last_month_last_date = new Carbon('last day of last month');
                    $from = Carbon::createFromFormat('Y-m-d H:s:i', $last_month_first_date->subdays(-15));
                    $to = Carbon::createFromFormat('Y-m-d H:s:i', $last_month_last_date);
                    $HubstuffCommandLogMessage->message = $HubstuffCommandLogMessage->message . '-->go to date 1 condition';
                    $get_activity = true;
                    // $res = $tasks_controller->getActivityUsers(new Request(), $req, 'HubstuffActivityCommand');
                }
            }

            if ($payment_frequency == 'monthly') {
                $monthly++;
                $date_monthly = Carbon::now()->format('d');
                $HubstuffCommandLogMessage->message = 'Go to MONTHLY condition';
                $last_month_first_date = new Carbon('first day of last month');
                $last_month_last_date = new Carbon('last day of last month');
                $from = Carbon::createFromFormat('Y-m-d H:s:i', $last_month_first_date);
                $to = Carbon::createFromFormat('Y-m-d H:s:i', $last_month_last_date);
                if ($date_monthly == 1) {
                    $HubstuffCommandLogMessage->message = $HubstuffCommandLogMessage->message . '-->go to date 1 condition';
                    $get_activity = true;
                    // $res = $tasks_controller->getActivityUsers(new Request(), $req, 'HubstuffActivityCommand');
                }
            }

            //dd($res);
            $HubstuffCommandLogMessage->start_date = $from;
            $HubstuffCommandLogMessage->end_date = $to;
            $HubstuffCommandLogMessage->save();
            if ($get_activity) {
                $req->request->add(['start_date' => $from]);
                $req->request->add(['end_date' => $to]);

                $res = $tasks_controller->getActivityUsers(new Request(), $req, 'HubstuffActivityCommand');
            }

            if (isset($res)) {
                $path = $res['file_data'];
                Auth::logout($user);

                // $path = storage_path('app/files').'/'.$path;

                // Mail::send('hubstaff.hubstaff-activities-mail', $data, function($message)use($data, $path) {
                //     $message->to($data["email"], $data["email"])
                //             ->subject($data["title"])->attach($path);
                // });

                $user->last_mail_sent_payment = $today;
                $user->save();

                // $storage_path = substr($path, strpos($path, 'framework'));

                $hubstaff_activity = new HubstaffActivityByPaymentFrequency;
                $hubstaff_activity->user_id = $user->id;
                $hubstaff_activity->activity_excel_file = $path;
                $hubstaff_activity->start_date = isset($res['start_date']) ? $res['start_date'] : '';
                $hubstaff_activity->end_date = isset($res['end_date']) ? $res['end_date'] : '';
                $hubstaff_activity->type = $payment_frequency;
                $hubstaff_activity->payment_receipt_ids = isset($res['receipt_ids']) ? json_encode($res['receipt_ids']) : '';
                $hubstaff_activity->save();

                // Add Code by Mitali for add in cash flow
                $admin_user_id = ! empty(auth()->id()) ? auth()->id() : 6;

                $paymentData = PayentMailData::where('user_id', $user->id)->orderBy('id', 'desc')->first();
                $cashflow = new CashFlow;

                if ($user->billing_frequency_day > 0) {
                    $cashflow->billing_due_date = date('Y-m-d', strtotime(now() . ' +' . $user->billing_frequency_day));
                }

                $cashflow->date = $hubstaff_activity->created_at;
                $cashflow->user_id = $user->id;
                $cashflow->updated_by = $admin_user_id;
                $cashflow->cash_flow_able_id = $hubstaff_activity->id;
                $cashflow->cash_flow_able_type = \App\HubstaffActivityByPaymentFrequency::class;
                $cashflow->description = "$payment_frequency Frequency Payment";
                $cashflow->type = 'pending';
                $cashflow->status = 1;
                $cashflow->amount = $paymentData->total_balance;
                //    $cashflow->currency=  ;
                $cashflow->save();

                //Query
                //      $cashflow->type= 'pending';
                //      $cashflow->currency=  $receipt->currency;
                //      $cashflow->status=  1;
                //      $cashflow->amount=  $receipt->created_at;

                // dd("555555");

                dump('Mail Sent Successfully => ' . $user->name);
                dump('');
            } else {
                dump('Frequency Not Match Of User ' . $user->name);
                dump('');
            }
        }
        $HubstuffCommandLog = $HubstuffCommandLog::find($HubstuffCommandLog_id);
        $HubstuffCommandLog->weekly = $weekly;
        $HubstuffCommandLog->biweekly = $biweekly;
        $HubstuffCommandLog->fornightly = $fornightly;
        $HubstuffCommandLog->monthly = $monthly;
        $HubstuffCommandLog->save();
    }
}
