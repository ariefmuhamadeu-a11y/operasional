<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['code' => 'SJR', 'name' => 'Jogger Pendek Basic'],
            ['code' => 'LJR', 'name' => 'Jogger Panjang Basic'],
            ['code' => 'SHT', 'name' => 'Shot Random'],
        ] as $r) {
            ProductCategory::firstOrCreate(['code' => $r['code']], $r);
        }
    }
}
