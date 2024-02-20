<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AssetManagerUserAccess extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['assets_management_id', 'user_id', 'created_by', 'username', 'password', 'usernamehost', 'login_type', 'key_type', 'user_role', 'request_data', 'response_data'];

    public function user()
    {
        return $this->hasOne(\App\User::class, 'id', 'created_by')->select('name', 'id');
    }
}
