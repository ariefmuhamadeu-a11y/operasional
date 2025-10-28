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
            --accent: #60a5fa;
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

        @media (max-width: 576px) {

            .form-control,
            .form-select {
                padding: .6rem .75rem;
                font-size: 1rem;
            }

            .btn {
                padding: .6rem .9rem;
            }

            #tblItems th,
            #tblItems td {
                padding: .65rem .5rem !important;
            }

            .suggest-item {
                padding: .7rem .85rem;
            }

            .actions-sticky {
                position: sticky;
                bottom: 10px;
                display: flex;
                justify-content: flex-end;
                gap: .5rem;
                backdrop-filter: blur(6px);
            }
        }

        .itemInput::placeholder {
            color: #cfe4ff;
            opacity: .95;
            font-weight: 700;
            letter-spacing: .02em;
            text-transform: uppercase;
        }

        #tblItems td {
            padding-top: .5rem;
            padding-bottom: .35rem;
            vertical-align: top;
        }

        .table-responsive {
            overflow: visible;
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
            display: none;
        }

        .suggest-item {
            padding: .6rem .9rem;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            gap: .75rem;
        }

        .suggest-item:hover,
        .suggest-item.active {
            background: rgba(96, 165, 250, .12);
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
            line-height: 1.15;
        }
    </style>
@endpush

