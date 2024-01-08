<?php

namespace App\Models;
use App\User;
use App\Models\OrderPurchaseProductStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPurchaseProductStatusHistory extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'old_value', 'new_value',  'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function newValue()
    {
        return $this->belongsTo(OrderPurchaseProductStatus::class, 'new_value');
    }

    public function oldValue()
    {
        return $this->belongsTo(OrderPurchaseProductStatus::class, 'old_value');
    }
}
