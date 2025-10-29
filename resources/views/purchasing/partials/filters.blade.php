<form method="GET">
    <div class="row g-2 align-items-end">
        <div class="col-12 col-sm-3">
            <label class="form-label">Cari</label>
            <input type="text" name="q" value="{{ $q }}" class="form-control"
                placeholder="Kode / Supplier">
        </div>
        <div class="col-6 col-sm-3">
            <label class="form-label">Supplier</label>
            <select name="supplier_id" class="form-select">
                <option value="">Semua</option>
                @foreach ($supplierOptions as $s)
                    <option value="{{ $s->id }}" {{ (string) $supplierId === (string) $s->id ? 'selected' : '' }}>
                        {{ $s->store_name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-sm-2">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">Semua</option>
                @foreach (['DRAFT', 'TERBIT', 'SEBAGIAN', 'LUNAS'] as $st)
                    <option value="{{ $st }}" {{ $status === $st ? 'selected' : '' }}>{{ $st }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-sm-2">
            <label class="form-label">Kelas Item</label>
            <select name="item_class_id" class="form-select">
                <option value="">Semua</option>
                @foreach ($classOptions as $c)
                    <option value="{{ $c->id }}" {{ (string) $classId === (string) $c->id ? 'selected' : '' }}>
                        {{ $c->code }} — {{ $c->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-sm-2">
            <label class="form-label">Periode</label>
            <div class="d-flex gap-2">
                <input type="date" class="form-control" name="date_from" value="{{ $dateFrom }}">
                <input type="date" class="form-control" name="date_to" value="{{ $dateTo }}">
            </div>
        </div>
        <div class="col-6 col-sm-2">
            <label class="form-label d-block">Belum Lunas</label>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="unpaid_only" id="unpaid_only" value="1"
                    {{ $unpaidOnly ? 'checked' : '' }}>
                <label class="form-check-label" for="unpaid_only">Tampilkan saja</label>
            </div>
        </div>
        <div class="col-12 col-sm-3 ms-sm-auto d-flex gap-2 justify-content-end">
            <button class="btn btn-light w-100 w-sm-auto"><i class="bi bi-funnel me-1"></i>Filter</button>
            <a href="{{ route('purchasing.index') }}" class="btn btn-outline-secondary w-100 w-sm-auto">Reset</a>
        </div>
    </div>
</form>
