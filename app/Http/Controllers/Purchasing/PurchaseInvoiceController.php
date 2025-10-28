<?php

namespace App\Http\Controllers\Purchasing;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ItemClass;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceLine;
use App\Models\Supplier;
use App\Models\SupplierItemPrice;
use App\Models\SupplierItemPriceHistory;
use App\Support\Code;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseInvoiceController extends Controller
{

    public function index(Request $r)
    {
        // Ambil parameter filter (semua opsional)
        $q = trim((string) $r->get('q', '')); // cari kode/supplier
        $supplierId = $r->integer('supplier_id'); // filter supplier spesifik
        $status = $r->get('status'); // DRAFT/TERBIT/SEBAGIAN/LUNAS
        $classId = $r->integer('item_class_id'); // BKU/ACC/BSJ/BJD
        $unpaidOnly = (bool) $r->boolean('unpaid_only'); // hanya yang masih terhutang
        $dateFrom = $r->get('date_from'); // YYYY-MM-DD
        $dateTo = $r->get('date_to'); // YYYY-MM-DD

        // Query dasar + eager supplier
        $query = PurchaseInvoice::query()
            ->with(['supplier:id,store_name']) // pakai store_name sesuai modelmu
            ->orderByDesc('date')
            ->orderByDesc('id');

        // Filter: search kode & nama supplier
        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('code', 'like', "%{$q}%")
                    ->orWhereHas('supplier', function ($s) use ($q) {
                        $s->where('store_name', 'like', "%{$q}%");
                    });
            });
        }

        // Filter: supplier
        if ($supplierId) {
            $query->where('supplier_id', $supplierId);
        }

        // Filter: status
        if (in_array($status, ['DRAFT', 'TERBIT', 'SEBAGIAN', 'LUNAS'], true)) {
            $query->where('status', $status);
        }

        // Filter: tanggal
        if ($dateFrom) {$query->whereDate('date', '>=', $dateFrom);}
        if ($dateTo) {$query->whereDate('date', '<=', $dateTo);}

        // Filter: hanya yang belum lunas
        if ($unpaidOnly) {
            $query->whereColumn('paid_total', '<', 'total');
        }

        // Filter: berdasarkan kelas item pada detail (BKU/ACC/BSJ/BJD)
        if ($classId) {
            $query->whereHas('lines', function ($l) use ($classId) {
                $l->where('item_class_id', $classId);
            });
        }

        // Ambil data (tanpa pagination agar live-search mulus)
        $invoices = $query->get([
            'id', 'code', 'supplier_id', 'date', 'total', 'paid_total', 'status', 'note',
        ]);

        // Ringkasan angka cepat
        $summary = [
            'count' => $invoices->count(),
            'total' => (float) $invoices->sum('total'),
            'paid_total' => (float) $invoices->sum('paid_total'),
            'outstanding' => (float) $invoices->sum(fn($i) => max(0, $i->total - $i->paid_total)),
        ];

        // Dropdown helper untuk filter di view (supplier & kelas)
        $supplierOptions = Supplier::orderBy('store_name')->get(['id', 'store_name']);
        $classOptions = ItemClass::orderBy('name')->get(['id', 'code', 'name']);

        // Jika butuh JSON untuk AJAX table (opsional)
        if ($r->wantsJson()) {
            return response()->json([
                'filters' => compact('q', 'supplierId', 'status', 'classId', 'unpaidOnly', 'dateFrom', 'dateTo'),
                'summary' => $summary,
                'invoices' => $invoices,
            ]);
        }

        // Kirim ke view index (purchasing.index)
        return view('purchasing.index', [
            'invoices' => $invoices,
            'summary' => $summary,
            'supplierOptions' => $supplierOptions,
            'classOptions' => $classOptions,

            // kirim balik nilai filter agar form tetap terisi
            'q' => $q,
            'supplierId' => $supplierId,
            'status' => $status,
            'classId' => $classId,
            'unpaidOnly' => $unpaidOnly,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }

    public function create()
    {
        $invoiceNo = Code::next('FKT-BKU', now()->toDateString());
        $suppliers = Supplier::with('itemClass:id,code,name')->orderBy('store_name')
            ->get(['id', 'store_name', 'item_class_id']);
        $items = Item::orderBy('name')->get([
            'id', 'code', 'name', 'unit', 'item_class_id', 'category_id',
        ]);
        $classes = ItemClass::orderBy('name')->get(['id', 'code', 'name']);

        return view('purchasing.create', compact('invoiceNo', 'suppliers', 'items', 'classes'));
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'date' => 'required|date',
            'due_date' => 'nullable|date',
            'note' => 'nullable|string',
            'other_costs' => 'nullable|numeric|min:0',
            'lines' => 'required|array|min:1',
            'lines.*.item_id' => 'nullable|exists:items,id',
            'lines.*.item_class_id' => 'required|exists:item_classes,id',
            'lines.*.item_name' => 'required|string',
            'lines.*.qty' => 'required|numeric|min:0.001',
            'lines.*.unit' => 'nullable|string|max:16',
            'lines.*.price' => 'required|numeric|min:0',
        ]);

        return DB::transaction(function () use ($data) {
            // Kelas supplier → semua baris harus mengikuti
            $supplier = Supplier::select('id', 'item_class_id')->findOrFail($data['supplier_id']);
            $supplierClassId = (int) $supplier->item_class_id;

            $subtotal = 0;

            // Normalisasi baris + hardening
            foreach ($data['lines'] as $idx => &$l) {
                // Wajib sama kelasnya dengan supplier
                if ((int) $l['item_class_id'] !== $supplierClassId) {
                    abort(422, "Baris #" . ($idx + 1) . " tidak sesuai kelas supplier.");
                }

                // Jika item dari master → override unit & nama agar konsisten
                if (!empty($l['item_id'])) {
                    $it = Item::select('id', 'name', 'unit', 'item_class_id')->find($l['item_id']);
                    if (!$it) {
                        abort(422, "Item di baris #" . ($idx + 1) . " tidak ditemukan.");
                    }

                    if ((int) $it->item_class_id !== (int) $l['item_class_id']) {
                        abort(422, "Kelas item baris #" . ($idx + 1) . " tidak cocok dengan master item.");
                    }
                    $l['unit'] = $it->unit ?: ($l['unit'] ?? 'pcs'); // kunci unit dari master
                    $l['item_name'] = $it->name; // snapshot nama
                } else {
                    // Item custom/non-master → biarkan unit input, default pcs jika kosong
                    if (empty($l['unit'])) {
                        $l['unit'] = 'pcs';
                    }

                }

                $lineTotal = (float) $l['qty'] * (float) $l['price'];
                $subtotal += $lineTotal;
            }
            unset($l);

            $other = $data['other_costs'] ?? 0;
            $total = $subtotal + $other;

            // Prefix kode bisa kamu sesuaikan—sementara pakai INV-BKU agar konsisten
            $code = Code::next('INV-BKU', $data['date']);

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
            ]);

            foreach ($data['lines'] as $l) {
                PurchaseInvoiceLine::create([
                    'purchase_invoice_id' => $inv->id,
                    'item_id' => $l['item_id'] ?? null,
                    'item_class_id' => $l['item_class_id'],
                    'item_name' => $l['item_name'],
                    'qty' => $l['qty'],
                    'unit' => $l['unit'],
                    'price' => $l['price'],
                    'total' => $l['qty'] * $l['price'],
                ]);

                // Update harga terakhir & histori hanya jika item dari master
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

            return redirect()->route('purchasing.index')->with('success', "Faktur {$inv->code} tersimpan");
            return back()->with('success', "Faktur {$inv->code} tersimpan");
        });
    }
}
