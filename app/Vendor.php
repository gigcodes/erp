<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Vendor extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'address',
        'phone',
        'default_phone',
        'whatsapp_number',
        'email',
        'social_handle',
        'website',
        'login',
        'password',
        'gst',
        'account_name',
        'account_swift',
        'account_iban',
        'is_blocked',
        'frequency',
        'reminder_message',
        'reminder_last_reply',
        'reminder_from',
        'updated_by',
        'status',
        'frequency_of_payment', 'bank_name', 'bank_address', 'city', 'country', 'ifsc_code', 'remark','chat_session_id'
    ];

    protected static function boot()
    {
        parent::boot();
        self::updating(function ($model) {
            if(!empty(\Auth::id())) {
               $model->updated_by = \Auth::id();
            }
        });
        self::saving(function ($model) {
            if(!empty(\Auth::id())) {
               $model->updated_by = \Auth::id();
            }
        });
        self::creating(function ($model) {
            if(!empty(\Auth::id())) {
               $model->updated_by = \Auth::id();
            }
        });
    }

    public function products()
    {
        return $this->hasMany('App\VendorProduct');
    }

    public function agents()
    {
        return $this->hasMany('App\Agent', 'model_id')->where('model_type', 'App\Vendor');
    }

    public function chat_messages()
    {
        return $this->hasMany('App\ChatMessage')->orderBy('id','desc');
    }

    public function category()
    {
        return $this->belongsTo('App\VendorCategory');
    }

    public function payments()
    {
        return $this->hasMany(VendorPayment::class);
    }

    public function cashFlows()
    {
        return $this->morphMany(CashFlow::class, 'cash_flow_able');
    }

    public function whatsappAll($needBroadCast = false)
    {
        if($needBroadCast) {
            return $this->hasMany('App\ChatMessage', 'vendor_id')->whereIn('status', ['7', '8', '9', '10'])->latest();    
        }

        return $this->hasMany('App\ChatMessage', 'vendor_id')->whereNotIn('status', ['7', '8', '9', '10'])->latest();
    }

    public function emails()
    {
        return $this->hasMany('App\Email', 'model_id', 'id');
    }

    public function whatsappLastTwentyFourHours()
    {
        return $this->hasMany('App\ChatMessage')->where('created_at','>=', Carbon::now()->subDay()->toDateTimeString())->orderBy('id','desc');
    }

    /**
     *  Get information by ids
     *  @param []
     *  @return Mixed
     */

    public static function getInfoByIds($ids, $fields = ["*"], $toArray = false)
    {
        $list = self::whereIn("id",$ids)->select($fields)->get();

        if($toArray) {
            $list = $list->toArray();
        }

        return $list;
    }
}
