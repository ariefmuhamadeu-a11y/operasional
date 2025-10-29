{{-- resources/views/purchasing/show.blade.php --}}
@extends('layouts.app')
@section('title', 'ERP • Detail Pembelian')

@push('head')
    <style>
        :root {
            --panel: #0f172a;
            --card: #0e1525;
            --line: #1e2a3f;
            --muted: #9aa4b2;
            --text: #e6ebf1;
            --brand: #60a5fa;

            /* Tone khusus Riwayat Pembayaran */
            --card-pay: #0f1d19;
            --line-pay: #1b3a33;
            --chip-pay: #b8f6e3;
            --thead-pay: #0c1512;
        }

        /* ===== Base ===== */
        .card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 14px
        }

        .muted {
            color: var(--muted)
        }

        .mono {
            font-variant-numeric: tabular-nums;
            font-family: ui-monospace, SFMono-Regular, Menlo, monospace
        }

        .table thead th {
            background: var(--panel);
            position: sticky;
            top: 0;
            z-index: 1;
            color: #aab2bd
        }

        .badge-draft {
            background: rgba(148, 163, 184, .1);
            border: 1px solid rgba(148, 163, 184, .25);
            color: #cbd5e1
        }

        .badge-terbit {
            background: rgba(96, 165, 250, .12);
            border: 1px solid rgba(96, 165, 250, .25);
            color: #dbeafe
        }

        .badge-sebagian {
            background: rgba(250, 204, 21, .12);
            border: 1px solid rgba(250, 204, 21, .25);
            color: #fde68a
        }

        .badge-lunas {
            background: rgba(16, 185, 129, .12);
            border: 1px solid rgba(16, 185, 129, .25);
            color: #a7f3d0
        }

        .section {
            padding: 1rem
        }

        .kv .label {
            color: var(--muted);
            font-size: .9rem
        }

        .kv .value {
            font-weight: 600
        }

        .total-box {
            display: grid;
            gap: .35rem
        }

        .total-box .row {
            display: flex;
            justify-content: space-between
        }

        .total-box .label {
            color: var(--muted)
        }

        .total-box .value {
            font-weight: 600
        }

        .actions .btn+.btn {
            margin-left: .5rem
        }

        /* ===== Mobile card list styles ===== */
        .mobile-list {
            display: none;
        }

        .mobile-card {
            border: 1px solid var(--line);
            border-radius: 14px;
            background: linear-gradient(180deg, rgba(14, 21, 37, .98), rgba(14, 21, 37, .92));
            padding: 12px;
        }

        .mobile-card+.mobile-card {
            margin-top: 10px;
        }

        .mobile-card .top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .5rem;
        }

        .mobile-chip {
            border: 1px solid var(--line);
            border-radius: 999px;
            padding: .1rem .6rem;
            color: #cdd6e5;
            font-size: .85rem;
        }

        .mobile-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: .25rem .75rem;
            margin-top: .5rem;
        }

        .mobile-grid-2 .lbl {
            color: var(--muted);
            font-size: .85rem
        }

        .mobile-grid-2 .val {
            text-align: right;
        }

        .mobile-line {
            border-top: 1px dashed var(--line);
            margin: .5rem 0;
            opacity: .7
        }

        /* ===== Mobile enhancements ===== */
        @media (max-width:576px) {
            .actions-desktop {
                display: none !important
            }

            .page-content {
                padding-bottom: calc(80px + env(safe-area-inset-bottom));
            }

            .table-desktop {
                display: none;
            }

            .mobile-list {
                display: block;
            }
        }

        @media (min-width:577px) {
            .table-desktop {
                display: block;
            }
        }

        /* ===== Sticky bar bawah (mobile) ===== */
        .mobile-sticky {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(14, 21, 37, .94);
            -webkit-backdrop-filter: blur(8px);
            backdrop-filter: blur(8px);
            border-top: 1px solid var(--line);
            padding: .6rem .75rem calc(.6rem + env(safe-area-inset-bottom));
            z-index: 50;
            display: none;
        }

        @media (max-width:576px) {
            .mobile-sticky {
                display: block
            }
        }

        .mobile-sticky .bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
        }

        .mobile-sticky .money {
            display: flex;
            flex-direction: column;
            line-height: 1.1;
        }

        .mobile-sticky .money .cap {
            font-size: .75rem;
            color: var(--muted)
        }

        .mobile-sticky .money .val {
            font-weight: 700
        }

        .btn-soft {
            background: rgba(255, 255, 255, .06);
            border: 1px solid var(--line);
            color: var(--text)
        }

        /* ===== Tone khusus RIWAYAT PEMBAYARAN ===== */
        .payments .table-desktop thead th {
            background: var(--thead-pay);
        }

        .payments .table-desktop tbody tr td {
            border-top-color: var(--line-pay);
        }

        .payments .mobile-list .mobile-card {
            background: linear-gradient(180deg, rgba(15, 29, 25, .98), rgba(15, 29, 25, .92));
            border-color: var(--line-pay);
        }

        .payments .mobile-list .mobile-line {
            border-top-color: var(--line-pay);
        }

        .payments .mobile-list .mobile-chip {
            border-color: var(--line-pay);
            color: var(--chip-pay);
        }

        .payments .mobile-list .mono {
            color: #d4fff0;
        }

        @media print {

            .btn,
            .modal,
            .mobile-sticky,
            .mobile-list {
                display: none !important;
            }

            .card {
                border: none
            }

            body {
                background: #fff;
                color: #000
            }
        }
    </style>
