<?php

namespace Database\Seeders;

use App\Models\ItemClass;
use Illuminate\Database\Seeder;

class ItemClassSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['code' => 'BKU', 'name' => 'Bahan Baku'],
            ['code' => 'BPU', 'name' => 'Aksesoris'],
            ['code' => 'BSJ', 'name' => 'Barang Setengah Jadi'],
            ['code' => 'BJD', 'name' => 'Barang Jadi'],
        ] as $r) {
            ItemClass::firstOrCreate(['code' => $r['code']], $r);
        }
    }
}
