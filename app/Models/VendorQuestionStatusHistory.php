<?php

namespace App\Models;
use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorQuestionStatusHistory extends Model
{
    use HasFactory;

    protected $fillable = ['vendor_id', 'question_id', 'old_value', 'new_value',  'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function newValue()
    {
        return $this->belongsTo(VendorQuestionStatus::class, 'new_value');
    }

    public function oldValue()
    {
        return $this->belongsTo(VendorQuestionStatus::class, 'old_value');
    }
}
