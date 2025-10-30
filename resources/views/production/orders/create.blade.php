{{-- resources/views/production/orders/create.blade.php --}}
@extends('layouts.app')
@section('title', "ERP • Produksi {$typeLabel} • Buat")

@push('head')
    <style>
        :root {
            --panel: #0f172a;
            --card: #0e1525;
            --line: #1e2a3f;
            --muted: #9aa4b2;
            --text: #e6ebf1;
        }

        .card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 14px;
        }

        label.form-label {
            color: #e2e8f0;
            font-weight: 500;
        }

        .form-control, .form-select {
            background: rgba(15, 23, 42, .6);
            border: 1px solid rgba(30, 42, 63, .9);
            color: var(--text);
        }

        .form-control:focus, .form-select:focus {
            background: rgba(15, 23, 42, .85);
            border-color: #60a5fa;
            box-shadow: 0 0 0 0.15rem rgba(96, 165, 250, .15);
            color: var(--text);
        }

        .btn-soft {
            background: rgba(255, 255, 255, .05);
            border: 1px solid var(--line);
            color: var(--text);
        }

        .helper {
            color: var(--muted);
            font-size: .9rem;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid px-lg-4 px-3 py-4">
        <div class="card mb-4">
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <div class="h4 mb-1">Perintah Produksi {{ $typeLabel }}</div>
                    <div class="text-muted">Buat rencana pekerjaan baru untuk proses {{ strtolower($typeLabel) }}.</div>
                </div>
                <a href="{{ route('production.orders.index', ['type' => $type]) }}" class="btn btn-soft">
                    <i class="bi bi-arrow-left me-2"></i> Kembali
                </a>
            </div>
        </div>

        <div class="card">
            <form action="{{ route('production.orders.store', ['type' => $type]) }}" method="post">
                @csrf
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-12 col-md-4">
                            <label for="scheduled_for" class="form-label">Jadwal Produksi</label>
                            <input type="date" name="scheduled_for" id="scheduled_for"
                                   class="form-control @error('scheduled_for') is-invalid @enderror"
                                   value="{{ old('scheduled_for', now()->format('Y-m-d')) }}" required>
                            @error('scheduled_for')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="helper mt-1">Tanggal rencana mulai produksi.</div>
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="item_id" class="form-label">Produk</label>
                            <select name="item_id" id="item_id" class="form-select @error('item_id') is-invalid @enderror" required>
                                <option value="">Pilih produk…</option>
                                @foreach ($items as $item)
                                    <option value="{{ $item->id }}" @selected(old('item_id') == $item->id)>
                                        {{ $item->code }} — {{ $item->name ?? 'Tanpa nama' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('item_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="helper mt-1">Barang jadi yang akan diproses.</div>
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="planned_quantity" class="form-label">Rencana Kuantitas</label>
                            <div class="input-group">
                                <input type="number" min="1" name="planned_quantity" id="planned_quantity"
                                       class="form-control @error('planned_quantity') is-invalid @enderror"
                                       value="{{ old('planned_quantity', 100) }}" required>
                                <span class="input-group-text">PCS</span>
                                @error('planned_quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="helper mt-1">Jumlah unit yang direncanakan.</div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="supervisor_id" class="form-label">Penanggung Jawab</label>
                            <select name="supervisor_id" id="supervisor_id"
                                    class="form-select @error('supervisor_id') is-invalid @enderror">
                                <option value="">Pilih penanggung jawab…</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}" @selected(old('supervisor_id') == $employee->id)>
                                        {{ $employee->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('supervisor_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="helper mt-1">Opsional. Pilih supervisor produksi.</div>
                        </div>

                        <div class="col-12">
                            <label for="notes" class="form-label">Catatan</label>
                            <textarea name="notes" id="notes" rows="4"
                                      class="form-control @error('notes') is-invalid @enderror"
                                      placeholder="Tambahkan instruksi khusus…">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end gap-2">
                    <a href="{{ route('production.orders.index', ['type' => $type]) }}" class="btn btn-outline-secondary">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        Simpan Perintah
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
