<?php

namespace App\Http\Controllers;

use App\Setting;
use App\SimplyDutySegment;
use Illuminate\Http\Request;

class SimplyDutySegmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $segments = SimplyDutySegment::paginate(Setting::get('pagination'));

        return view('simplyduty.segment.index', compact('segments'));
    }

    public function segment_add(Request $request)
    {
        $id = $request->segment_id;
        $segment = $request->segment;
        $price = $request->price;
        if ($id == 0) {
            SimplyDutySegment::insert(['segment' => $segment, 'price' => $price]);

            return response()->json(['success' => true, 'message' => 'Segment Updated Successfully']);
        } else {
            SimplyDutySegment::where('id', $id)->update(['segment' => $segment, 'price' => $price]);

            return response()->json(['success' => true, 'message' => 'Segment Updated Successfully']);
        }
    }

    public function segment_delete(Request $request)
    {
        $id = $request->segment_id;
        if ($id > 0) {
            SimplyDutySegment::where('id', $id)->delete();

            return response()->json(['success' => true, 'message' => 'Segment Deleted Successfully']);
        } else {
            return response()->json(['success' => false, 'message' => 'Invalid']);
        }
    }
}
