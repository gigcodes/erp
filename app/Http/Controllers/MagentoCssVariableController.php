<?php

namespace App\Http\Controllers;

use App\MagentoCssVariableJobLog;
use App\MagentoCssVariableValueHistory;
use App\MagentoCssVariableVerifyHistory;
use App\Models\MagentoCssVariable;
use App\Models\Project;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

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
        $magentoCssVariables = MagentoCssVariable::latest("id");

        if ($request->keyword) {
            $magentoCssVariables = $magentoCssVariables->where(function ($q) use ($request) {
                $q = $q->orWhere('value', 'LIKE', '%' . $request->keyword . '%')
                  ->orWhere('filename', 'LIKE', '%' . $request->keyword . '%');
            });
        }

        $search_project_id = $request->get('search_project_id');
        if ($search_project_id) {
            $magentoCssVariables = $magentoCssVariables->where('project_id', $search_project_id);
        }

        $search_file_path = $request->get('search_file_path');
        if ($search_file_path) {
            $magentoCssVariables = $magentoCssVariables->where('file_path', 'LIKE', '%' . $search_file_path . '%');
        }

        $search_variable = $request->get('search_variable');
        if ($search_variable) {
            $magentoCssVariables = $magentoCssVariables->where('variable', 'LIKE', '%' . $search_variable . '%');
        }

        $magentoCssVariables = $magentoCssVariables->paginate(50);

        $projects = Project::get()->pluck('name', 'id');
        $file_paths = MagentoCssVariable::groupBy('file_path')
            ->orderBy('file_path')
            ->select(DB::raw('TRIM(file_path) as trimmed_file_path'))
            ->pluck('trimmed_file_path')
            ->toArray();

        $variables = MagentoCssVariable::groupBy('variable')
            ->orderBy('variable')
            ->select(DB::raw('TRIM(variable) as trimmed_variable'))
            ->pluck('trimmed_variable')
            ->toArray();

        return view('magento-css-variable.index', compact('magentoCssVariables', 'projects', 'file_paths', 'variables'));
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

        $oldValue = $magentoCssVariable->value;

        // Save
        $magentoCssVariable->project_id = $data['project_id'];
        $magentoCssVariable->filename = $data['filename'];
        $magentoCssVariable->file_path = $data['file_path'];
        $magentoCssVariable->variable = $data['variable'];
        $magentoCssVariable->value = $data['value'];
        // $magentoCssVariable->create_by = Auth::user()->id;
        $magentoCssVariable->save();

        // Maintain history here
        if ($oldValue != $magentoCssVariable->value) {
            // If value change then is_verified shoule be 0
            $magentoCssVariable->is_verified = 0;
            $magentoCssVariable->save();

            $history = new MagentoCssVariableValueHistory();
            $history->magento_css_variable_id = $magentoCssVariable->id;
            $history->old_value = $oldValue;
            $history->new_value = $magentoCssVariable->value;
            $history->user_id = Auth::user()->id;
            $history->save();
        }
        
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

    public function verify($id)
    {
        $magentoCssVariable = MagentoCssVariable::findOrFail($id);
        $magentoCssVariable->is_verified = 1;
        $magentoCssVariable->save();

        // Maintain history here
        $history = new MagentoCssVariableVerifyHistory();
        $history->magento_css_variable_id = $magentoCssVariable->id;
        $history->value = $magentoCssVariable->value;
        $history->is_verified = 1;
        $history->user_id = Auth::user()->id;
        $history->save();

        return redirect()->route('magento-css-variable.index')
            ->with('success', 'Magento CSS variable verified successfully');
    }

    public function verifyHistories($id)
    {
        $datas = MagentoCssVariableVerifyHistory::with(['user'])
            ->where('magento_css_variable_id', $id)
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $datas,
            'message' => 'History get successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function valueHistories($id)
    {
        $datas = MagentoCssVariableValueHistory::with(['user'])
            ->where('magento_css_variable_id', $id)
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $datas,
            'message' => 'History get successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function jobLogs($id)
    {
        $datas = MagentoCssVariableJobLog::where('magento_css_variable_id', $id)
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $datas,
            'message' => 'History get successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function logs(Request $request)
    {
        $magentoCssVariableJobLogs = new MagentoCssVariableJobLog();

        $project = $request->search_project;
        $error = $request->search_error;
        $message =  $request->search_message;
        $command = $request->search_command;
        $date = $request->date;

        if ($project) {
            $magentoCssVariableJobLogs = MagentoCssVariableJobLog::whereHas('magentoCssVariable.project', function ($query) use ($project) {
                $query->where('name', 'LIKE', '%' . $project . '%');
            });
        }
        if ($error) {
            $magentoCssVariableJobLogs = $magentoCssVariableJobLogs->where('status', 'LIKE', '%' . $error . '%');
        }
        if ($message) {
            $magentoCssVariableJobLogs = $magentoCssVariableJobLogs->where('message', 'LIKE', '%' . $message . '%');
        } 
        if ($command) {
            $magentoCssVariableJobLogs = $magentoCssVariableJobLogs->where('command', 'LIKE', '%' . $command . '%');
        } 
        if ($date) {
            $magentoCssVariableJobLogs = $magentoCssVariableJobLogs->where('created_at', 'LIKE', '%' . $date . '%');
        }

        $magentoCssVariableJobLogs = $magentoCssVariableJobLogs->latest("id")->paginate(50);

        return view('magento-css-variable.logs', compact('magentoCssVariableJobLogs','project','error','message','command','date'));
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
        $oldValue=$magentoCssVariable->value;
        $value=$request->value;
        // Update new value in DB
        $magentoCssVariable->value = $value;
        if($oldValue != $value) {
            // If value change then is_verified shoule be 0
            $magentoCssVariable->is_verified = 0;
        }
        $magentoCssVariable->save();
        
        // Maintain history here
        $history = new MagentoCssVariableValueHistory();
        $history->magento_css_variable_id = $magentoCssVariable->id;
        $history->old_value = $oldValue;
        $history->new_value = $value;
        $history->user_id = Auth::user()->id;
        $history->save();

        $cmd = 'bash ' . getenv('DEPLOYMENT_SCRIPTS_PATH') . 'magento-cssvariable-update.sh -p "' . $project_name . '" -f "' . $filepath . '" -k "'.$key. '" -v "'.$value. '" 2>&1';
        
        \Log::info("Start Magento Css Variable Update Vaule");
        
        $result = exec($cmd, $output, $return_var);

        \Log::info("command:".$cmd);
        \Log::info("output:".print_r($output,true));
        \Log::info("return_var:".$return_var);

        \Log::info("End Magento Css Variable Update Vaule");
        if(!isset($output[0])){
           // Maintain Error Log here in new table. 
            MagentoCssVariableJobLog::create([
                'magento_css_variable_id' => $magentoCssVariable->id,
                'command' => $cmd,
                'message' => json_encode($output), 
                'status' => 'Error', 
            ]);
            return response()->json(['code' => 500, 'message' => 'The response is not found!']);
        }
        $response=json_decode($output[0]);
        if(isset($response->status)  && ($response->status=='true' || $response->status)){
            $message="Variable updated";
            if(isset($response->message) && $response->message!=''){
                $message=$response->message;
            }
            // Maintain Success Log here in new table. 
            MagentoCssVariableJobLog::create([
                'magento_css_variable_id' => $magentoCssVariable->id,
                'command' => $cmd,
                'message' => json_encode($output), 
                'status' => 'Success', 
            ]);
            return response()->json(['code' => 200, 'message' => $message]);
        }else{
            $message="Something Went Wrong! Please check Logs for more details";
            if(isset($response->message) && $response->message!=''){
                $message=$response->message;
            }
            // Maintain Error Log here in new table. 
            MagentoCssVariableJobLog::create([
                'magento_css_variable_id' => $magentoCssVariable->id,
                'command' => $cmd,
                'message' => json_encode($output), 
                'status' => 'Error', 
            ]);
            return response()->json(['code' => 500, 'message' => $message]);
        }
    }

    // Function to format a value for CSV, adding quotes if necessary
    public function formatForCSV($value) {
        // If the value contains a comma or a double quote, enclose it in double quotes and escape existing double quotes.
        if (strpos($value, ',') !== false || strpos($value, '"') !== false) {
            return '"' . str_replace('"', '""', $value) . '"';
        }
        return $value;
    }

    public function updateSelectedValues(Request $request)
    {
        if($request->has('selectedIds') && $request->selectedIds != ''){
            $selectedIds = $request->selectedIds;
            $magentoCssVariables = MagentoCssVariable::whereIn('id', $selectedIds)->get();
            // Create a new CSV file content
            $csvContent = '"Project","variable","value","filepath"' . "\n";
            foreach ($magentoCssVariables as $row) {
                // Adjust this according to your data structure
                $csvContent .= $this->formatForCSV($row->project->name) . ','
                . $this->formatForCSV($row->variable) . ','
                . $this->formatForCSV($row->value) . ','
                . $this->formatForCSV($row->file_path) . "\n";
            }

            // Set the file path where the CSV will be stored
            $filePath = 'magento-css-variable-csv/file_' . time() . '.csv';
            
            Storage::disk('public')->put($filePath, $csvContent);

            // Get the path to the stored CSV file
            $fullFilePath = Storage::disk('public')->path($filePath);

            $cmd = 'bash ' . getenv('DEPLOYMENT_SCRIPTS_PATH') . 'magento-cssvariable-update.sh -CF "' . $fullFilePath . '" 2>&1';
        
            \Log::info("Start Magento Css Variable Update Vaule");
            
            $result = exec($cmd, $output, $return_var);

            \Log::info("command:".$cmd);
            \Log::info("output:".print_r($output,true));
            \Log::info("return_var:".$return_var);

            \Log::info("End Magento Css Variable Update Vaule");
            if(!isset($output[0])){
                $selectedIds = implode(",", $selectedIds);
                // Maintain Error Log here in new table. 
                // ToDo: How to maintain log here ?
                MagentoCssVariableJobLog::create([
                    'command' => $cmd,
                    'message' => json_encode($output), 
                    'status' => 'Error', 
                    'csv_file_path' => $fullFilePath,
                    'magento_css_variable_id' => $selectedIds,
                ]);
                return response()->json(['code' => 500, 'message' => 'The response is not found!']);
            }
            $response=json_decode($output[0]);
            if(isset($response->status)  && ($response->status=='true' || $response->status)){
                $message="Variable updated";
                if(isset($response->message) && $response->message!=''){
                    $message=$response->message;
                }
                $selectedIds = implode(",", $selectedIds);
                // Maintain Error Log here in new table. 
                // ToDo: How to maintain log here ?
                MagentoCssVariableJobLog::create([
                    'command' => $cmd,
                    'message' => json_encode($output), 
                    'status' => 'Success', 
                    'csv_file_path' => $fullFilePath,
                    'magento_css_variable_id' => $selectedIds,
                ]);
                return response()->json(['code' => 200, 'message' => $message]);
            }else{
                $message="Something Went Wrong! Please check Logs for more details";
                if(isset($response->message) && $response->message!=''){
                    $message=$response->message;
                }
                $selectedIds = implode(",", $selectedIds);
                // Maintain Error Log here in new table. 
                // ToDo: How to maintain log here ?
                MagentoCssVariableJobLog::create([
                    'command' => $cmd,
                    'message' => json_encode($output), 
                    'status' => 'Error', 
                    'csv_file_path' => $fullFilePath,
                    'magento_css_variable_id' => $selectedIds,
                ]);
                return response()->json(['code' => 500, 'message' => $message]);
            }
        }

        return response()->json(['code' => 500, 'message' => "Please select the row"]);
    }

    public function updateValuesForProject(Request $request)
    {
        if($request->has('project_id') && $request->project_id != ''){
            $projectId = $request->project_id;
            $magentoCssVariables = MagentoCssVariable::where('project_id', $projectId)->get();

            // JOB concept not need. (OLD)
            // foreach($magentoCssVariables as $magentoCssVariable) {
            //     \App\Jobs\PushMagentoCssVariables::dispatch($magentoCssVariable)->onQueue('pushmagentocssvariables');
            // }

            // CSV concept (NEW)
            // Create a new CSV file content
            $csvContent = '"Project","variable","value","filepath"' . "\n";
            foreach ($magentoCssVariables as $row) {
                // Adjust this according to your data structure
                $csvContent .= $this->formatForCSV($row->project->name) . ','
                . $this->formatForCSV($row->variable) . ','
                . $this->formatForCSV($row->value) . ','
                . $this->formatForCSV($row->file_path) . "\n";
            }

            // Set the file path where the CSV will be stored
            $filePath = 'magento-css-variable-csv/file_' . time() . '.csv';
            
            Storage::disk('public')->put($filePath, $csvContent);

            // Get the path to the stored CSV file
            $fullFilePath = Storage::disk('public')->path($filePath);

            $cmd = 'bash ' . getenv('DEPLOYMENT_SCRIPTS_PATH') . 'magento-cssvariable-update.sh -CF "' . $fullFilePath . '" 2>&1';
        
            \Log::info("Start Magento Css Variable Update Vaule");
            
            $result = exec($cmd, $output, $return_var);

            \Log::info("command:".$cmd);
            \Log::info("output:".print_r($output,true));
            \Log::info("return_var:".$return_var);

            \Log::info("End Magento Css Variable Update Vaule");
            if(!isset($output[0])){
                // Maintain Error Log here in new table. 
                // ToDo: How to maintain log here ?
                MagentoCssVariableJobLog::create([
                    'command' => $cmd,
                    'message' => json_encode($output), 
                    'status' => 'Error', 
                    'csv_file_path' => $fullFilePath
                ]);
                return redirect(route('magento-css-variable.index'))->with('error', 'The response is not found!');
            }
            $response=json_decode($output[0]);
            if(isset($response->status)  && ($response->status=='true' || $response->status)){
                $message="Variable updated";
                if(isset($response->message) && $response->message!=''){
                    $message=$response->message;
                }
                // Maintain Error Log here in new table. 
                // ToDo: How to maintain log here ?
                MagentoCssVariableJobLog::create([
                    'command' => $cmd,
                    'message' => json_encode($output), 
                    'status' => 'Success', 
                    'csv_file_path' => $fullFilePath
                ]);
                return redirect(route('magento-css-variable.index'))->with('success', $message);
            }else{
                $message="Something Went Wrong! Please check Logs for more details";
                if(isset($response->message) && $response->message!=''){
                    $message=$response->message;
                }
                // Maintain Error Log here in new table. 
                // ToDo: How to maintain log here ?
                MagentoCssVariableJobLog::create([
                    'command' => $cmd,
                    'message' => json_encode($output), 
                    'status' => 'Error', 
                    'csv_file_path' => $fullFilePath
                ]);
                return redirect(route('magento-css-variable.index'))->with('error', $message);
            }
        }

        return redirect(route('magento-css-variable.index'))->with('error', 'Please select the project!');
    }


    public function download($id)
    {
        $fileName = MagentoCssVariableJobLog::find($id);

        $file_name = basename($fileName->csv_file_path);

        $filePath =   storage_path('app/public/magento-css-variable-csv/' . $file_name);

        if (file_exists($filePath)) {
            return Response::download($filePath);
        } else {
            abort(404, 'The file you are trying to download does not exist.');
        }
    }
}
