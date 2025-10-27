{{-- resources/views/orders/completed/index.blade.php --}}
@extends('layouts.app')
@section('title', 'ERP • Pesanan Selesai')

@push('head')
  <style>
    :root {
      --bg: #0b1220;
      --card: #0e1525;
      --muted: #9aa4b2;
      --text: #e6ebf1;
      --line: #172133;
      --accent: #60a5fa;
    }

    body {
      background: var(--bg);
      color: var(--text);
    }

    .card-soft {
      background: var(--card);
      border-color: var(--line);
    }

    .muted {
      color: var(--muted);
    }

    .chip {
      background: rgba(255, 255, 255, .06);
      border: 1px solid rgba(255, 255, 255, .12);
      padding: .25rem .5rem;
      border-radius: 999px;
    }

    .table thead th {
      border-bottom-color: var(--line);
    }

    .table tbody td {
      border-top-color: var(--line);
    }

    .btn-ghost {
      background: rgba(255, 255, 255, .06);
      border: 1px solid rgba(255, 255, 255, .12);
    }

    .btn-ghost:hover {
      background: rgba(255, 255, 255, .1);
    }

    .filter-wrap {
      gap: .75rem;
    }
  </style>
@endpush

@section('content')
  <div class="d-flex align-items-center justify-content-between mb-3">
    <div>
      <h1 class="h4 mb-1">Pesanan Selesai</h1>
      <div class="muted small">Preview tampilan. Kolom utama: <span class="chip">Tanggal Transaksi</span>, <span
          class="chip"
        >No Pesanan</span>, <span class="chip">Jumlah</span>.</div>
    </div>
    <div class="d-none d-lg-block">
      <a
        href="#"
        class="btn btn-sm btn-ghost"
      ><i class="bi bi-arrow-clockwise me-1"></i>Refresh</a>
    </div>
  </div>

  {{-- Filter Bar --}}
  <div class="card card-soft mb-3 p-3">
    <form>
      <div class="row g-3 align-items-end filter-wrap">
        <div class="col-12 col-md-4">
          <label class="form-label">Tanggal Transaksi</label>
          <input
            type="date"
            class="form-control"
            name="tanggal_transaksi"
            value="{{ request('tanggal_transaksi') }}"
          >
        </div>
        <div class="col-12 col-md-5">
          <label class="form-label">No Pesanan</label>
          <input
            type="text"
            class="form-control"
            name="no_pesanan"
            placeholder="Contoh: SPX-20251027-000123"
            value="{{ request('no_pesanan') }}"
          >
        </div>
        <div class="col-12 col-md-3 d-grid d-md-flex gap-2">
          <button
            type="submit"
            class="btn btn-primary flex-grow-1"
          ><i class="bi bi-search me-1"></i>Filter</button>
          <a
            href="#"
            class="btn btn-outline-secondary d-none d-md-inline-flex"
          ><i class="bi bi-x-circle me-1"></i>Reset</a>
        </div>
      </div>
    </form>
  </div>

  @php
    // Dummy data untuk preview
    $rows = [
        ['tanggal' => '2025-10-26', 'no' => 'SPX-20251026-001237', 'jumlah' => 12],
        ['tanggal' => '2025-10-26', 'no' => 'TTK-20251026-009912', 'jumlah' => 8],
        ['tanggal' => '2025-10-27', 'no' => 'SPX-20251027-000045', 'jumlah' => 5],
        ['tanggal' => '2025-10-27', 'no' => 'TTK-20251027-000301', 'jumlah' => 20],
    ];
  @endphp

  {{-- Table --}}
  <div class="card card-soft">
    <div class="table-responsive">
      <table class="mb-0 table align-middle">
        <thead>
          <tr class="text-uppercase small muted">
            <th style="width: 72px">No</th>
            <th>Tanggal Transaksi</th>
            <th>No Pesanan</th>
            <th class="text-end">Jumlah</th>
            <th
              class="text-end"
              style="width: 140px"
            >Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($rows as $i => $r)
            <tr>
              <td class="mono">{{ $i + 1 }}</td>
              <td>{{ \Carbon\Carbon::parse($r['tanggal'])->timezone('Asia/Jakarta')->format('d M Y') }}</td>
              <td><span class="fw-semibold">{{ $r['no'] }}</span></td>
              <td class="text-end">{{ $r['jumlah'] }}</td>
              <td class="text-end">
                <div class="btn-group btn-group-sm">
                  <a
                    href="#"
                    class="btn btn-ghost"
                    title="Lihat"
                  ><i class="bi bi-eye"></i></a>
                  <a
                    href="#"
                    class="btn btn-ghost"
                    title="Edit"
                  ><i class="bi bi-pencil-square"></i></a>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td
                colspan="5"
                class="py-5 text-center"
              >
                <div class="muted">Belum ada data pesanan selesai.</div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- Footer helper --}}
  <div class="d-flex justify-content-between align-items-center mt-3">
    <div class="small muted">Preview UI — belum tersambung ke database.</div>
    <div class="d-flex gap-2">
      <a
        href="#"
        class="btn btn-sm btn-outline-secondary"
      ><i class="bi bi-download me-1"></i>Export</a>
      <a
        href="#"
        class="btn btn-sm btn-outline-secondary"
      ><i class="bi bi-upload me-1"></i>Import</a>
    </div>
  </div>
@endsection
