<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\DeveloperTask;
use App\Task;
use App\User;
use App\PaymentReceipt;

class addUserPaymentData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'addUserPaymentData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'addUserPaymentData';

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
        $dev_tasks = DeveloperTask::where('is_resolved', 1)->get();
        foreach ($dev_tasks as $dev_task) {
            
            $dev_task_user = User::find($dev_task->team_lead_id !== null ? $dev_task->team_lead_id : $dev_task->assigned_to);
            if(empty($dev_task_user)){
                dump('dev_task-id - ' . $dev_task->id . ' user not exist');
                continue;
            }
            $dev_task_payment = PaymentReceipt::create([
                'status'            => 'Done',
                'rate_estimated'    => $dev_task_user->fixed_price_user_or_job == 1 ? $dev_task->cost ?? 0 : ($dev_task->estimate_minutes ?? 0) * ($dev_task_user->hourly_rate ?? 0),
                'date'              => date('Y-m-d'),
                'currency'          => '',
                'user_id'           => $dev_task_user->id,
                'by_command'        => 1,
                'developer_task_id' => $dev_task->id,
            ]);
            if($dev_task_payment){
                dump('dev_task-id - ' . $dev_task->id . ' payment-id - ' . $dev_task_payment->id . ' is done');
            }
            $dev_task_payment = 0;
        }
        $tasks = Task::whereNotNull('is_completed')->get();
        foreach ($tasks as $task) {

            $task_user = User::find($task->assign_to);
            if(empty($task_user)){
                dump('task-id - ' . $task->id . ' user not exist');
                continue;
            }
            $task_payment = PaymentReceipt::create([
                'status'            => 'Done',
                'rate_estimated'    => $task_user->fixed_price_user_or_job == 1 ? $task->cost ?? 0 : $task->approximate * ($task_user->hourly_rate ?? 0),
                'date'              => date('Y-m-d'),
                'currency'          => '',
                'user_id'           => $task_user->id,
                'by_command'        => 1,
                'developer_task_id' => $task->id,
            ]);
            if($task_payment){
                dump('task-id - ' . $task->id . ' payment-id - ' . $task_payment->id . ' is done');
            }
            $task_payment = 0;
            
        }
    }
}
