<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    public static function boot()
    {
        parent::boot();
        self::creating(function ($email) {
            try{
                if(isset($email->type) && !empty($email->type) && $email->type == 'incoming'){
                    $emailCategoryId = Email::where('from', 'like', '%'.$email->from.'%')
                        ->where('type', 'incoming')
                        ->orderBy('created_at', 'desc')
                        ->pluck('email_category_id')
                        ->first();
    
                    if(strlen($emailCategoryId) > 0){
                        $email->email_category_id = $emailCategoryId;
                    }
                }
            }
            catch(\Exception $e){

            }
        });
    }

    /**
     * @var string
     *
     * @SWG\Property(property="model_id",type="integer")
     * @SWG\Property(property="model_type",type="string")
     * @SWG\Property(property="seen",type="string")
     * @SWG\Property(property="from",type="string")
     * @SWG\Property(property="to",type="string")
     * @SWG\Property(property="subject",type="string")
     * @SWG\Property(property="message",type="string")
     * @SWG\Property(property="template",type="string")
     * @SWG\Property(property="additional_data",type="string")
     * @SWG\Property(property="created_at",type="datetime")
     * @SWG\Property(property="cc",type="string")
     * @SWG\Property(property="bcc",type="string")
     * @SWG\Property(property="status",type="string")
     * @SWG\Property(property="approve_mail",type="string")
     * @SWG\Property(property="origin_id",type="integer")
     * @SWG\Property(property="reference_id",type="integer")
     */
    protected $fillable = [
        'model_id', 'model_type', 'type', 'seen', 'from', 'to', 'subject', 'message', 'template', 'additional_data', 'created_at',
        'cc', 'bcc', 'origin_id', 'reference_id', 'status', 'approve_mail', 'is_draft', 'error_message', 'store_website_id',
        'message_en', 'schedule_at', 'mail_status', 'order_id', 'order_status',
    ];

    protected $casts = [
        'cc' => 'array',
        'bcc' => 'array',
    ];

    public function model()
    {
        return $this->morphTo();
    }

    public function remarks()
    {
        return $this->hasMany(EmailRemark::class);
    }

    public function category()
    {
        return $this->belongsTo(EmailCategory::class, 'email_category_id', 'id');
    }

    public static function emailModelTypeList()
    {
        return [
            '' => '-- Model Type --',
            'App\Affiliates' => 'Affiliates',
            'App\Contact' => 'Contact',
            'App\Coupon' => 'Coupon',
            'App\CouponCodeRules' => 'Coupon Code Rules',
            'App\Customer' => 'Customer',
            'App\CustomerCharity' => 'Customer Charity',
            'App\Email' => 'Email',
            'App\ErpLeads' => 'ErpLeads',
            'App\GiftCard' => 'GiftCard',
            'App\Order' => 'Order',
            'App\ReturnExchange' => 'ReturnExchange',
            'App\ScrapInfluencer' => 'ScrapInfluencer',
            'App\Supplier' => 'Supplier',
            'App\Tickets' => 'Tickets',
            'App\User' => 'User',
            'App\Vendor' => 'Vendor',
        ];
    }
}
