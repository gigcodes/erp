<?php

namespace App\Http\Controllers;

use App\GoogleBigQueryData;
use App\Setting;
use Illuminate\Http\Request;

class GoogleBigQueryDataController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $bigData = GoogleBigQueryData::paginate(Setting::get('pagination'));

        return view('google.big_data.index', compact('bigData'));
    }

    public function search(Request $request)
    {
        $bigData = new GoogleBigQueryData();
        if (! empty($request->project_id)) {
            $bigData = $bigData->where('google_project_id', 'like', '%'.$request->project_id.'%');
        }
        if (! empty($request->platform)) {
            $bigData = $bigData->where('platform', 'like', '%'.$request->platform.'%');
        }
        if (! empty($request->event_id)) {
            $bigData = $bigData->where('event_id', 'like', '%'.$request->event_id.'%');
        }
        $bigData = $bigData->paginate(Setting::get('pagination'));

        return view('google.big_data.index', compact('bigData'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\GoogleBigQueryData  $googleBigQueryData
     * @return \Illuminate\Http\Response
     */
    public function destroy(GoogleBigQueryData $googleBigQueryData, Request $request)
    {
        try {
            $bigData = GoogleBigQueryData::where('id', '=', $request->id)->delete();

            return response()->json(['code' => 200, 'data' => $bigData, 'message' => 'Deleted successfully!!!']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }
}
