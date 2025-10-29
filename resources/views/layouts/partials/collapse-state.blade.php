<script>
    // ====== STATE COLLAPSE (desktop & mobile) ======
    const COLLAPSE_DEFAULTS = {
        // Desktop
        navMaster: 'show',
        navPesanan: 'hide',
        navPembelian: 'hide',
        navProduksi: 'hide',

        // Mobile
        mMaster: 'show',
        mPesanan: 'hide',
        mPembelian: 'hide',
        mProduksi: 'hide',
    };

    const IDS = [
        // Desktop
        'navMaster', 'navPesanan', 'navPembelian', 'navProduksi',
        // Mobile
        'mMaster', 'mPesanan', 'mPembelian', 'mProduksi',
    ];

    document.addEventListener('DOMContentLoaded', () => {
        IDS.forEach(applyCollapseState);
        document.querySelectorAll('.collapse').forEach(el => el.style.visibility = 'visible');
    });

    function applyCollapseState(id) {
        const el = document.getElementById(id);
        if (!el) return;

        const key = 'collapse:' + id;
        const saved = localStorage.getItem(key);
        const desired = saved || COLLAPSE_DEFAULTS[id] || 'hide';

        el.classList.add('collapse');
        el.classList.toggle('show', desired === 'show');

        const togglers = document.querySelectorAll(
            `[data-bs-toggle="collapse"][href="#${id}"], [data-bs-toggle="collapse"][data-bs-target="#${id}"]`
        );
        togglers.forEach(t => t.setAttribute('aria-expanded', desired === 'show' ? 'true' : 'false'));

        bootstrap.Collapse.getOrCreateInstance(el, {
            toggle: false
        });

        el.addEventListener('shown.bs.collapse', () => localStorage.setItem(key, 'show'));
        el.addEventListener('hidden.bs.collapse', () => localStorage.setItem(key, 'hide'));
    }
</script>
