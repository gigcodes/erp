<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wetransfer extends Model
{
    public $fillable = [ 'type','url', 'supplier','is_processed', 'files_list', 'files_count'];
}
