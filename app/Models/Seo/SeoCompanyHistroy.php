<?php

namespace App\Models\Seo;

use App\EmailAddress;
use App\StoreWebsite;
use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeoCompanyHistroy extends Model
{
    use HasFactory;

    protected $table = "seo_company_histories";
    protected $fillable = [
        'seo_company_id',
        'user_id',
        'company_type_id',
        'website_id',
        'da',
        'pa',
        'ss',
        'email_address_id',
        'live_link',
    ];

    /**
     * Model relationship
     */
    public function companyType()
    {
        return $this->belongsTo(SeoCompanyType::class, 'company_type_id');
    }

    public function website()
    {
        return $this->belongsTo(StoreWebsite::class, 'website_id');
    }

    public function emailAddress()
    {
        return $this->belongsTo(EmailAddress::class, 'email_address_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
