@extends('layouts.app')
@section('title', 'ERP • Pembelian')

@push('head')
    <style>
        :root {
            --panel: #0f172a;
            --card: #0e1525;
            --line: #1e2a3f;
            --muted: #9aa4b2;
            --text: #e6ebf1;
            --brand: #60a5fa;
        }

        .sticky-head th {
            position: sticky;
            top: 0;
            background: var(--panel);
            z-index: 2;
        }

        .kpi-card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 14px;
        }

        .kpi-value {
            font-size: 1.1rem;
            font-weight: 700;
        }

        .kpi-label {
            color: var(--muted);
            font-size: .8rem;
        }

        .chip {
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, .03);
            padding: .35rem .55rem;
            border-radius: 999px;
        }

        .table thead th {
            border-bottom-color: var(--line) !important;
        }

        .table tbody td {
            border-top-color: var(--line) !important;
        }

        .form-inline-gap>* {
            margin-right: .5rem;
            margin-bottom: .5rem;
        }

        @media (max-width: 768px) {
            .filters-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: .5rem;
            }

            .filters-grid .col-12 {
                grid-column: 1 / -1;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container py-3">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-semibold mb-0">Faktur Pembelian</h4>
            {{-- <a href="{{ route('purchasing.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus"></i> Tambah</a> --}}
        </div>

        {{-- KPIs / Summary --}}
        <div class="row g-3 mb-3">
            <div class="col-6 col-md-3">
                <div class="kpi-card p-3 h-100">
                    <div class="kpi-label">Jumlah Faktur</div>
                    <div class="kpi-value">{{ $summary['count'] ?? 0 }}</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="kpi-card p-3 h-100">
                    <div class="kpi-label">Total Nilai</div>
                    <div class="kpi-value">{{ rupiah($summary['total'] ?? 0) }}</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="kpi-card p-3 h-100">
                    <div class="kpi-label">Sudah Dibayar</div>
                    <div class="kpi-value">{{ rupiah($summary['paid_total'] ?? 0) }}</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="kpi-card p-3 h-100">
                    <div class="kpi-label">Sisa Terhutang</div>
                    <div class="kpi-value">{{ rupiah($summary['outstanding'] ?? 0) }}</div>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="card p-3 mb-3" style="background: var(--card); border:1px solid var(--line)">
            <form method="GET" class="filters-grid">
                <div>
                    <label class="form-label">Cari</label>
                    <input type="text" class="form-control" name="q" value="{{ $q }}"
                        placeholder="Kode / Nama Supplier">
                </div>
                <div>
                    <label class="form-label">Dari</label>
                    <input type="date" class="form-control" name="date_from" value="{{ $dateFrom }}">
                </div>
                <div>
                    <label class="form-label">Sampai</label>
                    <input type="date" class="form-control" name="date_to" value="{{ $dateTo }}">
                </div>
                <div>
                    <label class="form-label">Supplier</label>
                    <select class="form-select" name="supplier_id">
                        <option value="">Semua</option>
                        @foreach ($supplierOptions as $s)
                            <option value="{{ $s->id }}" @selected($supplierId == $s->id)>{{ $s->store_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Kelas</label>
                    <select class="form-select" name="item_class_id">
                        <option value="">Semua</option>
                        @foreach ($classOptions as $c)
                            <option value="{{ $c->id }}" @selected($classId == $c->id)>{{ $c->code }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status">
                        <option value="">Semua</option>
                        @foreach (['DRAFT', 'TERBIT', 'SEBAGIAN', 'LUNAS'] as $st)
                            <option value="{{ $st }}" @selected($status === $st)>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 d-flex align-items-center">
                    <div class="form-check me-3">
                        <input class="form-check-input" type="checkbox" value="1" id="unpaidOnly" name="unpaid_only"
                            @checked($unpaidOnly)>
                        <label class="form-check-label" for="unpaidOnly">Hanya Belum Lunas</label>
                    </div>

                    <div class="ms-auto">
                        <button class="btn btn-primary btn-sm"><i class="bi bi-funnel"></i> Terapkan</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="btnReset">Reset</button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="table-responsive">
            <table class="table table-dark table-sm align-middle">
                <thead class="sticky-head">
                    <tr>
                        <th>Kode</th>
                        <th>Supplier</th>
                        <th>Tanggal</th>
                        <th class="text-end">Total</th>
                        <th class="text-end">Dibayar</th>
                        <th class="text-end">Sisa</th>
                        <th>Status</th>
                        <th class="d-none d-md-table-cell">Catatan</th>
                        {{-- <th></th> --}}
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $i)
                        @php
                            $sisa = max(0, ($i->total ?? 0) - ($i->paid_total ?? 0));
                            $badge =
                                $i->status === 'LUNAS'
                                    ? 'success'
                                    : ($i->status === 'SEBAGIAN'
                                        ? 'warning'
                                        : 'secondary');
                        @endphp
                        <tr>
                            <td class="mono text-nowrap">{{ $i->code }}</td>
                            <td>{{ $i->supplier->store_name ?? '-' }}</td>
                            <td class="text-nowrap">{{ optional($i->date)->format('d/m/Y') }}</td>
                            <td class="text-end">{{ rupiah($i->total) }}</td>
                            <td class="text-end">{{ rupiah($i->paid_total) }}</td>
                            <td class="text-end {{ $sisa > 0 ? 'text-warning' : 'text-muted' }}">{{ rupiah($sisa) }}</td>
                            <td><span class="badge bg-{{ $badge }}">{{ $i->status }}</span></td>
                            <td class="d-none d-md-table-cell text-truncate" style="max-width: 220px;">
                                {{ $i->note }}
                            </td>
                            {{-- <td class="text-end"><a href="{{ route('purchasing.show',$i) }}" class="btn btn-outline-info btn-sm">Detail</a></td> --}}
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">Tidak ada data untuk filter saat ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        // Reset filter cepat ke halaman dasar tanpa query
        document.getElementById('btnReset')?.addEventListener('click', () => {
            const url = window.location.pathname; // hapus query
            window.location.href = url;
        });
    </script>
@endpush
