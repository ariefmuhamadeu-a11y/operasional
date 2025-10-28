<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierPrice extends Model
{
    protected $fillable = ['supplier_id', 'item_id', 'price', 'effective_date', 'notes'];
    public function supplier()
    {return $this->belongsTo(\App\Models\Supplier::class);}
    public function item()
    {return $this->belongsTo(\App\Models\Item::class);}
}
