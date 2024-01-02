<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ThemeStructureLog extends Model
{
    protected $table = 'theme_structure_logs';

    use HasFactory;

    public $fillable = [
        'theme_id',
        'command',
        'message',
        'status',
    ];

    public function theme()
    {
        return $this->belongsTo(ProjectTheme::class, 'theme_id', 'id');
    }
}
