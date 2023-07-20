<?php

namespace App\Models;

use App\Models\ProjectTheme;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThemeStructure extends Model
{
    protected $table = 'theme_structure';

    use HasFactory;

    public $fillable = [
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
    
    public static function tree()
    {
        return static::orderByRaw('-position DESC')->get()->nest();
    
    }

    public static function treeList()
    {
        return static::orderByRaw('-position DESC')
                ->get()
                ->nest()
                ->setIndent('¦–– ')
                ->listsFlattened('name');
        
    }

    public function parent()
    {
        return $this->belongsTo(ThemeStructure::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ThemeStructure::class, 'parent_id')->with('children')->orderBy('position');
    }

    public function files()
    {
        return $this->hasMany(ThemeFile::class);
    }
}
