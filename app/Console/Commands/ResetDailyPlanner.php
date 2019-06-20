<?php

namespace App\Console\Commands;

use App\CronJobReport;
use App\User;
use Illuminate\Console\Command;
use Carbon\Carbon;

class ResetDailyPlanner extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset:daily-planner';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resets Daily Planner Complete flag for specific users';

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
      $report = CronJobReport::create([
        'signature' => $this->signature,
        'start_time'  => Carbon::now()
      ]);

      $users_array = [7];

      $users = User::whereIn('id', $users_array)->get();

      foreach ($users as $user) {
        $user->is_planner_completed = 0;
        $user->save();
      }

      $report->update(['end_time' => Carbon:: now()]);
    }
}
