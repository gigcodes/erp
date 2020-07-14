<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ScraperMapping;

class Scraper extends Model
{

    protected $fillable = [
        'supplier_id', 'parent_supplier_id', 'scraper_name', 'scraper_type', 'scraper_total_urls', 'scraper_new_urls', 'scraper_existing_urls', 'scraper_start_time', 'scraper_logic', 'scraper_made_by', 'scraper_priority', 'inventory_lifetime', 'next_step_in_product_flow', 'status', 'last_completed_at'];

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

}
