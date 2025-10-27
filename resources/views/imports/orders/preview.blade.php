@extends('layouts.app')
@section('title', 'Preview Import')

@section('content')
  <form
    action="{{ route('imports.orders.import') }}"
    method="POST"
  >
    @csrf

    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4 class="mb-0">Preview & Edit Data</h4>
      <button class="btn btn-success">Import Data</button>
    </div>

    <div class="alert alert-info">
      Baris bertanda <span class="badge bg-warning text-dark">Duplikat File</span> atau <span
        class="badge bg-secondary">Sudah Ada</span> akan di-skip saat import. Anda tetap bisa koreksi nilainya di sini.
    </div>

    <div
      class="table-responsive"
      style="max-height:72vh;"
    >
      <table class="table-sm table-bordered table align-middle">
        <thead class="table-dark">
          <tr>
            <th>#</th>
            <th>No. Pesanan</th>
            <th>No. Resi</th>
            <th>SKU Induk</th>
            <th>Nomor Ref SKU</th>
            <th>Status</th>
            <th>Shipped by Advance Fulfilment</th>
            <th>Wkt Pesanan Dibuat</th>
            <th>Wkt Pengiriman Diatur</th>
            <th>Jumlah</th>
            <th>Harga Setelah Diskon</th>
            <th>Nama Penerima</th>
            <th>No. Telepon</th>
            <th>Kota/Kabupaten</th>
            <th>Provinsi</th>
            <th>Wkt Pesanan Selesai</th>
            <th>Info</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($rows as $i => $r)
            @php
              $key =
                  ($r['no_pesanan'] ?? '') .
                  '|' .
                  ($r['no_resi'] ?? '') .
                  '|' .
                  ($r['sku_induk'] ?? '') .
                  '|' .
                  ($r['nomor_referensi_sku'] ?? '');
              $dupe = isset($inFileDupes[$i]) || isset($existingKeys[$key]);
            @endphp
            <tr class="{{ $dupe ? 'table-warning' : '' }}">
              <td>{{ $i + 1 }}</td>
              <td><input
                  name="rows[{{ $i }}][no_pesanan]"
                  class="form-control form-control-sm"
                  value="{{ $r['no_pesanan'] ?? '' }}"
                ></td>
              <td><input
                  name="rows[{{ $i }}][no_resi]"
                  class="form-control form-control-sm"
                  value="{{ $r['no_resi'] ?? '' }}"
                ></td>
              <td><input
                  name="rows[{{ $i }}][sku_induk]"
                  class="form-control form-control-sm"
                  value="{{ $r['sku_induk'] ?? '' }}"
                ></td>
              <td><input
                  name="rows[{{ $i }}][nomor_referensi_sku]"
                  class="form-control form-control-sm"
                  value="{{ $r['nomor_referensi_sku'] ?? '' }}"
                ></td>
              <td><input
                  name="rows[{{ $i }}][status_pesanan]"
                  class="form-control form-control-sm"
                  value="{{ $r['status_pesanan'] ?? '' }}"
                ></td>
              <td>
                <input
                  name="rows[{{ $i }}][shipped_by_advance_fulfilment]"
                  class="form-control form-control-sm"
                  value="{{ $r['shipped_by_advance_fulfilment'] ?? '' }}"
                >
              </td>

              <td><input
                  name="rows[{{ $i }}][waktu_pesanan_dibuat]"
                  class="form-control form-control-sm"
                  value="{{ $r['waktu_pesanan_dibuat'] ?? '' }}"
                  placeholder="YYYY-MM-DD HH:mm / serial Excel"
                ></td>
              <td><input
                  name="rows[{{ $i }}][waktu_pengiriman_diatur]"
                  class="form-control form-control-sm"
                  value="{{ $r['waktu_pengiriman_diatur'] ?? '' }}"
                  placeholder="YYYY-MM-DD HH:mm / serial Excel"
                ></td>
              <td><input
                  name="rows[{{ $i }}][jumlah]"
                  class="form-control form-control-sm text-end"
                  value="{{ $r['jumlah'] ?? '' }}"
                ></td>
              <td><input
                  name="rows[{{ $i }}][harga_setelah_diskon]"
                  class="form-control form-control-sm text-end"
                  value="{{ $r['harga_setelah_diskon'] ?? '' }}"
                ></td>
              <td><input
                  name="rows[{{ $i }}][nama_penerima]"
                  class="form-control form-control-sm"
                  value="{{ $r['nama_penerima'] ?? '' }}"
                ></td>
              <td><input
                  name="rows[{{ $i }}][no_telepon]"
                  class="form-control form-control-sm"
                  value="{{ $r['no_telepon'] ?? '' }}"
                ></td>
              <td><input
                  name="rows[{{ $i }}][kota_kabupaten]"
                  class="form-control form-control-sm"
                  value="{{ $r['kota_kabupaten'] ?? '' }}"
                ></td>
              <td><input
                  name="rows[{{ $i }}][provinsi]"
                  class="form-control form-control-sm"
                  value="{{ $r['provinsi'] ?? '' }}"
                ></td>
              <td><input
                  name="rows[{{ $i }}][waktu_pesanan_selesai]"
                  class="form-control form-control-sm"
                  value="{{ $r['waktu_pesanan_selesai'] ?? '' }}"
                  placeholder="YYYY-MM-DD HH:mm / serial Excel"
                ></td>
              <td>
                <input
                  name="rows[{{ $i }}][metode_pembayaran]"
                  class="form-control form-control-sm"
                  value="{{ $r['metode_pembayaran'] ?? '' }}"
                >

              </td>


              <td class="text-nowrap">
                @if (isset($inFileDupes[$i]))
                  <span class="badge bg-warning text-dark">Duplikat File</span>
                @endif
                @if (isset($existingKeys[$key]))
                  <span class="badge bg-secondary">Sudah Ada</span>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </form>
@endsection
