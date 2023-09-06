<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\CronJobReport;
use App\Meetings\ZoomMeetingDetails;
use App\Meetings\ZoomMeetingParticipant;
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

                    $this->fetchRecordings($accessToken, $meeting);
                    $this->fetchParticipants($accessToken, $meeting);
                }
            }

            $this->info('Zoom meetings synced successfully.');
        }
    }

    public function fetchRecordings($accessToken, $meeting) {
        // Fetch recordings for this meeting
        $recordingsResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
        ])->get('https://api.zoom.us/v2/meetings/' . $meeting['id'] . '/recordings');

        $meetingRecording = $recordingsResponse->json();

        \Log::info('meetingRecording -->' . json_encode($meetingRecording));

        // Code copied from app/Meetings/ZoomMeetings.php:saveRecordings()
        if ($meetingRecording && isset($meetingRecording['recording_files'])) {
            $folderPath = public_path() . '/zoom/0/' . $meeting['id'];
            $databsePath = '/zoom/0/' . $meeting['id'];
            \Log::info('folderPath -->' . $folderPath);
            foreach ($meetingRecording['recording_files'] as $recordings) {
                $checkfile = ZoomMeetingDetails::where('download_url_id', $recordings['id'])->first();
                if (! $checkfile) {
                    if ('shared_screen_with_speaker_view' == $recordings['recording_type']) {
                        \Log::info('shared_screen_with_speaker_view');
                        $fileName = $meeting['id'] . '_' . time() . '.mp4';
                        $urlOfFile = $recordings['download_url'];
                        $filePath = $folderPath . '/' . $fileName;
                        if (! file_exists($filePath) && ! is_dir($folderPath)) {
                            mkdir($folderPath, 0777, true);
                        }
                        $ch = curl_init($urlOfFile);
                        curl_exec($ch);
                        if (! curl_errno($ch)) {
                            $info = curl_getinfo($ch);
                            $downloadLink = $info['redirect_url'];
                        }
                        curl_close($ch);

                        if ($downloadLink) {
                            copy($downloadLink, $filePath);     
                        }

                        $zoom_meeting_details = new ZoomMeetingDetails();
                        $zoom_meeting_details->local_file_path = $databsePath . '/' . $fileName;
                        $zoom_meeting_details->file_name = $fileName;
                        $zoom_meeting_details->download_url_id = $recordings['id'];
                        $zoom_meeting_details->meeting_id = $recordings['meeting_id'];
                        $zoom_meeting_details->file_type = $recordings['file_type'];
                        $zoom_meeting_details->download_url = $recordings['download_url'];
                        $zoom_meeting_details->file_path = $recordings['file_path'];
                        $zoom_meeting_details->file_size = $recordings['file_size'];
                        $zoom_meeting_details->file_extension = $recordings['file_extension'];
                        $zoom_meeting_details->save();
                    }
                }
            }
        }
    }

    public function fetchParticipants($accessToken, $meeting) {
        // Fetch participants for this meeting
        $participantsResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
        ])->get('https://api.zoom.us/v2/meetings/' . $meeting['id'] . '/participants');

        $participants = $participantsResponse->json();

        if ($participants['total_records'] > 0) {
            // Store participants in the participants table
            foreach ($participants['participants'] as $participant) {
                ZoomMeetingParticipant::updateOrCreate(
                    ['meeting_id' => $meeting['id'], 'email' => $participant['user_email']],
                    ['name' => $participant['name']]
                );
            }
        }
    }
}
