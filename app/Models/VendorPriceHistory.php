<?php

namespace App\Models;
use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorPriceHistory extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','vendor_id','price','currency','hisotry'];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
