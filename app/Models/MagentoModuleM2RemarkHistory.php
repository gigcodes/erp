<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;

class MagentoModuleM2RemarkHistory extends Model
{
    use HasFactory;

    public $table = 'magento_modules_m2_remark_histories';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}