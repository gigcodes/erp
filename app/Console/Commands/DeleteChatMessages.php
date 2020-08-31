<?php

namespace App\Console\Commands;

use App\CronJobReport;
use App\ChatMessage;
use Carbon\Carbon;
use File;
use Illuminate\Console\Command;

class DeleteChatMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:chat-messages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'delete ChatMessages';

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
          try 
          {
               $report = CronJobReport::create([
                    'signature'  => $this->signature,
                    'start_time' => Carbon::now(),
               ]);

               $result = ChatMessage::whereIn('status',[7,8,9,10]);
               //$result->where('created_at', '< =', date('Y-m-d', strtotime("-90 days")));
               $result->where('created_at', '<=', date('Y-m-d',strtotime("-90 days")));
               $result->Where('message','=','');
               $row = $result->delete();
               
               
               

               $report->update(['end_time' => Carbon::now()]);
          } catch (\Exception $e) 
          {
               \App\CronJob::insertLastError($this->signature, $e->getMessage());
          }
    }
}
