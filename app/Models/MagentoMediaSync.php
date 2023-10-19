<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MagentoMediaSync extends Model
{
    use HasFactory;

    protected $fillable = ['created_by', 'source_store_website_id', 'dest_store_website_id', 'source_server_ip', 'source_server_dir', 'dest_server_ip' ,'dest_server_dir' ,'request_data', 'response_data'];
}
