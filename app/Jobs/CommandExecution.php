<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Helpers\CompareImagesHelper;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\DB;
use App\CommandExecutionHistory;

class CommandExecution implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $command_name;
    protected $store_user_id;
    protected $store_id;

    public function __construct($command_name,$store_user_id,$store_id)
    {
        $this->command_name = $command_name;
        $this->store_user_id = $store_user_id;
        $this->store_id = $store_id;
    }

    public function handle()
    {
        dump($this->command_name . ' : command is started...');
        $compare = Process::fromShellCommandline('php artisan '.$this->command_name, base_path());
        $compare->run();
        $match = $compare->getOutput();
        
        $command_answer	= $match ?? "Command ".$this->command_name." Excution Complete.";
        $status	= 1;

        CommandExecutionHistory::where("id", $this->store_id)->update(["command_answer" => $command_answer, "status" => $status]);

        $user_id = $this->store_user_id;
        $user = DB::table('users')->where('id', $user_id)->first();


        if($user->phone != '' && $user->whatsapp_number != '')
        {
            $message = "Command ".$this->command_name." Excution Complete.";
            app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi($user->phone, $user->whatsapp_number, $message);
        }
        dump($this->command_name . ' : job has been completed...');
        return true;
       
    }

}
