<?php

namespace App\Http\Controllers;

use App\SimplyDutySegment;
use Illuminate\Http\Request;
use App\Setting;
use Response;

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
  return view('simplyduty.segment.index',compact('segments'));
    }

    public function segment_add(Request $request)
    {
        $id=$request->segment_id;
        $segment=$request->segment;
        $price=$request->price;
        if ($id==0)
           {
               
            SimplyDutySegment::insert(['segment'=>$segment,'price'=>$price]);
            
           return redirect()->back()->with('success',"Segment Added Successfully") ;
           }
        else
          {
            SimplyDutySegment::where('id',$id)->update(['segment'=>$segment,'price'=>$price]) ;
            
            return redirect()->back()->with('success',"Segment Updated Successfully") ;
          }   

    }

    public function segment_delete(Request $request)
    {
        $id=$request->segment_id;
        if ($id>0)
           {
               
            SimplyDutySegment::where('id',$id)->delete() ;
            
            return redirect()->back()->with('success',"Segment Deleted Successfully") ;
           }
        else
          {
                       
            return redirect()->back()->with('error',"Invalid Call") ;
          }   

    }
   
    
   

  
    
}
