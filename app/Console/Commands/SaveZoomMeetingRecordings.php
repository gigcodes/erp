<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\CronJobReport;
use App\Meetings\ZoomMeetings;
use Illuminate\Console\Command;

class SaveZoomMeetingRecordings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'save:zoom-meetings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Save Zoom Meetings';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->zoomkey       = config('env.ZOOM_API_KEY');
        $this->zoomsecret    = config('env.ZOOM_API_SECRET');
        $this->zoommeetingid = config('env.ZOOM_MEETING_ID');
        $this->zoomuserid    = config('env.ZOOM_USER');
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $report = CronJobReport::create([
                'signature'  => $this->signature,
                'start_time' => Carbon::now(),
            ]);
            //   dd($report);
            $zoomKey       = $this->zoomkey;
            $zoomSecret    = $this->zoomsecret;
            $zoommeetingid = $this->zoommeetingid;
            $zoomuserid    = $this->zoomuserid;
            $meetings      = new ZoomMeetings();
            $date          = Carbon::now();
            $meetings->saveRecordings($zoomKey, $zoomSecret, $date, $zoommeetingid, $zoomuserid);
            $report->update(['end_time' => Carbon::now()]);
        } catch (\Exception $e) {
            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }
        exit('Data inserted in db..Now, you can check meetings screen');
    }
}
