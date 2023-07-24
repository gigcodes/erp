<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ThemeFile;
use App\Models\ThemeStructure;
use Illuminate\Http\Request;
use App\Models\ProjectTheme;

class ThemeStructureController extends Controller
{
    public function __construct()
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id=null)
    {
        $themes = ProjectTheme::get()->pluck('name','id')->toArray();
        
        if(!$id && !empty($themes) ){
           $id=array_key_first($themes);
        }
        $theme=ProjectTheme::find($id);
        
        $tree = json_encode($this->buildTree($id));
        $theme_id=$id;
        return view('theme-structure.index', compact('tree','themes','theme_id'));
    }

    public function reloadTree($theme)
    {
        $tree = $this->buildTree($theme);
        return response()->json($tree);
    }

    private function buildTree($theme,$parentID = null)
    {
        $tree = [];
        $items = ThemeStructure::where('theme_id', $theme)->where('parent_id', $parentID)->orderBy('position')->get(['id', 'name', 'is_file', 'is_root','theme_id']);
        
        foreach ($items as $item) {
            $node = [
                'id' => $item->id,
                'parent_id' => $parentID ?: '#',
                'text' => $item->name,
                'is_root' => $item->is_root,
                'theme_id' => $item->theme_id,
            ];

            if ($item->is_file) {
                $node['icon'] = 'jstree-file';
                $node['type'] = 'file';
            } else {
                $node['icon'] = 'jstree-folder';
                $node['type'] = 'folder';
                $node['children'] = $this->buildTree($theme,$item->id);
            }

            $tree[] = $node;
        }

        return $tree;
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'theme_id' => 'required',
            'is_file' => 'required|boolean',
            'parent_id' => 'nullable|exists:theme_structure,id'
        ]);
        
        $folder = ThemeStructure::create($validatedData);

        $action='add';
        $path=$request->fullpath.'/'.$request->name;
        $directory=true;
        $theme=$folder->theme->name;
        $project=$folder->theme->project->name;
        //dd([$action,$path,$directory,$theme,$project]);

        $cmd = 'bash ' . getenv('DEPLOYMENT_SCRIPTS_PATH') . 'theme_structure.sh -a "' . $action . '" -u "' . $path . '" -d "'.$directory. '" -t "'.$theme.'" -p "'.$project. '" 2>&1';
        
        \Log::info("Start Magento theme folder create");
        
        $result = exec($cmd, $output, $return_var);

        \Log::info("command:".$cmd);
        \Log::info("output:".print_r($output,true));
        \Log::info("return_var:".$return_var);
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

        \Log::info("End Magento theme folder create");

        return response()->json($folder);
    }

    public function themeFileStore(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'theme_id' => 'required',
            'is_file' => 'required|boolean',
            'parent_id' => 'required|exists:theme_structure,id'
        ]);

        $file = ThemeFile::create($validatedData);

        $action='add';
        $path=$request->fullpath.'/'.$request->name;
        $directory=false;
        $theme=$file->theme->name;
        $project=$file->theme->project->name;
        //dd([$action,$path,$directory,$theme,$project]);

        $cmd = 'bash ' . getenv('DEPLOYMENT_SCRIPTS_PATH') . 'theme_structure.sh -a "' . $action . '" -u "' . $path . '" -d "'.$directory. '" -t "'.$theme.'" -p "'.$project. '" 2>&1';
        
        \Log::info("Start Magento theme file create");
        
        $result = exec($cmd, $output, $return_var);

        \Log::info("command:".$cmd);
        \Log::info("output:".print_r($output,true));
        \Log::info("return_var:".$return_var);
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

        \Log::info("End Magento file folder create");

        return response()->json($file);
    }

    public function deleteItem(Request $request)
    {
        $itemId = $request->input('id');
        $item = ThemeStructure::find($itemId);

        if ($item) {
            if ($item->is_root) {
                return response()->json(['code'=>500,'message' => 'Root folder cannot be deleted'], 403);
            }
            
            $action='delete';
            $path=$request->fullpath.'/'.$item->name;
            $directory=true;
            if($item->is_file){
                $directory=false;
            }
            $theme=$item->theme->name;
            $project=$item->theme->project->name;

            $cmd = 'bash ' . getenv('DEPLOYMENT_SCRIPTS_PATH') . 'theme_structure.sh -a "' . $action . '" -u "' . $path . '" -d "'.$directory. '" -t "'.$theme.'" -p "'.$project. '" 2>&1';
        
            \Log::info("Start Magento theme folder/file delete");
            
            $result = exec($cmd, $output, $return_var);
    
            \Log::info("command:".$cmd);
            \Log::info("output:".print_r($output,true));
            \Log::info("return_var:".$return_var);
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

            \Log::info("End Magento file folder/file delete");

            $item->delete();
            return response()->json(['code'=>200,'message' => 'Item deleted successfully']);
        }

        return response()->json(['code'=>500,'message' => 'Item not found'], 404);
    }

    public function destroy($id)
    {
        $project = Project::find($id);
        $project->storeWebsites()->detach();
        $project->delete();

        return redirect()->route('project.index')
            ->with('success', 'Project deleted successfully');
    }

    public function edit(Request $request, $id)
    {
        $project = Project::with('storeWebsites')->where('id', $id)->first();

        if ($project) {
            return response()->json(['code' => 200, 'data' => $project]);
        }

        return response()->json(['code' => 500, 'error' => 'Id is wrong!']);
    }

    public function update(Request $request, $id)
    {
        // Validation Part
        $this->validate(
            $request, [
                'name' => 'required',
                'job_name' => 'required',
                'store_website_id' => 'required',
                'serverenv' => 'required'
            ]
        );

        $data = $request->except('_token');

        // Save Project
        $project = Project::find($data['id']);
        $project->name = $data['name'];
        $project->job_name = $data['job_name'];
        $project->serverenv = $data['serverenv'];
        $project->save();

        $project->storeWebsites()->detach();
        $project->storeWebsites()->attach($data['store_website_id']);

        return response()->json(
            [
                'code' => 200,
                'data' => [],
                'message' => 'Project updated successfully!',
            ]
        );
    }

    

}
