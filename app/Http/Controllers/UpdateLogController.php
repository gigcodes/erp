<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\UpdateLog;
use App\Setting;

class UpdateLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $updateLog = UpdateLog::paginate(15);
            return view("update-log.index", compact('updateLog'));
        } catch(\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
        
    }

    public function search(Request $request)
    {
        $updateLog = new UpdateLog();
        
        if (!empty($request->api_url)) {
            $updateLog = $updateLog->where("api_url", "like", "%".$request->api_url."%");
        }
        if (!empty($request->device)) {
            $updateLog = $updateLog->where("device", "like", "%".$request->device."%");
        }
        if (!empty($request->api_type)) {
            $updateLog = $updateLog->where("api_type", "like", "%".$request->api_type."%");
        }
        $updateLog = $updateLog->paginate(Setting::get('pagination'));
        return view("update-log.index", compact('updateLog'));
    }

    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $updateLog = new UpdateLog();
            $updateLog->api_url = $request->api_url;
            $updateLog->device = $request->device;
            $updateLog->api_type = $request->api_type;
            $updateLog->user_id = $request->user_id;
            $updateLog->request_header = is_array($request->header)? json_encode($request->header) : $request->header;
            $updateLog->app_version = $request->app_version;
            $updateLog->other = $request->other;
            $updateLog->save();
            if (!empty($updateLog)) {
                return response()->json(['status' => true, 'response_code' => 200, 'data' => $updateLog], JsonResponse::HTTP_OK);
            }
            return response()->json(['status' => false, 'response_code' => 404, 'message' => "Data not found"], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'response_code' => 500, 'message' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $updateLog = UpdateLog::all();

            if (!empty($updateLog)) {
                return response()->json([$updateLog]);
            }
            return response()->json(['message' => "Data not found"], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        try{
            $updateLog = UpdateLog::where('id', '=', $request->id)->delete();
            return response()->json(['code' => 200, 'data' => $updateLog,'message' => 'Deleted successfully!!!']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }
}
