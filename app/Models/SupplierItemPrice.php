<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierItemPrice extends Model
{
    public $timestamps = false;
    protected $fillable = ['supplier_id', 'item_id', 'last_price', 'last_date'];
}
