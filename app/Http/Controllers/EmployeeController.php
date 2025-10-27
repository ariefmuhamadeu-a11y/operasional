<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeRequest;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->toString();

        $employees = Employee::query()
            ->when($q, function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('code', 'like', "%{$q}%")
                        ->orWhere('name', 'like', "%{$q}%")
                        ->orWhere('phone', 'like', "%{$q}%");
                });
            })
            ->orderBy('code')
            ->paginate(10)
            ->withQueryString();

        return view('master.employees.index', compact('employees', 'q'));
    }

    public function create()
    {
        return view('master.employees.create');
    }

    public function store(EmployeeRequest $request)
    {
        $data = $request->validated();

        // Checkbox "active" hanya terkirim saat checked; fallback ke false
        $data['active'] = (bool) ($data['active'] ?? false);

        Employee::create($data);

        return redirect()
            ->route('master.employees.index')
            ->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function edit(Employee $employee)
    {
        return view('master.employees.edit', compact('employee'));
    }

    public function update(EmployeeRequest $request, Employee $employee)
    {
        $data = $request->validated();
        $data['active'] = (bool) ($data['active'] ?? false);

        $employee->update($data);

        return redirect()
            ->route('master.employees.index')
            ->with('success', 'Data karyawan diperbarui.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();

        return redirect()
            ->route('master.employees.index')
            ->with('success', 'Karyawan dihapus.');
    }
}
