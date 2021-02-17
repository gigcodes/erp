<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class CharityOrderHistory extends Model
{
    //
    /**
     * @var string
     * @SWG\Property(enum={"charity_order_history"})
     */
	protected $table="charity_order_history";

}
