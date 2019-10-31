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
}