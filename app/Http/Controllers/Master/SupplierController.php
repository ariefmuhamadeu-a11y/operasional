<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\ItemClass;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::with('itemClass')
            ->when($request->filled('q'), function ($q) use ($request) {
                $keyword = '%' . $request->q . '%';
                $q->where('code', 'like', $keyword)
                    ->orWhere('store_name', 'like', $keyword);
            })
            ->when($request->filled('item_class_id'), function ($q) use ($request) {
                $q->where('item_class_id', $request->item_class_id);
            })
            ->orderBy('store_name');

        $suppliers = $query->get();
        $itemClasses = ItemClass::orderBy('name')->get(['id', 'code', 'name']);

        return view('master.suppliers.index', compact('suppliers', 'itemClasses'));
    }

    public function create()
    {
        $itemClasses = ItemClass::orderBy('name')->get(['id', 'code', 'name']);
        return view('master.suppliers.create', compact('itemClasses'));
    }

    public function store(Request $r)
    {
        $r->validate([
            'code' => 'required|string|max:20|unique:suppliers,code',
            'store_name' => 'required|string|max:120',
            'item_class_id' => 'required|exists:item_classes,id',
            'type' => 'nullable|string|max:60',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string',
        ]);

        Supplier::create($r->all());
        return redirect()->route('master.suppliers.index')->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function edit(Supplier $supplier)
    {
        $itemClasses = ItemClass::orderBy('name')->get(['id', 'code', 'name']);
        return view('master.suppliers.edit', compact('supplier', 'itemClasses'));
    }

    public function update(Request $r, Supplier $supplier)
    {
        $r->validate([
            'code' => 'required|string|max:20|unique:suppliers,code,' . $supplier->id,
            'store_name' => 'required|string|max:120',
            'item_class_id' => 'required|exists:item_classes,id',
            'type' => 'nullable|string|max:60',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string',
        ]);

        $supplier->update($r->all());
        return redirect()->route('master.suppliers.index')->with('success', 'Supplier berhasil diupdate.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return back()->with('success', 'Supplier berhasil dihapus.');
    }
}
