<?php

namespace App\Http\Controllers\Logging;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Storage;

class WhatsappLogsController extends Controller
{
  
  public function getWhatsappLog()
  {
    $path =  base_path() . '/';

    $escaped = str_replace('/', '\/', $path);

    $errorData = array();

    $files =    Storage::disk('logs')->files('whatsapp');
    // dd(storage_path('logs/whatsapp/'));
// $files = File::allfiles(storage_path('logs/whatsapp/'));
// dd($files);

    foreach ($files as $file) {
        $yesterday = strtotime('yesterday');
        $today = strtotime('today');

        // $time = Storage::disk('logs')->lastModified($file);
        // if ($yesterday > $time || $time >= $today) {
        //     echo 'HERE' . PHP_EOL;
        //     continue;
        // }
        
        // echo '====== Getting logs from file:' . $file . ' ======' . PHP_EOL;
        $content = Storage::disk('logs')->get($file);

        $contents = explode('}', $content);
        foreach($contents as $c){
            // $x = explode('')
            $x = substr($c, 0, 15);
            dump([$x, $c]);

        }
        dd();
  }
  
}
    
}
