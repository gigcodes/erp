<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ScraperMapping;

class Scraper extends Model
{

    protected $fillable = [
        'supplier_id', 'parent_supplier_id', 'scraper_name', 'scraper_type', 'scraper_total_urls', 'scraper_new_urls', 'scraper_existing_urls', 'scraper_start_time', 'scraper_logic', 'scraper_made_by', 'scraper_priority', 'inventory_lifetime', 'next_step_in_product_flow', 'status', 'last_completed_at','last_started_at'];

    public function scraperMadeBy()
    {
        return $this->hasOne('App\User', "id", "scraper_made_by");
    }

    public function scraperParent()
    {
        return $this->hasOne('App\Scraper', "supplier_id", "parent_supplier_id");
    }

    public function supplier()
    {
        return $this->hasOne('App\Scraper', "id", "supplier_id");
    }

    public function mainSupplier()
    {
        return $this->hasOne('App\Supplier', "id", "supplier_id");
    }

    public function mapping()
    {
        return $this->hasMany('App\ScraperMapping', "scrapers_id", "id");
    }

    public function parent()
    {
        return $this->hasOne( 'App\Scraper', 'id', 'parent_id' );
    }

    public function getChildrenScraper($name)
    {
        $scraper = $this->where('scraper_name',$name)->first();
        return $parentScraper = $this->where('parent_id',$scraper->id)->get();
    }

     public function getChildrenScraperCount($name)
    {
        $scraper = $this->where('scraper_name',$name)->first();
        return $parentScraper = $this->where('parent_id',$scraper->id)->count();
    }

    public function getScrapHistory()
    {
        return $this->hasMany('App\ScrapRequestHistory', 'scraper_id', 'id')->orderBy('updated_at','desc')->take(20);
    }

    public function scraperRemark()
    {
       return \App\ScrapRemark::where("scraper_name",$this->scraper_name)->latest()->first();
    }

    public function developerTask()
    {
        return \App\DeveloperTask::where("scraper_id",$this->id)->latest()->first();
    }
}
