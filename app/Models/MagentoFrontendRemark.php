<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MagentoFrontendRemark extends Model
{
    use HasFactory;

    public $table = 'magento_frontend_remarks';

    protected $fillable = ['magento_frontend_docs_id', 'user_id',  'remark'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
