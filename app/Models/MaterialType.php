<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    protected $fillable = [
        'code', 'name',
        'item_class_id', 'product_category_id',
        'uom', 'current_hpp', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'current_hpp' => 'integer',
    ];

    public function itemClass(): BelongsTo
    {return $this->belongsTo(ItemClass::class);}
    public function productCategory(): BelongsTo
    {return $this->belongsTo(ProductCategory::class);}
    // HAPUS: materialType()

    public function hppHistories(): HasMany
    {return $this->hasMany(ItemHppHistory::class);}
}
