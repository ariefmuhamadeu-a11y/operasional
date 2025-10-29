<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

class Code
{
    public static function next(string $prefix, string $date): string
    {
        $base = $prefix . '-' . date('Ymd', strtotime($date)) . '-';
        $row = DB::table('counters')->where('key', $base)->lockForUpdate()->first();
        $seq = $row ? $row->seq + 1 : 1;
        DB::table('counters')->updateOrInsert(['key' => $base], ['seq' => $seq, 'updated_at' => now(), 'created_at' => now()]);
        return $base . str_pad((string) $seq, 3, '0', STR_PAD_LEFT);
    }
}
