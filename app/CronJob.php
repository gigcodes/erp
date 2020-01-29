<?php

namespace App;

use App\CronJob;
use Illuminate\Database\Eloquent\Model;

class CronJob extends Model
{
    public function index()
    {
        $cron = CronJob::orderBy('id')->get();
        return $cron;
    }

    public static function insertLastError($signature, $error = "")
    {
        $cron = self::where("signature", $signature)->first();

        if (!$cron) {
            $cron            = new self;
            $cron->signature = $signature;
            $cron->schedule  = "N/A";
        }
        $cron->last_status = 'error';
        $cron->error_count += 1;
        $cron->last_error = $error;
        $cron->save();
    }

}
