<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\MessageQueue;
use App\Customer;
use App\CronJobReport;
use Carbon\Carbon;
use App\Jobs\SendMessageToAll;
use App\Jobs\SendMessageToSelected;
use App\Http\Controllers\WhatsAppController;
use DB;

class TestQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:test-queues';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
      $images = array('https://images.pexels.com/photos/248797/pexels-photo-248797.jpeg',
        'https://images.pexels.com/photos/1006360/pexels-photo-1006360.jpeg',
        'https://images.pexels.com/photos/1120367/pexels-photo-1120367.jpeg',
        'https://images.pexels.com/photos/1656687/pexels-photo-1656687.jpeg',
        'https://images.pexels.com/photos/846362/pexels-photo-846362.jpeg',
        'https://images.pexels.com/photos/459225/pexels-photo-459225.jpeg',
        'https://images.pexels.com/photos/462118/pexels-photo-462118.jpeg',
        'https://www.gstatic.com/webp/gallery3/1_webp_ll.png',
        'https://www.gstatic.com/webp/gallery3/2_webp_a.png',
        'https://www.gstatic.com/webp/gallery/1.jpg',
        'https://www.gstatic.com/webp/gallery/2.jpg',
        'https://www.gstatic.com/webp/gallery/3.jpg',
        'https://www.gstatic.com/webp/gallery/5.jpg',
        'https://developers.google.com/_static/9b2935c280/images/share/devsite-google-green.png',
        'https://s2.best-wallpaper.net/wallpaper/1920x1080/1607/Google-logo-green-background_1920x1080.jpg',
        'https://www.numerama.com/content/uploads/2017/08/red-by-sfr.jpg',
        'https://i.ytimg.com/vi/xssSZVmeoSE/maxresdefault.jpg',
        
    );
      foreach ($images as $image) {
      
          $customer_phone = '971545889192';
      $send_number = '919152731486';
      $message = 'Check';
      $file = 'https://upload.wikimedia.org/wikipedia/ru/3/33/NatureCover2001.jpg';
      $chat_message_id = '6030';
      app( WhatsAppController::class )->sendWithThirdApi($customer_phone, $send_number, $message, $image , $chat_message_id ,'');
      DB::table('test')->insert(
    ['link' => $image]
);
      dump('its saved');
      }
    }
}
