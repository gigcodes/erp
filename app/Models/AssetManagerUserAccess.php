<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class AssetManagerUserAccess extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['assets_management_id', 'user_id', 'username', 'password', 'usernamehost'];
}