<?php

namespace App\Http\Controllers;

use App\PushNotification;
use App\Remark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PushNotificationController extends Controller {

	public function getJson() {

		return PushNotification::where( 'isread', 0 )
		                       ->where( function ( $query ) {
			                       return $query->where( 'sent_to', \Auth::id() )
			                                    ->orWhereIn( 'role', \Auth::user()->getRoleNames() );
		                       } )/*->limit( 3 )*/
							   ->orderBy('created_at','DESC')
		                       ->get()->toArray();

	}

	public function markRead( PushNotification $push_notification ) {

		$push_notification->isread = 1;
		$push_notification->save();

		return [ 'msg' => 'success' ];
	}

	public function changeStatus(PushNotification $push_notification,Request $request){

		$status = $request->input('status');
		$model_type = $push_notification->model_type;
		$remark = $request->input('remark');

		$model_class= new $model_type();
		$model_instance = $model_class->findOrFail($push_notification->model_id);

		$model_instance->assign_status = $status;

		if(!empty($remark))
		{
			if($model_type == 'App\\Task') {

				if($status != 1) {

					Remark::create( [
						'remark' => $remark,
						'taskid' => $push_notification->model_id

					] );
				}

				if($status == 3)
					$model_instance->assign_to = 0;
			}
			else{
				$model_instance->remark = $remark;
			}
		}

		$message = '';

		switch ($status){
			case 1:
				$message = 'Accepted';
			break;

			case 2:
				$message = 'Postponed';
			break;

			case 3:
				$message = 'Rejected';
		}

		PushNotification::create([
			'message' => $message . ' : '. $push_notification->message,
			'role' => '',
			'user_id' => Auth::id(),
			'sent_to' => $push_notification->user_id,
			'model_type' => $push_notification->model_type,
			'model_id' => $push_notification->model_id,
		]);

		$model_instance->save();

		if($status != 2)
			$push_notification->isread = 1;

		$push_notification->save();
	}
}
