<?php

namespace App\Models;

use App\Models\ProjectTheme;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
