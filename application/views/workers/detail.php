<!-- Breadcrumb -->
<nav aria-label="breadcrumb" style="margin-bottom:16px;">
    <ol class="breadcrumb" style="font-size:0.825rem;background:none;padding:0;margin:0;">
        <li class="breadcrumb-item"><a href="<?= base_url('workers') ?>" style="color:var(--primary-light);">Tukang</a></li>
        <li class="breadcrumb-item active"><?= htmlspecialchars($worker->nama) ?></li>
    </ol>
</nav>

<!-- Page Header -->
<div class="page-header">
    <div>
        <h1>Detail Upah: <?= htmlspecialchars($worker->nama) ?></h1>
        <p>Riwayat transaksi upah dan penarikan tukang</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalLedgerDetail"
            style="background:#10B981;border-color:#10B981;">
        <i class="bi bi-plus-circle me-1"></i> Catat Transaksi
    </button>
</div>

<!-- Saldo Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card stat-card" style="border-top:4px solid #10B981;">
            <div class="stat-icon" style="background:#D1FAE5;color:#065F46;"><i class="bi bi-plus-circle-fill"></i></div>
            <div class="stat-value" style="font-size:1.3rem;">Rp <?= number_format($saldo->total_hak_upah ?? 0, 0, ',', '.') ?></div>
            <div class="stat-label">Total Hak Upah</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card" style="border-top:4px solid #EF4444;">
            <div class="stat-icon" style="background:#FEE2E2;color:#991B1B;"><i class="bi bi-dash-circle-fill"></i></div>
            <div class="stat-value" style="font-size:1.3rem;">Rp <?= number_format($saldo->total_tarik ?? 0, 0, ',', '.') ?></div>
            <div class="stat-label">Total Sudah Ditarik</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card" style="border-top:4px solid var(--primary);">
            <div class="stat-icon" style="background:#DBEAFE;color:var(--primary);"><i class="bi bi-wallet2"></i></div>
            <div class="stat-value" style="font-size:1.3rem;color:var(--primary);">Rp <?= number_format($saldo->sisa_saldo ?? 0, 0, ',', '.') ?></div>
            <div class="stat-label">Sisa Saldo Pending</div>
        </div>
    </div>
</div>

