<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ResourceRemarksHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'resource_images_id', 'remarks', 'added_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}
