<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MagentoModuleM2ErrorStatusHistory extends Model
{
    use HasFactory;

    protected $table = 'magento_module_m2_error_status_histories';

    public function newM2ErrorStatus()
    {
        return $this->belongsTo(MagentoModuleM2ErrorStatus::class, 'new_m2_error_status_id');
    }

    public function oldM2ErrorStatus()
    {
        return $this->belongsTo(MagentoModuleM2ErrorStatus::class, 'old_m2_error_status_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
