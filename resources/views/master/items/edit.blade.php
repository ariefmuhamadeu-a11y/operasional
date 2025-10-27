@extends('layouts.app')

@section('title', 'ERP • Edit Item')

@push('head')
    <style>
        .muted {
            color: var(--muted)
        }

        .mono {
            font-variant-numeric: tabular-nums;
            font-family: ui-monospace, SFMono-Regular, Menlo, monospace
        }

        .help {
            font-size: .85rem;
            color: var(--muted)
        }
    </style>
@endpush

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <div class="fw-semibold mb-1">Periksa input:</div>
            <ul class="mb-0">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h5 class="mb-0">Edit Item</h5>
            <div class="muted small">{{ $item->code }}</div>
        </div>
        <a href="{{ route('items.index') }}" class="btn btn-outline-light">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card p-3">
        <form method="POST" action="{{ route('items.update', $item->id) }}" novalidate>
            @csrf
            @method('PUT')
            <div class="row g-3">
                {{-- KODE (readonly) --}}
                <div class="col-md-4">
                    <label class="form-label">Kode Item</label>
                    <input type="text" class="form-control mono" value="{{ $item->code }}" disabled>
                    <div class="help">Kode tidak dapat diubah.</div>
                </div>

                {{-- NAMA --}}
                <div class="col-md-8">
                    <label class="form-label">Nama Item</label>
                    <input type="text" name="name" id="name"
                        class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $item->name) }}"
                        maxlength="150" autocomplete="off">
                    <div class="invalid-feedback">
                        @error('name')
                            {{ $message }}
                        @enderror
                    </div>
                </div>

                {{-- KELAS --}}
                <div class="col-md-4">
                    <label class="form-label">Kelas Item <span class="text-danger">*</span></label>
                    <select class="form-select @error('item_class_id') is-invalid @enderror" id="item_class_id"
                        name="item_class_id" required>
                        @foreach ($itemClasses as $c)
                            <option value="{{ $c->id }}" data-code="{{ $c->code }}"
                                {{ (string) old('item_class_id', $item->item_class_id) === (string) $c->id ? 'selected' : '' }}>
                                {{ $c->name }} ({{ $c->code }})
                            </option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback">
                        @error('item_class_id')
                            {{ $message }}
                        @else
                            Wajib dipilih.
                        @enderror
                    </div>
                    <div class="help">BHBK: UOM otomatis <strong>KG</strong>. BJ: wajib pilih Kategori Produk.</div>
                </div>

                {{-- KATEGORI PRODUK (khusus BJ) --}}
                <div class="col-md-4">
                    <label class="form-label">Kategori Produk (khusus BJ)</label>
                    <select class="form-select @error('product_category_id') is-invalid @enderror" id="product_category_id"
                        name="product_category_id">
                        <option value="">— Pilih Kategori Produk —</option>
                        @foreach ($productCategories as $pc)
                            <option value="{{ $pc->id }}"
                                {{ (string) old('product_category_id', $item->product_category_id) === (string) $pc->id ? 'selected' : '' }}>
                                {{ $pc->name }} ({{ $pc->code }})
                            </option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback">
                        @error('product_category_id')
                            {{ $message }}
                        @enderror
                    </div>
                </div>

                {{-- UOM --}}
                <div class="col-md-2">
                    <label class="form-label">UOM <span class="text-danger">*</span></label>
                    <select class="form-select mono @error('uom') is-invalid @enderror" id="uom" name="uom"
                        required>
                        @foreach (['KG', 'PCS', 'SET', 'MTR'] as $u)
                            <option {{ old('uom', $item->uom) === $u ? 'selected' : '' }}>{{ $u }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback">
                        @error('uom')
                            {{ $message }}
                        @else
                            Wajib diisi.
                        @enderror
                    </div>
                    <div class="help">BHBK → otomatis KG (dikunci).</div>
                </div>

                {{-- HPP --}}
                <div class="col-md-2">
                    <label class="form-label">HPP (Rp) <span class="text-danger">*</span></label>
                    <input type="number" inputmode="numeric" min="0" step="1"
                        class="form-control mono @error('hpp') is-invalid @enderror" id="hpp" name="hpp"
                        value="{{ old('hpp', $item->current_hpp) }}" required>
                    <div class="invalid-feedback">
                        @error('hpp')
                            {{ $message }}
                        @else
                            Wajib diisi.
                        @enderror
                    </div>
                </div>

                {{-- STATUS --}}
                <div class="col-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="is_active" name="is_active"
                            {{ old('is_active', $item->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Aktif</label>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check2"></i> Simpan Perubahan</button>
                <a href="{{ route('items.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        // Rapikan nama (hapus spasi ganda)
        const nameInput = document.getElementById('name');
        nameInput?.addEventListener('input', () => {
            nameInput.value = nameInput.value.replace(/\s{2,}/g, ' ').trimStart();
        });

        // Aturan kelas: BJ => kategori wajib; BHBK => UOM KG (lock)
        const cls = document.getElementById('item_class_id');
        const cat = document.getElementById('product_category_id');
        const uom = document.getElementById('uom');
        const hpp = document.getElementById('hpp');

        function syncRules() {
            const opt = cls?.options[cls.selectedIndex];
            const code = opt ? opt.dataset.code : null;

            if (code === 'BJ') {
                cat.removeAttribute('disabled');
                cat.setAttribute('required', 'required');
            } else {
                cat.removeAttribute('required');
            }

            if (code === 'BHBK') {
                uom.value = 'KG';
                uom.setAttribute('disabled', 'disabled');
            } else {
                uom.removeAttribute('disabled');
            }
        }

        cls?.addEventListener('change', syncRules);
        window.addEventListener('DOMContentLoaded', syncRules);

        // Preview format rupiah saat ketik HPP
        hpp?.addEventListener('input', () => {
            const v = parseInt(hpp.value || '0', 10);
            hpp.title = isNaN(v) ? '' : new Intl.NumberFormat('id-ID').format(v);
        });
    </script>
@endpush
