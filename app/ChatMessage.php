<?php

namespace App;

use Plank\Mediable\Mediable;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use Mediable;

    protected $fillable = ['lead_id', 'order_id', 'customer_id', 'supplier_id', 'vendor_id', 'user_id', 'task_id', 'erp_user', 'contact_id', 'dubbizle_id', 'assigned_to', 'purchase_id', 'message', 'media_url', 'number', 'approved', 'status', 'error_status', 'resent', 'is_reminder', 'created_at', 'issue_id', 'developer_task_id', 'lawyer_id', 'case_id', 'blogger_id', 'voucher_id', 'document_id'];
    protected $table = "chat_messages";
    protected $dates = ['created_at', 'updated_at'];
    protected $casts = array(
        "approved" => "boolean"
    );

    public static function markMessageDelivered() {
        //
    }

    public function customer()
    {
        return $this->belongsTo('App\Customer');
    }

    public function lawyer()
    {
        return $this->belongsTo('App\Lawyer');
    }

    /**
     * Check if the message has received a broadcast price reply
     * @return bool
     */
    public function isSentBroadcastPrice()
    {
        // Get count
        $count = $this->hasMany('App\CommunicationHistory', 'model_id')->where('model_type', 'App\ChatMessage')->where('type', 'broadcast-prices')->count();

        // Return true or false
        return $count > 0 ? true : false;
    }
}
