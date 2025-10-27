<?php

// database/migrations/2025_10_26_000000_create_order_shipments_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('order_shipments', function (Blueprint $table) {
      $table->id();

      // Kunci anti-duplikat
      $table->string('no_pesanan');                               // wajib
      $table->string('no_resi')->default('');                     // default '' agar unik gabungan bekerja walau kosong
      $table->string('sku_induk')->default('');                   // <— BARU
      $table->string('nomor_referensi_sku')->default('');         // default ''

      // Kolom lain
      $table->string('status_pesanan')->nullable();
      $table->string('shipped_by_advance_fulfilment')->nullable();
      $table->string('status_pembatalan_pengembalian')->nullable();
      $table->dateTime('waktu_pengiriman_diatur')->nullable();
      $table->dateTime('waktu_pesanan_dibuat')->nullable();
      $table->string('metode_pembayaran')->nullable();
      $table->integer('jumlah')->nullable();
      $table->integer('harga_setelah_diskon')->nullable();
      $table->string('nama_penerima')->nullable();
      $table->string('no_telepon')->nullable();
      $table->string('kota_kabupaten')->nullable();
      $table->string('provinsi')->nullable();
      $table->dateTime('waktu_pesanan_selesai')->nullable();
      $table->timestamps();

      // Index & Unik Gabungan
      $table->index(['no_pesanan', 'no_resi']);
      $table->unique(['no_pesanan', 'no_resi', 'sku_induk', 'nomor_referensi_sku'], 'uniq_orderresi_skuinduk_refsku');
    });
  }

  public function down(): void {
    Schema::dropIfExists('order_shipments');
  }
};
