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
            try {
                if (isset($email->type) && ! empty($email->type) && $email->type == 'incoming') {
                    $emailCategoryId = Email::where('from', 'like', '%' . $email->from . '%')
                        ->where('type', 'incoming')
                        ->orderBy('created_at', 'desc')
                        ->pluck('email_category_id')
                        ->first();

                    if (strlen($emailCategoryId) > 0) {
                        $email->email_category_id = $emailCategoryId;
                    }

                    if (empty($email->module_type)) {
                        $email->is_unknow_module = 1;
                    }
                }

                if (! empty($email->from)) {
                    $explodeArray = explode('@', $email->from);
                    $email->name = $explodeArray[0];
                }
            } catch(\Exception $e) {
            }
        });

        self::created(function ($email) {
            try {
                $is_module_found = 0;
                $customer = Customer::where('email', $email->from)->first();

                if (! empty($customer)) {
                    $is_module_found = 1;
                    $params = [
                        'number' => $customer->phone,
                        'message' => $email->message,
                        'media_url' => null,
                        'approved' => 0,
                        'status' => 0,
                        'contact_id' => null,
                        'erp_user' => null,
                        'supplier_id' => null,
                        'task_id' => null,
                        'dubizzle_id' => null,
                        'vendor_id' => null,
                        'customer_id' => $customer->id,
                        'is_email' => 1,
                        'from_email' => $email->from,
                        'to_email' => $email->to,
                        'email_id' => $email->id,
                    ];

                    $email->is_unknow_module = 0;
                    $email->name = explode('@', $email->from)[0];
                    $email->save();

                    $messageModel = ChatMessage::create($params);
                }

                $supplier = Supplier::where('email', $email->from)->first();
                if ($supplier) {
                    $is_module_found = 1;
                    $params = [
                        'number' => $supplier->phone,
                        'message' => $email->message,
                        'media_url' => null,
                        'approved' => 0,
                        'status' => 0,
                        'contact_id' => null,
                        'erp_user' => null,
                        'supplier_id' => $supplier->id,
                        'task_id' => null,
                        'dubizzle_id' => null,
                        'is_email' => 1,
                        'from_email' => $email->from,
                        'to_email' => $email->to,
                        'email_id' => $email->id,
                    ];

                    $email->is_unknow_module = 0;
                    $email->name = explode('@', $email->from)[0];
                    $email->save();

                    $messageModel = ChatMessage::create($params);
                }

                $vandor = Vendor::where('email', $email->from)->first();
                if ($vandor) {
                    $is_module_found = 1;
                    $params = [
                        'number' => $vandor->phone,
                        'message' => $email->message,
                        'media_url' => null,
                        'approved' => 0,
                        'status' => 0,
                        'contact_id' => null,
                        'erp_user' => null,
                        'supplier_id' => null,
                        'task_id' => null,
                        'dubizzle_id' => null,
                        'vendor_id' => $vandor->id,
                        'is_email' => 1,
                        'from_email' => $email->from,
                        'to_email' => $email->to,
                        'email_id' => $email->id,
                    ];

                    $email->is_unknow_module = 0;
                    $email->name = explode('@', $email->from)[0];
                    $email->save();

                    $messageModel = ChatMessage::create($params);
                }

                if ($is_module_found == 0) {
                    $email->is_unknow_module = 1;
                    $email->name = explode('@', $email->from)[0];
                    $email->save();

                    $params = [
                        'number' => null,
                        'message' => $email->message,
                        'media_url' => null,
                        'approved' => 0,
                        'status' => 0,
                        'contact_id' => null,
                        'erp_user' => null,
                        'supplier_id' => null,
                        'task_id' => null,
                        'dubizzle_id' => null,
                        'is_email' => 1,
                        'from_email' => $email->from,
                        'to_email' => $email->to,
                        'email_id' => $email->id,
                        'message_type' => 'email',
                    ];

                    $messageModel = ChatMessage::create($params);
                    $mailFound = true;
                }
            } catch(\Exception $e) {
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
        'message_en', 'schedule_at', 'mail_status', 'order_id', 'order_status', 'is_unknow_module',
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

    public function whatsappAll($needBroadcast = false)
    {
        if ($needBroadcast) {
            return $this->hasMany(\App\ChatMessage::class, 'email_id')->where(function ($q) {
                $q->whereIn('status', ['7', '8', '9', '10'])->orWhere('group_id', '>', 0);
            })->latest();
        } else {
            return $this->hasMany(\App\ChatMessage::class, 'email_id')->whereNotIn('status', ['7', '8', '9', '10'])->latest();
        }
    }

    /**
     * Get the user's first name.
     *
     * @param  string  $value
     * @return string
     */
    public function getMessageAttribute($value)
    {
        // new lines removed.
        $properText = str_replace(["\n", "\r"], '', $value);
        if(\App\Helpers::isBase64Encoded($properText)) {
            return base64_decode($properText, true);
        }
        // If not a base64encoded string then pass value as usual. 
        return $value;
    }
}
