<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;

class Old extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="old",type="string")
     * @SWG\Property(property="serial_no",type="integer")
     * @SWG\Property(property="name",type="string")
     * @SWG\Property(property="description",type="string")
     * @SWG\Property(property="amount",type="integer")
     * @SWG\Property(property="commitment",type="string")
     * @SWG\Property(property="communication",type="string")
     * @SWG\Property(property="status",type="string")
     * @SWG\Property(property="is_blocked",type="boolean")
     * @SWG\Property(property="phone",type="string")
     * @SWG\Property(property="gst",type="float")
     * @SWG\Property(property="account_number",type="integer")
     * @SWG\Property(property="account_iban",type="string")
     * @SWG\Property(property="account_swift",type="string")
     * @SWG\Property(property="catgory_id",type="integer")
     * @SWG\Property(property="pending_payment",type="string")
     * @SWG\Property(property="currency",type="string")
     * @SWG\Property(property="account_name",type="string")
     * @SWG\Property(property="is_payable",type="boolean")
     */
    protected $table = 'old';

    protected $primaryKey = 'serial_no';

    /**
     * Fillables for the database
     *
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'amount', 'commitment', 'communication', 'status', 'is_blocked', 'phone', 'gst', 'account_number', 'account_iban', 'account_swift', 'catgory_id', 'pending_payment', 'currency', 'account_name', 'is_payable',
    ];

    /**
     * Protected Date
     *
     * @var array
     */
    /**
     * Get Status
     *
     * @return \Illuminate\Http\Response
     */
    public static function getStatus()
    {
        return OldStatus::all()->pluck('status');
    }

    public function emails()
    {
        return $this->hasMany(Email::class, 'model_id', 'serial_no');
    }

    public function category()
    {
        return $this->hasOne(OldCategory::class, 'id', 'category_id');
    }

    public function payments()
    {
        return $this->hasMany(OldPayment::class, 'old_id', 'serial_no');
    }

    public function whatsappAll($needBroadCast = false)
    {
        if ($needBroadCast) {
            return $this->hasMany(\App\ChatMessage::class, 'old_id')->whereIn('status', ['7', '8', '9', '10'])->latest();
        }

        return $this->hasMany(\App\ChatMessage::class, 'old_id')->whereNotIn('status', ['7', '8', '9', '10'])->latest();
    }

    public function agents()
    {
        return $this->hasMany(\App\Agent::class, 'model_id')->where('model_type', \App\Old::class);
    }
}
