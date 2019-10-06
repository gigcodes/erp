<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Meetings\ZoomMeetings;
use Carbon\Carbon;

class ZoomMeetingDeleteRecordings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'meeting:deleterecordings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete zoom recordings based on meeting id';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->zoomkey = env('ZOOM_API_KEY');
        $this->zoomsecret = env('ZOOM_API_SECRET');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
    $zoomKey = $this->zoomkey;
    $zoomSecret = $this->zoomsecret;
    $meetings = new ZoomMeetings();
    $date = Carbon::yesterday();
    $meetings->deleteRecordings($zoomKey, $zoomSecret, $date);
    exit('Deleted zoom videos which are already downloaded in server.');
    }
}