<!-- Riwayat Transaksi -->
<div class="card mb-4">
    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h5 class="mb-0"><i class="bi bi-clock-history me-2" style="color:var(--primary-light);"></i>Riwayat Transaksi</h5>
        <div class="d-flex align-items-center gap-2">
            <form action="<?= base_url('workers/detail/' . $worker->id) ?>" method="GET" class="d-flex align-items-center gap-1 mb-0">
                <input type="text" name="q" class="form-control form-control-sm" placeholder="Cari transaksi..." value="<?= isset($search) ? htmlspecialchars($search) : '' ?>" style="width: 200px;">
                <button type="submit" class="btn btn-sm btn-primary" style="padding: 5px 10px;"><i class="bi bi-search"></i></button>
                <?php if (!empty($search)): ?>
                    <a href="<?= base_url('workers/detail/' . $worker->id) ?>" class="btn btn-sm btn-light" style="padding: 5px 10px;"><i class="bi bi-x-circle"></i></a>
                <?php endif; ?>
            </form>
            <span class="badge" style="background:#DBEAFE;color:#1E40AF;padding:6px 12px;border-radius:20px;font-size:0.78rem;"><?= $total_rows ?> Transaksi</span>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tanggal</th>
                        <th>Jenis</th>
                        <th>Proyek</th>
                        <th>Keterangan</th>
                        <th style="text-align:right;">Jumlah</th>
                        <th style="text-align:center; width:120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($ledger)): ?>
                    <tr>
                        <td colspan="7" style="text-align:center;padding:40px;color:#9CA3AF;">
                            Belum ada riwayat transaksi
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($ledger as $i => $l): ?>
                    <tr>
                        <td style="color:#9CA3AF;font-size:0.8rem;"><?= (isset($offset) ? $offset : 0) + $i + 1 ?></td>
                        <td style="font-size:0.825rem;color:#6B7280;">
                            <?= date('d M Y', strtotime($l->tgl)) ?>
                        </td>
                        <td>
                            <?php if ($l->jenis === 'Hak_Upah'): ?>
                            <span class="badge-status badge-upah">
                                <i class="bi bi-plus-circle me-1"></i>Hak Upah
                            </span>
                            <?php else: ?>
                            <span class="badge-status badge-tarik">
                                <i class="bi bi-dash-circle me-1"></i>Tarik Tunai
                            </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?= $l->nama_project ? htmlspecialchars($l->nama_project) : '<span style="color:#D1D5DB;">-</span>' ?>
                        </td>
                        <td style="font-size:0.825rem;color:#6B7280;">
                            <?= $l->keterangan ? htmlspecialchars($l->keterangan) : '-' ?>
                        </td>
                        <td style="text-align:right;font-weight:700;
                            color:<?= $l->jenis === 'Hak_Upah' ? '#065F46' : '#991B1B' ?>;">
                            <?= ($l->jenis === 'Hak_Upah' ? '+' : '-') ?>Rp <?= number_format($l->jumlah, 0, ',', '.') ?>
                        </td>
                        <td style="text-align:center;">
                            <button class="btn-action btn-edit btn-edit-ledger me-1"
                                    data-id="<?= $l->id ?>"
                                    title="Edit Transaksi">
                                <i class="bi bi-pencil-fill"></i>
                            </button>
                            <button class="btn-action btn-delete btn-delete-ledger"
                                    data-id="<?= $l->id ?>"
                                    title="Hapus Transaksi">
                                <i class="bi bi-trash-fill"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if (isset($total_pages) && $total_pages > 1): ?>
    <div class="card-footer d-flex align-items-center justify-content-between" style="background:var(--bg-card); border-top:1px solid var(--border); padding: 12px 20px;">
        <div style="font-size:0.8rem; color:var(--text-muted);">
            Menampilkan <?= $offset + 1 ?> - <?= min($offset + $limit, $total_rows) ?> dari <?= $total_rows ?> transaksi
        </div>
        <nav aria-label="Page navigation">
            <ul class="pagination pagination-sm mb-0">
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= base_url('workers/detail/' . $worker->id . '?q=' . urlencode($search) . '&page=' . ($page - 1)) ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <?php for ($p = 1; $p <= $total_pages; $p++): ?>
                    <li class="page-item <?= $p == $page ? 'active' : '' ?>">
                        <a class="page-link" href="<?= base_url('workers/detail/' . $worker->id . '?q=' . urlencode($search) . '&page=' . $p) ?>"><?= $p ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= base_url('workers/detail/' . $worker->id . '?q=' . urlencode($search) . '&page=' . ($page + 1)) ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>

<!-- ── Modal Transaksi (Tambah) ── -->
<div class="modal fade" id="modalLedgerDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background:#10B981;">
                <h5 class="modal-title"><i class="bi bi-cash-stack me-2"></i>Catat Transaksi Upah</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formLedgerDetail">
                <div class="modal-body">
                    <input type="hidden" name="worker_id" value="<?= $worker->id ?>">
                    <div class="mb-3">
                        <label class="form-label">Tukang</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($worker->nama) ?>" readonly
                               style="background:#F9FAFB;font-weight:600;">
                    </div>
                    <div class="mb-3">
                        <label for="detailJenis" class="form-label">Jenis Transaksi <span style="color:#EF4444;">*</span></label>
                        <select class="form-select" id="detailJenis" name="jenis" required>
                            <option value="">-- Pilih Jenis --</option>
                            <option value="Hak_Upah">➕ Tambah Hak Upah</option>
                            <option value="Tarik_Tunai">➖ Tarik Tunai</option>
                        </select>
                    </div>
                    <div class="mb-3" id="wrapDetailProject"> 
                        <label for="detailProject" class="form-label">Proyek Terkait (Opsional)</label>
                        <select class="form-select" id="detailProject" name="project_id" style="width: 100%;">
                            <option value="">-- Tidak Terkait Proyek --</option>
                            <?php foreach ($projects_dropdown as $p): ?>
                            <option value="<?= $p->id ?>"><?= htmlspecialchars($p->nama_project) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="detailJumlah" class="form-label">Jumlah (Rp) <span style="color:#EF4444;">*</span></label>
                            <input type="text" class="form-control input-rupiah" id="..." name="jumlah" placeholder="0" required>
                        </div>
                        <div class="col-md-6">
                            <label for="detailTgl" class="form-label">Tanggal <span style="color:#EF4444;">*</span></label>
                            <input type="date" class="form-control" id="detailTgl" name="tgl" value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label for="detailKet" class="form-label">Keterangan</label>
                        <textarea class="form-control" id="detailKet" name="keterangan" rows="2" placeholder="Keterangan transaksi..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSimpanLedgerDetail"
                            style="background:#10B981;border-color:#10B981;">
                        <i class="bi bi-save me-1"></i><span>Simpan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ── Modal Transaksi (Edit) ── -->
