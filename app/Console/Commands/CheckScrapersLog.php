<?php

namespace App\Console\Commands;

use App\ScrapStatistics;
use App\Services\Whatsapp\ChatApi\ChatApi;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CheckScrapersLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CheckScrapersLog';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'for error and empty log file';

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
        $yesterdayDate = date('d', strtotime("-1 days"));
        $root = env('SCRAP_LOGS_FOLDER');
        $counter = 0;
        foreach (File::allFiles($root) as $file) {
            $needed = explode('-', $file->getFilename());
            if(isset($needed[1])){
                $day = explode('.', $needed[1]);
                if ($day[0] === $yesterdayDate) {
                    $filePath = $root . '/' . $file->getRelativePath() . '/' . $needed[0] . '-' . $day[0] . '.' . $day[1];
                    $result = File::get($filePath);
                    if (empty($result) ||
                        (strpos($result, 'exception') || strpos($result, 'Exception')) ||
                        (strpos($result, 'error') || strpos($result, 'Error'))) {
                        $suplier = \App\Scraper::where("scraper_name", $needed[0])->first();
                        if(!is_null($suplier)){
                            $user = \App\User::where("id", $suplier->id)->first();
                            if (!is_null($user)){
                                $whatsappNumber = $user->whatsapp_number;
                                $message = 'scraper log file ' . $filePath . ' has issue.';
                                $data = [
                                    'phone' => $whatsappNumber, // Receivers phone
                                    'body' => $message, // Message
                                ];
                                ChatApi::sendMessage($data);
                                ScrapStatistics::create([
                                    'supplier' => $suplier->scraper_name,
                                    'type' => 'EXISTING_SCRAP_PRODUCT',
                                    'description' => $message
                                ]);
                            }

                        }

                    }
                }
            }

        }
    }
}
