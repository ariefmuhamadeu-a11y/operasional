{{-- resources/views/imports/orders/edit.blade.php --}}
@extends('layouts.app')
@section('title', 'Edit Data')

@section('content')
  <form
    method="POST"
    action="{{ route('imports.orders.update', $shipment) }}"
  >
    @csrf
    @method('PUT')

    <div class="card mb-3 p-3">
      <h5 class="mb-3">Edit Order Shipment</h5>

      <div class="row g-3">
        {{-- Identitas & Kunci Unik --}}
        <div class="col-md-4">
          <label class="form-label">No. Pesanan*</label>
          <input
            name="no_pesanan"
            class="form-control"
            value="{{ old('no_pesanan', $shipment->no_pesanan) }}"
            required
          >
          @error('no_pesanan')
            <div class="text-danger small">{{ $message }}</div>
          @enderror
        </div>
        <div class="col-md-4">
          <label class="form-label">No. Resi</label>
          <input
            name="no_resi"
            class="form-control"
            value="{{ old('no_resi', $shipment->no_resi) }}"
          >
          @error('no_resi')
            <div class="text-danger small">{{ $message }}</div>
          @enderror
        </div>
        <div class="col-md-4">
          <label class="form-label">SKU Induk</label>
          <input
            name="sku_induk"
            class="form-control"
            value="{{ old('sku_induk', $shipment->sku_induk) }}"
          >
          @error('sku_induk')
            <div class="text-danger small">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-4">
          <label class="form-label">Kode Barang (Ref SKU)</label>
          <input
            name="nomor_referensi_sku"
            class="form-control"
            value="{{ old('nomor_referensi_sku', $shipment->nomor_referensi_sku) }}"
          >
          @error('nomor_referensi_sku')
            <div class="text-danger small">{{ $message }}</div>
          @enderror
        </div>

        {{-- Status & Pembayaran --}}
        <div class="col-md-4">
          <label class="form-label">Status Pesanan</label>
          <input
            name="status_pesanan"
            class="form-control"
            value="{{ old('status_pesanan', $shipment->status_pesanan) }}"
          >
          @error('status_pesanan')
            <div class="text-danger small">{{ $message }}</div>
          @enderror
        </div>
        <div class="col-md-4">
          <label class="form-label">Metode Pembayaran</label>
          <input
            name="metode_pembayaran"
            class="form-control"
            value="{{ old('metode_pembayaran', $shipment->metode_pembayaran) }}"
          >
          @error('metode_pembayaran')
            <div class="text-danger small">{{ $message }}</div>
          @enderror
        </div>

        {{-- Tanggal (pakai null-safe + fallback '') --}}
        <div class="col-md-4">
          <label class="form-label">Waktu Pesanan Dibuat</label>
          <input
            name="waktu_pesanan_dibuat"
            class="form-control"
            placeholder="YYYY-MM-DD HH:mm"
            value="{{ old('waktu_pesanan_dibuat', $shipment->waktu_pesanan_dibuat?->timezone('Asia/Jakarta')?->format('Y-m-d H:i') ?? '') }}"
          >
          @error('waktu_pesanan_dibuat')
            <div class="text-danger small">{{ $message }}</div>
          @enderror
        </div>
        <div class="col-md-4">
          <label class="form-label">Waktu Pengiriman Diatur</label>
          <input
            name="waktu_pengiriman_diatur"
            class="form-control"
            placeholder="YYYY-MM-DD HH:mm"
            value="{{ old('waktu_pengiriman_diatur', $shipment->waktu_pengiriman_diatur?->timezone('Asia/Jakarta')?->format('Y-m-d H:i') ?? '') }}"
          >
          @error('waktu_pengiriman_diatur')
            <div class="text-danger small">{{ $message }}</div>
          @enderror
        </div>
        <div class="col-md-4">
          <label class="form-label">Waktu Pesanan Selesai</label>
          <input
            name="waktu_pesanan_selesai"
            class="form-control"
            placeholder="YYYY-MM-DD HH:mm"
            value="{{ old('waktu_pesanan_selesai', $shipment->waktu_pesanan_selesai?->timezone('Asia/Jakarta')?->format('Y-m-d H:i') ?? '') }}"
          >
          @error('waktu_pesanan_selesai')
            <div class="text-danger small">{{ $message }}</div>
          @enderror
        </div>

        {{-- Angka --}}
        <div class="col-md-4">
          <label class="form-label">Jumlah</label>
          <input
            name="jumlah"
            class="form-control"
            value="{{ old('jumlah', $shipment->jumlah) }}"
          >
          @error('jumlah')
            <div class="text-danger small">{{ $message }}</div>
          @enderror
        </div>
        <div class="col-md-4">
          <label class="form-label">Harga Setelah Diskon</label>
          <input
            name="harga_setelah_diskon"
            class="form-control"
            value="{{ old('harga_setelah_diskon', $shipment->harga_setelah_diskon) }}"
          >
          @error('harga_setelah_diskon')
            <div class="text-danger small">{{ $message }}</div>
          @enderror
        </div>

        {{-- Penerima & Alamat --}}
        <div class="col-md-4">
          <label class="form-label">Nama Penerima</label>
          <input
            name="nama_penerima"
            class="form-control"
            value="{{ old('nama_penerima', $shipment->nama_penerima) }}"
          >
          @error('nama_penerima')
            <div class="text-danger small">{{ $message }}</div>
          @enderror
        </div>
        <div class="col-md-4">
          <label class="form-label">No. Telepon</label>
          <input
            name="no_telepon"
            class="form-control"
            value="{{ old('no_telepon', $shipment->no_telepon) }}"
          >
          @error('no_telepon')
            <div class="text-danger small">{{ $message }}</div>
          @enderror
        </div>
        <div class="col-md-4">
          <label class="form-label">Kota/Kabupaten</label>
          <input
            name="kota_kabupaten"
            class="form-control"
            value="{{ old('kota_kabupaten', $shipment->kota_kabupaten) }}"
          >
          @error('kota_kabupaten')
            <div class="text-danger small">{{ $message }}</div>
          @enderror
        </div>
        <div class="col-md-4">
          <label class="form-label">Provinsi</label>
          <input
            name="provinsi"
            class="form-control"
            value="{{ old('provinsi', $shipment->provinsi) }}"
          >
          @error('provinsi')
            <div class="text-danger small">{{ $message }}</div>
          @enderror
        </div>
      </div>

      <div class="d-flex mt-3 gap-2">
        <button class="btn btn-primary">Simpan Perubahan</button>
        <a
          href="{{ route('imports.orders.index') }}"
          class="btn btn-outline-secondary"
        >Batal</a>
      </div>
    </div>
  </form>
@endsection
