@extends('layouts.app')
@section('title', 'ERP • Pengiriman Pesanan')

@push('head')
    <style>
        .sticky-head th {
            position: sticky;
            top: 0;
            background: #0f172a;
            z-index: 2
        }

        .scroll-table {
            max-height: 70vh;
            overflow: auto
        }

        .text-nowrap {
            white-space: nowrap
        }

        /* Kartu mobile: bayangan lembut + spacing nyaman */
        .ship-card {
            background: #111827;
            border: 1px solid #1f2937;
            border-radius: .75rem;
            padding: 12px
        }

        .ship-row+.ship-row {
            margin-top: 10px
        }

        .label {
            font-size: .78rem;
            color: #9ca3af
        }

        .val {
            font-weight: 600
        }

        .badge-status {
            font-size: .72rem
        }

        .btn-touch {
            padding: .6rem .9rem;
            border-radius: .6rem
        }

        /* Rapikan input di HP */
        @media (max-width: 576px) {
            .form-label {
                font-size: .85rem;
                margin-bottom: .25rem
            }
        }
    </style>
@endpush

@section('content')

    {{-- Header --}}
    <div class="card p-3 mb-3">
        <h4 class="fw-semibold mb-1">Pengiriman Pesanan</h4>
        <div class="text-muted small">Upload file, filter, dan kelola data pengiriman pesanan.</div>
    </div>

    {{-- Upload & Preview --}}
    <div class="card p-3 mb-3">
        <h5 class="mb-3">Upload File (.xlsx)</h5>
        <form action="{{ route('imports.orders.preview') }}" method="POST" enctype="multipart/form-data" class="row g-3">
            @csrf
            <div class="col-12 col-md-6 col-lg-4">
                <label class="form-label">Pilih File</label>
                <input type="file" name="file" class="form-control" accept=".xlsx,.xls" required>
                @error('file')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-12 col-md-6 col-lg-3 d-flex align-items-end">
                <button class="btn btn-primary w-100 btn-touch">
                    <i class="bi bi-eye"></i> Preview
                </button>
            </div>
        </form>
    </div>

    {{-- Filter (mobile-first) --}}
    <div class="card p-3 mb-3">
        <form method="GET" class="row g-3">
            <div class="col-12">
                <label class="form-label">Cari (No. Pesanan / No. Resi)</label>
                <input name="q" value="{{ $q ?? '' }}" class="form-control"
                    placeholder="Contoh: INV-20251020 / 000123456">
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label">Tanggal Dari</label>
                <input type="date" name="date_from" value="{{ $dateFrom ?? '' }}" class="form-control">
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label">Tanggal Sampai</label>
                <input type="date" name="date_to" value="{{ $dateTo ?? '' }}" class="form-control">
            </div>
            <div class="col-12 d-flex gap-2">
                <button class="btn btn-outline-light btn-touch flex-fill">
                    <i class="bi bi-search"></i> Terapkan Filter
                </button>
                <a class="btn btn-outline-secondary btn-touch" href="{{ route('imports.orders.index') }}">
                    <i class="bi bi-x-circle"></i> Reset
                </a>
            </div>
        </form>
    </div>

    @php
        // Helper tanggal untuk Blade
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
    @endphp

    {{-- LIST MOBILE (kartu) --}}
    <div class="d-md-none">
        @forelse($shipments as $s)
            @php
                $jumlah = is_null($s->jumlah) ? 0 : (int) $s->jumlah;
                $dibuat = $fmt($s->waktu_pesanan_dibuat);
                $diatur = $fmt($s->waktu_pengiriman_diatur);
            @endphp
            <div class="ship-card ship-row">
                <div class="d-flex justify-content-between align-items-start mb-1">
                    <div class="val">{{ $s->no_resi ?: '—' }}</div>
                    <span class="badge bg-info-subtle text-info badge-status">{{ $s->status_pesanan ?: '—' }}</span>
                </div>

                <div class="row g-2 mt-1">
                    <div class="col-6">
                        <div class="label">Pesanan Dibuat</div>
                        <div class="val text-nowrap">{{ $dibuat ?: '—' }}</div>
                    </div>
                    <div class="col-6">
                        <div class="label">Pengiriman Diatur</div>
                        <div class="val text-nowrap">{{ $diatur ?: '—' }}</div>
                    </div>
                    <div class="col-7">
                        <div class="label">Items (Ref SKU)</div>
                        <div class="val">{{ $s->nomor_referensi_sku ?: '—' }}</div>
                    </div>
                    <div class="col-5 text-end">
                        <div class="label">Jumlah</div>
                        <div class="val">{{ $jumlah ? number_format($jumlah, 0, ',', '.') : '0' }}</div>
                    </div>
                </div>

                <div class="mt-3 d-flex justify-content-end">
                    <a href="{{ route('imports.orders.edit', $s) }}" class="btn btn-outline-light btn-touch">
                        <i class="bi bi-pencil-square"></i> Edit
                    </a>
                </div>
            </div>
        @empty
            <div class="text-center text-muted py-4">Belum ada data pengiriman pesanan.</div>
        @endforelse

        @if (method_exists($shipments, 'links'))
            <div class="mt-3">{{ $shipments->appends(request()->query())->links() }}</div>
        @endif
    </div>

    {{-- TABEL DESKTOP/TABLET --}}
    <div class="card p-3 d-none d-md-block">
        <div class="scroll-table">
            <table class="table table-sm table-hover align-middle">
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
                            $jumlah = is_null($s->jumlah) ? 0 : (int) $s->jumlah;
                        @endphp
                        <tr>
                            <td class="text-nowrap">{{ $s->no_resi }}</td>
                            <td class="text-nowrap">{{ $s->status_pesanan }}</td>
                            <td class="text-nowrap">{{ $fmt($s->waktu_pesanan_dibuat) }}</td>
                            <td class="text-nowrap">{{ $fmt($s->waktu_pengiriman_diatur) }}</td>
                            <td class="text-nowrap">{{ $s->nomor_referensi_sku }}</td>
                            <td class="text-end">{{ $jumlah ? number_format($jumlah, 0, ',', '.') : '' }}</td>
                            <td class="text-end">
                                <a href="{{ route('imports.orders.edit', $s) }}" class="btn btn-sm btn-outline-light">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Belum ada data pengiriman pesanan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if (method_exists($shipments, 'links'))
            <div class="mt-3">{{ $shipments->appends(request()->query())->links() }}</div>
        @endif
    </div>

@endsection
