{{-- resources/views/purchasing/index.blade.php --}}
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
            --brand: #60a5fa
        }

        .container-tight {
            max-width: 1200px
        }

        .muted {
            color: var(--muted)
        }

        .mono {
            font-variant-numeric: tabular-nums;
            font-family: ui-monospace, SFMono-Regular, Menlo, monospace
        }

        .card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 14px
        }

        .btn-soft {
            background: rgba(255, 255, 255, .06);
            border: 1px solid var(--line);
            color: var(--text)
        }

        .kpi {
            padding: 1rem
        }

        .kpi .label {
            color: var(--muted);
            font-size: .9rem
        }

        .kpi .value {
            font-size: 1.1rem
        }

        .table thead th {
            background: var(--panel);
            position: sticky;
            top: 0;
            z-index: 2;
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

        .table-wrap {
            overflow: auto;
            border-top: 1px solid var(--line)
        }

        /* ===== Mobile Card List + Swipe ===== */
        .list-mobile {
            display: block
        }

        .swipe-row {
            position: relative;
            overflow: hidden;
            border: 1px solid var(--line);
            border-radius: 14px;
            margin-bottom: 10px
        }

        .swipe-actions {
            position: absolute;
            inset: 0;
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            align-items: stretch;
            padding: 0 8px;
            background: linear-gradient(90deg, rgba(11, 19, 36, 0), rgba(11, 19, 36, .85));
            pointer-events: none
        }

        .swipe-actions .btn {
            pointer-events: auto;
            align-self: center
        }

        .swipe-content {
            position: relative;
            z-index: 2;
            background: transparent;
            transform: translateX(0);
            transition: transform .18s ease
        }

        .row-card {
            padding: 12px;
            display: grid;
            gap: .35rem
        }

        .row-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .5rem
        }

        .code {
            font-weight: 700
        }

        .supplier {
            color: #cfd6e4
        }

        .meta {
            display: flex;
            gap: .75rem;
            flex-wrap: wrap
        }

        .meta .chip {
            border: 1px solid var(--line);
            border-radius: 999px;
            padding: .1rem .6rem;
            color: #cdd6e5;
            font-size: .85rem
        }

        .amounts {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: .25rem;
            align-items: center
        }

        .amounts .label {
            color: var(--muted);
            font-size: .9rem
        }

        .amounts .value {
            justify-self: end
        }

        .highlight-sisa {
            color: #fde68a
        }

        .pay-cta {
            white-space: nowrap
        }

        .infinite-spinner {
            display: flex;
            justify-content: center;
            padding: 10px;
            color: #c2c9d6
        }

        @media (min-width: 576px) {
            .filters-sticky {
                position: sticky;
                top: 0;
                z-index: 3;
                background: linear-gradient(180deg, rgba(14, 21, 37, .97), rgba(14, 21, 37, .92));
                backdrop-filter: blur(6px);
                border: 1px solid var(--line);
                border-radius: 14px
            }
        }
    </style>
@endpush

