<?php

namespace App;

use App\StoreWebsite;
use Illuminate\Database\Eloquent\Model;

class Mailinglist extends Model
{
    protected $fillable = ['id', 'name', 'remote_id', 'service_id','website_id','email'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function service()
    {
       return $this->belongsTo(Service::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function website()
    {
       return $this->hasOne(StoreWebsite::class,'id','website_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function listCustomers()
    {
        return $this->belongsToMany(Customer::class, 'list_contacts', 'list_id', 'customer_id')->withTimestamps();
    }

}
