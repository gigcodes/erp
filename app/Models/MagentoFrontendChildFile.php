<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MagentoFrontendChildFile extends Model
{
    use HasFactory;

    public $table = 'magento_frontend_child_file';

    protected $fillable = ['magento_frontend_docs_id', 'user_id',  'file_name'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
