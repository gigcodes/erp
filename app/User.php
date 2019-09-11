<?php

namespace App;

use Modules\BookStack\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Auth\Authenticatable as iluAuthenticatable;
use Spatie\Permission\Traits\HasRoles;
use Cache;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Auth\Passwords\CanResetPassword;

class User extends Authenticatable implements AuthenticatableContract, CanResetPasswordContract
{
	use HasApiTokens, Notifiable;
	use HasRoles;
	use SoftDeletes;
	use CanResetPassword;
	use iluAuthenticatable;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name', 'email', 'phone', 'password', 'responsible_user', 'agent_role', 'whatsapp_number', 'amount_assigned', 'auth_token_hubstaff'
	];

	protected $kpermission;

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

	public function manualCropProducts() {
        return $this->belongsToMany(Product::class, 'user_manual_crop', 'user_id', 'product_id');

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

    

    /**
     * Check if the user has a particular permission.
     * @param $permissionName
     * @return bool
     */
    public function can($permissionName ,$arguements = [])
    {
    	if ($this->email === 'guest') {
            return false;
        }

        // this is for testing purpose for now
        return true;

        return $this->permissions()->pluck('name')->contains($permissionName);
    }

    /**
     * Check if the user is the default public user.
     * @return bool
     */
    public function isDefault()
    {
        return $this->system_name === 'public';
    }

    /**
     * Returns the user's avatar,
     * @param int $size
     * @return string
     */
    public function getAvatar($size = 50)
    {
        $default = url('/user_avatar.png');
        $imageId = $this->image_id;
        if ($imageId === 0 || $imageId === '0' || $imageId === null) {
            return $default;
        }

        try {
            $avatar = $this->avatar ? url($this->avatar->getThumb($size, $size, false)) : $default;
        } catch (\Exception $err) {
            $avatar = $default;
        }
        return $avatar;
    }

    /**
     * Get a shortened version of the user's name.
     * @param int $chars
     * @return string
     */
    public function getShortName($chars = 8)
    {
        if (mb_strlen($this->name) <= $chars) {
            return $this->name;
        }

        $splitName = explode(' ', $this->name);
        if (mb_strlen($splitName[0]) <= $chars) {
            return $splitName[0];
        }

        return '';
    }

}
