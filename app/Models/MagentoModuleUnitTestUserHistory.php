<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;

class MagentoModuleUnitTestUserHistory extends Model
{
    use HasFactory;

    use HasFactory;

    public $table = 'magento_modules_unit_test_user_histories';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function newTestUser()
    {
        return $this->belongsTo(User::class, 'new_unit_test_user_id');
    }

    public function oldTestUser()
    {
        return $this->belongsTo(User::class, 'old_unit_test_user_id');
    }

}
