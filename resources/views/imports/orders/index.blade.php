@extends('layouts.app')
@section('title', 'ERP • Pengiriman Pesanan')

@push('head')
  <style>
    .sticky-head th {
      position: sticky;
      top: 0;
      background: #0f172a;
      z-index: 2;
    }

    .scroll-table {
      max-height: 70vh;
      overflow: auto;
    }

    .text-nowrap {
      white-space: nowrap;
    }
  </style>
@endpush

@section('content')

  {{-- Header --}}
  <div class="card mb-3 p-3">
    <h4 class="fw-semibold mb-1">Pengiriman Pesanan</h4>
    <div class="text-muted small">Lihat dan kelola data pesanan yang sudah diimpor dari file Excel (.xlsx)</div>
  </div>

  {{-- Filter --}}
  <div class="card mb-3 p-3">
    <form
      method="GET"
      class="row g-2 align-items-end"
    >
      <div class="col-md-4">
        <label class="form-label">Cari (No. Pesanan / No. Resi)</label>
        <input
          name="q"
          value="{{ $q ?? '' }}"
          class="form-control"
          placeholder="Contoh: INV-20251020 / 000123456"
        >
      </div>
      <div class="col-md-3">
        <label class="form-label">Tanggal Dari</label>
        <input
          type="date"
          name="date_from"
          value="{{ $dateFrom ?? '' }}"
          class="form-control"
        >
      </div>
      <div class="col-md-3">
        <label class="form-label">Tanggal Sampai</label>
        <input
          type="date"
          name="date_to"
          value="{{ $dateTo ?? '' }}"
          class="form-control"
        >
      </div>
      <div class="col-12 d-flex gap-2">
        <button class="btn btn-outline-light"><i class="bi bi-search"></i> Terapkan Filter</button>
        <a
          class="btn btn-outline-secondary"
          href="{{ route('imports.orders.index') }}"
        ><i class="bi bi-x-circle"></i> Reset</a>
      </div>
    </form>
  </div>

  {{-- Tabel Data --}}
  <div class="card p-3">
    <div class="scroll-table">
      <table class="table-sm table-hover table align-middle">
        <thead class="sticky-head">
          <tr>
            <th>No Resi</th>
            <th>Status Pesanan</th>
            <th>Pesanan Dibuat</th>
            <th>Pengiriman Diatur</th>
            <th>Items</th>
            <th class="text-end">Jumlah</th>
            <th class="text-end">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($shipments as $s)
            @php
              $fmt = function ($val) {
                  if (!$val) {
                      return '';
                  }
                  if (!($val instanceof \DateTimeInterface)) {
                      try {
                          $val = \Carbon\Carbon::parse((string) $val, 'Asia/Jakarta');
                      } catch (\Throwable $e) {
                          return '';
                      }
                  }
                  return $val->timezone('Asia/Jakarta')->format('Y-m-d H:i');
              };
              $dibuat = $fmt($s->waktu_pesanan_dibuat);
              $diatur = $fmt($s->waktu_pengiriman_diatur);
              $jumlah = is_null($s->jumlah) ? 0 : (int) $s->jumlah;
            @endphp
            <tr>
              <td class="text-nowrap">{{ $s->no_resi }}</td>
              <td class="text-nowrap">{{ $s->status_pesanan }}</td>
              <td class="text-nowrap">{{ $dibuat }}</td>
              <td class="text-nowrap">{{ $diatur }}</td>
              <td class="text-nowrap">{{ $s->nomor_referensi_sku }}</td>
              <td class="text-end">{{ $jumlah ? number_format($jumlah, 0, ',', '.') : '' }}</td>
              <td class="text-end">
                <button
                  type="button"
                  class="btn btn-sm btn-outline-light"
                  data-bs-toggle="modal"
                  data-bs-target="#editModal"
                  {{-- data untuk modal --}}
                  data-id="{{ $s->id }}"
                  data-no-pesanan="{{ $s->no_pesanan }}"
                  data-no-resi="{{ $s->no_resi }}"
                  data-status="{{ $s->status_pesanan }}"
                  data-dibuat="{{ $dibuat }}"
                  data-diatur="{{ $diatur }}"
                  data-items="{{ $s->nomor_referensi_sku }}"
                  data-jumlah="{{ $jumlah }}"
                >
                  <i class="bi bi-pencil-square"></i> Edit
                </button>
              </td>
            </tr>
          @empty
            <tr>
              <td
                colspan="7"
                class="text-muted py-4 text-center"
              >Belum ada data pengiriman pesanan.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if (method_exists($shipments, 'links'))
      <div class="mt-3">{{ $shipments->appends(request()->query())->links() }}</div>
    @endif
  </div>

  {{-- Modal Edit (reusable) --}}
  <div
    class="modal fade"
    id="editModal"
    tabindex="-1"
    aria-hidden="true"
  >
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <form
        class="modal-content"
        method="POST"
        id="editForm"
      >
        @csrf
        @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title">Edit Pengiriman</h5>
          <button
            type="button"
            class="btn-close"
            data-bs-dismiss="modal"
            aria-label="Tutup"
          ></button>
        </div>
        <div class="modal-body">
          {{-- no_pesanan disembunyikan (required oleh controller update) --}}
          <input
            type="hidden"
            name="no_pesanan"
            id="f-no-pesanan"
          >

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">No Resi</label>
              <input
                name="no_resi"
                id="f-no-resi"
                class="form-control"
              >
            </div>

            <div class="col-md-6">
              <label class="form-label">Status Pesanan</label>
              <input
                name="status_pesanan"
                id="f-status"
                class="form-control"
              >
            </div>

            <div class="col-md-6">
              <label class="form-label">Pesanan Dibuat</label>
              <input
                name="waktu_pesanan_dibuat"
                id="f-dibuat"
                class="form-control"
                placeholder="YYYY-MM-DD HH:mm"
              >
            </div>

            <div class="col-md-6">
              <label class="form-label">Pengiriman Diatur</label>
              <input
                name="waktu_pengiriman_diatur"
                id="f-diatur"
                class="form-control"
                placeholder="YYYY-MM-DD HH:mm"
              >
            </div>

            <div class="col-md-6">
              <label class="form-label">Items (Kode Barang / Ref SKU)</label>
              <input
                name="nomor_referensi_sku"
                id="f-items"
                class="form-control"
              >
            </div>

            <div class="col-md-6">
              <label class="form-label">Jumlah</label>
              <input
                name="jumlah"
                id="f-jumlah"
                class="form-control"
                inputmode="numeric"
              >
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-primary"><i class="bi bi-save"></i> Simpan</button>
          <button
            type="button"
            class="btn btn-outline-secondary"
            data-bs-dismiss="modal"
          >Batal</button>
        </div>
      </form>
    </div>
  </div>

@endsection

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const modalEl = document.getElementById('editModal');
      modalEl.addEventListener('show.bs.modal', function(event) {
        const btn = event.relatedTarget;
        const id = btn.getAttribute('data-id');
        const noPesanan = btn.getAttribute('data-no-pesanan') || '';
        const noResi = btn.getAttribute('data-no-resi') || '';
        const status = btn.getAttribute('data-status') || '';
        const dibuat = btn.getAttribute('data-dibuat') || '';
        const diatur = btn.getAttribute('data-diatur') || '';
        const items = btn.getAttribute('data-items') || '';
        const jumlah = btn.getAttribute('data-jumlah') || '';

        // set action form (PUT /shipments/{id})
        const form = document.getElementById('editForm');
        form.action = "{{ url('/shipments') }}/" + id;

        // isi field
        document.getElementById('f-no-pesanan').value = noPesanan;
        document.getElementById('f-no-resi').value = noResi;
        document.getElementById('f-status').value = status;
        document.getElementById('f-dibuat').value = dibuat;
        document.getElementById('f-diatur').value = diatur;
        document.getElementById('f-items').value = items;
        document.getElementById('f-jumlah').value = jumlah;
      });
    });
  </script>
@endpush
