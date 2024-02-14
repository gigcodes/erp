<?php

namespace App\Models;

use App\User;
use App\StoreWebsite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StoreWebsiteCsvFile extends Model
{
    use HasFactory;

    protected $table = 'store_website_csv_files';

    protected $fillable = ['filename', 'storewebsite_id', 'status', 'action', 'path', 'message', 'user_id', 'command', 'csv_file_id'];

    public function storewebsite()
    {
        return $this->belongsTo(StoreWebsite::class, 'storewebsite_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
