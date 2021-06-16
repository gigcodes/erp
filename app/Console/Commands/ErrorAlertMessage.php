<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\LogKeyword;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use App\ChatMessage;
use Illuminate\Http\Request;

class errorAlertMessage extends Command
{
    //scrappersImagesDelete
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'errorAlertMessage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send message to user 6 if error occured.';

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
        $filename = '/laravel-' . now()->format('Y-m-d') . '.log';

        $path         = storage_path('logs');
        $fullPath     = $path . $filename;
        $errSelection = [];
        try {
            $content = File::get($fullPath);
            preg_match_all("/\[(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})\](.*)/", $content, $match);
            $logKeywords = LogKeyword::all();
            
            foreach ($match[0] as $value) {
                foreach ($logKeywords as $key => $logKeyword) {
                    if (strpos(strtolower($value), strtolower($logKeyword->text)) !== false) {
                        $context = 'site_development';
                        $user_id = 6;
                        $message = "You have error which matched the keyword  '".$logKeyword->text."'";
                        $message .=" | ".$value;
                        $params = [];
                        $params['message'] = $message;
                        $params['erp_user'] = $user_id;
                        $params['user_id'] = $user_id;
                        $params['approved'] = 1;
                        $params['status'] = 2;
                        $params['message_application_id'] = 10001;
                        $chat_message = ChatMessage::create($params);
                        
                        $requestData = new Request();
                        $requestData->setMethod('POST');
                        $requestData->request->add(['user_id' => $user_id, 'message' => $message, 'status' => 1]);
                        app('App\Http\Controllers\WhatsAppController')->sendMessage($requestData, 'user');
                    }
                }
            }
            $this->output->write('Cron Done', true);
        } catch (\Exception $e) {
            $this->output->write("Error is caught here! => ".$e->getMessage() , true);
        }
    }
}