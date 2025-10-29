@extends('layouts.app')
@section('title', 'Edit Supplier')

@section('content')
    <div class="container py-3">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <h5 class="mb-0">Edit Supplier</h5>
            <a href="{{ route('master.suppliers.index') }}" class="btn btn-outline-secondary btn-sm">&larr; Kembali</a>
        </div>

        <div class="card p-3 rounded-2xl">
            <form action="{{ route('master.suppliers.update', $supplier->id) }}" method="POST" class="row g-3">
                @csrf @method('PUT')
                <div class="col-6">
                    <label class="form-label">Kode</label>
                    <input type="text" name="code" value="{{ $supplier->code }}" class="form-control" required>
                </div>
                <div class="col-6">
                    <label class="form-label">Jenis</label>
                    <input type="text" name="type" value="{{ $supplier->type }}" class="form-control"
                        placeholder="Distributor / Lokal">
                </div>
                <div class="col-12">
                    <label class="form-label">Nama Toko</label>
                    <input type="text" name="store_name" value="{{ $supplier->store_name }}" class="form-control"
                        required>
                </div>
                <div class="col-12">
                    <label class="form-label">Kelas Item</label>
                    <select name="item_class_id" class="form-select" required>
                        @foreach ($itemClasses as $cls)
                            <option value="{{ $cls->id }}" @selected($supplier->item_class_id == $cls->id)>{{ $cls->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6">
                    <label class="form-label">Telepon</label>
                    <input type="tel" name="phone" value="{{ $supplier->phone }}" class="form-control"
                        inputmode="tel">
                </div>
                <div class="col-12">
                    <label class="form-label">Alamat</label>
                    <textarea name="address" rows="3" class="form-control">{{ $supplier->address }}</textarea>
                </div>
                <div class="col-12 text-end">
                    <button class="btn btn-primary px-4">Update</button>
                </div>
            </form>
        </div>
    </div>
@endsection
