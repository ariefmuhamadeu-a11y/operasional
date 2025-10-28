<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseInvoice extends Model
{
    protected $fillable = [
        'code', 'supplier_id', 'date', 'due_date', 'subtotal', 'other_costs', 'total', 'paid_total', 'status', 'note',
    ];
    protected $casts = ['date' => 'date', 'due_date' => 'date'];

    public function supplier()
    {return $this->belongsTo(Supplier::class);}
    public function lines()
    {return $this->hasMany(PurchaseInvoiceLine::class);}
    public function getOutstandingAttribute()
    {return max(0, (float) $this->total - (float) $this->paid_total);}
}
