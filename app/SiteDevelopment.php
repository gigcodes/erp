<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Plank\Mediable\Mediable;
use Illuminate\Database\Eloquent\Model;

class SiteDevelopment extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="site_development_category_id",type="integer")
     * @SWG\Property(property="status",type="string")

     * @SWG\Property(property="title",type="string")
     * @SWG\Property(property="brand_id",type="interger")
     * @SWG\Property(property="description",type="string")
     * @SWG\Property(property="developer_id",type="integer")
     * @SWG\Property(property="designer_id",type="integer")
     * @SWG\Property(property="website_id",type="integer")

     * @SWG\Property(property="html_designer",type="string")
     * @SWG\Property(property="artwork_status",type="string")
     * @SWG\Property(property="tester_id",type="integer")
     */
    use Mediable;

    protected $fillable = ['site_development_category_id', 'site_development_master_category_id', 'status', 'title', 'description', 'developer_id', 'designer_id', 'website_id', 'html_designer', 'artwork_status', 'tester_id', 'is_site_list', 'bug_id'];

    public function category()
    {
        return $this->belongsTo(SiteDevelopmentCategory::class, 'site_development_category_id');
    }

    public function store_website()
    {
        return $this->belongsTo(StoreWebsite::class, 'website_id');
    }

    public function lastChat()
    {
        return $this->hasOne(ChatMessage::class, 'site_development_id', 'id')->orderBy('created_at', 'desc')->latest();
    }

    //START - Purpose : Get Last Remarks - #DEVTASK-19918
    public function lastRemark()
    {
        return $this->hasOne(StoreDevelopmentRemark::class, 'store_development_id', 'id')->orderBy('created_at', 'desc')->latest();
    }
    //END - #DEVTASK-19918

    public function whatsappAll($needBroadcast = false)
    {
        if ($needBroadcast) {
            return $this->hasMany(\App\ChatMessage::class, 'site_development_id')->where(function ($q) {
                $q->whereIn('status', ['7', '8', '9', '10'])->orWhere('group_id', '>', 0);
            })->latest();
        } else {
            return $this->hasMany(\App\ChatMessage::class, 'site_development_id')->whereNotIn('status', ['7', '8', '9', '10'])->latest();
        }
    }

    public function developer()
    {
        return $this->hasOne(\App\User::class, 'id', 'developer_id');
    }

    public function designer()
    {
        return $this->hasOne(\App\User::class, 'id', 'designer_id');
    }

    public function statusHistories()
    {
        return $this->hasMany(\App\SiteDevelopmentStatusHistory::class, 'site_development_id', 'id');
    }

    public function site_development_status()
    {
        return $this->belongsTo(SiteDevelopmentStatus::class, 'status', 'id');
    }

    public static function getLastRemark($scci, $web_id)
    {
        $site_devs = self::where('site_development_category_id', $scci)->where('website_id', $web_id)->get()->pluck('id')->toArray();
        $remark = StoreDevelopmentRemark::whereIn('store_development_id', $site_devs)->latest()->first();

        return $remark->remarks ?? '';
    }
}
