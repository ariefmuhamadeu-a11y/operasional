@extends('layouts.app')
@section('title', 'Tambah Supplier')

@section('content')
    <div class="container py-3">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <h5 class="mb-0">Tambah Supplier</h5>
            <a href="{{ route('master.suppliers.index') }}" class="btn btn-outline-secondary btn-sm">&larr; Kembali</a>
        </div>

        <div class="card p-3 rounded-2xl">
            <form action="{{ route('master.suppliers.store') }}" method="POST" class="row g-3">
                @csrf
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
                    <input type="tel" name="phone" inputmode="tel" class="form-control">
                </div>
                <div class="col-12">
                    <label class="form-label">Alamat</label>
                    <textarea name="address" rows="3" class="form-control"></textarea>
                </div>
                <div class="col-12 text-end">
                    <button class="btn btn-primary px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection
