<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MagentoMulitipleCommand extends Model
{
    protected $table = 'magento_multiple_commands';

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
