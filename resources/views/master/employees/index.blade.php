@extends('layouts.app')

@section('title', 'Master Karyawan')

@push('head')
    <style>
        /* Card list look di mobile */
        .emp-card {
            border: 1px solid #e5e7eb;
            border-radius: .75rem;
        }

        .emp-card+.emp-card {
            margin-top: .75rem;
        }

        .emp-key {
            color: #6b7280;
            width: 90px;
        }

        /* label kecil */
        @media (min-width: 768px) {
            .emp-mobile {
                display: none !important;
            }
        }

        @media (max-width: 767.98px) {
            .emp-desktop {
                display: none !important;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Master Karyawan</h4>
            <div class="d-flex gap-2">
                <form method="GET" action="{{ route('master.employees.index') }}" class="d-flex">
                    <input type="search" name="q" value="{{ $q }}" class="form-control form-control-sm"
                        placeholder="Cari kode/nama/no hp...">
                </form>
                <a href="{{ route('master.employees.create') }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-lg me-1"></i> Tambah
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- ===== MOBILE: Card list ===== --}}
        <div class="emp-mobile">
            @forelse ($employees as $e)
                <div class="emp-card p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fw-semibold">{{ $e->name }}</div>
                            <div class="text-muted small">{{ $e->code }}</div>
                        </div>
                        @if ($e->active)
                            <span class="badge text-bg-success">Aktif</span>
                        @else
                            <span class="badge text-bg-secondary">Nonaktif</span>
                        @endif
                    </div>

                    <hr class="my-2">

                    <div class="small d-flex">
                        <div class="emp-key">No. HP</div>
                        <div class="flex-grow-1">{{ $e->phone ?? '-' }}</div>
                    </div>
                    <div class="small d-flex mt-1">
                        <div class="emp-key">Role</div>
                        <div class="flex-grow-1 text-capitalize">{{ $e->role }}</div>
                    </div>
                    <div class="small d-flex mt-1">
                        <div class="emp-key">Skema</div>
                        <div class="flex-grow-1">{{ str_replace('_', '/', $e->payment_type) }}</div>
                    </div>
                    <div class="small d-flex mt-1">
                        <div class="emp-key">Base Rate</div>
                        <div class="flex-grow-1">
                            {{ $e->base_rate ? 'Rp ' . number_format($e->base_rate, 0, ',', '.') : '-' }}
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-3">
                        <a href="{{ route('master.employees.edit', $e) }}" class="btn btn-outline-secondary btn-sm w-50">
                            <i class="bi bi-pencil me-1"></i>Edit
                        </a>
                        <form action="{{ route('master.employees.destroy', $e) }}" method="POST" class="w-50"
                            onsubmit="return confirm('Hapus karyawan ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-outline-danger btn-sm w-100">
                                <i class="bi bi-trash me-1"></i>Hapus
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-center text-muted py-4">Belum ada data.</div>
            @endforelse

            <div class="mt-3">
                {{ $employees->links() }}
            </div>
        </div>

        {{-- ===== DESKTOP: Table ===== --}}
        <div class="card emp-desktop">
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>No. HP</th>
                            <th>Role</th>
                            <th>Skema</th>
                            <th class="text-end">Base Rate</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($employees as $e)
                            <tr>
                                <td>{{ $e->code }}</td>
                                <td>{{ $e->name }}</td>
                                <td>{{ $e->phone ?? '-' }}</td>
                                <td><span class="badge text-bg-secondary text-capitalize">{{ $e->role }}</span></td>
                                <td>{{ str_replace('_', '/', $e->payment_type) }}</td>
                                <td class="text-end">
                                    {{ $e->base_rate ? 'Rp ' . number_format($e->base_rate, 0, ',', '.') : '-' }}</td>
                                <td>
                                    @if ($e->active)
                                        <span class="badge text-bg-success">Aktif</span>
                                    @else
                                        <span class="badge text-bg-secondary">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('master.employees.edit', $e) }}"
                                        class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('master.employees.destroy', $e) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('Hapus karyawan ini?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-3">Belum ada data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                {{ $employees->links() }}
            </div>
        </div>
    </div>
@endsection
