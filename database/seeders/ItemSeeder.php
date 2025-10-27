<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\ItemClass;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $BKU = ItemClass::firstWhere('code', 'BKU');
        $BPU = ItemClass::firstWhere('code', 'BPU');
        $BSJ = ItemClass::firstWhere('code', 'BSJ');
        $BJD = ItemClass::firstWhere('code', 'BJD');

        // contoh kategori, pastikan kategori "Joger Basic Pendek" sudah ada di ProductCategorySeeder
        $JBP = ProductCategory::firstWhere('code', 'SJR');

        foreach ([

            // 🔹 item baru sesuai permintaanmu
            [
                'code' => 'K7BLK',
                'name' => 'Celana Joger Pendek Basic Hitam 7L',
                'item_class_id' => $BJD?->id, // Barang Jadi
                'product_category_id' => $JBP?->id, // Joger Basic Pendek
                'uom' => 'PCS',
                'current_hpp' => 30000, // ubah sesuai kebutuhanmu
            ],
            [
                'code' => 'K5BLK',
                'name' => 'Celana Joger Pendek Basic Hitam 5L 6L',
                'item_class_id' => $BJD?->id, // Barang Jadi
                'product_category_id' => $JBP?->id, // Joger Basic Pendek
                'uom' => 'PCS',
                'current_hpp' => 30000, // ubah sesuai kebutuhanmu
            ],
            [
                'code' => 'K3BLK',
                'name' => 'Celana Joger Pendek Basic Hitam 3L 4L',
                'item_class_id' => $BJD?->id, // Barang Jadi
                'product_category_id' => $JBP?->id, // Joger Basic Pendek
                'uom' => 'PCS',
                'current_hpp' => 30000, // ubah sesuai kebutuhanmu
            ],

            [
                'code' => 'K7NVY',
                'name' => 'Celana Joger Pendek Basic Biru Tua 7L',
                'item_class_id' => $BJD?->id, // Barang Jadi
                'product_category_id' => $JBP?->id, // Joger Basic Pendek
                'uom' => 'PCS',
                'current_hpp' => 30000, // ubah sesuai kebutuhanmu
            ],
            [
                'code' => 'K5NVY',
                'name' => 'Celana Joger Pendek Basic Biru Tua 5L 6L',
                'item_class_id' => $BJD?->id, // Barang Jadi
                'product_category_id' => $JBP?->id, // Joger Basic Pendek
                'uom' => 'PCS',
                'current_hpp' => 30000, // ubah sesuai kebutuhanmu
            ],
            [
                'code' => 'K3NVY',
                'name' => 'Celana Joger Pendek Basic Biru Tua 3L 4L',
                'item_class_id' => $BJD?->id, // Barang Jadi
                'product_category_id' => $JBP?->id, // Joger Basic Pendek
                'uom' => 'PCS',
                'current_hpp' => 30000, // ubah sesuai kebutuhanmu
            ],

            [
                'code' => 'K7MST',
                'name' => 'Celana Joger Pendek Basic Abu Misty (Muda) 7L',
                'item_class_id' => $BJD?->id, // Barang Jadi
                'product_category_id' => $JBP?->id, // Joger Basic Pendek
                'uom' => 'PCS',
                'current_hpp' => 30000, // ubah sesuai kebutuhanmu
            ],
            [
                'code' => 'K5MST',
                'name' => 'Celana Joger Pendek Basic Abu Misty (Muda) 5L 6L',
                'item_class_id' => $BJD?->id, // Barang Jadi
                'product_category_id' => $JBP?->id, // Joger Basic Pendek
                'uom' => 'PCS',
                'current_hpp' => 30000, // ubah sesuai kebutuhanmu
            ],
            [
                'code' => 'K3MST',
                'name' => 'Celana Joger Pendek Basic Abu Misty (Muda) 3L 4L',
                'item_class_id' => $BJD?->id, // Barang Jadi
                'product_category_id' => $JBP?->id, // Joger Basic Pendek
                'uom' => 'PCS',
                'current_hpp' => 30000, // ubah sesuai kebutuhanmu
            ],

            [
                'code' => 'K7ABT',
                'name' => 'Celana Joger Pendek Basic Abu tua 7L',
                'item_class_id' => $BJD?->id, // Barang Jadi
                'product_category_id' => $JBP?->id, // Joger Basic Pendek
                'uom' => 'PCS',
                'current_hpp' => 30000, // ubah sesuai kebutuhanmu
            ],
            [
                'code' => 'K5ABT',
                'name' => 'Celana Joger Pendek Basic Abu tua 5L 6L',
                'item_class_id' => $BJD?->id, // Barang Jadi
                'product_category_id' => $JBP?->id, // Joger Basic Pendek
                'uom' => 'PCS',
                'current_hpp' => 30000, // ubah sesuai kebutuhanmu
            ],
            [
                'code' => 'K3ABT',
                'name' => 'Celana Joger Pendek Basic Abu tua 3L 4L',
                'item_class_id' => $BJD?->id, // Barang Jadi
                'product_category_id' => $JBP?->id, // Joger Basic Pendek
                'uom' => 'PCS',
                'current_hpp' => 30000, // ubah sesuai kebutuhanmu
            ],

        ] as $r) {
            Item::firstOrCreate(['code' => $r['code']], $r);
        }

        $this->command->info('✅ ItemSeeder berhasil dijalankan');
    }
}
