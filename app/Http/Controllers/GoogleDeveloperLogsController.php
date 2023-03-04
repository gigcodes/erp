<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Google\Client;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\GoogleDeveloperLogs;

session_start();  

class GoogleDeveloperLogsController extends Controller
{
   
    public static function index()
    {
 $id=0;
$anrcrashes = GoogleDeveloperLogs::get();
return view('google.developer-api.logs',['anrcrashes'=>$anrcrashes,'id'=>$id]);
   
}



}
