<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionOrder extends Model
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'order_number',
        'type',
        'item_id',
        'supervisor_id',
        'planned_quantity',
        'completed_quantity',
        'status',
        'scheduled_for',
        'started_at',
        'finished_at',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'scheduled_for' => 'date',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function scopeType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'supervisor_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'cutting' => 'Cutting',
            'sewing' => 'Jahit',
            default => ucfirst($this->type),
        };
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_IN_PROGRESS => 'Sedang Jalan',
            self::STATUS_COMPLETED => 'Selesai',
            default => 'Draft',
        };
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_IN_PROGRESS => 'badge-info',
            self::STATUS_COMPLETED => 'badge-success',
            default => 'badge-secondary',
        };
    }
}
