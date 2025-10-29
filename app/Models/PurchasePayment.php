<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchasePayment extends Model
{
    protected $fillable = [
        'purchase_invoice_id', 'date', 'account', 'amount', 'note',
    ];

    public function invoice()
    {
        return $this->belongsTo(\App\Models\PurchaseInvoice::class, 'purchase_invoice_id');
    }

    public function payments()
    {
        return $this->hasMany(\App\Models\PurchasePayment::class, 'purchase_invoice_id');
    }

/** Recalculate paid_total & status (TERBIT/SEBAGIAN/LUNAS) */
    public function recalcPaidAndStatus(): void
    {
        $sum = (float) $this->payments()->sum('amount');
        $this->paid_total = $sum;

        if ($sum <= 0) {
            // Biarkan default 'TERBIT' (atau DRAFT kalau Anda inginkan logika lain)
            $this->status = 'TERBIT';
        } elseif ($sum + 0.5 < (float) $this->total) { // toleransi float kecil
            $this->status = 'SEBAGIAN';
        } else {
            $this->status = 'LUNAS';
        }

        $this->save();
    }

}
