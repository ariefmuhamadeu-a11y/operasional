<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'paid_total', // boleh tetap ada jika kamu maintain kolom ini
        'status', // DRAFT | TERBIT | SEBAGIAN | LUNAS
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

    /* ========= RELATIONS ========= */

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(PurchaseInvoiceLine::class, 'purchase_invoice_id');
    }

    public function payments(): HasMany
    {
        // pastikan FK di tabel purchase_payments = purchase_invoice_id
        return $this->hasMany(PurchasePayment::class, 'purchase_invoice_id');
    }

    /* ========= COMPUTED HELPERS (opsional, enak dipakai di Blade) ========= */

    // Jika kamu ingin pakai agregat real-time (tanpa mengandalkan kolom paid_total)
    public function getPaidTotalEffectiveAttribute(): float
    {
        // Kalau kolom paid_total kamu rawat, pakai itu; kalau null, fallback ke sum payments
        $val = $this->attributes['paid_total'] ?? null;
        if ($val !== null) {
            return (float) $val;
        }

        // NOTE: gunakan ->loadSum('payments','amount') di query agar tidak n+1
        $sum = $this->payments_sum_amount ?? $this->payments()->sum('amount');
        return (float) $sum;
    }

    public function getOutstandingAttribute(): float
    {
        return max(0, (float) $this->total - (float) $this->paid_total_effective);
    }

    public function getBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'DRAFT' => 'badge-draft',
            'TERBIT' => 'badge-terbit',
            'SEBAGIAN' => 'badge-sebagian',
            'LUNAS' => 'badge-lunas',
            default => 'badge-terbit',
        };
    }
}
