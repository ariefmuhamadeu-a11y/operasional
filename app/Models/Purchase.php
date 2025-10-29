<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = [
        'code', 'supplier_id', 'date', 'due_date', 'note', 'other_costs',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function lines()
    {
        return $this->hasMany(PurchaseLine::class);
    }

    public function payments()
    {
        return $this->hasMany(PurchasePayment::class);
    }
}
