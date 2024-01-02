<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MagentoModuleReturnTypeErrorHistoryStatus extends Model
{
    use HasFactory;

    protected $table = 'magento_module_return_type_error_status_histories';

    public function newLocation()
    {
        return $this->belongsTo(MagentoModuleReturnTypeErrorStatus::class, 'new_location_id');
    }

    public function oldLocation()
    {
        return $this->belongsTo(MagentoModuleReturnTypeErrorStatus::class, 'old_location_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
