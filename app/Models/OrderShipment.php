<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderShipment extends Model
{
    // app/Models/OrderShipment.php
    protected $fillable = [
    'no_pesanan','no_resi','sku_induk','nomor_referensi_sku',
    'status_pesanan','shipped_by_advance_fulfilment','status_pembatalan_pengembalian',
    'waktu_pengiriman_diatur','waktu_pesanan_dibuat','waktu_pesanan_selesai',
    'metode_pembayaran','jumlah','harga_setelah_diskon','nama_penerima','no_telepon',
    'kota_kabupaten','provinsi',
    ];


    protected $casts = [
        'waktu_pengiriman_diatur' => 'datetime',
        'waktu_pesanan_dibuat'    => 'datetime',
        'waktu_pesanan_selesai'   => 'datetime',
        'harga_setelah_diskon'    => 'decimal:2',
    ];
}

