@extends('layouts.app')

@section('title', 'Edit Karyawan')

@push('head')
    <style>
        /* Responsif di layar kecil */
        @media (max-width: 768px) {
            .card-body .row>[class*="col-md"] {
                flex: 0 0 100%;
                max-width: 100%;
            }

            .d-flex.justify-content-between {
                flex-direction: column;
                align-items: stretch !important;
                gap: .5rem;
            }

            .d-flex.justify-content-between .btn-sm {
                width: 100%;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Edit Karyawan</h4>
            <a href="{{ route('master.employees.index') }}" class="btn btn-outline-secondary btn-sm">&larr; Kembali</a>
        </div>

        {{-- Error message (opsional) --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <div class="fw-semibold mb-1">Periksa kembali inputan:</div>
                <ul class="mb-0">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <form action="{{ route('master.employees.update', $employee) }}" method="POST" id="employeeEditForm">
                @csrf
                @method('PUT')

                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Kode</label>
                            <input type="text" name="code" id="code" class="form-control"
                                value="{{ old('code', $employee->code) }}" placeholder="Contoh: MYD / RDN / ANG" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Nama</label>
                            <input type="text" name="name" id="name" class="form-control"
                                value="{{ old('name', $employee->name) }}" placeholder="Contoh: Mang Yadi / Jang Ridwan"
                                required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">No. HP</label>
                            <input type="text" name="phone" id="phone" class="form-control"
                                value="{{ old('phone', $employee->phone) }}" placeholder="Contoh: 0812 3456 7890">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Role</label>
                            <select name="role" id="role" class="form-select" required>
                                @foreach (['jahit' => 'Jahit', 'cutting' => 'Cutting', 'operasional' => 'Operasional'] as $val => $label)
                                    <option value="{{ $val }}" @selected(old('role', $employee->role) === $val)>{{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Skema Pembayaran</label>
                            {{-- Select untuk interaksi user --}}
                            <select id="payment_type_select" class="form-select" required>
                                <option value="per_pcs" @selected(old('payment_type', $employee->payment_type) === 'per_pcs')>Per/Pcs</option>
                                <option value="harian" @selected(old('payment_type', $employee->payment_type) === 'harian')>Harian</option>
                            </select>
                            {{-- Hidden mirror yang dikirim ke server --}}
                            <input type="hidden" name="payment_type" id="payment_type_hidden"
                                value="{{ old('payment_type', $employee->payment_type) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Base Rate</label>
                            <input type="number" name="base_rate" id="base_rate" class="form-control"
                                value="{{ old('base_rate', $employee->base_rate) }}" placeholder="Contoh: 5000"
                                min="0" step="1">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="active" id="active" class="form-select">
                                <option value="1" @selected(old('active', $employee->active))>Aktif</option>
                                <option value="0" @selected(!old('active', $employee->active))>Nonaktif</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            (function() {
                const role = document.getElementById('role');
                const paySel = document.getElementById('payment_type_select');
                const payHidden = document.getElementById('payment_type_hidden');
                const form = document.getElementById('employeeEditForm');

                function syncHidden() {
                    payHidden.value = paySel.value;
                }

                function handleRoleChange() {
                    if (role.value === 'operasional') {
                        paySel.value = 'harian';
                        paySel.disabled = true;
                        payHidden.value = 'harian';
                    } else {
                        paySel.disabled = false;
                        syncHidden();
                    }
                }

                role.addEventListener('change', handleRoleChange);
                paySel.addEventListener('change', syncHidden);

                // Pastikan hidden up-to-date saat submit
                form.addEventListener('submit', function() {
                    if (!paySel.disabled) syncHidden();
                });

                // Inisialisasi pertama kali (pakai nilai yang ada)
                handleRoleChange();
            })();
        </script>
    @endpush
@endsection
