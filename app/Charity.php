<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class Charity extends Model
{
    //
    /**
     * @var string
     * @SWG\Property(enum={"name", "contact_no", "email", "whatsapp_number", "assign_to"})
     */
	protected $fillable = ['name', 'contact_no', 'email', 'whatsapp_number', 'assign_to'];
}
