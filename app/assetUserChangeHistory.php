<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class assetUserChangeHistory extends Model
{
    //use SoftDeletes;
    protected $table = 'asset_user_change_histories';

    protected $fillable = [
        'asset_id', 'user_id', 'new_user_id', 'old_user_id', 'created_at', ];
}
