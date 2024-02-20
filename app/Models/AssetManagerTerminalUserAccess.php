<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AssetManagerTerminalUserAccess extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['assets_management_id', 'created_by', 'username', 'password'];

    public function user()
    {
        return $this->hasOne(\App\User::class, 'id', 'created_by')->select('name', 'id');
    }
}
