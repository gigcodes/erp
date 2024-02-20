<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MagentoModuleUnitTestRemarkHistory extends Model
{
    use HasFactory;

    public $table = 'magento_modules_unit_test_remark_histories';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
