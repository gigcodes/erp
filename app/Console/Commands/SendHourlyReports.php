<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\DailyActivity;
use Carbon\Carbon;
use App\Exports\HourlyReportsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Mail;
use App\Mail\HourlyReport;

class SendHourlyReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:hourly-reports';

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
      $date = Carbon::now()->format('Y-m-d');
      $daily_activities = DailyActivity::where('for_date', $date)->get()->groupBy('user_id');

      if (count($daily_activities) > 0) {
        $path = "hourly_reports/" . $date . "_hourly_reports.xlsx";
        Excel::store(new HourlyReportsExport($daily_activities), $path, 'uploads');

        Mail::to('hr@sololuxury.co.in')->send(new HourlyReport($path));
      }

    }
}