@endpush

@section('content')
    <div class="container py-4 page-content">

        {{-- Header --}}
        <div class="card p-3 mb-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <div class="small muted">Faktur</div>
                    <h3 class="mb-0 mono">{{ $invoice->code }}</h3>
                </div>
                <div><span class="badge {{ $badge }}">{{ $invoice->status }}</span></div>
            </div>
        </div>

        {{-- Info utama --}}
        <div class="row g-2 mb-3">
            <div class="col-md-6">
                <div class="card section">
                    <div class="kv mb-2">
                        <div class="label">Supplier</div>
                        <div class="value">{{ $invoice->supplier->store_name ?? '—' }}</div>
                    </div>
                    <div class="kv mb-2">
                        <div class="label">Tanggal</div>
                        <div class="value">{{ \Carbon\Carbon::parse($invoice->date)->format('d/m/Y') }}</div>
                    </div>
                    <div class="kv">
                        <div class="label">Catatan</div>
                        <div class="value">{{ $invoice->note ?: '—' }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card section">
                    <div class="total-box">
                        <div class="row">
                            <div class="label">Total</div>
                            <div class="value mono">Rp {{ number_format($total, 0, ',', '.') }}</div>
                        </div>
                        <div class="row">
                            <div class="label">Dibayar</div>
                            <div class="value mono">Rp {{ number_format($paidTotal, 0, ',', '.') }}</div>
                        </div>
                        <div class="row">
                            <div class="label">Sisa</div>
                            <div class="value mono {{ $outstanding > 0 ? 'text-warning' : '' }}">Rp
                                {{ number_format($outstanding, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ====== RINCIAN ITEM ====== --}}
        <div class="card p-0 mb-3">
            <div class="section pb-0">
                <h5 class="mb-2">Rincian Item</h5>
            </div>

            {{-- Desktop Table --}}
            <div class="table-responsive table-desktop">
                <table class="table table-dark table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width:6%;text-align:center">No</th>
                            <th style="width:14%">Kode</th>
                            <th>Nama</th>
                            <th style="width:10%" class="text-end">Qty</th>
                            <th style="width:10%">Uom</th>
                            <th style="width:14%" class="text-end">Harga</th>
                            <th style="width:16%" class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($invoice->lines as $ln)
                            @php
                                $qty = (float) $ln->qty;
                                $price = (float) $ln->price;
                                $sub = $qty * $price;
                            @endphp
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="mono">{{ $ln->item->code ?? '—' }}</td>
                                <td>{{ $ln->item->name ?? $ln->item_name }}</td>
                                <td class="text-end mono">{{ number_format($qty, 2, ',', '.') }}</td>
                                <td>{{ $ln->unit ?: $ln->item->unit ?? 'pcs' }}</td>
                                <td class="text-end mono">Rp {{ number_format($price, 0, ',', '.') }}</td>
                                <td class="text-end mono">Rp {{ number_format($sub, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">Tidak ada item.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="mobile-list p-3 pt-2">
                @forelse ($invoice->lines as $ln)
                    @php
                        $qty = (float) $ln->qty;
                        $price = (float) $ln->price;
                        $sub = $qty * $price;
                        $uom = $ln->unit ?: $ln->item->unit ?? 'pcs';
                    @endphp
                    <div class="mobile-card">
                        <div class="top">
                            <div class="d-flex align-items-center gap-2">
                                <span class="mobile-chip mono">#{{ $loop->iteration }}</span>
                                <div class="fw-semibold mono">{{ $ln->item->code ?? '—' }}</div>
                            </div>
                        </div>
                        <div class="small muted">{{ $ln->item->name ?? $ln->item_name }}</div>
                        <div class="mobile-line"></div>
                        <div class="mobile-grid-2 mono">
                            <div class="lbl">Qty</div>
                            <div class="val">{{ number_format($qty, 2, ',', '.') }} {{ $uom }}</div>
                            <div class="lbl">Harga</div>
                            <div class="val">Rp {{ number_format($price, 0, ',', '.') }}</div>
                            <div class="lbl">Subtotal</div>
                            <div class="val">Rp {{ number_format($sub, 0, ',', '.') }}</div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted">Tidak ada item.</div>
                @endforelse
            </div>
        </div>

        {{-- ====== RIWAYAT PEMBAYARAN (tema hijau) ====== --}}
        <div class="card p-0 mb-3 payments">
            <div class="section pb-0">
                <h5 class="mb-2">Riwayat Pembayaran</h5>
            </div>

            {{-- Desktop Table --}}
            <div class="table-responsive table-desktop">
                <table class="table table-dark align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width:6%;text-align:center">No</th>
                            <th style="width:14%">Tanggal</th>
                            <th style="width:14%">Akun</th>
                            <th class="text-end" style="width:18%">Jumlah</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($invoice->payments as $p)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ \Carbon\Carbon::parse($p->date)->format('d/m/Y') }}</td>
                                <td class="mono">{{ $p->account }}</td>
                                <td class="text-end mono">Rp {{ number_format($p->amount, 0, ',', '.') }}</td>
                                <td>{{ $p->note ?: '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Belum ada pembayaran.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards (tema hijau) --}}
            <div class="mobile-list p-3 pt-2">
                @forelse ($invoice->payments as $p)
                    <div class="mobile-card">
                        <div class="top">
                            <div class="d-flex align-items-center gap-2">
                                <span class="mobile-chip mono">#{{ $loop->iteration }}</span>
                                <div class="fw-semibold mono">{{ \Carbon\Carbon::parse($p->date)->format('d/m/Y') }}</div>
                            </div>
                            <span class="mobile-chip mono">{{ $p->account }}</span>
                        </div>
                        <div class="mobile-line"></div>
                        <div class="mobile-grid-2 mono">
                            <div class="lbl">Jumlah</div>
                            <div class="val">Rp {{ number_format($p->amount, 0, ',', '.') }}</div>
                            <div class="lbl">Catatan</div>
                            <div class="val"
                                style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:60vw">
                                {{ $p->note ?: '—' }}</div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted">Belum ada pembayaran.</div>
                @endforelse
            </div>
        </div>

        {{-- Aksi desktop --}}
        <div class="d-flex justify-content-between align-items-center actions actions-desktop">
            <a href="{{ route('purchasing.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            <div>
                <div>
                    {{-- Preview (HTML, tab baru) --}}
                    <a class="btn btn-soft" id="btnPrintPreview" href="#" target="_blank" rel="noopener">
                        <i class="bi bi-printer"></i> Preview
                    </a>

                    {{-- PDF (download) --}}
                    <a class="btn btn-outline-light" id="btnPrintPdf" href="#" rel="noopener">
                        <i class="bi bi-file-earmark-pdf"></i> PDF
                    </a>

                    <button class="btn btn-primary" id="btnPay" data-id="{{ $invoice->id }}"
                        data-code="{{ $invoice->code }}" data-date="{{ $invoice->date }}"
                        data-remaining="{{ (int) $outstanding }}" data-total="{{ (int) $total }}"
                        data-paid="{{ (int) $paidTotal }}">
                        <i class="bi bi-cash-coin"></i> Tambah Pembayaran
                    </button>
                </div>

            </div>
        </div>
    </div>

    {{-- Mobile Sticky Bar --}}
    <div class="mobile-sticky">
        <div class="container">
            <div class="bar">
                <div class="money">
                    <span class="cap">Total</span>
                    <span class="val mono">Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>
                <div class="money">
                    <span class="cap">Sisa</span>
                    <span class="val mono {{ $outstanding > 0 ? 'text-warning' : '' }}">Rp
                        {{ number_format($outstanding, 0, ',', '.') }}</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-soft btn-sm" id="btnPrintMobile" title="Cetak" aria-label="Cetak">
                        <i class="bi bi-printer"></i>
                    </button>
                    <button class="btn btn-primary btn-sm" id="btnPayMobile" data-id="{{ $invoice->id }}"
                        data-code="{{ $invoice->code }}" data-date="{{ $invoice->date }}"
                        data-remaining="{{ (int) $outstanding }}" data-total="{{ (int) $total }}"
                        data-paid="{{ (int) $paidTotal }}">
                        <i class="bi bi-cash-coin me-1"></i> Bayar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Tambah Pembayaran --}}
    <div class="modal fade" id="payModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" class="modal-content card" id="payForm"
                action="{{ url('purchasing/' . $invoice->id . '/payments') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <div class="small muted">Faktur</div>
                        <div class="fw-semibold" id="payInvCode">{{ $invoice->code }}</div>
                    </div>
                    <div class="row g-2">
                        <div class="col-sm-6">
                            <label class="form-label">Tanggal</label>
                            <input type="date" class="form-control" name="date" id="payDate"
                                value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Akun</label>
                            <select name="account" class="form-select" id="payAccount" required>
                                <option value="CASH">Cash</option>
                                <option value="JAGO">Jago</option>
                                <option value="BCA">BCA</option>
                                <option value="SEABANK">SeaBank</option>
                                <option value="TRANSFER">Transfer</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-2">
                        <label class="form-label">Jumlah</label>
                        <input type="text" inputmode="numeric" class="form-control text-end" id="payAmountDisplay"
                            placeholder="Rp0">
                        <input type="hidden" name="amount" id="payAmount">
                        <div class="form-text muted">
                            Sisa saat ini: <span class="mono" id="payRemainText">Rp
                                {{ number_format($outstanding, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <div class="mt-2">
                        <label class="form-label">Catatan (opsional)</label>
                        <input type="text" name="note" class="form-control" placeholder="No ref/ket lainnya">
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light">Simpan Pembayaran</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        /* Helpers */
        const rupiah = n => new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(Math.round(n || 0));
        const intFromCur = s => parseInt(String(s || '').replace(/[^\d]/g, '') || '0', 10);

        /* Print */
        document.getElementById('btnPrint')?.addEventListener('click', () => window.print());
        document.getElementById('btnPrintMobile')?.addEventListener('click', () => window.print());

        /* Modal Bayar */
        (function() {
            const payModalEl = document.getElementById('payModal');
            const payForm = document.getElementById('payForm');
            const payInvCode = document.getElementById('payInvCode');
            const payDate = document.getElementById('payDate');
            const payAccount = document.getElementById('payAccount');
            const payAmountDisplay = document.getElementById('payAmountDisplay');
            const payAmount = document.getElementById('payAmount');
            const payRemainText = document.getElementById('payRemainText');

            function setPayAmount(val) {
                const raw = Math.max(0, intFromCur(val));
                payAmount.value = String(raw);
                payAmountDisplay.value = rupiah(raw);
                return raw;
            }
            payAmountDisplay?.addEventListener('input', () => setPayAmount(payAmountDisplay.value));

            function openPay(inv) {
                payInvCode.textContent = inv.code || '—';
                payDate.value = (inv.date || new Date().toISOString().slice(0, 10));
                payAccount.value = 'CASH';
                setPayAmount(inv.remain || 0);
                payRemainText.textContent = rupiah(inv.remain || 0);
                payForm.setAttribute('action', `{{ url('purchasing') }}/${inv.id}/payments`);
                new bootstrap.Modal(payModalEl).show();
            }

            const btnPay = document.getElementById('btnPay');
            btnPay?.addEventListener('click', () => {
                const inv = {
                    id: btnPay.dataset.id,
                    code: btnPay.dataset.code,
                    date: btnPay.dataset.date,
                    total: parseInt(btnPay.dataset.total || '0', 10),
                    paid: parseInt(btnPay.dataset.paid || '0', 10),
                    remain: parseInt(btnPay.dataset.remaining || '0', 10),
                };
                openPay(inv);
            });

            const btnPayMobile = document.getElementById('btnPayMobile');
            btnPayMobile?.addEventListener('click', () => {
                const inv = {
                    id: btnPayMobile.dataset.id,
                    code: btnPayMobile.dataset.code,
                    date: btnPayMobile.dataset.date,
                    total: parseInt(btnPayMobile.dataset.total || '0', 10),
                    paid: parseInt(btnPayMobile.dataset.paid || '0', 10),
                    remain: parseInt(btnPayMobile.dataset.remaining || '0', 10),
                };
                openPay(inv);
            });

            payForm?.addEventListener('submit', (e) => {
                const raw = intFromCur(payAmountDisplay.value);
                const remain = intFromCur(payRemainText.textContent);
                if (raw <= 0) {
                    e.preventDefault();
                    alert('Jumlah pembayaran harus lebih dari 0.');
                    return;
                }
                if (raw > remain) {
                    e.preventDefault();
                    setPayAmount(remain);
                }
            });
        })();
    </script>
@endpush
