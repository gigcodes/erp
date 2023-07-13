<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;

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
