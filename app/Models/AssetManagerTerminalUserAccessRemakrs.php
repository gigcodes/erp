<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AssetManagerTerminalUserAccessRemakrs extends Model
{
    use HasFactory;

    protected $fillable = [
        'amtua_id', 'remarks', 'added_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}
