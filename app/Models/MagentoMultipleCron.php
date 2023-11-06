<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MagentoMultipleCron extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'command_id',
        'user_id',
        'website_ids',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function website()
    {
        return $this->belongsTo(\App\StoreWebsite::class, 'website_ids');
    }

    public function command()
    {
        return $this->belongsTo(\App\MagentoCommand::class, 'command_id');
    }
}
