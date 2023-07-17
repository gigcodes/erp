<?php

namespace App\Http\Controllers;

use App\Models\MagentoCssVariable;
use App\Models\Project;
use Illuminate\Http\Request;
use Auth;

class MagentoCssVariableController extends Controller
{
    public function __construct()
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $magentoCssVariables = MagentoCssVariable::latest();

        if ($request->keyword) {
            $magentoCssVariables = $magentoCssVariables->where(function ($q) use ($request) {
                $q = $q->orWhere('variable', 'LIKE', '%' . $request->keyword . '%')
                  ->orWhere('value', 'LIKE', '%' . $request->keyword . '%')
                  ->orWhere('filename', 'LIKE', '%' . $request->keyword . '%')
                  ->orWhere('file_path', 'LIKE', '%' . $request->keyword . '%');
            });
        }

        $search_project_id = $request->get('search_project_id');
        if ($search_project_id) {
            $magentoCssVariables = $magentoCssVariables->where('project_id', $search_project_id);
        }

        $magentoCssVariables = $magentoCssVariables->paginate(10);

        $projects = Project::get()->pluck('name', 'id');
        
        return view('magento-css-variable.index', compact('magentoCssVariables', 'projects'));
    }

    public function store(Request $request)
    {
        // Validation Part
        $this->validate(
            $request, [
                'project_id' => 'required',
                'filename' => 'required',
                'file_path' => 'required',
                'variable' => 'required',
                'value' => 'required',
            ]
        );

        $data = $request->except('_token');

        // Save
        $magentoCssVariable = new MagentoCssVariable();
        $magentoCssVariable->project_id = $data['project_id'];
        $magentoCssVariable->filename = $data['filename'];
        $magentoCssVariable->file_path = $data['file_path'];
        $magentoCssVariable->variable = $data['variable'];
        $magentoCssVariable->value = $data['value'];
        $magentoCssVariable->create_by = Auth::user()->id;
        $magentoCssVariable->save();

        return response()->json(
            [
                'code' => 200,
                'data' => [],
                'message' => 'Magento CSS variable created successfully!',
            ]
        );
    }

    public function edit(Request $request, $id)
    {
        $magentoCssVariable = MagentoCssVariable::where('id', $id)->first();

        if ($magentoCssVariable) {
            return response()->json(['code' => 200, 'data' => $magentoCssVariable]);
        }

        return response()->json(['code' => 500, 'error' => 'Id is wrong!']);
    }

    public function update(Request $request, $id)
    {
        // Validation Part
        $this->validate(
            $request, [
                'project_id' => 'required',
                'filename' => 'required',
                'file_path' => 'required',
                'variable' => 'required',
                'value' => 'required',
            ]
        );

        $data = $request->except('_token');

        $magentoCssVariable = MagentoCssVariable::where('id', $id)->firstOrFail();

        // Save
        $magentoCssVariable->project_id = $data['project_id'];
        $magentoCssVariable->filename = $data['filename'];
        $magentoCssVariable->file_path = $data['file_path'];
        $magentoCssVariable->variable = $data['variable'];
        $magentoCssVariable->value = $data['value'];
        // $magentoCssVariable->create_by = Auth::user()->id;
        $magentoCssVariable->save();

        return response()->json(
            [
                'code' => 200,
                'data' => [],
                'message' => 'Magento CSS variable updated successfully!',
            ]
        );
    }

    public function destroy($id)
    {
        $magentoCssVariable = MagentoCssVariable::findOrFail($id);
        $magentoCssVariable->delete();

        return redirect()->route('magento-css-variable.index')
            ->with('success', 'Magento CSS variable deleted successfully');
    }
    
    public function updateValue(Request $request){

        $id=$request->id;
        $magentoCssVariable=MagentoCssVariable::where('id', $id)->first();
        if(!$magentoCssVariable){
            return response()->json(['code' => 500, 'message' => 'Variable data is not found!']);
        }
        $project_name=optional($magentoCssVariable->project)->name;
        $filepath=$magentoCssVariable->file_path;
        $key=$magentoCssVariable->variable;
        $value=$magentoCssVariable->value;
        
        $cmd = 'bash ' . getenv('DEPLOYMENT_SCRIPTS_PATH') . 'magento-cssvariable-update.sh -p "' . $project_name . '" -f "' . $filepath . '" -k "'.$key. '" -v "'.$value. '" 2>&1';
        
        \Log::info("Start Magento Css Variable Update Vaule");
        
        $result = exec($cmd, $output, $return_var);

        \Log::info("command:".$cmd);
        \Log::info("output:".print_r($output,true));
        \Log::info("return_var:".$return_var);

        \Log::info("End Magento Css Variable Update Vaule");
        if(!isset($output[0])){
           
            return response()->json(['code' => 500, 'message' => 'The response is not found!']);
        }
        $response=json_decode($output[0]);
        if(isset($response->status)  && ($response->status=='true' || $response->status)){
            $message="Variable updated";
            if(isset($response->message) && $response->message!=''){
                $message=$response->message;
            }
            return response()->json(['code' => 200, 'message' => $message]);
        }else{
            $message="Something Went Wrong! Please check Logs for more details";
            if(isset($response->message) && $response->message!=''){
                $message=$response->message;
            }
            return response()->json(['code' => 500, 'message' => $message]);
        }
    }

}
