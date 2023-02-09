<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Google\Client;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\GoogleDeveloper;

session_start();  

class GoogleDeveloperController extends Controller
{
   
    public static function getDeveloperApianr()
    {
 $id=0;
$anrs = GoogleDeveloper::where('report', 'anr')->get();
return view('google.developer-api.anr',['anrs'=>$anrs,'id'=>$id]);
   
}


public function getDeveloperApicrash()
{

    $id=0;
        $crashes = GoogleDeveloper::where('report', 'crash')->get();
return view('google.developer-api.crash',['crashes'=>$crashes,'id'=>$id]);

    }
}
