<?php

namespace App;


use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Cache;


class User extends Authenticatable
{
	use HasApiTokens, Notifiable;
	use HasRoles;
	use SoftDeletes;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name', 'email', 'phone', 'password', 'responsible_user', 'agent_role', 'whatsapp_number', 'amount_assigned'
	];

    public function getIsAdminAttribute()
    {
        return true;
    }


	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password', 'remember_token',
	];

	public function messages()
	{
	    return $this->hasMany(Message::class);
	}

    public function actions()
    {
        return $this->hasMany(UserActions::class);
    }

	public function isOnline()
	{
	    return Cache::has('user-is-online-' . $this->id);
	}

	public function contacts()
	{
		return $this->hasMany('App\Contact');
	}

	public function products()
	{
		return $this->belongsToMany('App\Product', 'user_products', 'user_id', 'product_id');
	}

	public function approved_products()
	{
		return $this->belongsToMany('App\Product', 'user_products', 'user_id', 'product_id')->where('is_approved', 1);
	}

	public function customers()
	{
		return $this->belongsToMany('App\Customer', 'user_customers', 'user_id', 'customer_id');
	}

	public function cropApproval() {
	    return $this->hasMany(User::class)->where('action', 'CROP_APPROVAL');
    }

    public function cropRejection() {
        return $this->hasMany(ListingHistory::class)->where('action', 'CROP_REJECTED');
    }

    public function attributeApproval() {
        return $this->hasMany(ListingHistory::class)->where('action', 'LISTING_APPROVAL');
    }

    public function attributeRejected() {
        return $this->hasMany(ListingHistory::class)->where('action', 'LISTING_REJECTED');

    }
    public function cropSequenced() {
        return $this->hasMany(ListingHistory::class)->where('action', 'CROP_SEQUENCED');

    }


    public function instagramAutoComments() {
	    return $this->hasManyThrough(AutoCommentHistory::class, 'users_auto_comment_histories', 'user_id', 'auto_comment_history_id', 'id');
    }

}
