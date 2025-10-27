{{-- resources/views/layouts/operasional.blade.php
<!doctype html>
<html
  lang="id"
  data-bs-theme="dark"
>

<head>
  <meta charset="utf-8">
  <meta
    name="viewport"
    content="width=device-width, initial-scale=1"
  >
  <title>@yield('title', 'ERP • Operasional')</title>

  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
  >
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
  >

  <style>
    :root {
      --bg: #0b1220;
      --card: #0e1525;
      --muted: #9aa4b2;
      --text: #e6ebf1;
      --line: #172133;
      --brand: #60a5fa;
      --topbar-h: 64px;
      --sidebar-w: 280px;
    }

    html,
    body {
      height: 100%
    }

    body {
      background: var(--bg);
      color: var(--text)
    }

    .card {
      background: var(--card);
      border-color: var(--line)
    }

    /* === TOPBAR FIXED === */
    .topbar {
      background: rgba(14, 21, 37, .9);
      backdrop-filter: blur(8px);
      border-bottom: 1px solid var(--line);
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 1030;
      height: var(--topbar-h);
    }

    /* Ruang konten di bawah topbar */
    .with-topbar {
      padding-top: var(--topbar-h);
    }

    /* === Sidebar FIXED (desktop) === */
    .sidebar {
      background: linear-gradient(180deg, #0e1525, #0b1220);
      border-right: 1px solid var(--line);
    }

    @media (min-width: 992px) {
      .sidebar.desktop-fixed {
        position: fixed;
        top: var(--topbar-h);
        left: 0;
        width: var(--sidebar-w);
        height: calc(100dvh - var(--topbar-h));
        overflow-y: auto;
        /* scroll di dalam sidebar */
      }

      .content-wrap {
        margin-left: var(--sidebar-w);
        padding: 1rem 1.25rem 2rem;
      }
    }

    /* Link & group */
    .brand {
      font-weight: 700;
      letter-spacing: .3px
    }

    .nav-link {
      color: var(--text);
      opacity: .9;
      border-radius: .6rem;
      padding: .55rem .75rem
    }

    .nav-link:hover {
      opacity: 1;
      background: #141d31
    }

    .nav-group-title {
      font-size: .8rem;
      color: var(--muted);
      text-transform: uppercase;
      letter-spacing: .08em;
      margin: 1rem .25rem .5rem
    }

    .collapse .nav-link {
      padding-left: 2.25rem
    }

    .offcanvas.sidebar {
      width: var(--sidebar-w)
    }

    .active-soft {
      background: rgba(96, 165, 250, .15);
      outline: 1px solid rgba(96, 165, 250, .25)
    }

    .table thead th {
      position: sticky;
      top: 0;
      background: #0f172a;
      z-index: 1
    }

    /* --- Anti-kedip: sembunyikan collapse sebelum disinkronkan --- */
    .collapse {
      visibility: hidden;
    }

    .collapse.show {
      visibility: visible;
    }
  </style>
  @stack('head')
</head>

<body class="with-topbar">
  <!-- TOPBAR (fixed) -->
  <header class="topbar">
    <div class="container-fluid px-lg-4 h-100 px-3">
      <div class="d-flex align-items-center justify-content-between h-100">
        <div class="d-flex align-items-center gap-2">
          <button
            class="btn btn-outline-light d-lg-none"
            type="button"
            data-bs-toggle="offcanvas"
            data-bs-target="#offSidebar"
            aria-controls="offSidebar"
            aria-label="Menu"
          >
            <i class="bi bi-list"></i>
          </button>
          <div class="fw-semibold">Operasional</div>
        </div>
        <div class="d-flex align-items-center gap-2"><!-- quick actions --></div>
      </div>
    </div>
  </header>

  <!-- SIDEBAR DESKTOP (fixed) -->
  <aside class="sidebar desktop-fixed d-none d-lg-flex flex-column">
    <div class="p-3">
      <div class="brand mb-2">ERP • Operasional</div>
      <div class="text-muted small">Navigasi</div>
    </div>
    <div class="px-2 pb-3">
      <nav class="nav flex-column">
        <a
          href="#"
          class="nav-link active-soft mx-1 mb-1"
        >
          <i class="bi bi-house-door me-2"></i> Home
        </a>

        <!-- Pesanan (ganti dari Pengiriman) -->
        <div class="nav-group-title">Pesanan</div>
        <a
          class="nav-link mx-1 mb-1"
          data-bs-toggle="collapse"
          href="#navPesanan"
          role="button"
          aria-expanded="false"
          aria-controls="navPesanan"
        >
          <i class="bi bi-clipboard-check me-2"></i> Pesanan
          <i class="bi bi-chevron-down small ms-auto"></i>
        </a>
        <div
          class="collapse"
          id="navPesanan"
        >
          <a
            href="/shipments"
            class="nav-link mx-1 mb-1"
          ><i class="bi bi-truck me-2"></i> Pengiriman</a>
          <a
            href="#"
            class="nav-link mx-1 mb-1"
          ><i class="bi bi-check2-circle me-2"></i> Pesanan Selesai</a>
        </div>

        <!-- Pembelian -->
        <div class="nav-group-title mt-2">Pembelian</div>
        <a
          class="nav-link mx-1 mb-1"
          data-bs-toggle="collapse"
          href="#navPembelian"
          role="button"
          aria-expanded="false"
          aria-controls="navPembelian"
        >
          <i class="bi bi-bag-check me-2"></i> Pembelian
          <i class="bi bi-chevron-down small ms-auto"></i>
        </a>
        <div
          class="collapse"
          id="navPembelian"
        >
          <a
            href="#"
            class="nav-link mx-1 mb-1"
          ><i class="bi bi-hexagon-half me-2"></i> Bahan Baku</a>
          <a
            href="#"
            class="nav-link mx-1 mb-1"
          ><i class="bi bi-puzzle me-2"></i> Bahan Pendukung</a>
        </div>

        <!-- Produksi -->
        <div class="nav-group-title mt-2">Produksi</div>
        <a
          class="nav-link mx-1 mb-1"
          data-bs-toggle="collapse"
          href="#navProduksi"
          role="button"
          aria-expanded="false"
          aria-controls="navProduksi"
        >
          <i class="bi bi-cpu me-2"></i> Produksi
          <i class="bi bi-chevron-down small ms-auto"></i>
        </a>
        <div
          class="collapse"
          id="navProduksi"
        >
          <a
            href="#"
            class="nav-link mx-1 mb-1"
          ><i class="bi bi-scissors me-2"></i> Cutting</a>
          <a
            href="#"
            class="nav-link mx-1 mb-1"
          ><i class="bi bi-threads me-2"></i> Jahit</a>
        </div>

        <!-- Lainnya -->
        <div class="nav-group-title mt-2">Lainnya</div>
        <a
          href="#"
          class="nav-link mx-1 mb-1"
        ><i class="bi bi-boxes me-2"></i> Gudang</a>
        <a
          href="#"
          class="nav-link mx-1 mb-1"
        ><i class="bi bi-bar-chart-line me-2"></i> Laporan</a>
      </nav>
    </div>
  </aside>

  <!-- OFFCANVAS SIDEBAR (mobile) -->
  <div
    class="offcanvas offcanvas-start sidebar d-lg-none"
    tabindex="-1"
    id="offSidebar"
    aria-labelledby="offSidebarLabel"
  >
    <div class="offcanvas-header">
      <h5
        class="offcanvas-title brand"
        id="offSidebarLabel"
      >ERP • Operasional</h5>
      <button
        type="button"
        class="btn-close btn-close-white"
        data-bs-dismiss="offcanvas"
        aria-label="Close"
      ></button>
    </div>
    <div class="offcanvas-body px-2">
      <nav class="nav flex-column">
        <a
          href="#"
          class="nav-link active-soft mx-1 mb-1"
        >
          <i class="bi bi-house-door me-2"></i> Home
        </a>

        <div class="nav-group-title">Pesanan</div>
        <a
          class="nav-link mx-1 mb-1"
          data-bs-toggle="collapse"
          href="#mPesanan"
          role="button"
          aria-expanded="false"
          aria-controls="mPesanan"
        >
          <i class="bi bi-clipboard-check me-2"></i> Pesanan
          <i class="bi bi-chevron-down small ms-auto"></i>
        </a>
        <div
          class="collapse"
          id="mPesanan"
        >
          <a
            href="#"
            class="nav-link mx-1 mb-1"
          ><i class="bi bi-truck me-2"></i> Pengiriman</a>
          <a
            href="#"
            class="nav-link mx-1 mb-1"
          ><i class="bi bi-check2-circle me-2"></i> Pesanan Selesai</a>
        </div>

        <div class="nav-group-title mt-2">Pembelian</div>
        <a
          class="nav-link mx-1 mb-1"
          data-bs-toggle="collapse"
          href="#mPembelian"
          role="button"
          aria-expanded="false"
          aria-controls="mPembelian"
        >
          <i class="bi bi-bag-check me-2"></i> Pembelian
          <i class="bi bi-chevron-down small ms-auto"></i>
        </a>
        <div
          class="collapse"
          id="mPembelian"
        >
          <a
            href="#"
            class="nav-link mx-1 mb-1"
          ><i class="bi bi-hexagon-half me-2"></i> Bahan Baku</a>
          <a
            href="#"
            class="nav-link mx-1 mb-1"
          ><i class="bi bi-puzzle me-2"></i> Bahan Pendukung</a>
        </div>

        <div class="nav-group-title mt-2">Produksi</div>
        <a
          class="nav-link mx-1 mb-1"
          data-bs-toggle="collapse"
          href="#mProduksi"
          role="button"
          aria-expanded="false"
          aria-controls="mProduksi"
        >
          <i class="bi bi-cpu me-2"></i> Produksi
          <i class="bi bi-chevron-down small ms-auto"></i>
        </a>
        <div
          class="collapse"
          id="mProduksi"
        >
          <a
            href="#"
            class="nav-link mx-1 mb-1"
          ><i class="bi bi-scissors me-2"></i> Cutting</a>
          <a
            href="#"
            class="nav-link mx-1 mb-1"
          ><i class="bi bi-threads me-2"></i> Jahit</a>
        </div>

        <div class="nav-group-title mt-2">Lainnya</div>
        <a
          href="#"
          class="nav-link mx-1 mb-1"
        ><i class="bi bi-boxes me-2"></i> Gudang</a>
        <a
          href="#"
          class="nav-link mx-1 mb-1"
        ><i class="bi bi-bar-chart-line me-2"></i> Laporan</a>
      </nav>
    </div>
  </div>

  <!-- KONTEN -->
  <main class="content-wrap">
    @if (session('status'))
      <div class="alert alert-success alert-dismissible fade show">
        {{ session('status') }}
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="alert"
        ></button>
      </div>
    @endif
    @if (session('error'))
      <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="alert"
        ></button>
      </div>
    @endif

    @yield('content')
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // ====== STATE COLLAPSE (desktop & mobile) ======
    const COLLAPSE_DEFAULTS = {
      navPesanan: 'show',
      navPembelian: 'hide',
      navProduksi: 'hide',
      mPesanan: 'show',
      mPembelian: 'hide',
      mProduksi: 'hide',
    };

    const IDS = ['navPesanan', 'navPembelian', 'navProduksi', 'mPesanan', 'mPembelian', 'mProduksi'];

    // Terapkan state segera setelah DOM siap, lalu lepas visibility guard
    document.addEventListener('DOMContentLoaded', () => {
      IDS.forEach(applyCollapseState);
      // Lepas anti-kedip
      document.querySelectorAll('.collapse').forEach(el => el.style.visibility = 'visible');
    });

    function applyCollapseState(id) {
      const el = document.getElementById(id);
      if (!el) return;

      const key = 'collapse:' + id;
      const saved = localStorage.getItem(key);
      const desired = saved || COLLAPSE_DEFAULTS[id] || 'hide';

      // Pastikan kelas dasar & kondisi awal
      el.classList.add('collapse');
      el.classList.toggle('show', desired === 'show');

      // Sinkron aria-expanded untuk semua toggler yang mengontrol elemen ini
      const togglers = document.querySelectorAll(
        `[data-bs-toggle="collapse"][href="#${id}"], [data-bs-toggle="collapse"][data-bs-target="#${id}"]`
      );
      togglers.forEach(t => t.setAttribute('aria-expanded', desired === 'show' ? 'true' : 'false'));

      // Inisialisasi instance tanpa auto-toggle
      const instance = bootstrap.Collapse.getOrCreateInstance(el, {
        toggle: false
      });

      // Saat user toggle, simpan state terbaru
      el.addEventListener('shown.bs.collapse', () => localStorage.setItem(key, 'show'));
      el.addEventListener('hidden.bs.collapse', () => localStorage.setItem(key, 'hide'));
    }
  </script>

  @stack('scripts')
</body>

</html> --}}



{{-- resources/views/layouts/operasional.blade.php --}}
<!doctype html>
<html
  lang="id"
  data-bs-theme="dark"
>

<head>
  <meta charset="utf-8">
  <meta
    name="viewport"
    content="width=device-width, initial-scale=1"
  >
  <title>@yield('title', 'ERP • Operasional')</title>

  {{-- Vendor CSS --}}
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
  >
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
  >

  {{-- App styles --}}
  @include('layouts.partials.styles')
  @stack('head')
</head>

<body class="with-topbar">
  {{-- TOPBAR --}}
  @include('layouts.partials.topbar')

  {{-- SIDEBAR DESKTOP --}}
  @include('layouts.partials.sidebar')

  {{-- OFFCANVAS MOBILE --}}
  @include('layouts.partials.offcanvas')

  {{-- KONTEN --}}
  <main class="content-wrap">
    @include('layouts.partials.alerts')
    @yield('content')
  </main>

  {{-- Vendor JS --}}
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  {{-- Collapse state script --}}
  @include('layouts.partials.collapse-state')

  @stack('scripts')
</body>

</html>
