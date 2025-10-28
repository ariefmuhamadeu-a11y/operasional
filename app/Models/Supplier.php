<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = ['code', 'store_name', 'item_class_id', 'type', 'phone', 'address'];
    public function itemClass()
    {return $this->belongsTo(\App\Models\ItemClass::class);}
    public function prices()
    {return $this->hasMany(\App\Models\SupplierPrice::class);}

    // ➕ Alias: $supplier->name -> store_name
    protected $appends = ['name'];
    public function getNameAttribute()
    {return $this->store_name;}
}
