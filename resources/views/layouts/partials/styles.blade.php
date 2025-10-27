{{-- resources/views/layouts/partials/styles.blade.php --}}
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
