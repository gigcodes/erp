<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ThemeFile extends Model
{
    protected $table = 'theme_structure';

    use HasFactory;

    protected $fillable = [
        'theme_id',
        'name',
        'is_file',
        'parent_id',
        'position',
    ];

    public function theme()
    {
        return $this->belongsTo(ProjectTheme::class, 'theme_id', 'id');
    }

    public function folder()
    {
        return $this->belongsTo(ThemeStructure::class, 'parent_id');
    }
}
