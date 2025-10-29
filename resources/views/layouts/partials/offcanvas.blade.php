<div class="offcanvas offcanvas-start sidebar d-lg-none" tabindex="-1" id="offSidebar" aria-labelledby="offSidebarLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title brand" id="offSidebarLabel">ERP • Operasional</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body px-2">
        <nav class="nav flex-column">
            <a href="#" class="nav-link active-soft mx-1 mb-1">
                <i class="bi bi-house-door me-2"></i> Home
            </a>

            <!-- MASTER -->
            <div class="nav-group-title mt-2">Master</div>
            <a class="nav-link mx-1 mb-1" data-bs-toggle="collapse" href="#mMaster" role="button" aria-expanded="true"
                aria-controls="mMaster">
                <i class="bi bi-gear me-2"></i> Master
                <i class="bi bi-chevron-down small ms-auto"></i>
            </a>
            <div class="collapse show" id="mMaster">
                <a href="{{ url('master/items') }}" class="nav-link mx-1 mb-1">
                    <i class="bi bi-box me-2"></i> Master Item
                </a>
                <a href="{{ url('master/employees') }}" class="nav-link mx-1 mb-1">
                    <i class="bi bi-people me-2"></i> Master Karyawan
                </a>
                <a href="{{ url('master/suppliers') }}" class="nav-link mx-1 mb-1">
                    <i class="bi bi-truck-front me-2"></i> Master Supplier
                </a>
            </div>

            <!-- PESANAN -->
            <div class="nav-group-title mt-2">Pesanan</div>
            <a class="nav-link mx-1 mb-1" data-bs-toggle="collapse" href="#mPesanan" role="button"
                aria-expanded="false" aria-controls="mPesanan">
                <i class="bi bi-clipboard-check me-2"></i> Pesanan
                <i class="bi bi-chevron-down small ms-auto"></i>
            </a>
            <div class="collapse" id="mPesanan">
                <a href="#" class="nav-link mx-1 mb-1">
                    <i class="bi bi-truck me-2"></i> Pengiriman
                </a>
                <a href="#" class="nav-link mx-1 mb-1">
                    <i class="bi bi-check2-circle me-2"></i> Pesanan Selesai
                </a>
            </div>

            <!-- PEMBELIAN -->
            <div class="nav-group-title mt-2">Pembelian</div>
            <a class="nav-link mx-1 mb-1" data-bs-toggle="collapse" href="/purchasing" role="button"
                aria-expanded="false" aria-controls="mPembelian">
                <i class="bi bi-bag-check me-2"></i> Pembelian
                <i class="bi bi-chevron-down small ms-auto"></i>
            </a>
            <div class="collapse" id="mPembelian">
                <a href="/purchasing/create" class="nav-link mx-1 mb-1">
                    <i class="bi bi-hexagon-half me-2"></i> Bahan Baku
                </a>
                <a href="#" class="nav-link mx-1 mb-1">
                    <i class="bi bi-puzzle me-2"></i> Bahan Pendukung
                </a>
            </div>

            <!-- PRODUKSI -->
            <div class="nav-group-title mt-2">Produksi</div>
            <a class="nav-link mx-1 mb-1" data-bs-toggle="collapse" href="#mProduksi" role="button"
                aria-expanded="false" aria-controls="mProduksi">
                <i class="bi bi-cpu me-2"></i> Produksi
                <i class="bi bi-chevron-down small ms-auto"></i>
            </a>
            <div class="collapse" id="mProduksi">
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
</div>
