<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MailinglistTemplate extends Model
{
    protected $fillable = ['name', 'mail_class', 'mail_tpl', 'image_count', 'text_count', 'example_image', 'subject', 'static_template', 'category_id', 'store_website_id'];

    public function file()
    {
        return $this->hasMany(MailingTemplateFile::class, 'mailing_id', 'id');
    }

    public function category()
    {
        return $this->hasOne(MailinglistTemplateCategory::class, 'id', 'category_id');
    }

    public function storeWebsite()
    {
        return $this->hasOne(StoreWebsite::class, 'id', 'store_website_id');
    }

    public static function getIssueCredit($store = null)
    {
        $category = \App\MailinglistTemplateCategory::where('title', 'Issue Credit')->first();

        if ($category) {
            return self::getTemplate($category, $store);

        }

        return false;
    }

    public static function getOrderConfirmationTemplate($store)
    {
        $category = \App\MailinglistTemplateCategory::where('title', 'Order Confirmation')->first();

        if ($category) {
            return self::getTemplate($category, $store);
        }

        return false;
    }

    public function getOrderStatusChangeTemplate($store)
    {
        $category = \App\MailinglistTemplateCategory::where('title', 'Order Status Change')->first();

        if ($category) {
            return self::getTemplate($category, $store);
        }

        return false;
    }

    public function getOrderCancellationTemplate($store)
    {
        $category = \App\MailinglistTemplateCategory::where('title', 'Order Cancellation')->first();

        if ($category) {
            return self::getTemplate($category, $store);
        }

        return false;
    }

    public function getIntializeReturn($store)
    {
        $category = \App\MailinglistTemplateCategory::where('title', 'Initialize Return')->first();

        if ($category) {
            return self::getTemplate($category, $store);
        }

        return false;
    }

    public function getIntializeRefund($store)
    {
        $category = \App\MailinglistTemplateCategory::where('title', 'Initialize Refund')->first();

        if ($category) {
            return self::getTemplate($category, $store);
        }

        return false;
    }

    public function getIntializeExchange($store)
    {
        $category = \App\MailinglistTemplateCategory::where('title', 'Initialize Exchange')->first();

        if ($category) {
            // get the template for that cateogry and store website
            return self::getTemplate($category, $store);
        }

        return false;
    }

    public function getTemplate($cateogry, $store = null)
    {
        if ($store) {
            return self::where('store_website_id', $store)->where('category_id', $category->id)->first();
        } else {
            return self::whereNull('store_website_id')->where('category_id', $category->id)->first();
        }
    }

}
