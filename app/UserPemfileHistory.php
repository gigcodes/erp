<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserPemfileHistory extends Model
{
    use SoftDeletes;

    protected $table = 'user_pemfile_history';

    protected $fillable = [
        'user_id',
        'server_id',
        'server_name',
        'username',
        'public_key',
        'access_type',
        'action',
        'created_by',
        'extra',
    ];

    public function user()
    {
        return $this->belongsTo(\App\User::class, 'user_id', 'id');
    }

    public function createby()
    {
        return $this->belongsTo(\App\User::class, 'created_by', 'id');
    }
}
