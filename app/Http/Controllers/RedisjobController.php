<?php

namespace App\Http\Controllers;

use App\Redisjob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class RedisjobController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $redis_data = new Redisjob();
        $redis_data = $redis_data->paginate(25);
        $total_count = $redis_data->total();

        return view('radis_job.index', compact('redis_data', 'total_count'));
    }

    /**
     * Display a listing of the resource Data.
     *
     * @return \Illuminate\Http\Response
     */
    public function listData(Request $request)
    {
        $redis_data = new Redisjob();
        $redis_data = $redis_data->paginate(25)->get();
        $total_count = $redis_data->total();

        return response()->json([
            'tbody' => view('radis_job.partials.list_more_record_ajax', compact('redis_data', 'total_count'))->render(),
            'links' => (string) $redis_data->render(),
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $redis = new Redisjob();
            $redis->name = $request->name;
            $redis->type = $request->type;
            $redis->save();

            return response()->json(['code' => 200, 'data' => [], 'message' => 'Data added successfully!']);
        } catch (\Exception $e) {
            return response()->json(['code' => 500, 'data' => [], 'message' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Redisjob  $redisjob
     * @return \Illuminate\Http\Response
     */
    public function clearQue(Request $request)
    {
        try {
            Artisan::call('queue:clear redis --queue=' . $request->name);

            return response()->json(['code' => 200, 'data' => [], 'message' => 'Clear Queue successfully!']);
        } catch (\Exception $e) {
            return response()->json(['code' => 500, 'data' => [], 'message' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Redisjob  $redisjob
     * @return \Illuminate\Http\Response
     */
    public function restartManagement(Request $request)
    {
        try {
            Artisan::call('queue:retry --queue=' . $request->name);

            return response()->json(['code' => 200, 'data' => [], 'message' => 'Clear Queue successfully!']);
        } catch (\Exception $e) {
            return response()->json(['code' => 500, 'data' => [], 'message' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function removeQue(Redisjob $redisjob, Request $request)
    {
        try {
            $redis_data = Redisjob::where('id', $request->id)->delete();

            return response()->json(['code' => 200, 'data' => [], 'message' => 'Deleted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['code' => 500, 'data' => [], 'message' => $e->getMessage()]);
        }
    }
}