<div class="modal fade" id="modalEditLedger" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil-fill me-2"></i>Edit Transaksi Upah</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditLedger">
                <div class="modal-body">
                    <input type="hidden" id="editLedgerId" name="id">
                    <div class="mb-3">
                        <label class="form-label">Tukang</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($worker->nama) ?>" readonly style="background:#F9FAFB;font-weight:600;">
                    </div>
                    <div class="mb-3">
                        <label for="editLedgerJenis" class="form-label">Jenis Transaksi <span style="color:#EF4444;">*</span></label>
                        <select class="form-select" id="editLedgerJenis" name="jenis" required>
                            <option value="Hak_Upah">➕ Tambah Hak Upah</option>
                            <option value="Tarik_Tunai">➖ Tarik Tunai</option>
                        </select>
                    </div>
                    <div class="mb-3" id="wrapEditProject">
                        <label for="editLedgerProject" class="form-label">Proyek Terkait (Opsional)</label>
                        <select class="form-select" id="editLedgerProject" name="project_id">
                            <option value="">-- Tidak Terkait Proyek --</option>
                            <?php foreach ($projects_dropdown as $p): ?>
                            <option value="<?= $p->id ?>"><?= htmlspecialchars($p->nama_project) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="editLedgerJumlah" class="form-label">Jumlah (Rp) <span style="color:#EF4444;">*</span></label>
                            <input type="number" class="form-control" id="editLedgerJumlah" name="jumlah" min="1" step="1000" placeholder="0" required>
                        </div>
                        <div class="col-md-6">
                            <label for="editLedgerTgl" class="form-label">Tanggal <span style="color:#EF4444;">*</span></label>
                            <input type="date" class="form-control" id="editLedgerTgl" name="tgl" required>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label for="editLedgerKet" class="form-label">Keterangan</label>
                        <textarea class="form-control" id="editLedgerKet" name="keterangan" rows="2" placeholder="Keterangan transaksi..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnUpdateLedger">
                        <i class="bi bi-save me-1"></i><span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Hapus Ledger -->
<div class="modal fade" id="modalHapusLedger" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header" style="background:#EF4444;">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle-fill me-2"></i>Hapus Transaksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="text-align:center;padding:24px;">
                <p style="margin:0;font-size:0.875rem;">Hapus transaksi upah ini?<br><span style="font-size:0.8rem;color:#6B7280;">Tindakan ini tidak dapat dibatalkan.</span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger btn-sm" id="btnKonfirmasiHapusLedger">Hapus</button>
            </div>
        </div>
    </div>
</div>

<script>
var deleteLedgerId = null;

// Auto-format angka dengan titik ribuan
$(document).on('keyup', '.input-rupiah', function(e) {
    var value = $(this).val().replace(/[^,\d]/g, ''); // Hanya izinkan angka
    var split = value.split(',');
    var sisa = split[0].length % 3;
    var rupiah = split[0].substr(0, sisa);
    var ribuan = split[0].substr(sisa).match(/\d{3}/gi);

    if (ribuan) {
        var separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }
    
    $(this).val(rupiah);
});

// Add Submit
$('#formLedgerDetail').on('submit', function(e) {
    e.preventDefault();
    
    // --- TAMBAHKAN 3 BARIS INI SEBELUM SERIALIZE ---
    var inputJumlah = $(this).find('.input-rupiah');
    var angkaBersih = inputJumlah.val().replace(/\./g, ''); // Hapus semua titik
    inputJumlah.val(angkaBersih); // Ubah input jadi angka murni sesaat
    // -----------------------------------------------

    var formData = $(this).serialize(); // Ambil data yang sudah bersih
    
    // --- KEMBALIKAN FORMATNYA AGAR TAMPILAN TIDAK RUSAK ---
    inputJumlah.val(angkaBersih.replace(/\B(?=(\d{3})+(?!\d))/g, ".")); 
    // ------------------------------------------------------

    var btn = $('#btnSimpanLedgerDetail');
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...');
    
    $.ajax({
        url: BASE_URL + 'workers/add_ledger',
        type: 'POST',
        data: formData, // <--- UBAH BAGIAN INI SAJA
        dataType: 'json',
        success: function(res) {
            if (res.status === 'success') {
                showToast(res.message, 'success');
                $('#modalLedgerDetail').modal('hide');
                setTimeout(function() { location.reload(); }, 800);
            } else {
                showToast(res.message, 'error');
            }
        },
        complete: function() {
            btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i><span>Simpan</span>');
        }
    });
});

