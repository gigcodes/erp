<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Waybillinvoice extends Model
{
    protected $table='waybill_invoices';
    protected $fillable=['line_type','billing_source','original_invoice_number','invoice_number','invoice_identifier','invoice_type','invoice_date','payment_terms','due_date','billing_account','billing_account_name','billing_account_name_additional','billing_address_1','billing_postcode','billing_city','billing_state_province','billing_country_code','billing_contact','shipment_number','shipment_date','product','product_name','pieces','origin','orig_name','orig_country_code','orig_country_name','senders_name','senders_city','invoice_amount','invoice_currency'];
}
