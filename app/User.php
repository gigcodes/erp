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
		'name', 'email', 'phone', 'password', 'responsible_user', 'agent_role', 'whatsapp_number'
	];


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
}
