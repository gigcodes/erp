<?php

namespace App\Exports;

use App\DailyActivity;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class HourlyReportsExport implements WithMultipleSheets
{
    protected $hourly_reports;

    public function __construct($hourly_reports)
    {
      $this->hourly_reports = $hourly_reports;
    }

    public function sheets(): array
    {
      $sheets = [];
      $daily_activities = DailyActivity::where('for_date', Carbon::now()->format('Y-m-d'))->get()->groupBy('user_id');

      foreach ($daily_activities as $user_id => $activity) {
        $sheets[] = new ReportPerUserSheet($user_id);
      }

      return $sheets;
    }


}
