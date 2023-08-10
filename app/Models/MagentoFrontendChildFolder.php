<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MagentoFrontendChildFolder extends Model
{
    use HasFactory;

    public $table = 'magento_frontend_child_folders';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
