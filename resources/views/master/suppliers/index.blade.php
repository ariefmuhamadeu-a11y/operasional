@extends('layouts.app')
@section('title', 'ERP • Master Supplier')

@push('head')
    <style>
        :root {
            --panel: #0f172a;
            --card: #0e1525;
            --line: #22324a;
            --muted: #9aa4b2;
            --text: #e6ebf1;
        }

        .sticky-head th {
            position: sticky;
            top: 0;
            background: var(--panel);
            z-index: 5;
        }

        .scroll-y {
            max-height: 65vh;
            overflow-y: auto;
        }

        .rounded-2xl {
            border-radius: 1rem;
        }

        @media (max-width: 576px) {
            .mobile-gap>* {
                margin-bottom: .5rem;
            }

            .btn-icon-sm {
                padding: .35rem .55rem;
                font-size: .9rem;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container py-3">

        {{-- Topbar / Search --}}
        <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
            <h5 class="mb-0">Supplier</h5>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-secondary btn-sm d-sm-none" data-bs-toggle="offcanvas"
                    data-bs-target="#filterCanvas">
                    <i class="bi bi-funnel"></i> Filter
                </button>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createSupplierModal">
                    <i class="bi bi-plus-lg"></i><span class="d-none d-sm-inline ms-1">Tambah</span>
                </button>
            </div>
        </div>

        {{-- Quick search (sticky on mobile) --}}
        <div class="card p-2 mb-3 rounded-2xl">
            <form class="row g-2 align-items-center" id="searchForm">
                <div class="col-12 col-md-6">
                    <input type="search" name="q" id="q" class="form-control" value="{{ request('q') }}"
                        placeholder="Cari kode / nama toko" inputmode="search" autocomplete="off">
                </div>
                <div class="col-12 col-md-4 d-none d-md-block">
                    <select name="item_class_id" id="item_class_id" class="form-select">
                        <option value="">Semua Kelas</option>
                        @foreach ($itemClasses as $cls)
                            <option value="{{ $cls->id }}" @selected(request('item_class_id') == $cls->id)>{{ $cls->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2 d-none d-md-block">
                    <button class="btn btn-outline-light w-100">Terapkan</button>
                </div>
            </form>
        </div>

        {{-- Offcanvas filter (mobile) --}}
        <div class="offcanvas offcanvas-bottom" tabindex="-1" id="filterCanvas" style="height: auto;">
            <div class="offcanvas-header">
                <h6 class="offcanvas-title">Filter</h6>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
            </div>
            <div class="offcanvas-body">
                <form class="mobile-gap" onsubmit="this.querySelector('button[type=submit]').disabled=true;">
                    <div>
                        <label class="form-label small text-muted">Kelas Item</label>
                        <select class="form-select" name="item_class_id" id="item_class_id_mobile">
                            <option value="">Semua Kelas</option>
                            @foreach ($itemClasses as $cls)
                                <option value="{{ $cls->id }}" @selected(request('item_class_id') == $cls->id)>{{ $cls->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="d-grid">
                        <button class="btn btn-primary">Terapkan</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Desktop Table --}}
        <div class="card p-0 d-none d-md-block rounded-2xl">
            <div class="scroll-y">
                <table class="table table-dark table-hover align-middle mb-0 sticky-head">
                    <thead>
                        <tr>
                            <th style="width:10%">Kode</th>
                            <th style="width:22%">Nama Toko</th>
                            <th style="width:18%">Kelas Item</th>
                            <th style="width:14%">Jenis</th>
                            <th style="width:14%">Telepon</th>
                            <th>Alamat</th>
                            <th class="text-end" style="width:10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="desktopList">
                        @forelse($suppliers as $s)
                            <tr>
                                <td class="fw-semibold">{{ $s->code }}</td>
                                <td>{{ $s->store_name }}</td>
                                <td><span
                                        class="badge bg-secondary-subtle text-light">{{ $s->itemClass->name ?? '-' }}</span>
                                </td>
                                <td>{{ $s->type ?? '-' }}</td>
                                <td><a href="tel:{{ $s->phone }}">{{ $s->phone ?? '-' }}</a></td>
                                <td>{{ Str::limit($s->address, 60) }}</td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <a href="{{ route('master.suppliers.edit', $s->id) }}"
                                            class="btn btn-outline-info btn-icon-sm">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('master.suppliers.destroy', $s->id) }}" method="POST"
                                            onsubmit="return confirm('Hapus supplier ini?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-outline-danger btn-icon-sm"><i
                                                    class="bi bi-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">Belum ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Mobile Cards --}}
        <div class="d-md-none">
            @forelse($suppliers as $s)
                <div class="card mb-2 rounded-2xl">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="small text-muted">Kode</div>
                                <div class="fw-semibold">{{ $s->code }}</div>
                            </div>
                            <span class="badge bg-secondary-subtle text-light">{{ $s->itemClass->name ?? '-' }}</span>
                        </div>

                        <div class="mt-2">
                            <div class="small text-muted">Nama Toko</div>
                            <div>{{ $s->store_name }}</div>
                        </div>

                        <div class="row mt-2 g-2">
                            <div class="col-6">
                                <div class="small text-muted">Jenis</div>
                                <div>{{ $s->type ?? '-' }}</div>
                            </div>
                            <div class="col-6">
                                <div class="small text-muted">Telepon</div>
                                <div><a href="tel:{{ $s->phone }}">{{ $s->phone ?? '-' }}</a></div>
                            </div>
                            <div class="col-12">
                                <div class="small text-muted">Alamat</div>
                                <div>{{ $s->address ? Str::limit($s->address, 120) : '-' }}</div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <a href="{{ route('master.suppliers.edit', $s->id) }}"
                                class="btn btn-outline-info btn-sm btn-icon-sm">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <form action="{{ route('master.suppliers.destroy', $s->id) }}" method="POST"
                                onsubmit="return confirm('Hapus supplier ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-outline-danger btn-sm btn-icon-sm"><i
                                        class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center text-muted py-4">Belum ada data</div>
            @endforelse
        </div>
    </div>

    {{-- Modal Create (quick add) --}}
    <div class="modal fade" id="createSupplierModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-2xl">
                <div class="modal-header">
                    <h6 class="modal-title">Tambah Supplier</h6>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('master.suppliers.store') }}" method="POST" class="modal-body">
                    @csrf
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label">Kode</label>
                            <input type="text" name="code" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Jenis</label>
                            <input type="text" name="type" class="form-control" placeholder="Distributor / Lokal">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Nama Toko</label>
                            <input type="text" name="store_name" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Kelas Item</label>
                            <select name="item_class_id" class="form-select" required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach ($itemClasses as $cls)
                                    <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Telepon</label>
                            <input type="tel" name="phone" class="form-control" inputmode="tel">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Alamat</label>
                            <textarea name="address" rows="2" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="mt-3 d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- auto-submit search & sync mobile filter --}}
    @push('scripts')
        <script>
            // Debounce cari
            (function() {
                const q = document.getElementById('q');
                const cls = document.getElementById('item_class_id');
                let t;

                function submitNow() {
                    document.getElementById('searchForm').submit();
                }
                ['input', 'change'].forEach(ev => {
                    q.addEventListener(ev, () => {
                        clearTimeout(t);
                        t = setTimeout(submitNow, 350);
                    });
                    cls.addEventListener(ev, () => {
                        clearTimeout(t);
                        t = setTimeout(submitNow, 0);
                    });
                });

                // Offcanvas filter -> sinkron ke form utama
                const clsMobile = document.getElementById('item_class_id_mobile');
                if (clsMobile) {
                    clsMobile.form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        document.getElementById('item_class_id').value = clsMobile.value || '';
                        submitNow();
                    });
                }
            })();
        </script>
    @endpush
@endsection
