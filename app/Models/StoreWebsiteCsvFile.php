<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\StoreWebsite;
use App\User;

class StoreWebsiteCsvFile extends Model
{
    use HasFactory;

    protected $table = 'store_website_csv_files';

    protected $fillable = ['filename', 'storewebsite_id','status','action','path','message','user_id','command'];

    public function storewebsite()
    {
        return $this->belongsTo(StoreWebsite::class, 'storewebsite_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}