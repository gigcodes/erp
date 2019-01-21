<?php

namespace App\Console\Commands;

use App\Image;
use App\ImageSchedule;
use Illuminate\Console\Command;
use App\Services\Instagram\Instagram;
use App\Services\Facebook\Facebook;

class PostScheduledMedia extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'post:scheduled-media';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private $facebook;
    private $instagram;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Facebook $facebook, Instagram $instagram)
    {
        $this->facebook = $facebook;
        $this->instagram = $instagram;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $images = Image::whereHas('schedule', function($query) {
            $query->where('scheduled_for', date('Y-m-d H-i-00'));
        })->get()->all();

        foreach ($images as $image) {
            if ($image->schedule->facebook) {
                $this->facebook->postMedia($image);
                ImageSchedule::whereIn('image_id', $this->facebook->getImageIds())->update([
                    'status' => 1
                ]);
            }
            if ($image->schedule->instagram) {
                $this->instagram->postMedia($image);
                ImageSchedule::whereIn('image_id', $this->instagram->getImageIds())->update([
                    'status' => 1
                ]);
            }
        }
    }
}
