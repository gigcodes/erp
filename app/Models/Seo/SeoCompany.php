<?php

namespace App\Models\Seo;

use App\User;
use App\EmailAddress;
use App\StoreWebsite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SeoCompany extends Model
{
    use HasFactory;

    protected $table = 'seo_companies';

    protected $fillable = [
        'user_id',
        'company_type_id',
        'website_id',
        'da',
        'pa',
        'ss',
        'email_address_id',
        'live_link',
        'status',
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
