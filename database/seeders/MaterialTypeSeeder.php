<?php

namespace Database\Seeders;

use App\Models\MaterialType;
use Illuminate\Database\Seeder;

class MaterialTypeSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['code' => 'FLEECE280BLK', 'name' => 'Fleece 280 Black'],
            ['code' => 'FLEECE280NVY', 'name' => 'Fleece 280 NAVY'],
            ['code' => 'RIB2X2BLK', 'name' => 'Rib 2x2'],
        ] as $r) {
            MaterialType::firstOrCreate(['code' => $r['code']], $r);
        }
    }
}
