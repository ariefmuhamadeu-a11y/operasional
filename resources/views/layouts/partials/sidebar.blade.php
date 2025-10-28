<aside class="sidebar desktop-fixed d-none d-lg-flex flex-column">
    <div class="p-3">
        <div class="brand mb-2">ERP • Operasional</div>
        <div class="text-muted small">Navigasi</div>
    </div>

    <div class="px-2 pb-3">
        <nav class="nav flex-column">
            <a href="#" class="nav-link active-soft mx-1 mb-1">
                <i class="bi bi-house-door me-2"></i> Home
            </a>

            <!-- MASTER -->
            <div class="nav-group-title mt-2">Master</div>
            <a class="nav-link mx-1 mb-1" data-bs-toggle="collapse" href="#navMaster" role="button"
                aria-expanded="true" aria-controls="navMaster">
                <i class="bi bi-gear me-2"></i> Master
                <i class="bi bi-chevron-down small ms-auto"></i>
            </a>
            <div class="collapse show" id="navMaster">
                <a href="{{ url('/master/items') }}" class="nav-link mx-1 mb-1">
                    <i class="bi bi-box me-2"></i> Master Item
                </a>
                <a href="{{ url('/master/employees') }}" class="nav-link mx-1 mb-1">
                    <i class="bi bi-people me-2"></i> Master Karyawan
                </a>
                <a href="{{ url('/master/suppliers') }}" class="nav-link mx-1 mb-1">
                    <i class="bi bi-truck-front me-2"></i> Master Supplier
                </a>
            </div>

            <!-- PESANAN -->
            <div class="nav-group-title mt-2">Pesanan</div>
            <a class="nav-link mx-1 mb-1" data-bs-toggle="collapse" href="#navPesanan" role="button"
                aria-expanded="false" aria-controls="navPesanan">
                <i class="bi bi-clipboard-check me-2"></i> Pesanan
                <i class="bi bi-chevron-down small ms-auto"></i>
            </a>
            <div class="collapse" id="navPesanan">
                <a href="/shipments" class="nav-link mx-1 mb-1">
                    <i class="bi bi-truck me-2"></i> Pengiriman
                </a>
                <a href="#" class="nav-link mx-1 mb-1">
                    <i class="bi bi-check2-circle me-2"></i> Pesanan Selesai
                </a>
            </div>

            <!-- PEMBELIAN -->
            <div class="nav-group-title mt-2">Pembelian</div>
            <a class="nav-link mx-1 mb-1" data-bs-toggle="collapse" href="#navPembelian" role="button"
                aria-expanded="false" aria-controls="navPembelian">
                <i class="bi bi-bag-check me-2"></i> Pembelian
                <i class="bi bi-chevron-down small ms-auto"></i>
            </a>
            <div class="collapse" id="navPembelian">
                <a href="/purchasing/create" class="nav-link mx-1 mb-1">
                    <i class="bi bi-hexagon-half me-2"></i> Bahan Baku
                </a>
                <a href="#" class="nav-link mx-1 mb-1">
                    <i class="bi bi-puzzle me-2"></i> Bahan Pendukung
                </a>
            </div>

            <!-- PRODUKSI -->
            <div class="nav-group-title mt-2">Produksi</div>
            <a class="nav-link mx-1 mb-1" data-bs-toggle="collapse" href="#navProduksi" role="button"
                aria-expanded="false" aria-controls="navProduksi">
                <i class="bi bi-cpu me-2"></i> Produksi
                <i class="bi bi-chevron-down small ms-auto"></i>
            </a>
            <div class="collapse" id="navProduksi">
                <a href="#" class="nav-link mx-1 mb-1">
                    <i class="bi bi-scissors me-2"></i> Cutting
                </a>
                <a href="#" class="nav-link mx-1 mb-1">
                    <i class="bi bi-threads me-2"></i> Jahit
                </a>
            </div>

            <!-- LAINNYA -->
            <div class="nav-group-title mt-2">Lainnya</div>
            <a href="#" class="nav-link mx-1 mb-1">
                <i class="bi bi-boxes me-2"></i> Gudang
            </a>
            <a href="#" class="nav-link mx-1 mb-1">
                <i class="bi bi-bar-chart-line me-2"></i> Laporan
            </a>
        </nav>
    </div>
</aside>
