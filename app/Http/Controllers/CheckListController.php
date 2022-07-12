<?php

namespace App\Http\Controllers;

use App\Checklist;
use Illuminate\Http\Request;

class CheckListController extends Controller
{

    public function __construct()
    {
        //view files
        $this->index_view = 'checklist.index';
        $this->create_view = 'checklist.create';
        $this->detail_view = 'checklist.details';
        $this->edit_view = 'checklist.edit';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        if ($request->ajax()) {
            $items = Checklist::select('id','category_name', 'sub_category_name','subjects','status');

            
            if (isset($request->category_name) && !empty($request->category_name)) {
                $items->where('checklist.category_name', 'Like', '%' . $request->category_name . '%');
            }

            if (isset($request->sub_category_name) && !empty($request->sub_category_name)) {
                $items->where('checklist.sub_category_name', 'Like', '%' . $request->sub_category_name . '%');
            }
            if (isset($request->subjects) && !empty($request->subjects)) {
                $items->whereRaw("find_in_set('".$request->subjects."',checklist.subjects)");
            
            }
            return datatables()->eloquent($items)->toJson();
        }else{
            $title = 'CheckLists Module';
            return view($this->index_view, compact('title'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->except(['_token']);
        $checklist = Checklist::where('category_name',$input['category_name'])->where('sub_category_name',$input['sub_category_name'])->where('status',1)->first();
        if($checklist){
            
            return response()->json([
                'status' => false,
                'message' => 'Category and Sub Category already exists!',
                'status_name' => 'error'
            ], 500);
        }else{
            $data = Checklist::create($input);
            if($data){
                return response()->json([
                    'status' => true,
                    'message' => 'Checklist saved successfully',
                    'status_name' => 'success'
                ], 200);
            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'something error occurred',
                    'status_name' => 'error'
                ], 500);
            }
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
        //
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
        
          $input = $request->except('_token');
          $checklist = Checklist::find($id);
          if($checklist){
            $checklist->update($input);
            if($checklist){
                return response()->json([
                    'status' => true,
                    'message' => 'Checklist updated successfully',
                    'status_name' => 'success'
                ], 200);
            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'something error occurred',
                    'status_name' => 'error'
                ], 500);
            }
          }else{
            $saveRecord = Checklist::create($input);
                if($saveRecord){
                    return response()->json([
                        'status' => true,
                        'message' => 'Checklist saved successfully',
                        'status_name' => 'success'
                    ], 200);
                }else{
                    return response()->json([
                        'status' => false,
                        'message' => 'something error occurred',
                        'status_name' => 'error'
                    ], 500);
                }
          }
          
   
         
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Checklist::where('id',$id)->delete();

        if ($data) {
            return response()->json([
                'status' => true,
                'message' => 'Checklist Deleted successfully',
                'status_name' => 'success'
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Deleted unsuccessfully',
                'status_name' => 'error'
            ], 500);
        }
    }
}
