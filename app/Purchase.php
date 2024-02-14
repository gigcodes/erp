<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use App\Events\PurchaseCreated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="communication",type="string")
     * @SWG\Property(property="whatsapp_number",type="string")
     */
    use SoftDeletes;

    protected $communication = '';

    protected $fillable = ['whatsapp_number'];

    public function messages()
    {
        return $this->hasMany(\App\Message::class, 'moduleid')->where('moduletype', 'purchase')->latest()->first();
    }

    public function products()
    {
        return $this->belongsToMany(\App\Product::class, 'purchase_products', 'purchase_id', 'product_id');
    }

    public function orderProducts()
    {
        return $this->belongsToMany(\App\OrderProduct::class, 'purchase_products', 'purchase_id', 'order_product_id');
    }

    public function files()
    {
        return $this->hasMany(\App\File::class, 'model_id')->where('model_type', \App\Purchase::class);
    }

    public function purchase_supplier()
    {
        return $this->belongsTo(\App\Supplier::class, 'supplier_id');
    }

    public function agent()
    {
        return $this->belongsTo(\App\Agent::class, 'agent_id');
    }

    public function emails()
    {
        return $this->hasMany(\App\Email::class, 'model_id')->where('model_type', \App\Purchase::class)->orWhere('model_type', \App\Supplier::class);
    }

    public function status_changes()
    {
        return $this->hasMany(\App\StatusChange::class, 'model_id')->where('model_type', \App\Purchase::class)->latest();
    }

    public function is_sent_in_italy()
    {
        $count = $this->hasMany(\App\CommunicationHistory::class, 'model_id')->where('model_type', \App\Purchase::class)->where('type', 'purchase-in-italy')->count();

        return $count > 0 ? true : false;
    }

    public function is_sent_in_dubai()
    {
        $count = $this->hasMany(\App\CommunicationHistory::class, 'model_id')->where('model_type', \App\Purchase::class)->where('type', 'purchase-in-dubai')->count();

        return $count > 0 ? true : false;
    }

    public function is_sent_dubai_to_india()
    {
        $count = $this->hasMany(\App\CommunicationHistory::class, 'model_id')->where('model_type', \App\Purchase::class)->where('type', 'purchase-dubai-to-india')->count();

        return $count > 0 ? true : false;
    }

    public function is_sent_in_mumbai()
    {
        $count = $this->hasMany(\App\CommunicationHistory::class, 'model_id')->where('model_type', \App\Purchase::class)->where('type', 'purchase-in-mumbai')->count();

        return $count > 0 ? true : false;
    }

    public function is_sent_awb_actions()
    {
        $count = $this->hasMany(\App\CommunicationHistory::class, 'model_id')->where('model_type', \App\Purchase::class)->where('type', 'purchase-awb-generated')->count();

        return $count > 0 ? true : false;
    }

    public function cashFlows()
    {
        return $this->morphMany(CashFlow::class, 'cash_flow_able');
    }

    public function customers()
    {
        return $this->belongsToMany(\App\Customer::class, 'purchase_order_customer', 'purchase_id', 'customer_id');
    }

    public function purchaseProducts()
    {
        return $this->hasMany(\App\PurchaseProduct::class, 'purchase_id', 'id');
    }
}
