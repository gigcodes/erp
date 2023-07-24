<?php

namespace App\Models;

use App\MagentoCssVariableJobLog;
use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MagentoCssVariable extends Model
{
    use HasFactory;

    public $fillable = [
        'project_id',
        'filename',
        'file_path',
        'variable',
        'value',
        'create_by',
        'is_verified'
    ];

    const VERIFIED = 1;
    const NOTVERIFIED = 0;

    public static $verifiedOptions = [
        self::VERIFIED => 'Yes',
        self::NOTVERIFIED  => 'No',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'create_by');
    }

    public function lastLog()
    {
        return $this->hasOne(MagentoCssVariableJobLog::class)
            ->orderByDesc('created_at')
            ->latest();
    }

}
