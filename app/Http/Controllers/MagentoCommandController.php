<?php

namespace App\Http\Controllers;

use App\MagentoCommand;
use App\StoreWebsite;
use App\User;
use App\MagentoCommandRunLog;
use App\Setting;
use Illuminate\Http\Request;

class MagentoCommandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $magentoCommand = MagentoCommand::paginate(Setting::get('pagination'))->appends(request()->except(['page']));
            
            $websites = StoreWebsite::all();
            //dd($websites);
            $users = User::all();
            return view("magento-command.index", compact('magentoCommand', 'websites', 'users'));
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return redirect()->back()->withErrors($msg);
        }
    }

    public function search(Request $request)
    {
        $magentoCommand = MagentoCommand::whereNotNull('id');
        
        if (!empty($request->website)) {
            $magentoCommand->where("website_ids", "like", "%".$request->website."%");
        }
        if (!empty($request->command_name)) {
            $magentoCommand->where("command_name", "like", "%".$request->command_name."%");
        }
        if (!empty($request->user_id)) {
            $magentoCommand->where("user_id", "=", $request->user_id);
        }
        $magentoCommand = $magentoCommand->paginate(Setting::get('pagination'));
        $users = User::all();
        $websites = StoreWebsite::all();
        return view("magento-command.index", compact('magentoCommand', 'websites', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            $created_user_permission = '';
            if(isset($request->id) && $request->id > 0){
                $mCom = MagentoCommand::find($request->id);
                $type = 'Update';
            } else {
                $mCom = new MagentoCommand();
                $type = 'Created';
            }

            $mCom->user_id = \Auth::user()->id;
            $mCom->website_ids = isset($request->websites_ids)?implode(",",$request->websites_ids): $mCom->websites_ids;
            $mCom->command_name = $request->command_name;
            $mCom->command_type = $request->command_type;
            $mCom->save();
            //$this->createPostmanHistory($mCom->id, $type);
            return response()->json(['code' => 200, 'message' => 'Added successfully!!!']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    
    /**
     * Display the specified resource.
     *
     * @param  \App\MagentoCommand  $magentoCommand
     * @return \Illuminate\Http\Response
     */
    public function runCommand(Request $request)
    {

        try{
            
            $comd = \Artisan::call("command:MagentoCreatRunCommand", ['id' => $request->id]);
            return response()->json(['code' => 200, 'message' => 'Magento Command Run successfully']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return response()->json(['code' => 500, 'message' => $msg]);
        }
    
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\MagentoCommand  $magentoCommand
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        try{
            $magentoCom = MagentoCommand::find($request->id);
            $websites = StoreWebsite::all();
            $ops = '';
            foreach($websites as $website){
                $selected = '';
                if($magentoCom->website_ids == $website->id)
                    $selected = 'selected';
                $ops .= '<option value="'.$website->id.'" '.$selected.'>'.$website->name.'</option>';
            }
            
            return response()->json(['code' => 200, 'data' => $magentoCom, 'ops' => $ops, 'message' => 'Listed successfully!!!']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function commandHistoryLog(Request $request)
    {
        try{
            $postHis = MagentoCommandRunLog::select('magento_command_run_logs.*', 'u.name AS userName')
            ->leftJoin('users AS u', 'u.id', 'magento_command_run_logs.user_id')
            ->where('command_id', '=', $request->id)->orderby('id', 'DESC')->get();
            return response()->json(['code' => 200, 'data' => $postHis,'message' => 'Listed successfully!!!']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\MagentoCommand  $magentoCommand
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        try{
            $postman = MagentoCommand::where('id', '=', $request->id)->delete();
            return response()->json(['code' => 200, 'data' => $postman,'message' => 'Deleted successfully!!!']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }
}
