{{-- resources/views/production/orders/index.blade.php --}}
@extends('layouts.app')
@section('title', "ERP • Produksi {$typeLabel}")

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

        .card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 14px;
        }

        .muted {
            color: var(--muted);
        }

        .mono {
            font-variant-numeric: tabular-nums;
            font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
        }

        .badge-status {
            border-radius: 999px;
            padding: .25rem .6rem;
            font-size: .75rem;
            font-weight: 600;
        }

        .badge-draft {
            background: rgba(148, 163, 184, .12);
            border: 1px solid rgba(148, 163, 184, .3);
            color: #d1d5db;
        }

        .badge-progress {
            background: rgba(96, 165, 250, .15);
            border: 1px solid rgba(96, 165, 250, .3);
            color: #dbeafe;
        }

        .badge-done {
            background: rgba(16, 185, 129, .18);
            border: 1px solid rgba(16, 185, 129, .35);
            color: #a7f3d0;
        }

        .table thead th {
            background: var(--panel);
            position: sticky;
            top: 0;
            z-index: 2;
            color: #aab2bd;
        }

        .table-wrap {
            border-top: 1px solid var(--line);
            overflow-x: auto;
        }

        .kpi {
            padding: 1rem;
        }

        .kpi .label {
            font-size: .8rem;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .kpi .value {
            font-size: 1.4rem;
            font-weight: 700;
        }

        .toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: .75rem;
            align-items: center;
            justify-content: space-between;
        }

        .toolbar .title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #f8fafc;
        }

        .toolbar .subtitle {
            color: var(--muted);
        }

        .btn-soft {
            background: rgba(255, 255, 255, .05);
            border: 1px solid var(--line);
            color: var(--text);
        }

        .empty-state {
            padding: 2.5rem 1rem;
            text-align: center;
            color: var(--muted);
        }

        @media (max-width: 768px) {
            .table-wrap {
                display: none;
            }

            .card-list {
                display: grid;
                gap: 1rem;
            }

            .list-card {
                border: 1px solid var(--line);
                border-radius: 14px;
                padding: 1rem;
                background: var(--card);
            }

            .list-card .heading {
                display: flex;
                justify-content: space-between;
                gap: .75rem;
                margin-bottom: .75rem;
                font-weight: 600;
            }

            .list-card .meta {
                display: grid;
                gap: .35rem;
                font-size: .9rem;
            }
        }

        @media (min-width: 769px) {
            .card-list {
                display: none;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid px-lg-4 px-3 py-4">
        <div class="card mb-4">
            <div class="card-body">
                <div class="toolbar">
                    <div>
                        <div class="title">Produksi {{ $typeLabel }}</div>
                        <div class="subtitle">Kelola perintah produksi untuk proses {{ strtolower($typeLabel) }}.</div>
                    </div>
                    <a href="{{ route('production.orders.create', ['type' => $type]) }}" class="btn btn-soft">
                        <i class="bi bi-plus-lg me-2"></i> Perintah Baru
                    </a>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12 col-md-3">
                <div class="card h-100">
                    <div class="card-body kpi">
                        <div class="label">Total</div>
                        <div class="value">{{ $stats['total'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card h-100">
                    <div class="card-body kpi">
                        <div class="label">Draft</div>
                        <div class="value">{{ $stats['draft'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card h-100">
                    <div class="card-body kpi">
                        <div class="label">Sedang Proses</div>
                        <div class="value">{{ $stats['in_progress'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card h-100">
                    <div class="card-body kpi">
                        <div class="label">Selesai</div>
                        <div class="value">{{ $stats['completed'] }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-wrap">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase small">No Produksi</th>
                                <th class="text-uppercase small">Produk</th>
                                <th class="text-uppercase small">Jadwal</th>
                                <th class="text-uppercase small text-end">Rencana Qty</th>
                                <th class="text-uppercase small">Penanggung Jawab</th>
                                <th class="text-uppercase small">Status</th>
                                <th class="text-uppercase small">Catatan</th>
                                <th class="text-uppercase small">Dibuat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($orders as $order)
                                <tr>
                                    <td class="fw-semibold mono">{{ $order->order_number }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $order->item?->name ?? '—' }}</div>
                                        <div class="muted small">{{ $order->item?->code }}</div>
                                    </td>
                                    <td>
                                        {{ optional($order->scheduled_for)->translatedFormat('d F Y') ?? 'Belum dijadwalkan' }}
                                    </td>
                                    <td class="text-end">{{ number_format($order->planned_quantity) }}</td>
                                    <td>{{ $order->supervisor?->name ?? '—' }}</td>
                                    <td>
                                        @php
                                            $badge = match ($order->status) {
                                                \App\Models\ProductionOrder::STATUS_COMPLETED => 'badge-done',
                                                \App\Models\ProductionOrder::STATUS_IN_PROGRESS => 'badge-progress',
                                                default => 'badge-draft',
                                            };
                                        @endphp
                                        <span class="badge-status {{ $badge }}">{{ $order->statusLabel() }}</span>
                                    </td>
                                    <td class="small">{{ $order->notes ?? '—' }}</td>
                                    <td class="small">{{ $order->created_at?->diffForHumans() }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        Belum ada perintah produksi untuk proses ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="card-list p-3">
                    @forelse ($orders as $order)
                        <div class="list-card">
                            <div class="heading">
                                <span class="mono">{{ $order->order_number }}</span>
                                @php
                                    $badge = match ($order->status) {
                                        \App\Models\ProductionOrder::STATUS_COMPLETED => 'badge-done',
                                        \App\Models\ProductionOrder::STATUS_IN_PROGRESS => 'badge-progress',
                                        default => 'badge-draft',
                                    };
                                @endphp
                                <span class="badge-status {{ $badge }}">{{ $order->statusLabel() }}</span>
                            </div>
                            <div class="meta">
                                <div><span class="muted">Produk:</span> {{ $order->item?->name ?? '—' }} ({{ $order->item?->code }})</div>
                                <div><span class="muted">Jadwal:</span> {{ optional($order->scheduled_for)->translatedFormat('d F Y') ?? 'Belum dijadwalkan' }}</div>
                                <div><span class="muted">Rencana Qty:</span> {{ number_format($order->planned_quantity) }}</div>
                                <div><span class="muted">Penanggung Jawab:</span> {{ $order->supervisor?->name ?? '—' }}</div>
                                <div><span class="muted">Catatan:</span> {{ $order->notes ?? '—' }}</div>
                                <div><span class="muted">Dibuat:</span> {{ $order->created_at?->diffForHumans() }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">
                            Belum ada perintah produksi untuk proses ini.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
