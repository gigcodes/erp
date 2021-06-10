<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


/**
 */
class CustomerBulkMessageDND extends Model
{
  
public $table = 'customer_bulk_messages_dnd';

  protected $fillable = [
    'customer_id', 'filter'
  ];

  
}
