<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\StoreWebsite;

class StoreWebsiteCsvFile extends Model
{
    use HasFactory;

    protected $table = 'store_website_csv_files';

    protected $fillable = ['filename', 'storewebsite_id','status','action','path','message']; // Add 'filename' to the fillable array

    public function storewebsite()
    {
        return $this->belongsTo(StoreWebsite::class, 'storewebsite_id');
    }

}
