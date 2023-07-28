<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;

class MagentoFrontendParentFolder extends Model
{
    use HasFactory;

    public $table = 'magento_frontend_parent_folders';

    protected $fillable = ['magento_frontend_docs_id', 'user_id',  'parent_folder_name'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
