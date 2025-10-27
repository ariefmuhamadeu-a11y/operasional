@extends('layouts.app')

@section('title', 'ERP • Master Items')

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

        .mono {
            font-variant-numeric: tabular-nums;
            font-family: ui-monospace, SFMono-Regular, Menlo, monospace
        }

        .muted {
            color: var(--muted)
        }

        .badge-soft {
            background: rgba(96, 165, 250, .15);
            border: 1px solid rgba(96, 165, 250, .25);
            color: #cfe4ff;
        }

        /* ===== Panels ===== */
        .page-wrap {
            display: grid;
            gap: .8rem;
        }

        .panel {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: .8rem;
            padding: 1rem 1.2rem;
        }

        .panel-sub {
            color: var(--muted);
            font-size: .9rem;
        }

        /* ===== Filters ===== */
        .filter-bar .form-control,
        .filter-bar .form-select {
            background: #1b2436;
            border-color: #2a3550;
            color: var(--text);
        }

        .filter-bar .input-group-text {
            background: #1b2436;
            border-color: #2a3550;
            color: #aab4c4;
        }

        /* ===== Desktop table ===== */
        .table-wrap {
            max-height: 65vh;
            overflow: auto;
            border: 1px solid var(--line);
            border-radius: .8rem;
            background: var(--card);
        }

        thead th {
            position: sticky;
            top: 0;
            background: #0e162b;
            border-bottom: 1px solid var(--line);
            z-index: 2;
        }

        tbody tr:hover td {
            background: rgba(255, 255, 255, .03);
        }

        .col-code {
            width: 12rem;
        }

        .col-name {
            width: 26rem;
        }

        .col-class {
            width: 12rem;
        }

        .col-cat {
            width: 20rem;
        }

        .col-uom {
            width: 6rem;
            text-align: center;
        }

        .col-hpp {
            width: 12rem;
            text-align: right;
        }

        .col-act {
            width: 9rem;
            text-align: right;
            white-space: nowrap;
        }

        .truncate {
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* ===== Mobile cards ===== */
        .m-cards .card {
            background: var(--card);
            border-color: var(--line);
        }

        .m-cards .title {
            font-weight: 600;
            line-height: 1.2;
        }

        .m-cards .code {
            font-size: .9rem;
            color: #cbd5e1
        }

        .m-cards .meta {
            color: var(--muted);
            font-size: .9rem
        }

        .m-cards .price {
            font-weight: 600
        }

        .m-cards .btn {
            border-radius: .6rem
        }

        .chip {
            padding: .15rem .5rem;
            border-radius: .6rem;
            border: 1px solid rgba(148, 163, 184, .25);
            color: #cbd5e1;
            font-size: .78rem;
        }

        /* Responsive: sembunyikan kolom kategori di desktop kecil */
        @media (max-width: 1200px) {
            .col-cat {
                display: none;
            }
        }
    </style>
@endpush

@section('content')
    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show mb-3 border-0 shadow-sm">
            <i class="bi bi-check-circle me-2"></i> {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3 border-0 shadow-sm">
            <i class="bi bi-exclamation-triangle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @php
        $total = $items instanceof \Illuminate\Pagination\LengthAwarePaginator ? $items->total() : $items->count();
    @endphp

    <div class="page-wrap">
        {{-- Header --}}
        <div class="panel">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h5 class="mb-1">Master Items</h5>
                    <div class="panel-sub">Kelola kelas, kategori (BJ), UOM, dan HPP</div>
                </div>
                <span class="badge text-bg-secondary">{{ number_format($total, 0, ',', '.') }} item</span>
            </div>
        </div>

        {{-- Filter bar --}}
        <div class="panel filter-bar">
            <form class="row g-2 align-items-end" onsubmit="return false;">
                <div class="col-12 col-lg-6">
                    <label class="form-label mb-1">Cari (Kode / Nama / Kategori)</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input id="q" type="search" class="form-control"
                            placeholder="Contoh: K7BLK / Jogger / Fleece" autocomplete="off">
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <label class="form-label mb-1">Kelas</label>
                    <select id="filterClass" class="form-select">
                        <option value="">Semua Kelas</option>
                        @foreach ($itemClasses as $c)
                            <option value="{{ $c->code }}">{{ $c->name }} ({{ $c->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-lg-3 d-flex gap-2 justify-content-end">
                    <button type="button" id="btnReset" class="btn btn-outline-light"><i class="bi bi-x-circle me-1"></i>
                        Reset</button>
                    <a href="{{ route('items.create') }}" class="btn btn-primary" title="Tambah Item"><i
                            class="bi bi-plus-lg"></i></a>
                </div>
            </form>
        </div>

        {{-- ===== Desktop Tabel ===== --}}
        <div class="table-wrap d-none d-lg-block">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="col-code">Kode</th>
                        <th class="col-name">Nama</th>
                        <th class="col-class">Kelas</th>
                        <th class="col-cat">Kategori Produk (BJ)</th>
                        <th class="col-uom">UOM</th>
                        <th class="col-hpp">HPP</th>
                        <th class="col-act">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tblBody">
                    @forelse ($items as $it)
                        <tr data-class-code="{{ $it->itemClass->code ?? '' }}">
                            <td class="mono fw-semibold">
                                <div class="truncate" title="{{ $it->code }}">{{ $it->code }}</div>
                            </td>
                            <td>
                                <div class="truncate" title="{{ $it->name ?? '-' }}">{{ $it->name ?? '—' }}</div>
                            </td>
                            <td>
                                @if ($it->itemClass)
                                    <span class="badge badge-soft"
                                        title="{{ $it->itemClass->code }}">{{ $it->itemClass->name }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="col-cat">
                                <div class="truncate" title="{{ $it->productCategory->name ?? '-' }}">
                                    {{ $it->productCategory->name ?? '—' }}</div>
                            </td>
                            <td class="mono">{{ $it->uom }}</td>
                            <td class="mono text-end">Rp {{ number_format((int) $it->current_hpp, 0, ',', '.') }}</td>
                            <td class="text-end">
                                <a href="{{ route('items.edit', $it->id) }}" class="btn btn-sm btn-outline-light"
                                    title="Edit"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('items.destroy', $it->id) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Hapus item {{ $it->code }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus"><i
                                            class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">Belum ada data item.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ===== Mobile Cards ===== --}}
        <div id="cardsWrap" class="m-cards d-lg-none">
            @forelse ($items as $it)
                <div class="card mb-2" data-card data-class-code="{{ $it->itemClass->code ?? '' }}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <div>
                                <div class="title">{{ $it->name ?? '—' }}</div>
                                <div class="code mono">{{ $it->code }}</div>
                                <div class="meta mt-1 d-flex flex-wrap gap-1">
                                    @if ($it->itemClass)
                                        <span class="chip" title="Kelas">{{ $it->itemClass->name }}</span>
                                    @endif
                                    @if ($it->productCategory)
                                        <span class="chip" title="Kategori">{{ $it->productCategory->name }}</span>
                                    @endif
                                    <span class="chip mono" title="UOM">{{ $it->uom }}</span>
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="meta">HPP</div>
                                <div class="price mono">Rp {{ number_format((int) $it->current_hpp, 0, ',', '.') }}</div>
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-3">
                            <a href="{{ route('items.edit', $it->id) }}" class="btn btn-outline-light w-100">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <form action="{{ route('items.destroy', $it->id) }}" method="POST" class="w-100"
                                onsubmit="return confirm('Hapus item {{ $it->code }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger w-100">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                            </form>
                        </div>

                        {{-- teks untuk pencarian realtime (agar haystack jelas) --}}
                        <div class="d-none" data-hay>
                            {{ $it->code }} {{ $it->name }} {{ $it->itemClass->name ?? '' }}
                            {{ $it->productCategory->name ?? '' }} {{ $it->uom }}
                        </div>
                    </div>
                </div>
            @empty
                <div class="card">
                    <div class="card-body text-center text-muted">Belum ada data item.</div>
                </div>
            @endforelse
        </div>

        {{-- Footer --}}
        <div class="d-flex align-items-center justify-content-between mt-2">
            <div class="muted small">Total <span class="mono">{{ number_format($total, 0, ',', '.') }}</span> item</div>
            @if ($items instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="mb-0">{{ $items->withQueryString()->links() }}</div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function() {
            const q = document.getElementById('q');
            const ddl = document.getElementById('filterClass');
            const btnReset = document.getElementById('btnReset');

            // Desktop rows
            const rows = Array.from(document.querySelectorAll('#tblBody tr'));
            // Mobile cards
            const cards = Array.from(document.querySelectorAll('#cardsWrap [data-card]'));

            let timer;

            function scheduleApply() {
                clearTimeout(timer);
                timer = setTimeout(apply, 120);
            }

            function apply() {
                const text = (q?.value || '').toLowerCase();
                const cls = (ddl?.value || '').toUpperCase();

                // Filter rows (desktop)
                rows.forEach(tr => {
                    const hay = tr.innerText.toLowerCase();
                    const rowCls = (tr.getAttribute('data-class-code') || '').toUpperCase();
                    const ok = (!text || hay.includes(text)) && (!cls || rowCls === cls);
                    tr.style.display = ok ? '' : 'none';
                });

                // Filter cards (mobile)
                cards.forEach(card => {
                    const hay = (card.querySelector('[data-hay]')?.textContent || '').toLowerCase();
                    const rowCls = (card.getAttribute('data-class-code') || '').toUpperCase();
                    const ok = (!text || hay.includes(text)) && (!cls || rowCls === cls);
                    card.style.display = ok ? '' : 'none';
                });
            }

            q?.addEventListener('input', scheduleApply);
            ddl?.addEventListener('change', apply);
            btnReset?.addEventListener('click', () => {
                if (q) q.value = '';
                if (ddl) ddl.value = '';
                apply();
            });

            apply();
        })();
    </script>
@endpush
