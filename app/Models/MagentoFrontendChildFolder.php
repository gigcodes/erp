<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;

class MagentoFrontendChildFolder extends Model
{
    use HasFactory;
    
    public $table = 'magento_frontend_child_folders';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
