<?php

namespace App\Models;

#use App\Models\Theme;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function folder()
    {
       return $this->belongsTo(ThemeStructure::class, 'parent_id');
    }
}
