{{-- resources/views/purchasing/create.blade.php --}}
@extends('layouts.app')
@section('title', 'ERP • Faktur Pembelian')

@push('head')
    <style>
        :root {
            --panel: #0f172a;
            --card: #0e1525;
            --line: #1e2a3f;
            --muted: #9aa4b2;
            --text: #e6ebf1;
            --accent: #60a5fa
        }

        .muted {
            color: var(--muted)
        }

        .btn-soft {
            background: rgba(255, 255, 255, .06);
            border: 1px solid var(--line);
            color: var(--text)
        }

        .table>thead th {
            color: #aab2bd
        }

        .border-secondary-subtle {
            border-color: var(--line) !important
        }

        .mono {
            font-variant-numeric: tabular-nums;
            font-family: ui-monospace, SFMono-Regular, Menlo, monospace
        }

        @media (max-width:576px) {

            .form-control,
            .form-select {
                padding: .6rem .75rem;
                font-size: 1rem
            }

            .btn {
                padding: .6rem .9rem
            }

            #tblItems th,
            #tblItems td {
                padding: .65rem .5rem !important
            }

            .suggest-item {
                padding: .7rem .85rem
            }

            .actions-sticky {
                position: sticky;
                bottom: 10px;
                display: flex;
                justify-content: flex-end;
                gap: .5rem;
                backdrop-filter: blur(6px)
            }
        }

        .itemInput::placeholder {
            color: #cfe4ff;
            opacity: .95;
            font-weight: 700;
            letter-spacing: .02em;
            text-transform: uppercase
        }

        #tblItems td {
            padding-top: .5rem;
            padding-bottom: .35rem;
            vertical-align: top
        }

        .table-responsive {
            overflow: visible
        }

        .suggest-wrap {
            position: relative
        }

        .suggest-menu {
            position: absolute;
            inset-inline: 0;
            top: calc(100% + 6px);
            background: #0b1324;
            border: 1px solid var(--line);
            border-radius: .75rem;
            z-index: 1000;
            max-height: 300px;
            overflow: auto;
            box-shadow: 0 10px 28px rgba(0, 0, 0, .45);
            display: none
        }

        .suggest-item {
            padding: .6rem .9rem;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            gap: .75rem
        }

        .suggest-item:hover,
        .suggest-item.active {
            background: rgba(96, 165, 250, .12)
        }

        .suggest-item .label {
            color: var(--text);
            font-weight: 600
        }

        .suggest-item .code {
            color: var(--muted);
            font-size: .9rem;
            white-space: nowrap
        }

        .suggest-empty {
            padding: .75rem .95rem;
            color: var(--muted)
        }

        .last-price {
            margin-top: .3rem;
            font-size: .85rem;
            color: var(--muted);
            line-height: 1.15
        }
    </style>
@endpush

