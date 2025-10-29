{{-- resources/views/purchasing/partials/pay-modal.blade.php --}}
<div class="modal fade" id="payModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" class="modal-content card" id="payForm">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Tambah Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <div class="small muted">Faktur</div>
                    <div class="fw-semibold" id="payInvCode">—</div>
                </div>
                <div class="row g-2">
                    <div class="col-sm-6">
                        <label class="form-label">Tanggal</label>
                        <input type="date" class="form-control" name="date" id="payDate" required>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">Akun</label>
                        <select name="account" class="form-select" id="payAccount" required>
                            <option value="CASH">Cash</option>
                            <option value="JAGO">Jago</option>
                            <option value="BCA">BCA</option>
                            <option value="SEABANK">SeaBank</option>
                            <option value="TRANSFER">Transfer</option>
                        </select>
                    </div>
                </div>
                <div class="mt-2">
                    <label class="form-label">Jumlah</label>
                    <input type="text" inputmode="numeric" class="form-control text-end" id="payAmountDisplay"
                        placeholder="Rp0">
                    <input type="hidden" name="amount" id="payAmount">
                    <div class="form-text muted">
                        Sisa saat ini: <span class="mono" id="payRemainText">Rp0</span>
                    </div>
                </div>
                <div class="mt-2">
                    <label class="form-label">Catatan (opsional)</label>
                    <input type="text" name="note" class="form-control" placeholder="No ref/ket lainnya">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-light">Simpan Pembayaran</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
    <script>
        (function() {
            const rupiah = n => new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(Math.round(n || 0));
            const intFromCur = s => parseInt(String(s || '').replace(/[^\d]/g, '') || '0', 10);

            const payModalEl = document.getElementById('payModal');
            const payForm = document.getElementById('payForm');
            const payInvCode = document.getElementById('payInvCode');
            const payDate = document.getElementById('payDate');
            const payAccount = document.getElementById('payAccount');
            const payAmountDisplay = document.getElementById('payAmountDisplay');
            const payAmount = document.getElementById('payAmount');
            const payRemainText = document.getElementById('payRemainText');

            function setPayAmount(val) {
                const raw = Math.max(0, intFromCur(val));
                payAmount.value = String(raw);
                payAmountDisplay.value = rupiah(raw);
                return raw;
            }
            payAmountDisplay?.addEventListener('input', () => setPayAmount(payAmountDisplay.value));

            window.openPayModal = function(inv) {
                payInvCode.textContent = inv.code || '—';
                payDate.value = (inv.date || new Date().toISOString().slice(0, 10));
                payAccount.value = 'CASH';
                setPayAmount(inv.remain || 0);
                payRemainText.textContent = rupiah(inv.remain || 0);
                payForm.setAttribute('action', `{{ url('purchasing') }}/${inv.id}/payments`);
                new bootstrap.Modal(payModalEl).show();
            };

            payForm?.addEventListener('submit', (e) => {
                const raw = intFromCur(payAmountDisplay.value);
                const remain = intFromCur(payRemainText.textContent);
                if (raw <= 0) {
                    e.preventDefault();
                    alert('Jumlah pembayaran harus lebih dari 0.');
                    return;
                }
                if (raw > remain) {
                    e.preventDefault();
                    setPayAmount(remain);
                }
            });
        })();
    </script>
@endpush
