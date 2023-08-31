<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\CronJobReport;
use App\Meetings\ZoomMeetings;
use App\ZoomOAuthHelper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ZoomMeetingsSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zoom:meetings-sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To get all the zoom meetings';

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
        // Get an access token (use your authentication method)
        $tokenResponse = ZoomOAuthHelper::getAccessToken();
        
        if (isset($tokenResponse['access_token'])) {
            $accessToken = $tokenResponse['access_token'];

            // Make an API request to fetch Zoom meetings
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
            ])->get('https://api.zoom.us/v2/users/me/meetings');
    
            $meetings = $response->json();

            if ($meetings['total_records'] > 0) {
                foreach ($meetings['meetings'] as $meeting) {
                    // Store the meeting in the meetings table
                    ZoomMeetings::updateOrCreate(
                        ['meeting_id' => $meeting['id']],
                        [
                            'meeting_topic' => $meeting['topic'],
                            'meeting_type' => $meeting['type'],
                            'meeting_agenda' => $meeting['agenda'] ?? "",
                            'join_meeting_url' => $meeting['join_url'],
                            'start_date_time' => $meeting['start_time'],
                            'meeting_duration' => $meeting['duration'],
                            'timezone' => $meeting['timezone'],
                            'host_zoom_id' => $meeting['host_id'],
                        ]
                    );
                }
            }

            $this->info('Zoom meetings synced successfully.');
        }
    }
}
