<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        // Ambil ID dari item_classes yang sudah diseed sebelumnya
        $bahanBakuId = DB::table('item_classes')->where('code', 'BKU')->value('id');
        $aksesorisId = DB::table('item_classes')->where('code', 'BPU')->value('id');

        // Tambah / update supplier berdasarkan code unik
        DB::table('suppliers')->updateOrInsert(
            ['code' => 'ORIGAMI'],
            [
                'store_name' => 'Origami Textile',
                'item_class_id' => $bahanBakuId,
                'type' => 'Distributor',
                'phone' => '08123456789',
                'address' => 'Jl. Industri Tekstil No. 12, Bandung',
            ]
        );

        DB::table('suppliers')->updateOrInsert(
            ['code' => 'TOPLIS'],
            [
                'store_name' => 'Toplis Jaya',
                'item_class_id' => $aksesorisId,
                'type' => 'Supplier Lokal',
                'phone' => '08211222333',
                'address' => 'Jl. Raya Gajah Mada No. 45, Jakarta',
            ]
        );
    }

}
