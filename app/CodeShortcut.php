<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Supplier;
use App\CodeShortCutPlatform;
use App\Models\CodeShortcutFolder;

class CodeShortcut extends Model
{
    public $table = 'code_shortcuts';

    protected $fillable = [
        'user_id',
        'supplier_id',
        'code',
        'description',
        'code_shortcuts_platform_id',
        'title',
        'solution',
    ];

    public function user_detail()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function supplier_detail()
    {
        return $this->hasOne(Supplier::class, 'id', 'supplier_id');
    }

    public function platform()
    {
        return $this->hasOne(CodeShortCutPlatform::class , 'id', 'code_shortcuts_platform_id');
    }

    public function folder()
    {
        return $this->hasOne(CodeShortcutFolder::class , 'id', 'folder_id');
    }

}
