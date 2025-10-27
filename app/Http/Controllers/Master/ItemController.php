<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ItemClass;
use App\Models\ItemHppHistory;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    public function index()
    {
        $items = \App\Models\Item::with(['itemClass', 'productCategory'])
            ->orderBy('code')
            ->get();

        // ⬇️ tambahkan ini
        $itemClasses = \App\Models\ItemClass::orderBy('name')->get(['id', 'code', 'name']);

        return view('master.items.index', compact('items', 'itemClasses'));
    }

    public function create()
    {
        $itemClasses = ItemClass::orderBy('name')->get(['id', 'code', 'name']);
        $productCategories = ProductCategory::orderBy('name')->get(['id', 'code', 'name']);
        return view('master.items.create', compact('itemClasses', 'productCategories'));
    }

    public function store(Request $req)
    {
        $data = $req->validate([
            'code' => ['required', 'string', 'max:64', 'unique:items,code'],
            'name' => ['nullable', 'string', 'max:150'],
            'item_class_id' => ['required', 'integer', 'exists:item_classes,id'],
            'product_category_id' => ['nullable', 'integer', 'exists:product_categories,id'],
            'uom' => ['nullable', 'string', 'max:16'],
            'hpp' => ['required', 'integer', 'min:0'],
        ]);

        $class = ItemClass::findOrFail($data['item_class_id']);
        if ($class->code === 'BJ' && empty($data['product_category_id'])) {
            return back()->withInput()->with('error', 'Barang Jadi wajib memilih Kategori Produk.');
        }

        $uom = $data['uom'] ?? null;
        if ($class->code === 'BHBK') {
            $uom = 'KG';
        } elseif (!$uom) {
            $uom = 'PCS';
        }

        DB::transaction(function () use ($data, $uom) {
            $item = Item::create([
                'code' => $data['code'],
                'name' => $data['name'] ?? null,
                'item_class_id' => $data['item_class_id'],
                'product_category_id' => $data['product_category_id'] ?? null,
                'uom' => $uom,
                'current_hpp' => (int) $data['hpp'],
                'is_active' => true,
            ]);

            ItemHppHistory::create([
                'item_id' => $item->id,
                'old_hpp' => null,
                'new_hpp' => $item->current_hpp,
                'reason' => 'Set awal',
                'ref_type' => 'manual',
                'changed_at' => now(),
                'created_by' => auth()->id(),
            ]);
        });

        return redirect()->route('items.index')->with('status', 'Item berhasil dibuat.');
    }

    public function edit(Item $item)
    {
        $item->load(['itemClass', 'productCategory']);
        $itemClasses = ItemClass::orderBy('name')->get(['id', 'code', 'name']);
        $productCategories = ProductCategory::orderBy('name')->get(['id', 'code', 'name']);
        return view('master.items.edit', compact('item', 'itemClasses', 'productCategories'));
    }

    public function update(Request $req, Item $item)
    {
        $data = $req->validate([
            'name' => ['nullable', 'string', 'max:150'],
            'item_class_id' => ['required', 'integer', 'exists:item_classes,id'],
            'product_category_id' => ['nullable', 'integer', 'exists:product_categories,id'],
            'uom' => ['nullable', 'string', 'max:16'],
            'hpp' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $class = ItemClass::findOrFail($data['item_class_id']);
        if ($class->code === 'BJ' && empty($data['product_category_id'])) {
            return back()->withInput()->with('error', 'Barang Jadi wajib memilih Kategori Produk.');
        }

        $uom = $data['uom'] ?? $item->uom;
        if ($class->code === 'BHBK') {
            $uom = 'KG';
        }

        DB::transaction(function () use ($item, $data, $uom) {
            $old = (int) $item->current_hpp;

            $item->update([
                'name' => $data['name'] ?? null,
                'item_class_id' => $data['item_class_id'],
                'product_category_id' => $data['product_category_id'] ?? null,
                'uom' => $uom,
                'current_hpp' => (int) $data['hpp'],
                'is_active' => (bool) ($data['is_active'] ?? true),
            ]);

            if ($old !== (int) $item->current_hpp) {
                ItemHppHistory::create([
                    'item_id' => $item->id,
                    'old_hpp' => $old,
                    'new_hpp' => (int) $item->current_hpp,
                    'reason' => 'Perubahan HPP',
                    'ref_type' => 'manual',
                    'changed_at' => now(),
                    'created_by' => auth()->id(),
                ]);
            }
        });

        return redirect()->route('items.index')->with('status', 'Item diperbarui.');
    }

    public function destroy(Item $item)
    {
        $item->delete();
        return redirect()->route('items.index')->with('status', 'Item dihapus.');
    }
}
