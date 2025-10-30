<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use App\Models\Counter;
use App\Models\Employee;
use App\Models\Item;
use App\Models\ProductionOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\View\View;

class ProductionOrderController extends Controller
{
    /**
     * Mapping tipe produksi ke label.
     */
    protected array $types = [
        'cutting' => 'Cutting',
        'sewing' => 'Jahit',
    ];

    public function index(string $type): View
    {
        $type = $this->resolveType($type);

        $orders = ProductionOrder::with(['item', 'supervisor'])
            ->type($type)
            ->latest('scheduled_for')
            ->latest()
            ->get();

        $stats = [
            'total' => $orders->count(),
            'draft' => $orders->where('status', ProductionOrder::STATUS_DRAFT)->count(),
            'in_progress' => $orders->where('status', ProductionOrder::STATUS_IN_PROGRESS)->count(),
            'completed' => $orders->where('status', ProductionOrder::STATUS_COMPLETED)->count(),
        ];

        return view('production.orders.index', [
            'orders' => $orders,
            'stats' => $stats,
            'type' => $type,
            'typeLabel' => $this->types[$type],
        ]);
    }

    public function create(string $type): View
    {
        $type = $this->resolveType($type);

        $items = Item::orderBy('name')->get(['id', 'code', 'name']);
        $employees = Employee::orderBy('name')->get(['id', 'name']);

        return view('production.orders.create', [
            'type' => $type,
            'typeLabel' => $this->types[$type],
            'items' => $items,
            'employees' => $employees,
        ]);
    }

    public function store(Request $request, string $type): RedirectResponse
    {
        $type = $this->resolveType($type);

        $data = $request->validate([
            'scheduled_for' => ['required', 'date'],
            'item_id' => ['required', 'exists:items,id'],
            'planned_quantity' => ['required', 'integer', 'min:1'],
            'supervisor_id' => ['nullable', 'exists:employees,id'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $orderNumber = $this->generateOrderNumber($type);

        ProductionOrder::create([
            'order_number' => $orderNumber,
            'type' => $type,
            'item_id' => $data['item_id'],
            'scheduled_for' => $data['scheduled_for'],
            'planned_quantity' => $data['planned_quantity'],
            'supervisor_id' => $data['supervisor_id'] ?? null,
            'status' => ProductionOrder::STATUS_DRAFT,
            'notes' => Arr::get($data, 'notes'),
            'created_by' => auth()->id(),
        ]);

        return redirect()
            ->route('production.orders.index', ['type' => $type])
            ->with('status', 'Perintah produksi baru berhasil dibuat.');
    }

    protected function resolveType(string $type): string
    {
        if (! array_key_exists($type, $this->types)) {
            abort(404);
        }

        return $type;
    }

    protected function generateOrderNumber(string $type): string
    {
        $period = now()->format('ym');
        $seq = Counter::next("production:{$type}:{$period}");
        $prefix = strtoupper(substr($type, 0, 3));

        return sprintf('PRD-%s-%s-%03d', $prefix, $period, $seq);
    }
}
