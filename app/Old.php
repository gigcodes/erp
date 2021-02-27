<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\OldCategory;
use App\OldPayment;
use App\Email;

class Old extends Model
{

    protected $table = 'old';
    protected $primaryKey = 'serial_no';
   /**
     * Fillables for the database
     *
     * @access protected
     *
     * @var array $fillable
     */
    protected $fillable = array(
        'name', 'description', 'amount','commitment', 'communication','status','is_blocked','phone','gst','account_number','account_iban','account_swift','catgory_id','pending_payment','currency','account_name','is_payable'
    );

    /**
     * Protected Date
     *
     * @access protected
     * @var    array $dates
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

   /**
     * Get Status
     *
     * @return \Illuminate\Http\Response
     */
    public static function getStatus()
    {
        $types = array(
            'pending'  => 'pending',
            'disputed' => 'disputed',
            'settled'  => 'settled',
            'paid'     => 'paid',
            'closed'  => 'closed',
        );
        return $types;
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
        return $this->hasMany(OldPayment::class,'old_id','serial_no');
    }

    public function whatsappAll($needBroadCast = false)
    {
        if($needBroadCast) {
            return $this->hasMany('App\ChatMessage', 'old_id')->whereIn('status', ['7', '8', '9', '10'])->latest();    
        }

        return $this->hasMany('App\ChatMessage', 'old_id')->whereNotIn('status', ['7', '8', '9', '10'])->latest();
    }

    public function agents()
    {
        return $this->hasMany('App\Agent', 'model_id')->where('model_type', 'App\Old');
    }

    


}
