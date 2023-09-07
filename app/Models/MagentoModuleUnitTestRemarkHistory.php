<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;

class MagentoModuleUnitTestRemarkHistory extends Model
{
    use HasFactory;

    public $table = 'magento_modules_unit_test_remark_histories';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
