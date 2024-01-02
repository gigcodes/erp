<?php

namespace App\Console\Commands;

use App\RedisQueue;
use Illuminate\Console\Command;
use App\RedisQueueCommandExecutionLog;

class ExecuteQueueCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:QueueExecutionCommand {id} {command_tail}';

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
            $queue = RedisQueue::find($this->argument('id'));
            $cmd = 'bash ' . getenv('DEPLOYMENT_SCRIPTS_PATH') . 'horizon.sh ' . $this->argument('command_tail');

            $allOutput = [];
            $allOutput[] = $cmd;
            $result = exec($cmd, $allOutput);
            if ($result == '') {
                $result = 'No response';
            } elseif ($result == 0) {
                $result = 'Command run success. Response ' . $result;
            } elseif ($result == 1) {
                $result = 'Command run fail. Response ' . $result;
            } else {
                $result = is_array($result) ? json_encode($result, true) : $result;
            }

            $command = new RedisQueueCommandExecutionLog();
            $command->user_id = \Auth::user()->id;
            $command->redis_queue_id = $queue->id;
            $command->command = $cmd;
            $command->server_ip = env('SERVER_IP');
            $command->response = $result;
            $command->save();
        } catch (\Exception $e) {
            echo 4;
            $command = new RedisQueueCommandExecutionLog();
            $command->user_id = \Auth::user()->id;
            $command->redis_queue_id = $queue->id;
            $command->command = $cmd;
            $command->server_ip = env('SERVER_IP');
            $command->response = $result;
            $command->save();
        }
    }
}
