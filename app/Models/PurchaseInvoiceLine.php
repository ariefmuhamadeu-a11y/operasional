<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseInvoiceLine extends Model
{
    protected $fillable = [
        'purchase_invoice_id', 'item_id', 'item_class_id', 'item_name', 'qty', 'unit', 'price', 'total',
    ];

    public function invoice()
    {return $this->belongsTo(PurchaseInvoice::class, 'purchase_invoice_id');}
    public function item()
    {return $this->belongsTo(Item::class);}
    public function itemClass()
    {return $this->belongsTo(ItemClass::class, 'item_class_id');}
}
