<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Notification extends Model
{
	protected $fillable = [ "message","role","product_id","user_id" ];

	public static function getUserNotificationByRoles($limit = 10){

		//		$notifications = self::all()->whereIn('role',$roles);
		$notifications = DB::table('notifications as n')
		                    ->select('n.message','n.isread','n.id','p.sku','p.name as pname','u.name as uname')
							->whereIn('role',\Auth::user()->getRoleNames())
							->latest('n.created_at')
							->limit($limit)
							->leftJoin('products as p','n.product_id','=','p.id')
							->leftJoin('users as u','n.user_id','=','u.id')
							->get();
		return $notifications;
	}

	public static function getUserNotificationByRolesPaginate(){

		$notifications = DB::table('notifications as n')
		                    ->select('n.message','n.isread','n.id','p.sku','p.name as pname','u.name as uname')
		                    ->whereIn('role',\Auth::user()->getRoleNames())
		                    ->latest('n.created_at')
		                    ->leftJoin('products as p','n.product_id','=','p.id')
		                    ->leftJoin('users as u','n.user_id','=','u.id')
							->paginate(20);

		return $notifications;
	}

	public function product(){

		return $this->belongsTo('App\Product','product_id','id');
	}

	public function role(){

		return $this->belongsTo('Spatie\Permission\Models\Role','role','name');
	}

	public function user(){

		return $this->belongsTo('App\User','user_id','id');
	}

	public function getAll(){
		return self::all();
	}

}
