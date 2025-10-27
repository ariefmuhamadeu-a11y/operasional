@extends('layouts.app')

@section('title', 'Tambah Karyawan')

@push('head')
    <style>
        /* Styling agar form tetap nyaman di layar kecil */
        @media (max-width: 768px) {
            .card-body .row>[class*="col-md"] {
                flex: 0 0 100%;
                max-width: 100%;
            }

            .btn {
                width: 100%;
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
            <h4 class="mb-0">Tambah Karyawan</h4>
            <a href="{{ route('master.employees.index') }}" class="btn btn-outline-secondary btn-sm">&larr; Kembali</a>
        </div>

        <div class="card">
            <form action="{{ route('master.employees.store') }}" method="POST" id="employeeCreateForm">
                @csrf
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Kode</label>
                            <input type="text" name="code" id="code" class="form-control"
                                placeholder="Contoh: MYD / RDN / ANG" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Nama</label>
                            <input type="text" name="name" id="name" class="form-control"
                                placeholder="Contoh: Mang Yadi / Jang Ridwan" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">No. HP</label>
                            <input type="text" name="phone" id="phone" class="form-control"
                                placeholder="Contoh: 0812 3456 7890">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Role</label>
                            <select name="role" id="role" class="form-select" required>
                                <option value="">-- Pilih Role --</option>
                                <option value="jahit">Jahit</option>
                                <option value="cutting">Cutting</option>
                                <option value="operasional">Operasional</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Skema Pembayaran</label>
                            <select id="payment_type_select" class="form-select" required>
                                <option value="per_pcs">Per/Pcs</option>
                                <option value="harian">Harian</option>
                            </select>
                            <input type="hidden" name="payment_type" id="payment_type_hidden" value="per_pcs">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Base Rate</label>
                            <input type="number" name="base_rate" id="base_rate" class="form-control"
                                placeholder="Contoh: 5000" min="0" step="1">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="active" id="active" class="form-select">
                                <option value="1" selected>Aktif</option>
                                <option value="0">Nonaktif</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary">Simpan</button>
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
                const form = document.getElementById('employeeCreateForm');

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
                form.addEventListener('submit', function() {
                    if (!paySel.disabled) syncHidden();
                });
                handleRoleChange();
            })();
        </script>
    @endpush
@endsection