@section('content')
    <div class="container py-4">

        <div class="card border-secondary-subtle mb-4 border p-4 text-center">
            <h2 class="mb-1">Faktur Pembelian</h2>
            <h5 class="text-secondary mb-0">No:
                <span class="text-light fw-semibold">{{ $invoiceNo ?? 'FKT-BKU-' . now()->format('Ymd') . '-00001' }}</span>
            </h5>
            <div class="small muted">Format: FKT-BKU-YYYYMMDD-##### (auto)</div>
        </div>

        <form id="formPembelian" method="POST" novalidate>
            {{-- @csrf --}}
            <input type="hidden" name="invoice_no" value="{{ $invoiceNo ?? 'FKT-BKU-' . now()->format('Ymd') . '-00001' }}">

            <div class="card mb-3 p-3">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Transaksi</label>
                        <input type="date" class="form-control" name="transaction_date"
                            value="{{ now()->toDateString() }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Supplier</label>
                        <select class="form-select" name="supplier_id" id="supplierSelect" required>
                            <option value="">Pilih Supplier...</option>
                            @foreach ($suppliers as $s)
                                <option value="{{ $s->id }}" data-class-id="{{ $s->item_class_id }}">
                                    {{ $s->store_name }} ({{ $s->itemClass->code ?? '' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Metode Pembayaran</label>
                        <select class="form-select" name="payment_method" required>
                            <option value="CASH">Cash</option>
                            <option value="TRANSFER">Transfer</option>
                            <option value="TERM30">Term 30 Hari</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">OTENG / Ongkir</label>
                        <input type="text" inputmode="numeric" class="form-control text-end" name="shipping_cost"
                            id="shipping_cost" value="0">
                        <div class="form-text muted">Masuk HPP invoice</div>
                    </div>
                </div>
            </div>

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

            <div class="row g-3">
                <div class="col-lg-7">
                    <div class="card h-100 p-3">
                        <label class="form-label">Catatan (opsional)</label>
                        <textarea class="form-control" name="notes" rows="3"></textarea>
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
                        <hr class="border-secondary">
                        <div class="d-flex justify-content-between fs-5">
                            <span>Grand Total</span><strong id="grandTotalText" class="mono">Rp0</strong>
                        </div>
                        <input type="hidden" id="subtotal" value="0">
                        <input type="hidden" id="grand_total" value="0">
                    </div>
                </div>
            </div>

            <div class="actions-sticky d-flex justify-content-end mt-3 gap-2">
                <a href="#" class="btn btn-outline-secondary">Batal</a>
                <button type="submit" class="btn btn-light">Simpan Faktur</button>
            </div>

            {{-- Template row --}}
            <template id="rowTemplate">
                <tr>
                    <td>
                        <div class="suggest-wrap">
                            <input class="form-control form-control-sm itemInput" placeholder="TEKAN F2 — LIST ITEM"
                                autocomplete="off">
                            <div class="suggest-menu"></div>
                        </div>
                        <input type="hidden" class="itemIdInput" name="items[][item_id]">
                    </td>

                    <td>
                        <input type="text" class="form-control form-control-sm text-end qtyInput" placeholder="0,00">
                    </td>

                    <td>
                        <input type="text" class="form-control form-control-sm text-end priceInput" placeholder="Rp0">
                        <input type="hidden" class="priceRawInput" name="items[][price]">
                        <div class="last-price">Harga terakhir: <span class="lastPriceText">—</span></div>
                    </td>

                    <td class="text-end mono">
                        <span class="subtotalCell">Rp0</span>
                        <input type="hidden" class="subtotalInput" name="items[][total]" value="0">
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
        // ===== Helpers angka =====
        const rupiah = n => new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(Math.round(n || 0));
        const intFromCur = s => parseInt(String(s || '').replace(/[^\d]/g, '') || '0', 10);

        // Qty 2 desimal: format & parse
        const formatQtyValue = num => Number(num || 0).toLocaleString('id-ID', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        const parseQty = el => parseFloat(String(el.value || '').replace(/\./g, '').replace(',', '.')) || 0;
        const formatQtyInput = el => {
            let raw = String(el.value || '').trim();
            if (raw === '') {
                el.value = '';
                return;
            }
            raw = raw.replace(/\./g, '').replace(',', '.');
            const num = parseFloat(raw);
            if (isNaN(num)) {
                el.value = '';
                return;
            }
            el.value = formatQtyValue(num);
        };

        // ===== Data dari controller =====
        const ITEMS = @json($items);
        const LAST_PRICES = @json($lastPrices ?? []);
        const LAST_BY_SUP = @json($lastPricesBySupplier ?? []);

        // ===== Refs =====
        const $tbody = document.querySelector('#tblItems tbody');
        const $rowTpl = document.getElementById('rowTemplate');
        const $btnAdd = document.getElementById('btnAdd');
        const $supplier = document.getElementById('supplierSelect');
        const $ship = document.getElementById('shipping_cost');

        // Deteksi mobile (tanpa F2)
        const isMobile = (('ontouchstart' in window) || navigator.maxTouchPoints > 0 || window.matchMedia(
            '(max-width: 768px)').matches);

        // Ongkir
        const fmtShip = () => {
            $ship.value = rupiah(intFromCur($ship.value));
        };
        $ship.addEventListener('input', () => {
            $ship.value = rupiah(intFromCur($ship.value));
            recalcSummary();
        });
        fmtShip();

        // Supplier & item helpers
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

        function addRow() {
            if (!activeSupplierClassId()) {
                $supplier.focus();
                return;
            }
            $tbody.querySelector('.empty-state')?.remove();

            const tr = $rowTpl.content.cloneNode(true).querySelector('tr');
            const input = tr.querySelector('.itemInput');
            const menu = tr.querySelector('.suggest-menu');
            const qty = tr.querySelector('.qtyInput');
            const price = tr.querySelector('.priceInput');
            const priceRaw = tr.querySelector('.priceRawInput');
            const subCell = tr.querySelector('.subtotalCell');
            const subInp = tr.querySelector('.subtotalInput');
            const lastTxt = tr.querySelector('.lastPriceText');

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

            // === Fokus langsung ke input Item saat tambah baris
            // (Dipanggil di akhir addRow)
            function focusItemOpenSuggestIfMobile() {
                input.focus();
                input.select?.();
                if (isMobile) {
                    cache = findItems(activeSupplierClassId(), input.value);
                    activeIdx = cache.length ? 0 : -1;
                    openMenu(cache);
                    setTimeout(() => menu.scrollTop = 0, 0);
                }
            }

            // Desktop: F2; Mobile: auto buka
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
                input.dataset.id = '';
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
                            input.dataset.id = it.id;
                            setLast(it.id);
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
                    activeIdx = (e.key === 'ArrowDown') ?
                        Math.min(cache.length - 1, Math.max(0, activeIdx + 1)) :
                        Math.max(0, activeIdx - 1);
                    openMenu(cache);
                } else if (e.key === 'Enter') {
                    if (activeIdx >= 0 && cache[activeIdx]) {
                        e.preventDefault();
                        const it = cache[activeIdx];
                        input.value = labelOf(it);
                        input.dataset.id = it.id;
                        setLast(it.id);
                        closeMenu();
                        qty.focus();
                    }
                } else if (e.key === 'Escape') {
                    closeMenu();
                }
            });

            menu.addEventListener('click', (ev) => {
                const el = ev.target.closest('.suggest-item');
                if (!el) return;
                const it = cache[parseInt(el.dataset.index)];
                input.value = labelOf(it);
                input.dataset.id = it.id;
                setLast(it.id);
                closeMenu();
                qty.focus();
            });

            // Blur resolve (tahan sedikit agar klik menu tetap kebaca)
            input.addEventListener('blur', () => {
                setTimeout(() => {
                    if (menu.contains(document.activeElement)) return;
                    if (!input.dataset.id) {
                        const it = resolveItem(input.value, activeSupplierClassId());
                        if (it) {
                            input.value = labelOf(it);
                            input.dataset.id = it.id;
                            setLast(it.id);
                        }
                    }
                    if (!isMobile) closeMenu();
                }, 120);
            });

            // Qty & Harga
            qty.addEventListener('focus', () => qty.select?.());
            qty.addEventListener('input', () => {
                recalcRow();
                recalcSummary();
            });
            qty.addEventListener('blur', () => {
                formatQtyInput(qty);
                recalcRow();
                recalcSummary();
            });

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
                const q = parseQty(qty),
                    p = intFromCur(price.value),
                    st = Math.round(q * p);
                subCell.textContent = rupiah(st);
                subInp.value = st;
            }

            // ===== Hapus baris
            tr.querySelector('.btnDel')?.addEventListener('click', () => {
                tr.remove();
                recalcSummary();
                if (!$tbody.querySelector('tr')) {
                    $tbody.innerHTML = `<tr class="empty-state"><td colspan="5" class="text-center text-muted py-4">
          <div class="small">Pilih <strong>Supplier</strong> terlebih dahulu.</div></td></tr>`;
                }
            });

            $tbody.appendChild(tr);
            recalcSummary();

            // Fokus langsung ke input Item + auto suggest di mobile
            focusItemOpenSuggestIfMobile();
        }

        function recalcSummary() {
            const rows = $tbody.querySelectorAll('tr:not(.empty-state)');
            document.getElementById('totalItems').textContent = rows.length;
            let subtotal = 0;
            rows.forEach(r => subtotal += parseInt(r.querySelector('.subtotalInput')?.value || 0));
            const ship = intFromCur(document.getElementById('shipping_cost').value);
            document.getElementById('subtotalText').textContent = rupiah(subtotal);
            document.getElementById('shippingText').textContent = rupiah(ship);
            document.getElementById('grandTotalText').textContent = rupiah(subtotal + ship);
        }

        function handleSupplierChange() {
            const ok = !!activeSupplierClassId();
            $btnAdd.disabled = !ok;
            $tbody.innerHTML = '';
            if (!ok) {
                $tbody.innerHTML = `<tr class="empty-state"><td colspan="5" class="text-center text-muted py-4">
        <div class="small">Pilih <strong>Supplier</strong> terlebih dahulu.</div></td></tr>`;
                recalcSummary();
                return;
            }
            addRow();
        }

        // Init
        document.getElementById('btnAdd').addEventListener('click', addRow);
        document.getElementById('supplierSelect').addEventListener('change', handleSupplierChange);
        document.getElementById('shipping_cost').addEventListener('input', recalcSummary);
        handleSupplierChange();
    </script>
@endpush
