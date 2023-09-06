<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Models\MagentoModuleUnitTestStatus;

class MagentoModuleUnitTestStatusHistory extends Model
{
    use HasFactory;

    public $table = 'magento_modules_unit_test_statuses_histories';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function newTestStatus()
    {
        return $this->belongsTo(MagentoModuleUnitTestStatus::class, 'new_unit_test_status_id');
    }

    public function oldTestStatus()
    {
        return $this->belongsTo(MagentoModuleUnitTestStatus::class, 'old_unit_test_status_id');
    }
}
