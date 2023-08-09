<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmailStatusChangeHistory extends Model
{
    use HasFactory;

    protected $table = 'email_status_update_history';

    protected $fillable = [
        'user_id',
        'status_id',
        'old_user_id',
        'old_status_id',
        'email_id',
    ];

    public function status()
    {
        return $this->belongsTo(EmailStatus::class, 'status_id', 'id');
    }

    public function oldStatus()
    {
        return $this->belongsTo(EmailStatus::class, 'old_status_id', 'id');
    }

    public function updatedByUser()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'old_user_id', 'id');
    }
}
