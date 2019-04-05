<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
  protected $fillable = [
    'description', 'date', 'amount', 'type', 'budget_category_id', 'budget_subcategory_id'
  ];

  public function category()
  {
    return $this->belongsTo('App\BudgetCategory', 'budget_category_id');
  }

  public function subcategory()
  {
    return $this->belongsTo('App\BudgetCategory', 'budget_subcategory_id');
  }
}
