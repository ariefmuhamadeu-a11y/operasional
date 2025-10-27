<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemHppHistory extends Model
{
    protected $fillable = [
        'item_id','old_hpp','new_hpp','reason','ref_type','ref_id','changed_at','created_by'
    ];

    protected $casts = [
        'old_hpp' => 'integer',
        'new_hpp' => 'integer',
        'changed_at' => 'datetime',
    ];

    public function item(): BelongsTo { return $this->belongsTo(Item::class); }
}
