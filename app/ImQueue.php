<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ChatMessage;

class ImQueue extends Model
{
    protected $fillable = ['id', 'im_client','number_to','number_from','text','image','priority','send_after','sent_at','marketing_message_type_id','broadcast_id'];

    public function marketingMessageTypes()
    {
        return $this->hasOne(MarketingMessageType::class,'id','marketing_message_type_id');
    }

}
