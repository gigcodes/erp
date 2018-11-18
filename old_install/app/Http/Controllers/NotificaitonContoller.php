<?php

namespace App\Http\Controllers;

use App\Notification;
use Illuminate\Contracts\Auth\Guard;

class NotificaitonContoller extends Controller
{

	public function index(){

//		$roleNames = $auth->user()->getRoleNames();
//		$notifications = Notification::latest()->whereIn('role',$roleNames)->paginate(20);
		$notifications = Notification::getUserNotificationByRolesPaginate();

		return view('notification.index',compact('notifications'))
			->with('i', (request()->input('page', 1) - 1) * 10);

	}

	public static function json(){

		$notifications = Notification::getUserNotificationByRoles();

		return $notifications;
	}

	public static function store($messsage,$forRoles,$product_id){

		$notification = new Notification();

		foreach ($forRoles as $role) {
			$notification->create( [
				'message'    => $messsage,
				'role'       => $role,
				'product_id' => $product_id,
				'user_id' => \Auth::id(),
			] );
		}
	}


	public function markRead(Notification $notificaion){

		$notificaion->isread = 1;
		$notificaion->save();

		return ['msg' => 'success'];
	}


/*	public function getRoleIDs(){

		$roleNames = $this->user->getRoleNames();

		$roleIDs = [];

		foreach ($roleNames as $roleName){

			$role = Role::findByName($roleName);
			$roleIDs = $role->get('id');
		}

		return $roleIDs;
	}*/

}
