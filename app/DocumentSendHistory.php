<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentSendHistory extends Model
{
    protected $fillable = ['send_by','send_to','remarks','type','via','document_id'];
}
