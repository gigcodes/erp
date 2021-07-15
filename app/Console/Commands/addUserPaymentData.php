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
        foreach ($dev_tasks as $task) {
            
            $task_user = User::find($task->team_lead_id !== null ? $task->team_lead_id : $task->user_id);
 
            PaymentReceipt::create([
                'status'            => 'Done',
                'rate_estimated'    => $task->cost,
                'date'              => date('Y-m-d'),
                'currency'          => 'required',
                'user_id'           => $task_user->id,
                'by_command'        => 1,
                'worked_minutes'    => $task->approximate,
                'task_id'           => $task->id,
            ]);
        }
        $tasks = Task::whereNotNull('is_completed')->get();
        foreach ($tasks as $task) {
            
        }
    }
}
