<?php

namespace App\Models\Seo;

use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeoHistory extends Model
{
    use HasFactory;

    protected $table = 'seo_histories';
    protected $fillable = [
        'user_id',
        'type',
        'seo_process_id',
        'message'
    ];

    /** 
     * Model relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function msgUser()
    {
        return $this->belongsTo(User::class, 'message');
    }
}