@section('content')
    <div class="container py-4">

        {{-- ===== Header ===== --}}
        <div class="card border-secondary-subtle mb-4 border p-4 text-center">
            <h2 class="mb-1">Faktur Pembelian</h2>
            <h5 class="text-secondary mb-0">No:
                <span class="text-light fw-semibold">{{ $invoiceNo }}</span>
            </h5>
            <div class="small muted">Format: INV-BKU-YYYYMMDD-##### (auto)</div>
        </div>

        {{-- ===== Error Bag ===== --}}
        @if ($errors->any())
            <div class="alert alert-danger border border-danger-subtle">
                <div class="fw-semibold mb-1">Periksa kembali input Anda:</div>
                <ul class="mb-0">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('purchasing.store') }}" method="POST" autocomplete="off">
            @csrf

            {{-- ===== Form Meta ===== --}}
            <div class="card mb-3 p-3">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Transaksi</label>
                        <input type="date" class="form-control" name="date"
                            value="{{ old('date', now()->toDateString()) }}" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Jatuh Tempo (opsional)</label>
                        <input type="date" class="form-control" name="due_date" value="{{ old('due_date') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Supplier</label>
                        <select class="form-select" name="supplier_id" id="supplierSelect" required>
                            <option value="">Pilih Supplier...</option>
                            @foreach ($suppliers as $s)
                                <option value="{{ $s->id }}" data-class-id="{{ $s->item_class_id }}"
                                    {{ (string) old('supplier_id') === (string) $s->id ? 'selected' : '' }}>
                                    {{ $s->store_name }} ({{ $s->itemClass->code ?? '' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Metode Pembayaran</label>
                        <select class="form-select" name="payment_method">
                            @php $pm = old('payment_method','CASH'); @endphp
                            <option value="CASH" {{ $pm === 'CASH' ? 'selected' : '' }}>Cash</option>
                            <option value="TRANSFER" {{ $pm === 'TRANSFER' ? 'selected' : '' }}>Transfer</option>
                            <option value="TERM30" {{ $pm === 'TERM30' ? 'selected' : '' }}>Term 30 Hari</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">OTENG / Ongkir</label>
                        {{-- tampilan --}}
                        <input type="text" inputmode="numeric" class="form-control text-end" id="other_costs_display"
                            value="{{ old('other_costs', 0) }}">
                        {{-- server --}}
                        <input type="hidden" name="other_costs" id="other_costs" value="{{ old('other_costs', 0) }}">
                        <div class="form-text muted">Masuk HPP invoice</div>
                    </div>
                </div>
            </div>

            {{-- ===== Lines ===== --}}
            <div class="card mb-3 p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h6 class="mb-0">Daftar Item</h6>
                    <button type="button" class="btn btn-soft btn-sm" id="btnAdd" disabled>
                        <i class="bi bi-plus-lg me-1"></i>Tambah Baris
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-dark table-sm align-middle mb-0" id="tblItems">
                        <thead>
                            <tr class="text-secondary">
                                <th style="width:44%">Item</th>
                                <th style="width:18%" class="text-end">Qty</th>
                                <th style="width:22%" class="text-end">Harga</th>
                                <th style="width:8%" class="text-end">Subtotal</th>
                                <th style="width:8%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="empty-state">
                                <td colspan="5" class="text-center text-muted py-4">
                                    <div class="small">Pilih <strong>Supplier</strong> terlebih dahulu.</div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ===== Notes & Summary ===== --}}
            <div class="row g-3">
                <div class="col-lg-7">
                    <div class="card h-100 p-3">
                        <label class="form-label">Catatan (opsional)</label>
                        <textarea class="form-control" name="note" rows="3">{{ old('note') }}</textarea>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="card p-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="muted">Total Barang</span><strong id="totalItems">0</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="muted">Subtotal</span><strong id="subtotalText" class="mono">Rp0</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="muted">Ongkir</span><strong id="shippingText" class="mono">Rp0</strong>
                        </div>

                        {{-- ===== Dibayar Sekarang ===== --}}
                        <div class="mb-2">
                            <label class="form-label mb-1">Dibayar Sekarang (opsional)</label>
                            {{-- tampilan --}}
                            <input type="text" inputmode="numeric" class="form-control text-end" id="paidNow_display"
                                value="{{ old('paid_now', 0) }}">
                            {{-- server --}}
                            <input type="hidden" name="paid_now" id="paidNow" value="{{ old('paid_now', 0) }}">

                            <div class="row mt-2 g-2">
                                <div class="col-6 d-flex justify-content-between">
                                    <span class="muted">Sisa</span><strong id="remainingText"
                                        class="mono">Rp0</strong>
                                </div>
                                <div class="col-6">
                                    <select class="form-select form-select-sm" name="paid_account">
                                        @php $acc = old('paid_account','CASH'); @endphp
                                        <option value="CASH" {{ $acc === 'CASH' ? 'selected' : '' }}>Cash</option>
                                        <option value="JAGO" {{ $acc === 'JAGO' ? 'selected' : '' }}>Jago</option>
                                        <option value="BCA" {{ $acc === 'BCA' ? 'selected' : '' }}>BCA</option>
                                        <option value="SEABANK" {{ $acc === 'SEABANK' ? 'selected' : '' }}>SeaBank</option>
                                        <option value="TRANSFER" {{ $acc === 'TRANSFER' ? 'selected' : '' }}>Transfer</option>
                                    </select>
                                    <input type="text" class="form-control form-control-sm mt-2" name="paid_note"
                                        placeholder="Catatan pembayaran (opsional)" value="{{ old('paid_note') }}">
                                </div>
                            </div>
                        </div>

                        <hr class="border-secondary">
                        <div class="d-flex justify-content-between fs-5">
                            <span>Grand Total</span><strong id="grandTotalText" class="mono">Rp0</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="actions-sticky d-flex justify-content-end mt-3 gap-2">
                <a href="{{ route('purchasing.index') }}" class="btn btn-outline-secondary">Batal</a>
                <button type="submit" class="btn btn-light">Simpan Faktur</button>
            </div>

            {{-- ===== Row Template ===== --}}
            <template id="rowTemplate">
                <tr>
                    <td>
                        <div class="suggest-wrap">
                            <input class="form-control form-control-sm itemInput" placeholder="TEKAN F2 — LIST ITEM"
                                autocomplete="off">
                            <div class="suggest-menu"></div>
                        </div>
                        {{-- hidden fields sesuai validator --}}
                        <input type="hidden" class="itemIdInput" name="lines[][item_id]">
                        <input type="hidden" class="itemNameInput" name="lines[][item_name]" required>
                        <input type="hidden" class="classIdInput" name="lines[][item_class_id]" required>
                        <input type="hidden" class="unitInput" name="lines[][unit]" value="pcs">
                    </td>

                    <td>
                        {{-- qty tampil --}}
                        <input type="text" class="form-control form-control-sm text-end qtyInput" placeholder="0,00"
                            inputmode="decimal">
                        {{-- qty server --}}
                        <input type="hidden" class="qtyRawInput" name="lines[][qty]" required>
                    </td>

                    <td>
                        {{-- harga tampil --}}
                        <input type="text" class="form-control form-control-sm text-end priceInput" placeholder="Rp0">
                        {{-- harga server --}}
                        <input type="hidden" class="priceRawInput" name="lines[][price]" required min="0">
                        <div class="last-price">Harga terakhir: <span class="lastPriceText">—</span></div>
                    </td>

                    <td class="text-end mono">
                        <span class="subtotalCell">Rp0</span>
                    </td>

                    <td class="text-end">
                        <button type="button" class="btn btn-outline-danger btn-sm btnDel" title="Hapus baris">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </td>
                </tr>
            </template>
        </form>
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
        const formatQtyValue = num => Number(num || 0).toLocaleString('id-ID', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        const parseQtyTextToNumber = txt => {
            if (!txt) return 0;
            const normalized = String(txt).replace(/\./g, '').replace(',', '.').trim();
            const f = parseFloat(normalized);
            return isNaN(f) ? 0 : f;
        };

        /* ===== Data dari controller ===== */
        const ITEMS = @json($items);
        const LAST_PRICES = @json($lastPrices ?? []);
        const LAST_BY_SUP = @json($lastPricesBySupplier ?? []);
        const OLD_LINES = @json(old('lines', []));

        /* ===== Refs ===== */
        const $tbody = document.querySelector('#tblItems tbody');
        const $rowTpl = document.getElementById('rowTemplate');
        const $btnAdd = document.getElementById('btnAdd');
        const $supplier = document.getElementById('supplierSelect');
        const $form = document.querySelector('form[action="{{ route('purchasing.store') }}"]');

        const $otherDisp = document.getElementById('other_costs_display');
        const $otherHidden = document.getElementById('other_costs');

        const $paidNowDisp = document.getElementById('paidNow_display');
        const $paidNow = document.getElementById('paidNow');

        const isMobile = (('ontouchstart' in window) || navigator.maxTouchPoints > 0 || window.matchMedia(
            '(max-width: 768px)').matches);
        const activeSupplierClassId = () => $supplier.value ? parseInt($supplier.selectedOptions[0].dataset.classId) : null;
        const activeSupplierId = () => $supplier.value ? parseInt($supplier.value) : null;

        const norm = s => String(s || '').toLowerCase().trim();
        const labelOf = i => `${i.name}${i.code?` (${i.code})`:''}`;
        const findItems = (classId, q) => {
            let pool = classId ? ITEMS.filter(i => i.item_class_id === classId) : ITEMS;
            q = norm(q);
            return q ? pool.filter(i => norm(i.name).includes(q) || norm(i.code).includes(q)) : pool.slice(0, 50);
        };
        const resolveItem = (t, classId) => {
            t = norm(t);
            if (!t) return null;
            const pool = classId ? ITEMS.filter(i => i.item_class_id === classId) : ITEMS;
            return pool.find(i => norm(labelOf(i)) === t || norm(i.code) === t || norm(i.name) === t) || null;
        };

        /* ===== other_costs & paid_now sync ===== */
        function syncOtherCosts() {
            const raw = intFromCur($otherDisp.value);
            $otherHidden.value = String(raw);
            $otherDisp.value = rupiah(raw);
        }
        $otherDisp.addEventListener('input', () => {
            syncOtherCosts();
            recalcSummary();
        });
        syncOtherCosts();

        function syncPaidNow() {
            const raw = intFromCur($paidNowDisp.value);
            $paidNow.value = String(raw);
            $paidNowDisp.value = rupiah(raw);
            return raw;
        }
        $paidNowDisp?.addEventListener('input', () => {
            syncPaidNow();
            recalcSummary();
        });
        syncPaidNow();

        /* ===== Utility ===== */
        function setClassIdOnAllRows() {
            const cid = activeSupplierClassId() || '';
            $tbody.querySelectorAll('tr:not(.empty-state) .classIdInput').forEach(h => h.value = cid);
        }

        function addRow(prefill = null) {
            if (!activeSupplierClassId() && !prefill) {
                $supplier.focus();
                return;
            }
            $tbody.querySelector('.empty-state')?.remove();

            const tr = $rowTpl.content.cloneNode(true).querySelector('tr');
            const input = tr.querySelector('.itemInput');
            const menu = tr.querySelector('.suggest-menu');
            const qty = tr.querySelector('.qtyInput');
            const qtyRaw = tr.querySelector('.qtyRawInput');
            const price = tr.querySelector('.priceInput');
            const priceRaw = tr.querySelector('.priceRawInput');
            const subCell = tr.querySelector('.subtotalCell');
            const lastTxt = tr.querySelector('.lastPriceText');

            // hidden fields
            const hidItemId = tr.querySelector('.itemIdInput');
            const hidItemName = tr.querySelector('.itemNameInput');
            const hidClassId = tr.querySelector('.classIdInput');
            const hidUnit = tr.querySelector('.unitInput');

            // default class id mengikuti supplier (kecuali ada prefill)
            hidClassId.value = prefill?.item_class_id ?? (activeSupplierClassId() || '');

            let cache = [],
                activeIdx = -1,
                visible = false;

            const setLast = (itemId) => {
                const sup = activeSupplierId();
                const bySup = (sup && LAST_BY_SUP[sup]) ? LAST_BY_SUP[sup][itemId] : null;
                const any = LAST_PRICES[itemId] ?? null;
                const v = bySup ?? any;
                lastTxt.textContent = (v && v > 0) ? rupiah(v) : 'Belum ada';
            };

            const openMenu = (list) => {
                menu.innerHTML = list.length ?
                    list.map((it, idx) => `
        <div class="suggest-item ${idx===activeIdx?'active':''}" data-index="${idx}">
          <span class="label">${it.name}</span>
          <span class="code">${it.code||''}</span>
        </div>`).join('') :
                    `<div class="suggest-empty">Tidak ada hasil</div>`;
                menu.style.display = 'block';
                visible = true;
            };
            const closeMenu = () => {
                menu.style.display = 'none';
                visible = false;
            };

            input.addEventListener('focus', () => {
                if (isMobile) {
                    cache = findItems(activeSupplierClassId(), input.value);
                    activeIdx = cache.length ? 0 : -1;
                    openMenu(cache);
                }
            });
            input.addEventListener('input', () => {
                cache = findItems(activeSupplierClassId(), input.value);
                activeIdx = cache.length ? 0 : -1;
                if (isMobile || visible) openMenu(cache);
                hidItemId.value = '';
                hidItemName.value = '';
                hidUnit.value = 'pcs';
                lastTxt.textContent = '—';
            });
            input.addEventListener('keydown', (e) => {
                if (!isMobile && e.key === 'F2') {
                    e.preventDefault();
                    cache = findItems(activeSupplierClassId(), input.value);
                    activeIdx = cache.length ? 0 : -1;
                    openMenu(cache);
                    return;
                }
                if (!visible && !isMobile) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        const it = resolveItem(input.value, activeSupplierClassId());
                        if (it) {
                            input.value = labelOf(it);
                            hidItemId.value = it.id;
                            hidItemName.value = it.name;
                            hidUnit.value = it.unit || 'pcs';
                            setLast(it.id);
                            qty.focus();
                        } else {
                            hidItemId.value = '';
                            hidItemName.value = (input.value || '').trim();
                            hidUnit.value = 'pcs';
                            qty.focus();
                        }
                    }
                    if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                        cache = findItems(activeSupplierClassId(), input.value);
                        activeIdx = cache.length ? 0 : -1;
                        openMenu(cache);
                        e.preventDefault();
                    }
                    return;
                }
                if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                    e.preventDefault();
                    if (!cache.length) return;
                    activeIdx = (e.key === 'ArrowDown') ? Math.min(cache.length - 1, Math.max(0, activeIdx + 1)) :
                        Math.max(0, activeIdx - 1);
                    openMenu(cache);
                } else if (e.key === 'Enter') {
                    if (activeIdx >= 0 && cache[activeIdx]) {
                        e.preventDefault();
                        const it = cache[activeIdx];
                        input.value = labelOf(it);
                        hidItemId.value = it.id;
                        hidItemName.value = it.name;
                        hidUnit.value = it.unit || 'pcs';
                        setLast(it.id);
                        closeMenu();
                        qty.focus();
                    }
                } else if (e.key === 'Escape') {
                    closeMenu();
                }
            });
            menu.addEventListener('click', ev => {
                const el = ev.target.closest('.suggest-item');
                if (!el) return;
                const it = cache[parseInt(el.dataset.index)];
                input.value = labelOf(it);
                hidItemId.value = it.id;
                hidItemName.value = it.name;
                hidUnit.value = it.unit || 'pcs';
                setLast(it.id);
                closeMenu();
                qty.focus();
            });
            input.addEventListener('blur', () => {
                setTimeout(() => {
                    if (menu.contains(document.activeElement)) return;
                    if (!hidItemId.value) {
                        const it = resolveItem(input.value, activeSupplierClassId());
                        if (it) {
                            input.value = labelOf(it);
                            hidItemId.value = it.id;
                            hidItemName.value = it.name;
                            hidUnit.value = it.unit || 'pcs';
                            setLast(it.id);
                        } else {
                            hidItemName.value = (input.value || '').trim();
                        }
                    }
                    if (!isMobile) closeMenu();
                }, 120);
            });

            // qty sync
            const syncQty = () => {
                const n = parseQtyTextToNumber(qty.value);
                qtyRaw.value = (n > 0) ? n : '';
            };
            qty.addEventListener('focus', () => qty.select?.());
            qty.addEventListener('input', () => {
                syncQty();
                recalcRow();
                recalcSummary();
            });
            qty.addEventListener('blur', () => {
                syncQty();
                qty.value = qty.value ? formatQtyValue(parseQtyTextToNumber(qty.value)) : '';
                recalcRow();
                recalcSummary();
            });

            // price sync
            const normPrice = () => {
                const raw = intFromCur(price.value);
                price.value = rupiah(raw);
                priceRaw.value = raw;
                return raw;
            };
            price.addEventListener('focus', () => price.select?.());
            price.addEventListener('input', () => {
                normPrice();
                recalcRow();
                recalcSummary();
            });
            price.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    addRow();
                }
            });

            function recalcRow() {
                const q = parseQtyTextToNumber(qty.value);
                const p = intFromCur(price.value);
                const st = Math.round(q * p);
                subCell.textContent = rupiah(st);
            }

            tr.querySelector('.btnDel')?.addEventListener('click', () => {
                tr.remove();
                recalcSummary();
                if (!$tbody.querySelector('tr')) {
                    $tbody.innerHTML =
                        `<tr class="empty-state"><td colspan="5" class="text-center text-muted py-4"><div class="small">Pilih <strong>Supplier</strong> terlebih dahulu.</div></td></tr>`;
                }
            });

            // Prefill (old)
            if (prefill) {
                if (prefill.item_id) {
                    const it = ITEMS.find(x => String(x.id) === String(prefill.item_id));
                    if (it) {
                        input.value = labelOf(it);
                        hidItemId.value = it.id;
                        hidItemName.value = it.name;
                        hidUnit.value = it.unit || 'pcs';
                        setLast(it.id);
                    } else {
                        input.value = prefill.item_name || '';
                        hidItemId.value = '';
                        hidItemName.value = prefill.item_name || '';
                        hidUnit.value = prefill.unit || 'pcs';
                    }
                } else {
                    input.value = prefill.item_name || '';
                    hidItemName.value = prefill.item_name || '';
                    hidUnit.value = prefill.unit || 'pcs';
                }
                if (prefill.item_class_id) hidClassId.value = prefill.item_class_id;

                const qn = parseFloat(prefill.qty || 0);
                qty.value = qn ? formatQtyValue(qn) : '';
                qtyRaw.value = qn || '';
                const pn = parseInt(prefill.price || 0, 10);
                price.value = rupiah(pn);
                priceRaw.value = pn;
                recalcRow();
            } else {
                qtyRaw.value = '';
                priceRaw.value = 0;
            }

            $tbody.appendChild(tr);
            recalcSummary();
            if (!prefill) {
                input.focus();
                input.select?.();
                if (isMobile) {
                    let cache = findItems(activeSupplierClassId(), input.value);
                    let activeIdx = cache.length ? 0 : -1;
                    openMenu(cache);
                }
            }
        }

        function recalcSummary() {
            const rows = $tbody.querySelectorAll('tr:not(.empty-state)');
            document.getElementById('totalItems').textContent = rows.length;
            let subtotal = 0;
            rows.forEach(r => {
                const q = parseQtyTextToNumber(r.querySelector('.qtyInput')?.value);
                const p = intFromCur(r.querySelector('.priceInput')?.value || 0);
                subtotal += Math.round(q * p);
            });
            const ship = intFromCur(document.getElementById('other_costs_display').value);
            const grand = subtotal + ship;

            document.getElementById('subtotalText').textContent = rupiah(subtotal);
            document.getElementById('shippingText').textContent = rupiah(ship);
            document.getElementById('grandTotalText').textContent = rupiah(grand);

            const paid = intFromCur($paidNowDisp?.value || 0);
            const remaining = Math.max(0, grand - paid);
            const $rem = document.getElementById('remainingText');
            if ($rem) $rem.textContent = rupiah(remaining);
        }

        function handleSupplierChange() {
            const ok = !!activeSupplierClassId();
            $btnAdd.disabled = !ok;
            if (!ok) {
                $tbody.innerHTML =
                    `<tr class="empty-state"><td colspan="5" class="text-center text-muted py-4"><div class="small">Pilih <strong>Supplier</strong> terlebih dahulu.</div></td></tr>`;
                recalcSummary();
                return;
            }
            setClassIdOnAllRows();
            if (!$tbody.querySelector('tr:not(.empty-state)')) addRow();
        }
        document.getElementById('btnAdd').addEventListener('click', addRow);
        document.getElementById('supplierSelect').addEventListener('change', handleSupplierChange);
        document.getElementById('other_costs_display').addEventListener('input', recalcSummary);

        /* Restore old lines jika validasi gagal */
        (function restoreIfAny() {
            if (OLD_LINES && OLD_LINES.length) {
                $btnAdd.disabled = !activeSupplierClassId();
                $tbody.innerHTML = '';
                OLD_LINES.forEach(line => addRow(line));
                setClassIdOnAllRows();
                recalcSummary();
            } else {
                handleSupplierChange();
            }
        })();

        /* ===== REINDEX & GUARD SUBMIT ===== */
        function isRowEmpty(r) {
            const name = r.querySelector('.itemNameInput')?.value?.trim();
            const qty = r.querySelector('.qtyRawInput')?.value;
            const price = r.querySelector('.priceRawInput')?.value;
            return (!name && !qty && (!price || parseInt(price, 10) === 0));
        }

        $form.addEventListener('submit', (e) => {
            // 1) sync biaya & bayar awal
            syncOtherCosts();
            syncPaidNow();

            // 2) buang baris kosong, isi item_name dari input ketik jika perlu
            const allRows = [...$tbody.querySelectorAll('tr:not(.empty-state)')];
            allRows.forEach(r => {
                const itemInput = r.querySelector('.itemInput');
                const itemName = r.querySelector('.itemNameInput');
                if (itemInput && itemName && !itemName.value) {
                    itemName.value = (itemInput.value || '').trim();
                }
                if (isRowEmpty(r)) r.remove();
            });

            const rows = [...$tbody.querySelectorAll('tr:not(.empty-state)')];
            if (!rows.length) {
                e.preventDefault();
                alert('Tambahkan minimal 1 baris item.');
                return;
            }

            // 3) validasi & REINDEX
            setClassIdOnAllRows();
            let idx = 0,
                firstInvalid = null,
                valid = true;

            rows.forEach(r => {
                const itemId = r.querySelector('.itemIdInput');
                const itemName = r.querySelector('.itemNameInput');
                const classId = r.querySelector('.classIdInput');
                const unit = r.querySelector('.unitInput');
                const qtyRaw = r.querySelector('.qtyRawInput');
                const priceRaw = r.querySelector('.priceRawInput');

                const qtyNum = parseFloat(qtyRaw.value || '');
                const priceStr = priceRaw.value;
                const priceNum = priceStr === '' ? NaN : parseFloat(priceStr);

                const ok = !!classId.value && !!itemName.value && (qtyNum >= 0.001) && (priceStr !== '' &&
                    priceNum >= 0);
                if (!ok) {
                    valid = false;
                    firstInvalid = firstInvalid || r;
                    r.classList.add('table-danger');
                    setTimeout(() => r.classList.remove('table-danger'), 2000);
                    return;
                }

                // REINDEX
                itemId.name = `lines[${idx}][item_id]`;
                itemName.name = `lines[${idx}][item_name]`;
                classId.name = `lines[${idx}][item_class_id]`;
                unit.name = `lines[${idx}][unit]`;
                qtyRaw.name = `lines[${idx}][qty]`;
                priceRaw.name = `lines[${idx}][price]`;
                idx++;
            });

            if (!valid) {
                e.preventDefault();
                firstInvalid?.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                alert('Periksa baris item: kelas item wajib, nama wajib, qty ≥ 0,001, dan harga ≥ 0.');
            }
        });
    </script>
@endpush