@section('content')
    <div class="container container-tight py-4">

        {{-- Header --}}
        <div class="card p-3 mb-3 text-center">
            <h2 class="mb-1">Faktur Pembelian</h2>
            <div class="small muted">Kelola faktur, pembayaran, dan status</div>
        </div>

        {{-- Alerts --}}
        @if (session('success'))
            <div class="alert alert-success border border-success-subtle">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger border border-danger-subtle">{{ session('error') }}</div>
        @endif

        {{-- KPI --}}
        <div class="row g-2 mb-3">
            <div class="col-6 col-md-3">
                <div class="card kpi text-center">
                    <div class="label">Jumlah Faktur</div>
                    <div class="value mono">{{ number_format($summary['count'] ?? 0, 0, ',', '.') }}</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card kpi text-center">
                    <div class="label">Total</div>
                    <div class="value mono">Rp {{ number_format($summary['total'] ?? 0, 0, ',', '.') }}</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card kpi text-center">
                    <div class="label">Dibayar</div>
                    <div class="value mono">Rp {{ number_format($summary['paid_total'] ?? 0, 0, ',', '.') }}</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card kpi text-center">
                    <div class="label">Sisa</div>
                    @php $out = (float)($summary['outstanding'] ?? 0); @endphp
                    <div class="value mono {{ $out > 0 ? 'highlight-sisa' : '' }}">Rp {{ number_format($out, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="mb-2 d-sm-none">
            <button class="btn btn-soft w-100" data-bs-toggle="collapse" data-bs-target="#filterSheet">
                <i class="bi bi-funnel me-1"></i> Filter & Sort
            </button>
            <div class="collapse mt-2" id="filterSheet">
                <div class="card p-3">
                    @include('purchasing.partials.filters')
                </div>
            </div>
        </div>
        <div class="filters-sticky p-3 d-none d-sm-block mb-3">
            @include('purchasing.partials.filters')
        </div>

        {{-- ===== Mobile: Swipe + Infinite Scroll ===== --}}
        <div class="d-sm-none">
            <div id="mobileList" class="list-mobile">
                @forelse ($invoices as $row)
                    @php
                        $sisa = max(0, (float) $row->total - (float) $row->paid_total);
                        $dateTxt = \Carbon\Carbon::parse($row->date)->format('d/m/Y');
                        $badge = match ($row->status) {
                            'DRAFT' => 'badge-draft',
                            'TERBIT' => 'badge-terbit',
                            'SEBAGIAN' => 'badge-sebagian',
                            'LUNAS' => 'badge-lunas',
                            default => 'badge-terbit',
                        };
                    @endphp
                    <div class="swipe-row" data-id="{{ $row->id }}">
                        <div class="swipe-actions">
                            <button class="btn btn-outline-secondary btn-sm" data-action="detail"
                                data-id="{{ $row->id }}">
                                <i class="bi bi-eye"></i> Detail
                            </button>
                            <button class="btn btn-soft btn-sm" data-action="pay" data-id="{{ $row->id }}"
                                data-code="{{ $row->code }}" data-date="{{ $row->date }}"
                                data-remaining="{{ (int) $sisa }}" data-total="{{ (int) $row->total }}"
                                data-paid="{{ (int) $row->paid_total }}">
                                <i class="bi bi-cash-coin"></i> Bayar
                            </button>
                        </div>
                        <div class="swipe-content">
                            <div class="row-card">
                                <div class="row-top">
                                    <div class="code mono">{{ $row->code }}</div>
                                    <span class="badge {{ $badge }}">{{ $row->status }}</span>
                                </div>
                                <div class="supplier small">{{ $row->supplier->store_name ?? '—' }}</div>
                                <div class="meta small">
                                    <span class="chip"><i class="bi bi-calendar2 me-1"></i>{{ $dateTxt }}</span>
                                    <span class="chip mono">Total: Rp {{ number_format($row->total, 0, ',', '.') }}</span>
                                </div>
                                <div class="amounts mt-1">
                                    <div class="label">Dibayar</div>
                                    <div class="value mono">Rp {{ number_format($row->paid_total, 0, ',', '.') }}</div>
                                    <div class="label">Sisa</div>
                                    <div class="value mono {{ $sisa > 0 ? 'highlight-sisa' : '' }}">Rp
                                        {{ number_format($sisa, 0, ',', '.') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="card p-4 text-center text-muted">Tidak ada data.</div>
                @endforelse
            </div>

            {{-- Pagination container (hidden) + sentinel --}}
            <div id="mobilePagination" class="d-none">
                {{ $invoices->onEachSide(1)->links() }}
            </div>
            <div id="infiniteSentinel" class="infinite-spinner">
                <div class="spinner-border spinner-border-sm" role="status"></div>
                <span class="ms-2">Memuat...</span>
            </div>
        </div>

        {{-- ===== Desktop Table ===== --}}
        <div class="card p-0 d-none d-sm-block">
            <div class="table-wrap">
                <table class="table table-dark table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width:18%">Kode</th>
                            <th>Supplier</th>
                            <th style="width:12%">Tanggal</th>
                            <th style="width:12%" class="text-end">Total</th>
                            <th style="width:12%" class="text-end">Dibayar</th>
                            <th style="width:12%" class="text-end">Sisa</th>
                            <th style="width:10%">Status</th>
                            <th style="width:14%" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="desktopTableBody">
                        @forelse ($invoices as $row)
                            @php
                                $sisa = max(0, (float) $row->total - (float) $row->paid_total);
                                $dateTxt = \Carbon\Carbon::parse($row->date)->format('d/m/Y');
                                $badge = match ($row->status) {
                                    'DRAFT' => 'badge-draft',
                                    'TERBIT' => 'badge-terbit',
                                    'SEBAGIAN' => 'badge-sebagian',
                                    'LUNAS' => 'badge-lunas',
                                    default => 'badge-terbit',
                                };
                            @endphp
                            <tr>
                                <td class="mono">{{ $row->code }}</td>
                                <td>{{ $row->supplier->store_name ?? '—' }}</td>
                                <td>{{ $dateTxt }}</td>
                                <td class="text-end mono">Rp {{ number_format($row->total, 0, ',', '.') }}</td>
                                <td class="text-end mono">Rp {{ number_format($row->paid_total, 0, ',', '.') }}</td>
                                <td class="text-end mono {{ $sisa > 0 ? 'text-warning' : '' }}">Rp
                                    {{ number_format($sisa, 0, ',', '.') }}</td>
                                <td><span class="badge {{ $badge }}">{{ $row->status }}</span></td>
                                <td class="text-end">
                                    <button class="btn btn-soft btn-sm" data-action="pay" data-id="{{ $row->id }}"
                                        data-code="{{ $row->code }}" data-date="{{ $row->date }}"
                                        data-remaining="{{ (int) $sisa }}" data-total="{{ (int) $row->total }}"
                                        data-paid="{{ (int) $row->paid_total }}">
                                        <i class="bi bi-cash-coin me-1"></i> Bayar
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">Tidak ada data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($invoices->hasPages())
                <div class="p-3 border-top d-flex justify-content-between align-items-center">
                    <div class="small muted">Menampilkan {{ $invoices->firstItem() }}–{{ $invoices->lastItem() }} dari
                        {{ $invoices->total() }}</div>
                    <div id="desktopPagination">{{ $invoices->onEachSide(1)->links() }}</div>
                </div>
            @endif
        </div>

    </div>

    {{-- Modal Tambah Pembayaran --}}
    <div class="modal fade" id="payModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" class="modal-content card" id="payForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <div class="small muted">Faktur</div>
                        <div class="fw-semibold" id="payInvCode">—</div>
                    </div>
                    <div class="row g-2">
                        <div class="col-sm-6">
                            <label class="form-label">Tanggal</label>
                            <input type="date" class="form-control" name="date" id="payDate" required>
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
                            Sisa saat ini: <span class="mono" id="payRemainText">Rp0</span>
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
        /* ===== Helpers ===== */
        const rupiah = n => new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(Math.round(n || 0));
        const intFromCur = s => parseInt(String(s || '').replace(/[^\d]/g, '') || '0', 10);

        /* ===== Modal Bayar ===== */
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
        payAmountDisplay.addEventListener('input', () => setPayAmount(payAmountDisplay.value));

        function openPayModal(inv) {
            payInvCode.textContent = inv.code || '—';
            payDate.value = (inv.date || new Date().toISOString().slice(0, 10));
            payAccount.value = 'CASH';
            setPayAmount(inv.remain || 0);
            payRemainText.textContent = rupiah(inv.remain || 0);
            payForm.setAttribute('action', `{{ url('purchasing') }}/${inv.id}/payments`);
            new bootstrap.Modal(payModalEl).show();
        }

        function bindPayButtons(root = document) {
            root.querySelectorAll('[data-action="pay"]').forEach(btn => {
                btn.addEventListener('click', () => {
                    const inv = {
                        id: btn.dataset.id,
                        code: btn.dataset.code,
                        date: btn.dataset.date,
                        total: parseInt(btn.dataset.total || '0', 10),
                        paid: parseInt(btn.dataset.paid || '0', 10),
                        remain: parseInt(btn.dataset.remaining || '0', 10),
                    };
                    openPayModal(inv);
                });
            });
        }
        bindPayButtons(document);

        payForm.addEventListener('submit', (e) => {
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

        /* ===== Swipe Row (Mobile) ===== */
        const openOffset = 140; // px content digeser untuk nampilin actions
        function setupSwipeRow(row) {
            const content = row.querySelector('.swipe-content');
            let startX = 0,
                currentX = 0,
                isDragging = false,
                opened = false;

            function setX(x) {
                content.style.transform = `translateX(${x}px)`;
            }

            function open() {
                setX(-openOffset);
                opened = true;
            }

            function close() {
                setX(0);
                opened = false;
            }

            row.addEventListener('touchstart', (e) => {
                startX = e.touches[0].clientX;
                isDragging = true;
            }, {
                passive: true
            });

            row.addEventListener('touchmove', (e) => {
                if (!isDragging) return;
                currentX = e.touches[0].clientX;
                const dx = currentX - startX;
                if (dx < 0) { // geser ke kiri
                    e.preventDefault();
                    setX(Math.max(-openOffset, dx));
                } else { // geser kanan untuk close
                    if (opened) {
                        e.preventDefault();
                        setX(Math.min(0, -openOffset + dx));
                    }
                }
            }, {
                passive: false
            });

            row.addEventListener('touchend', () => {
                if (!isDragging) return;
                isDragging = false;
                const matrix = new WebKitCSSMatrix(getComputedStyle(content).transform);
                const tx = matrix.m41; // translateX
                if (tx < -openOffset / 2) {
                    open();
                } else {
                    close();
                }
            });

            // tap di luar untuk close
            document.addEventListener('touchstart', (e) => {
                if (!row.contains(e.target) && opened) {
                    close();
                }
            }, {
                passive: true
            });
        }

        document.querySelectorAll('.swipe-row').forEach(setupSwipeRow);

        /* ===== Infinite Scroll (Mobile) ===== */
        const sentinel = document.getElementById('infiniteSentinel');
        const mobileList = document.getElementById('mobileList');
        const hiddenPag = document.getElementById('mobilePagination');
        let isLoading = false,
            nextUrl = (() => {
                const a = hiddenPag?.querySelector('a[rel="next"]');
                return a ? a.href : null;
            })();

        const io = ('IntersectionObserver' in window) ? new IntersectionObserver(async (entries) => {
            for (const ent of entries) {
                if (ent.isIntersecting && !isLoading && nextUrl) {
                    isLoading = true;
                    try {
                        const res = await fetch(nextUrl, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const html = await res.text();
                        // Parse dokumen hasil
                        const doc = new DOMParser().parseFromString(html, 'text/html');

                        // Ambil kartu mobile baru
                        doc.querySelectorAll('#mobileList .swipe-row').forEach(node => {
                            const imported = document.importNode(node, true);
                            mobileList.appendChild(imported);
                            setupSwipeRow(imported);
                            bindPayButtons(imported);
                        });

                        // Update next link
                        const nextA = doc.querySelector('#mobilePagination a[rel="next"]');
                        nextUrl = nextA ? nextA.href : null;

                        // Jika tidak ada next, sembunyikan sentinel
                        if (!nextUrl) {
                            sentinel.style.display = 'none';
                        }
                    } catch (err) {
                        console.error(err);
                        // hentikan infinite jika error
                        nextUrl = null;
                        sentinel.style.display = 'none';
                    } finally {
                        isLoading = false;
                    }
                }
            }
        }, {
            rootMargin: '200px 0px'
        }) : null;

        if (io && nextUrl) {
            io.observe(sentinel);
        } else {
            sentinel.style.display = 'none';
        }
    </script>
@endpush
