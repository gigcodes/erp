<?php

namespace App\Http\Controllers;

use App\GoogleDeveloperLogs;
use Illuminate\Http\Request;

session_start();

class GoogleDeveloperLogsController extends Controller
{
    public static function index()
    {
        $id = 0;
        $anrcrashes = GoogleDeveloperLogs::get();

        return view('google.developer-api.logs', ['anrcrashes' => $anrcrashes, 'id' => $id]);
    }

   public static function logsfilter(Request $request)
   {
       $anrcrashes = new GoogleDeveloperLogs();
       if ($request->input('app_name')) {
           $app_name = $request->input('app_name');
           $anrcrashes = $anrcrashes->Where('log_name', 'like', '%' . $app_name . '%');
       }
       if ($request->input('date')) {
           $date = $request->input('date');
           $anrcrashes = $anrcrashes->Where('created_at', 'like', '%' . $date . '%');
       }
       $id = 0;
       $anrcrashes = $anrcrashes->get();

       return view('google.developer-api.logs', ['anrcrashes' => $anrcrashes, 'id' => $id]);
   }

// public function destroy()
//        {
//           $user = GoogleDeveloperLogs::delete();
//           // echo ("User Record deleted successfully.");
//           return redirect()->route('google.developer-api.logs');
//        }
}
