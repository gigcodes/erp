<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SshLogin extends Model
{
    use HasFactory;

    protected $table = 'ssh_logins';

}