// Edit Button Click - Load data
$(document).on('click', '.btn-edit-ledger', function() {
    var id = $(this).data('id');
    $.ajax({
        url: BASE_URL + 'workers/get_ledger/' + id,
        type: 'GET',
        dataType: 'json',
        success: function(res) {
            if (res.status === 'success') {
                var d = res.data;
                $('#editLedgerId').val(d.id);
                $('#editLedgerJenis').val(d.jenis);
                $('#editLedgerProject').val(d.project_id || '');
                $('#editLedgerJumlah').val(d.jumlah);
                $('#editLedgerTgl').val(d.tgl);
                $('#editLedgerKet').val(d.keterangan);
                $('#modalEditLedger').modal('show');
            }
        }
    });
});

// Edit Submit
$('#formEditLedger').on('submit', function(e) {
    e.preventDefault();
    var id = $('#editLedgerId').val();
    var btn = $('#btnUpdateLedger');
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...');
    $.ajax({
        url: BASE_URL + 'workers/update_ledger/' + id,
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(res) {
            if (res.status === 'success') {
                showToast(res.message, 'success');
                $('#modalEditLedger').modal('hide');
                setTimeout(function() { location.reload(); }, 800);
            } else {
                showToast(res.message, 'error');
            }
        },
        complete: function() {
            btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i><span>Simpan Perubahan</span>');
        }
    });
});

// Delete Click
$(document).on('click', '.btn-delete-ledger', function() {
    deleteLedgerId = $(this).data('id');
    $('#modalHapusLedger').modal('show');
});

// Delete Confirm
$('#btnKonfirmasiHapusLedger').on('click', function() {
    if (!deleteLedgerId) return;
    var btn = $(this);
    btn.prop('disabled', true);
    $.ajax({
        url: BASE_URL + 'workers/destroy_ledger/' + deleteLedgerId,
        type: 'POST',
        dataType: 'json',
        success: function(res) {
            if (res.status === 'success') {
                showToast(res.message, 'success');
                $('#modalHapusLedger').modal('hide');
                setTimeout(function() { location.reload(); }, 800);
            } else {
                showToast(res.message, 'error');
            }
        },
        complete: function() {
            btn.prop('disabled', false);
        }
    });
});

// 1. INISIALISASI SELECT2 SAAT MODAL TERBUKA (Solusi anti-error untuk Bootstrap)
$('#modalLedgerDetail').on('shown.bs.modal', function () {
    $('#detailProject').select2({ dropdownParent: $('#modalLedgerDetail'), width: '100%' });
});

$('#modalEditLedger').on('shown.bs.modal', function () {
    $('#editLedgerProject').select2({ dropdownParent: $('#modalEditLedger'), width: '100%' });
});

$('#modalLedger').on('shown.bs.modal', function () {
    $('#ledgerProject').select2({ dropdownParent: $('#modalLedger'), width: '100%' });
});

// 2. FUNGSI SEMBUNYIKAN PROYEK JIKA TARIK TUNAI
function toggleProyek(jenisSelect, wrapId, selectId) {
    if ($(jenisSelect).val() === 'Tarik_Tunai') {
        $(wrapId).slideUp(200); 
        // Kosongkan dan update tampilan Select2
        $(selectId).val('').trigger('change.select2'); 
    } else {
        $(wrapId).slideDown(200);
    }
}

// 3. PASANG EVENT LISTENER SAAT JENIS TRANSAKSI DIUBAH
$(document).ready(function() {
    $('#detailJenis').on('change', function() { toggleProyek(this, '#wrapDetailProject', '#detailProject'); });
    $('#ledgerJenis').on('change', function() { toggleProyek(this, '#wrapLedgerProject', '#ledgerProject'); });
    $('#editLedgerJenis').on('change', function() { toggleProyek(this, '#wrapEditProject', '#editLedgerProject'); });
});
</script>
