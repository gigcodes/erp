<?php

namespace App\Models;
use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorRemarksHistory extends Model
{
    use HasFactory;

    protected $fillable = ['vendors_id', 'pre_name',
        'vendor_id',
        'user_id',
        'remarks'];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
