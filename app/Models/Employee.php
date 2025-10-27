<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'name', 'phone', 'role', 'payment_type', 'base_rate', 'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'base_rate' => 'decimal:2',
    ];
}
