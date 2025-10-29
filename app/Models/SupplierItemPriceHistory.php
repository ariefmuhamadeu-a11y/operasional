<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierItemPriceHistory extends Model
{
    protected $fillable = ['supplier_id', 'item_id', 'price', 'date', 'purchase_invoice_id'];
    protected $casts = ['date' => 'date'];
}
