<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GithubRepository extends Model
{
   protected $fillable = [
       'id',
       'name',
       'html',
       'webhook',
       'created_at',
       'updated_at'
   ];
}
