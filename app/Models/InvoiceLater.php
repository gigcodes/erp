<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceLater extends Model
{
    use HasFactory;
    public $table = 'invoices_later';

    public function orders()
    {
        
    }
}
