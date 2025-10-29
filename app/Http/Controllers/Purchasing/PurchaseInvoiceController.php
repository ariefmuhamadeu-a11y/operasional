<?php

namespace App\Http\Controllers\Purchasing;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Item;
use App\Models\ItemClass;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceLine;
use App\Models\PurchasePayment;
use App\Models\Supplier;
use App\Models\SupplierItemPrice;
use App\Models\SupplierItemPriceHistory;
use App\Support\Code;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseInvoiceController extends Controller
{
    /**
     * LIST / FILTER
     * View: resources/views/purchasing/index.blade.php
     * Route name: purchasing.index
     */
    // public function index(Request $r)
    // {
    //     $q = trim((string) $r->get('q', ''));
    //     $supplierId = $r->integer('supplier_id');
    //     $status = $r->get('status');
    //     $classId = $r->integer('item_class_id');
    //     $unpaidOnly = (bool) $r->boolean('unpaid_only');
    //     $dateFrom = $r->get('date_from');
    //     $dateTo = $r->get('date_to');

    //     // ===== Base query + eager + aggregate (sum payments) =====
    //     $query = PurchaseInvoice::query()
    //         ->with(['supplier:id,store_name'])
    //         ->withSum('payments', 'amount') // -> payments_sum_amount
    //         ->orderByDesc('date')
    //         ->orderByDesc('id');

    //     // ===== Filters =====
    //     if ($q !== '') {
    //         $query->where(function ($w) use ($q) {
    //             $w->where('code', 'like', "%{$q}%")
    //                 ->orWhereHas('supplier', fn($s) => $s->where('store_name', 'like', "%{$q}%"));
    //         });
    //     }
    //     if ($supplierId) {
    //         $query->where('supplier_id', $supplierId);
    //     }
    //     if (in_array($status, ['DRAFT', 'TERBIT', 'SEBAGIAN', 'LUNAS'], true)) {
    //         $query->where('status', $status);
    //     }
    //     if ($dateFrom) {$query->whereDate('date', '>=', $dateFrom);}
    //     if ($dateTo) {$query->whereDate('date', '<=', $dateTo);}

    //     // unpaidOnly:
    //     // - kalau kolom paid_total dirawat → cukup whereColumn(paid_total < total)
    //     // - kalau tidak, fallback ke subquery SUM(payments.amount)
    //     if ($unpaidOnly) {
    //         $query->where(function ($w) {
    //             $w->whereColumn('paid_total', '<', 'total') // case: kolom paid_total ada & dipakai
    //                 ->orWhereRaw(
    //                     // fallback: kalau paid_total NULL/0 tapi ada payment, pakai sum payments
    //                     '(COALESCE(paid_total, 0) = 0 AND (SELECT COALESCE(SUM(pp.amount),0)
    //                   FROM purchase_payments pp
    //                   WHERE pp.purchase_invoice_id = purchase_invoices.id) < total)'
    //                 );
    //         });
    //     }

    //     if ($classId) {
    //         $query->whereHas('lines', fn($l) => $l->where('item_class_id', $classId));
    //     }

    //     // ===== Summary (pakai effective paid: paid_total ?? payments_sum_amount) =====
    //     $summary = [
    //         'count' => (int) (clone $query)->count(),
    //         'total' => (float) (clone $query)->sum('total'),
    //         'paid_total' => 0.0,
    //         'outstanding' => 0.0,
    //     ];

    //     // chunk supaya aman & gunakan withSum agar kolom agregat tersedia
    //     (clone $query)->select(['id', 'total', 'paid_total'])
    //         ->withSum('payments', 'amount')
    //         ->chunk(1000, function ($rows) use (&$summary) {
    //             foreach ($rows as $row) {
    //                 $paidEff = ($row->paid_total !== null)
    //                 ? (float) $row->paid_total
    //                 : (float) ($row->payments_sum_amount ?? 0);
    //                 $summary['paid_total'] += $paidEff;
    //                 $summary['outstanding'] += max(0, (float) ($row->total ?? 0) - $paidEff);
    //             }
    //         });

    //     // ===== Data tabel (paginate) =====
    //     $invoices = (clone $query)
    //         ->select(['id', 'code', 'supplier_id', 'date', 'total', 'paid_total', 'status', 'note'])
    //         ->withSum('payments', 'amount') // pastikan ikut di halaman berikutnya
    //         ->paginate(50)
    //         ->withQueryString();

    //     $supplierOptions = Supplier::orderBy('store_name')->get(['id', 'store_name']);
    //     $classOptions = ItemClass::orderBy('name')->get(['id', 'code', 'name']);

    //     if ($r->wantsJson()) {
    //         return response()->json([
    //             'filters' => compact('q', 'supplierId', 'status', 'classId', 'unpaidOnly', 'dateFrom', 'dateTo'),
    //             'summary' => $summary,
    //             'invoices' => $invoices,
    //         ]);
    //     }

    //     return view('purchasing.index', [
    //         'invoices' => $invoices,
    //         'summary' => $summary,
    //         'supplierOptions' => $supplierOptions,
    //         'classOptions' => $classOptions,
    //         'q' => $q,
    //         'supplierId' => $supplierId,
    //         'status' => $status,
    //         'classId' => $classId,
    //         'unpaidOnly' => $unpaidOnly,
    //         'dateFrom' => $dateFrom,
    //         'dateTo' => $dateTo,
    //     ]);
    // }

    public function index(Request $r)
    {
        $q = trim((string) $r->get('q', ''));
        $supplierId = $r->integer('supplier_id');
        $status = $r->get('status');
        $classId = $r->integer('item_class_id');
        $unpaidOnly = (bool) $r->boolean('unpaid_only');
        $dateFrom = $r->get('date_from');
        $dateTo = $r->get('date_to');

        // ===== Base query + eager + aggregate (sum payments) =====
        $query = PurchaseInvoice::query()
            ->with([
                'supplier:id,store_name',
                'operator:id,name', // ⇦ muat operator
            ])
            ->withSum('payments', 'amount') // -> payments_sum_amount
            ->orderByDesc('date')
            ->orderByDesc('id');

        // ===== Filters =====
        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('code', 'like', "%{$q}%")
                    ->orWhereHas('supplier', fn($s) => $s->where('store_name', 'like', "%{$q}%"))
                    ->orWhereHas('operator', fn($o) => $o->where('name', 'like', "%{$q}%")); // cari juga via operator
            });
        }
        if ($supplierId) {
            $query->where('supplier_id', $supplierId);
        }
        if (in_array($status, ['DRAFT', 'TERBIT', 'SEBAGIAN', 'LUNAS'], true)) {
            $query->where('status', $status);
        }
        if ($dateFrom) {$query->whereDate('date', '>=', $dateFrom);}
        if ($dateTo) {$query->whereDate('date', '<=', $dateTo);}

        if ($unpaidOnly) {
            $query->where(function ($w) {
                $w->whereColumn('paid_total', '<', 'total')
                    ->orWhereRaw('(COALESCE(paid_total, 0) = 0 AND (SELECT COALESCE(SUM(pp.amount),0)
                        FROM purchase_payments pp
                        WHERE pp.purchase_invoice_id = purchase_invoices.id) < total)');
            });
        }

        if ($classId) {
            $query->whereHas('lines', fn($l) => $l->where('item_class_id', $classId));
        }

        // ===== Summary =====
        $summary = [
            'count' => (int) (clone $query)->count(),
            'total' => (float) (clone $query)->sum('total'),
            'paid_total' => 0.0,
            'outstanding' => 0.0,
        ];

        (clone $query)->select(['id', 'total', 'paid_total'])
            ->withSum('payments', 'amount')
            ->chunk(1000, function ($rows) use (&$summary) {
                foreach ($rows as $row) {
                    $paidEff = ($row->paid_total !== null)
                    ? (float) $row->paid_total
                    : (float) ($row->payments_sum_amount ?? 0);
                    $summary['paid_total'] += $paidEff;
                    $summary['outstanding'] += max(0, (float) ($row->total ?? 0) - $paidEff);
                }
            });

        // ===== Data tabel (paginate) =====
        $invoices = (clone $query)
            ->select(['id', 'code', 'supplier_id', 'operator_id', 'date', 'total', 'paid_total', 'status', 'note']) // ⇦ operator_id ikut diseleksi
            ->withSum('payments', 'amount')
            ->paginate(50)
            ->withQueryString();

        $supplierOptions = Supplier::orderBy('store_name')->get(['id', 'store_name']);
        $classOptions = ItemClass::orderBy('name')->get(['id', 'code', 'name']);

        if ($r->wantsJson()) {
            return response()->json([
                'filters' => compact('q', 'supplierId', 'status', 'classId', 'unpaidOnly', 'dateFrom', 'dateTo'),
                'summary' => $summary,
                'invoices' => $invoices,
            ]);
        }

        return view('purchasing.index', [
            'invoices' => $invoices,
            'summary' => $summary,
            'supplierOptions' => $supplierOptions,
            'classOptions' => $classOptions,
            'q' => $q,
            'supplierId' => $supplierId,
            'status' => $status,
            'classId' => $classId,
            'unpaidOnly' => $unpaidOnly,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }

    /**
     * CREATE
     * View: resources/views/purchasing/create.blade.php
     * Route name: purchasing.create
     */
    public function create()
    {
        // Nomor otomatis (format konsisten INV-BKU)
        $invoiceNo = Code::next('INV-BKU', now()->toDateString());

        $suppliers = Supplier::with('itemClass:id,code,name')
            ->orderBy('store_name')
            ->get(['id', 'store_name', 'item_class_id']);

        // Pool item untuk suggest (bisa diubah ke AJAX server-side jika data sudah besar)
        $items = Item::orderBy('name')->get([
            'id', 'code', 'name', 'unit', 'item_class_id', 'category_id',
        ]);

        $operators = Employee::operational()->orderBy('name')->get(['id', 'name']);

        // Harga terakhir per supplier
        $lastPricesBySupplier = SupplierItemPrice::query()
            ->get(['supplier_id', 'item_id', 'last_price'])
            ->groupBy('supplier_id')
            ->map(fn($rows) => $rows->pluck('last_price', 'item_id'));

        // Harga terakhir (any supplier) → MAX(last_price) per item
        $lastPrices = SupplierItemPrice::query()
            ->select('item_id', DB::raw('MAX(last_price) as p'))
            ->groupBy('item_id')
            ->pluck('p', 'item_id');

        $classes = ItemClass::orderBy('name')->get(['id', 'code', 'name']);
        return view('purchasing.create', compact(
            'invoiceNo', 'suppliers', 'items', 'lastPrices', 'lastPricesBySupplier', 'operators'
        ));
    }

    public function show($id)
    {
        $invoice = PurchaseInvoice::with([
            'supplier:id,store_name,code,phone',
            'operator:id,name', // jika ada kolom operator_id
            'lines' => fn($q) => $q->orderBy('id'),
            'lines.item:id,code,name,unit',
            'payments' => fn($q) => $q->orderByDesc('date')->orderByDesc('id'),
        ])->findOrFail($id);

        $total = (float) $invoice->total;
        $paidTotal = (float) ($invoice->paid_total ?? $invoice->payments->sum('amount'));
        $outstanding = max(0, $total - $paidTotal);

        $badge = match ($invoice->status) {
            'DRAFT' => 'badge-draft',
            'TERBIT' => 'badge-terbit',
            'SEBAGIAN' => 'badge-sebagian',
            'LUNAS' => 'badge-lunas',
            default => 'badge-terbit',
        };

        return view('purchasing.show', [
            'invoice' => $invoice,
            'total' => $total,
            'paidTotal' => $paidTotal,
            'outstanding' => $outstanding,
            'badge' => $badge,
        ]);
    }

    /**
     * STORE: simpan faktur + detail + (opsional) pembayaran awal "dibayar sekarang"
     * Route name: purchasing.store
     */
    public function store(Request $r)
    {
        $data = $r->validate([
            'date' => 'required|date',
            'due_date' => 'nullable|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'operator_id' => 'required|exists:employees,id', // <— tambah ini
            'other_costs' => 'nullable|numeric|min:0',
            'paid_now' => 'nullable|numeric|min:0',
            'paid_account' => 'nullable|string|max:20',
            'paid_note' => 'nullable|string|max:255',
            'note' => 'nullable|string',

            'lines' => 'required|array|min:1',
            'lines.*.item_id' => 'nullable|exists:items,id',
            'lines.*.item_class_id' => 'required|exists:item_classes,id',
            'lines.*.item_name' => 'required|string',
            'lines.*.qty' => 'required|numeric|min:0.001',
            'lines.*.unit' => 'nullable|string|max:16',
            'lines.*.price' => 'required|numeric|min:0',
        ]);

        return DB::transaction(function () use ($data) {
            $supplier = Supplier::select('id', 'item_class_id')->findOrFail($data['supplier_id']);
            $supplierClassId = (int) $supplier->item_class_id;

            $subtotal = 0;

            // Normalisasi + validasi baris
            foreach ($data['lines'] as $idx => &$l) {
                if ((int) $l['item_class_id'] !== $supplierClassId) {
                    abort(422, "Baris #" . ($idx + 1) . " tidak sesuai kelas supplier.");
                }

                if (!empty($l['item_id'])) {
                    $it = Item::select('id', 'name', 'unit', 'item_class_id')->find($l['item_id']);
                    if (!$it) {
                        abort(422, "Item di baris #" . ($idx + 1) . " tidak ditemukan.");
                    }

                    if ((int) $it->item_class_id !== (int) $l['item_class_id']) {
                        abort(422, "Kelas item baris #" . ($idx + 1) . " tidak cocok dengan master item.");
                    }
                    $l['unit'] = $it->unit ?: ($l['unit'] ?? 'pcs');
                    $l['item_name'] = $it->name;
                } else {
                    // item adhoc
                    $l['unit'] = $l['unit'] ?: 'pcs';
                }

                $subtotal += (float) $l['qty'] * (float) $l['price'];
            }
            unset($l);

            $other = (float) ($data['other_costs'] ?? 0);
            $total = $subtotal + $other;

            $code = Code::next('INV-BKU', $data['date']);

            // Header
            $inv = PurchaseInvoice::create([
                'code' => $code,
                'supplier_id' => $data['supplier_id'],
                'date' => $data['date'],
                'due_date' => $data['due_date'] ?? null,
                'subtotal' => $subtotal,
                'other_costs' => $other,
                'total' => $total,
                'paid_total' => 0,
                'status' => 'TERBIT',
                'note' => $data['note'] ?? null,
                'operator_id' => $data['operator_id'],
            ]);

            // Detail
            foreach ($data['lines'] as $l) {
                PurchaseInvoiceLine::create([
                    'purchase_invoice_id' => $inv->id,
                    'item_id' => $l['item_id'] ?? null,
                    'item_class_id' => $l['item_class_id'],
                    'item_name' => $l['item_name'],
                    'qty' => $l['qty'],
                    'unit' => $l['unit'],
                    'price' => $l['price'],
                    'total' => (float) $l['qty'] * (float) $l['price'],
                ]);

                // tracker harga terakhir (jika item master)
                if (!empty($l['item_id'])) {
                    SupplierItemPrice::updateOrCreate(
                        ['supplier_id' => $inv->supplier_id, 'item_id' => $l['item_id']],
                        ['last_price' => $l['price'], 'last_date' => $inv->date]
                    );
                    SupplierItemPriceHistory::create([
                        'supplier_id' => $inv->supplier_id,
                        'item_id' => $l['item_id'],
                        'price' => $l['price'],
                        'date' => $inv->date,
                        'purchase_invoice_id' => $inv->id,
                    ]);
                }
            }

            // Pembayaran awal (opsional)
            $paidNow = (float) ($data['paid_now'] ?? 0);
            if ($paidNow > 0) {
                $amount = min($paidNow, $total);
                PurchasePayment::create([
                    'purchase_invoice_id' => $inv->id,
                    'date' => $data['date'],
                    'account' => $data['paid_account'] ?? 'CASH',
                    'amount' => $amount,
                    'note' => $data['paid_note'] ?? 'Pembayaran awal',
                ]);

                // Recalc paid_total & status agar tampil benar di index
                $this->recalcInvoicePaidAndStatus($inv->id);
            }

            return redirect()
                ->route('purchasing.index')
                ->with('success', "Faktur {$inv->code} tersimpan" . ($paidNow > 0 ? " + pembayaran awal" : ""));
        });
    }

    /**
     * TAMBAH PEMBAYARAN LANJUTAN (dari modal di index)
     * Route name: purchasing.payments.store
     * POST /purchasing/{invoice}/payments
     */
    public function storePayment(Request $r, PurchaseInvoice $invoice)
    {
        $data = $r->validate([
            'date' => 'required|date',
            'account' => 'required|string|max:32',
            'amount' => 'required|numeric|min:1',
            'note' => 'nullable|string|max:200',
        ]);

        return DB::transaction(function () use ($invoice, $data) {
            // maksimal tidak boleh melebihi sisa
            $currentPaid = (float) $invoice->paid_total;
            $remaining = max(0, (float) $invoice->total - $currentPaid);
            $amount = min($remaining, (float) $data['amount']);

            if ($amount <= 0) {
                return back()->with('error', 'Faktur sudah lunas atau jumlah pembayaran tidak valid.');
            }

            PurchasePayment::create([
                'purchase_invoice_id' => $invoice->id,
                'date' => $data['date'],
                'account' => $data['account'],
                'amount' => $amount,
                'note' => $data['note'] ?? null,
            ]);

            // Recalc paid_total & status
            $this->recalcInvoicePaidAndStatus($invoice->id);

            return back()->with('success', "Pembayaran Rp " . number_format($amount, 0, ',', '.') . " tercatat.");
        });
    }

    /**
     * UPDATE STATUS MANUAL (opsional)
     * Route name: purchasing.status.update
     * POST /purchasing/{invoice}/status
     */
    public function updateStatus(Request $r, PurchaseInvoice $invoice)
    {
        $data = $r->validate([
            'status' => 'required|in:DRAFT,TERBIT,SEBAGIAN,LUNAS',
        ]);

        // Guard opsional:
        // if ($data['status'] === 'LUNAS' && ($invoice->paid_total < $invoice->total)) {
        //     return back()->with('error', "Tidak bisa set LUNAS: pembayaran belum penuh.");
        // }
        // if ($data['status'] === 'DRAFT' && $invoice->paid_total > 0) {
        //     return back()->with('error', "Tidak bisa set DRAFT: sudah ada pembayaran.");
        // }

        $invoice->update(['status' => $data['status']]);

        return back()->with('success', "Status faktur {$invoice->code} diubah ke {$data['status']}.");
    }

    /* ==========================
    | UTIL: Recalc paid & status
     * ========================== */
    private function recalcInvoicePaidAndStatus(int $invoiceId): void
    {
        /** @var PurchaseInvoice $inv */
        $inv = PurchaseInvoice::lockForUpdate()->findOrFail($invoiceId);

        $sum = (float) PurchasePayment::where('purchase_invoice_id', $inv->id)->sum('amount');
        $inv->paid_total = $sum;

        if ($sum <= 0) {
            $inv->status = 'TERBIT';
        } elseif ($sum + 0.5 < (float) $inv->total) { // toleransi kecil
            $inv->status = 'SEBAGIAN';
        } else {
            $inv->status = 'LUNAS';
        }

        $inv->save();
    }
}
