<?php

namespace App\Models;

use App\User;
use App\Models\WebsiteLogStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebsiteLogStatusHistory extends Model
{
    use HasFactory;

    protected $fillable = ['website_log_id', 'old_value', 'new_value',  'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function newValue()
    {
        return $this->belongsTo(WebsiteLogStatus::class, 'new_value');
    }

    public function oldValue()
    {
        return $this->belongsTo(WebsiteLogStatus::class, 'old_value');
    }
}