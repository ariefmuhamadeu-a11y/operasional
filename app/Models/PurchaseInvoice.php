<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseInvoice extends Model
{
    protected $fillable = [
        'code',
        'supplier_id',
        'date',
        'due_date',
        'subtotal',
        'other_costs',
        'total',
        'paid_total',
        'status',
        'note',
    ];

    protected $casts = [
        'date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'other_costs' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_total' => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function lines()
    {
        return $this->hasMany(PurchaseInvoiceLine::class);
    }
}
