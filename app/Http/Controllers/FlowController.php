<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Flow;
use App\FlowType;
use App\FlowPath;
use App\FlowAction;
use App\FlowMessage;
use App\StoreWebsite;
use Validator;
use Illuminate\Support\Str;


class FlowController extends Controller{

    public function index(){
		$flows = Flow::leftJoin('store_websites', 'flows.store_website_id', '=', 'store_websites.id')
		->select('store_websites.title', 'flows.*')->get();
		$websites = StoreWebsite::pluck('title', 'id')->toArray();
        return view('flow.index', compact('flows', 'websites'));
    }
	
	public function createFlow(Request $request) {
		$validator = Validator::make($request->all(), [
            'store_website_id' => 'required',
            'flow_name' => 'required',
            'flow_description' => 'required',
        ]);
		
		if ($validator->fails()) {  
			$errors = $validator->getMessageBag();
			$errors = $errors->toArray();
			$message = '';
			foreach($errors as $error) {
				$message .= $error[0].'<br>';
			}
            return response()->json(['status' => 'failed', 'statusCode'=>500,'message' => $message]);
        }
		$inputs = $request->input();
		if($inputs['id'] == null) {
			$inputs['flow_code'] = $this->randomFlowCode();
		}
		$flow = Flow::updateOrCreate(['id'=>$inputs['id']], $inputs);
		if($inputs['id'] == null) {
			FlowPath::create(['flow_id'=>$flow['id']]);
		}
		return response()->json(['status' => 'success', 'statusCode'=>200,'message' => 'Flow Created successfully']);
    }
	
	public function createType(Request $request) {
		$validator = Validator::make($request->all(), [
           'type' => 'required',
        ]);
		
		if ($validator->fails()) {  
			$errors = $validator->getMessageBag();
			$errors = $errors->toArray();
			$message = '';
			foreach($errors as $error) {
				$message .= $error[0].'<br>';
			}
            return response()->json(['status' => 'failed', 'statusCode'=>500,'message' => $message]);
        }
		$inputs = $request->input();
		FlowType::updateOrCreate(['id'=>$inputs['id']], $inputs);
		return response()->json(['status' => 'success', 'statusCode'=>200,'message' => 'Flow Type Created successfully']);
    }
	
	public function editFlow($flowCode) {
		$flowDetail = Flow::where('flow_code', $flowCode)->first();	
		return view('flow.create', compact('flowDetail'));
	}

	public function updateFlow(Request $request) {
		$inputs = $request->input(); 
		$flowDetail = Flow::where('id', $inputs['flow_id'])->first();
		$flowTypeId = FlowType::where('type', $inputs['action_type'])->pluck('id')->first();
		$rank = FlowAction::where(['path_id'=>$inputs['path_id']])->orderBy('rank', 'desc')->whereNotNull('rank')->pluck('rank')->first();
		if($rank == null) {
			$rank = 1;
		} else {
			$rank = $rank + 1;
		}
		FlowAction::create(['path_id'=>$inputs['path_id'], 'type_id'=>$flowTypeId, 'rank'=>$rank ]);
		$flowActions = FlowAction::leftJoin('flow_types', 'flow_types.id', '=', 'flow_actions.type_id')
					   ->select('flow_actions.*', 'flow_types.type')->where(['path_id'=>$inputs['path_id']])->orderBy('rank')->get();
		$flowPathId = $inputs['path_id']; 
		$html =  view('flow.create', compact('flowDetail', 'flowActions', 'flowPathId'))->render();
		return ['message'=>$html, 'statusCode'=>200];
	}
	
	public function updateFlowActions(Request $request) {
		$inputs = $request->input();
		if(isset($inputs['action_id'])) {
			foreach($inputs['action_id'] as $key=>$actionId) {
				if($inputs['action_type'][$actionId] == "Time Delay") {
					$data = ['time_delay'=>$inputs['time_delay'][$actionId], 'time_delay_type'=>$inputs['time_delay_type'][$actionId], 'rank'=>$key];
					FlowAction::where('id', $actionId)->update($data);
				} else {
					$data = [ 'rank'=>$key];
					FlowAction::where('id', $actionId)->update($data);
				}
			}
		}
		
		return ['message'=>"Successfully updated", 'statusCode'=>200];
		//return $this->flowDetail($inputs['flow_id']);
	}
	
	public function flowDetail($flowId) {
		$flowDetail = Flow::where('id', $flowId)->first();
		$flowPathId = FlowPath::where('flow_id', $flowId)->pluck('id')->first();
		$flowActions = FlowAction::leftJoin('flow_types', 'flow_types.id', '=', 'flow_actions.type_id')
					   ->select('flow_actions.*', 'flow_types.type')->where('path_id', $flowPathId)->orderBy('rank')->get();
		return view('flow.create', compact('flowDetail', 'flowActions', 'flowPathId'));
	}
	
	public function randomFlowCode() {
		$random = Str::random(6);
		$codeExist = Flow::where('flow_code', $random)->first();
		if($codeExist) {
			$this->randomFlowCode();
		}else {
			return $random;
		}
	}
	
	public function updateAction(Request $request) {
		$input = $request->input();
		FlowAction::updateOrCreate(['action_id'=>$input['action_id']], $input);
		return ['message'=>"Successfully added", 'statusCode'=>200];
	}
	
	public function deleteActionMessage(Request $request) {
		$messageId = $request->messageId;
		FlowAction::where('id', $messageId)->delete();
		return ['message'=>"Message deleted successfully", 'statusCode'=>200];
	}
	
	public function getActionMessage($actionId) {
		$flowMessage = FlowMessage::where(['action_id'=>$actionId])->first();
		return view('flow.message', compact('flowMessage'));
	}
	
	public function updateActionMessage(Request $request) {
		$validator = Validator::make($request->all(), [
            'sender_name'=>'required',
			'sender_email_address'=>'required',
			'subject'=>'required',
			'html_content'=>'required',
        ]);
		
		if ($validator->fails()) {  
			$errors = $validator->getMessageBag();
			$errors = $errors->toArray();
			$message = '';
			foreach($errors as $error) {
				$message .= $error[0].'<br>';
			}
            return response()->json(['status' => 'failed', 'statusCode'=>500,'message' => $message]);
        }
		$inputs = $request->input();
		FlowMessage::updateOrCreate(['id'=>$inputs['id']], $inputs);
		return redirect()->back();
		//return response()->json(['status' => 'success', 'statusCode'=>200,'message' => 'Message Saved successfully']);
	}

	public function flowDelete(Request $request){
		$input = $request->input(); 
		$flow = Flow::find($input['id']);
		$paths = FlowPath::where('flow_id', $flow->id)->get();
		foreach($paths as $path) {
			$flowActions = FlowAction::where('path_id', $path->id)->get();
			foreach($flowActions as $flowAction) {
				FlowMessage::where('action_id', $flowAction->id)->delete();
				$flowAction->delete();
			}
			$path->delete();
		}
		$flow->delete();
		return ['message'=>"Flow Successfully Deleted", 'statusCode'=>200];
	}

	public function flowActionDelete(Request $request){
		FlowAction::where('id', $request->input('id'))->delete();
		FlowMessage::where('action_id', $request->input('id'))->delete();
		return ['message'=>"Flow Successfully Deleted", 'statusCode'=>200];
	}
	
	
}
