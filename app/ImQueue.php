<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ImQueue extends Model
{
    protected $fillable = ['im_client','number_to','number_from','text','image','priority','send_after','sent_at','marketing_message_type_id'];
     
}
