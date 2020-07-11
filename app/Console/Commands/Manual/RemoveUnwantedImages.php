<?php

namespace App\Console\Commands\Manual;

use App\CronJobReport;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Plank\Mediable\Media;

class RemoveUnwantedImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remove-unwanted:images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove Unwanted Images';

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
        $medibles = Media::all();
        foreach($medibles as $media) {
            // check file exist or not if not the delete it
            if(file_exists($media->getAbsolutePath())) {
                // start to found mediable usage
                $mediables = \DB::table("mediables")->where("media_id",$media->id)->get();
                if(!$mediables->isEmpty()) {
                    foreach($mediables as $aModal) {

                    }
                }else{
                    // check file exist or not 
                    $media->delete();
                }
            }else{
                $media->delete();
            }
            echo "<pre>"; print_r([$mediables,$media->id]);  echo "</pre>";die;
            die;

        }
    }
}
