<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GiftCard extends Model
{
    protected $fillable = ['sender_name','sender_email','receiver_name','receiver_email','gift_card_coupon_code','gift_card_description','gift_card_amount','gift_card_message','expiry_date','store_website_id'];
}
