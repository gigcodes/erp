<?php

namespace App\Http\Controllers;

use App\GoogleDeveloper;
use Illuminate\Http\Request;

class GoogleDeveloperController extends Controller
{
    public static function getDeveloperApianr()
    {
        $anrs = GoogleDeveloper::where('report', 'anr');

        $id = 0;
        $anrs = $anrs->get();

        return view('google.developer-api.anr', ['anrs' => $anrs, 'id' => $id]);
    }

public function getDeveloperApianrfilter(Request $request)
{
    $anrs = GoogleDeveloper::where('report', 'anr');
    if ($request->input('app_name')) {
        $app_name = $request->input('app_name');
        $anrs = $anrs->Where('name', 'like', '%' . $app_name . '%');
    }
    if ($request->input('date')) {
        $date = $request->input('date');
        $anrs = $anrs->Where('latestEndTime', 'like', '%' . $date . '%');
    }
    $id = 0;
    $anrs = $anrs->get();

    return view('google.developer-api.anr', ['anrs' => $anrs, 'id' => $id]);
}

public function getDeveloperApicrash()
{
    $id = 0;
    $crashes = GoogleDeveloper::where('report', 'crash')->get();

    return view('google.developer-api.crash', ['crashes' => $crashes, 'id' => $id]);
}

    public function getDevelopercrashfilter(Request $request)
    {
        $crashes = GoogleDeveloper::where('report', 'crash');
        if ($request->input('app_name')) {
            $app_name = $request->input('app_name');
            $crashes = $crashes->Where('name', 'like', '%' . $app_name . '%');
        }
        if ($request->input('date')) {
            $date = $request->input('date');
            $crashes = $crashes->Where('latestEndTime', 'like', '%' . $date . '%');
        }
        $id = 0;
        $crashes = $crashes->get();

        return view('google.developer-api.crash', ['crashes' => $crashes, 'id' => $id]);
    }
}
