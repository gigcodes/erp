<?php

namespace App\Models;

#use App\Models\Theme;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThemeStructure extends Model
{
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
        return $this->belongsTo(Theme::class, 'theme_id', 'id');
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
}
