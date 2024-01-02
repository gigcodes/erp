<?php

namespace App\Http\Controllers;

use App\ModelName;
use Illuminate\Http\Request;

class ModelNameController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $modelName = ModelName::latest('created_at')->paginate(10);

        return view('model-name.index', compact('modelName'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $modelName = new ModelName();
            $modelName->name = $request->name;
            $modelName->save();

            return response()->json(['code' => 200, 'data' => $modelName, 'message' => 'You have successfully added Model!']);
        } catch (\Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(ModelName $modelName, Request $request)
    {
        try {
            $modelName = ModelName::where('id', $request->id)->first();

            return response()->json(['code' => 200, 'data' => $modelName, 'message' => 'Listed successfully!']);
        } catch (\Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ModelName $modelName)
    {
        try {
            $modelName = ModelName::where('id', $request->model_id)->update(['name' => $request->name]);
            $modelNameData = ModelName::where('id', $request->model_id)->first();

            return response()->json(['code' => 200, 'data' => $modelNameData, 'message' => 'Updated successfully!']);
        } catch (\Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(ModelName $modelName, Request $request)
    {
        try {
            $deleted = ModelName::where('id', $request->id)->first();
            \DB::table('model_names')->where('id', $request->id)->delete();

            return response()->json(['code' => 200, 'data' => $deleted->name, 'message' => 'Deleted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }
}
