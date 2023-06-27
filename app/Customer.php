<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="name",type="string")
     * @SWG\Property(property="phone",type="string")
     * @SWG\Property(property="city",type="string")
     * @SWG\Property(property="whatsapp_number",type="integer")
     * @SWG\Property(property="chat_session_id",type="integer")
     * @SWG\Property(property="in_w_list",type="string")

     * @SWG\Property(property="store_website_id",type="integer")
     * @SWG\Property(property="user_id",type="integer")

     * @SWG\Property(property="reminder_from",type="sting")
     * @SWG\Property(property="reminder_last_reply",type="sting")
     * @SWG\Property(property="wedding_anniversery",type="sting")
     * @SWG\Property(property="dob",type="datetime")
     * @SWG\Property(property="do_not_disturb",type="sting")
     */
    use SoftDeletes;

    // protected $appends = ['communication'];
    protected $fillable = [
        'name',
        'email',
        'phone',
        'city',
        'whatsapp_number',
        'chat_session_id',
        'in_w_list',
        'store_website_id',
        'user_id',
        'reminder_from',
        'reminder_last_reply',
        'wedding_anniversery',
        'dob',
        'do_not_disturb',
        'store_name',
        'language',
        'newsletter',
        'platform_id',
        'priority',
    ];

    protected $casts = [
        'notes' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        self::updating(function ($model) {
            if (! empty(\Auth::id())) {
                $model->updated_by = \Auth::id();
            }
        });
        self::saving(function ($model) {
            if (! empty(\Auth::id())) {
                $model->updated_by = \Auth::id();
            }
        });
        self::creating(function ($model) {
            if (! empty(\Auth::id())) {
                $model->updated_by = \Auth::id();
            }
        });
    }

    public function leads()
    {
        return $this->hasMany(\App\ErpLeads::class)->orderBy('created_at', 'DESC');
    }

    public function customerAddress()
    {
        return $this->hasMany(CustomerAddressData::class, 'customer_id', 'id');
    }

    public function dnd()
    {
        return $this->hasMany(\App\CustomerBulkMessageDND::class, 'customer_id', 'id')->where('filter', app('request')->keyword_filter);
    }

    public function orders()
    {
        return $this->hasMany(\App\Order::class)->orderBy('created_at', 'DESC');
    }

    public function latestOrder()
    {
        return $this->hasMany(\App\Order::class)->orderBy('created_at', 'DESC')->first();
    }

    public function latestRefund()
    {
        return $this->hasMany(\App\ReturnExchange::class)->orderBy('created_at', 'DESC')->first();
    }

    public function refunds()
    {
        return $this->hasMany(\App\ReturnExchange::class)->orderBy('created_at', 'DESC');
    }

    public function suggestion()
    {
        return $this->hasOne(\App\SuggestedProduct::class);
    }

    public function instructions()
    {
        return $this->hasMany(\App\Instruction::class);
    }

    public function private_views()
    {
        return $this->hasMany(\App\PrivateView::class);
    }

    public function latest_order()
    {
        return $this->hasMany(\App\Order::class)->latest()->first();
    }

    public function many_reports()
    {
        return $this->hasMany(\App\OrderReport::class, 'customer_id')->latest();
    }

    public function allMessages()
    {
        return $this->hasMany(ChatMessage::class, 'customer_id', 'id');
    }

    public function messages()
    {
        return $this->hasMany(\App\Message::class, 'customer_id')->latest()->first();
    }

    public function messages_all()
    {
        return $this->hasMany(\App\Message::class, 'customer_id')->latest();
    }

    public function emails()
    {
        return $this->hasMany(\App\Email::class, 'model_id')->where('model_type', \App\Customer::class);
    }

    public function whatsapps()
    {
        return $this->hasMany(\App\ChatMessage::class, 'customer_id')->where('status', '!=', '7')->latest()->first();
    }

    public function call_recordings()
    {
        return $this->hasMany(\App\CallRecording::class, 'customer_id')->latest();
    }

    public function whatsapps_all()
    {
        return $this->hasMany(\App\ChatMessage::class, 'customer_id')->whereNotIn('status', ['7', '8', '9'])->latest();
    }

    public function messageHistory($count = 3)
    {
        return $this->hasMany(ChatMessage::class, 'customer_id')->whereNotIn('status', ['7', '8', '9', '10'])->take($count)->latest();
    }

    public function bulkMessagesKeywords()
    {
        return $this->belongsToMany(BulkCustomerRepliesKeyword::class, 'bulk_customer_replies_keyword_customer', 'customer_id', 'keyword_id');
    }

    public function latestMessage()
    {
        return $this->hasMany(ChatMessage::class, 'customer_id')->whereNotIn('status', ['7', '8', '9'])->latest()->first();
    }

    public function credits_issued()
    {
        return $this->hasMany(\App\CommunicationHistory::class, 'model_id')->where('model_type', \App\Customer::class)->where('type', 'issue-credit')->where('method', 'email');
    }

    public function instagramThread()
    {
        return $this->hasOne(InstagramThread::class);
    }

    public function is_initiated_followup()
    {
        $count = $this->hasMany(\App\CommunicationHistory::class, 'model_id')->where('model_type', \App\Customer::class)->where('type', 'initiate-followup')->where('is_stopped', 0)->count();

        return $count > 0 ? true : false;
    }

    public function whatsappAll($needBroadcast = false)
    {
        if ($needBroadcast) {
            return $this->hasMany(\App\ChatMessage::class, 'customer_id')->where(function ($q) {
                $q->whereIn('status', ['7', '8', '9', '10'])->orWhere('group_id', '>', 0);
            })->latest();
        } else {
            return $this->hasMany(\App\ChatMessage::class, 'customer_id')->whereNotIn('status', ['7', '8', '9', '10'])->latest();
        }
    }

    public function whatsapp_number_change_notified()
    {
        $count = $this->hasMany(\App\CommunicationHistory::class, 'model_id')->where('model_type', \App\Customer::class)->where('type', 'number-change')->count();

        return $count > 0 ? true : false;
    }

    public function getCommunicationAttribute()
    {
        $message = $this->messages();
        $whatsapp = $this->whatsapps();

        if ($message && $whatsapp) {
            if ($message->created_at > $whatsapp->created_at) {
                return $message;
            }

            return $whatsapp;
        }

        if ($message) {
            return $message;
        }

        return $whatsapp;
    }

    public function getLeadAttribute()
    {
        return $this->leads()->latest()->first();
    }

    public function getOrderAttribute()
    {
        return $this->orders()->latest()->first();
    }

    public function facebookMessages()
    {
        return $this->hasMany(FacebookMessages::class);
    }

    public function broadcastLatest()
    {
        return $this->hasOne(\App\ChatMessage::class, 'customer_id', 'id')->where('status', '8')->where('group_id', '>', 0)->latest();
    }

    public function customerMarketingPlatformRemark()
    {
        return $this->hasMany(CustomerMarketingPlatform::class, 'customer_id', 'id')->whereNotNull('remark')->orderBy('created_at', 'desc');
    }

    public function customerMarketingPlatformActive()
    {
        return $this->hasOne(CustomerMarketingPlatform::class, 'customer_id', 'id')->whereNull('remark');
    }

    public function broadcastAll()
    {
        return $this->hasMany(\App\ChatMessage::class, 'customer_id', 'id')->where('status', '8')->where('group_id', '>', 0)->orderby('id', 'desc');
    }

    public function lastBroadcastSend()
    {
        return $this->hasOne(ImQueue::class, 'number_to', 'phone')->whereNotNull('sent_at')->latest();
    }

    public function lastImQueueSend()
    {
        return $this->hasOne(ImQueue::class, 'number_to', 'phone')->orderby('sent_at', 'desc');
    }

    public function notDelieveredImQueueMessage()
    {
        return $this->hasOne(ImQueue::class, 'number_to', 'phone')->where('sent_at', '2002-02-02 02:02:02')->latest();
    }

    public function receivedLastestMessage()
    {
        return $this->hasOne(\App\ChatMessage::class, 'customer_id', 'id')->whereNotNull('number')->latest();
    }

    public function hasDND()
    {
        return ($this->do_not_disturb == 1) ? true : false;
    }

    public function kyc()
    {
        return $this->hasMany(App\CustomerKycDocument::class, 'customer_id', 'id');
    }

    /**
     *  Get information by ids
     *
     *  @param []
     *  @return mixed
     */
    public static function getInfoByIds($ids, $fields = ['*'], $toArray = false)
    {
        $list = self::whereIn('id', $ids)->select($fields)->get();

        if ($toArray) {
            $list = $list->toArray();
        }

        return $list;
    }

    /**
     * Get store website detail
     */
    public function storeWebsite()
    {
        return $this->belongsTo(\App\StoreWebsite::class, 'store_website_id');
    }

    public function return_exchanges()
    {
        return $this->hasMany(ReturnExchange::class, 'customer_id');
    }

    public static function ListSource()
    {
        return [
            'instagram' => 'Instagram',
            'default' => 'Default',
        ];
    }

    public function wishListBasket()
    {
        return $this->hasOne(\App\CustomerBasket::class, 'customer_id', 'id');
    }

    public function maillistCustomerHistory()
    {
        return  $this->hasOne(\App\MaillistCustomerHistory::class, 'customer_id', 'id');
    }

    public function getOrderById($id)
    {
        return  $this->orders()->where('id', $id)->first();
    }

    public function getRefundById($id)
    {
        return  $this->refunds()->where('id', $id)->first();
    }
}
