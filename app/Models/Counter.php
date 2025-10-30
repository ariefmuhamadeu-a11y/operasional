<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Counter extends Model
{
    protected $fillable = [
        'key',
        'seq',
    ];

    /**
     * Ambil nomor urut berikutnya untuk kunci tertentu.
     */
    public static function next(string $key): int
    {
        return (int) DB::transaction(function () use ($key) {
            /** @var self|null $counter */
            $counter = static::query()
                ->where('key', $key)
                ->lockForUpdate()
                ->first();

            if (! $counter) {
                $counter = static::create([
                    'key' => $key,
                    'seq' => 0,
                ]);
            }

            $counter->increment('seq');

            return $counter->seq;
        });
    }
}
