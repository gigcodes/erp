<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class Budget extends Model
{
  /**
     * @var string
     * @SWG\Property(enum={"description", "date", "amount", "type", "budget_category_id", "budget_subcategory_id"})
     */
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
