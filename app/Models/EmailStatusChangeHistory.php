<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailStatusChangeHistory extends Model
{
    use HasFactory;

    protected $table = "email_status_update_history";
}
