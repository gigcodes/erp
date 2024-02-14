<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PostmanStatusHistory extends Model
{
    use HasFactory;

    protected $table = 'postman_status_histories';

    protected $fillable = ['postman_create_id', 'old_value', 'new_value', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function newValue()
    {
        return $this->belongsTo(PostmanStatus::class, 'new_value');
    }

    public function oldValue()
    {
        return $this->belongsTo(PostmanStatus::class, 'old_value');
    }
}
