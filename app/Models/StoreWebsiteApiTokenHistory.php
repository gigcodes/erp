<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;


class StoreWebsiteApiTokenHistory extends Model
{
    use HasFactory;

    protected $table = 'store_websites_api_tokens_histories';

    protected $fillable = ['store_websites_id','old_api_token', 'new_api_token',  'updatedBy'];

    public function user()
    {
        return $this->belongsTo(User::class,'updatedBy');
    }
}
