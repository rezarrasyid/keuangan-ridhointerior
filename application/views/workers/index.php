<!-- Page Header -->
<div class="page-header">
    <div>
        <h1>Upah Tukang</h1>
        <p>Kelola data tukang dan saldo upah mereka</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTukang" id="btnTambahTukang">
            <i class="bi bi-person-plus me-1"></i> Tambah Tukang
        </button>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalLedger" id="btnTambahLedger"
                style="background:#10B981;border-color:#10B981;">
            <i class="bi bi-cash-stack me-1"></i> Catat Transaksi Upah
        </button>
    </div>
</div>

<!-- Tabel Tukang + Saldo -->
<div class="card mb-4">
    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h5 class="mb-0"><i class="bi bi-person-workspace me-2" style="color:var(--primary-light);"></i>Daftar Tukang & Saldo Upah</h5>
        <div class="d-flex align-items-center gap-2">
            <form action="<?= base_url('workers') ?>" method="GET" class="d-flex align-items-center gap-1 mb-0">
                <input type="text" name="q" class="form-control form-control-sm" placeholder="Cari tukang..." value="<?= isset($search) ? htmlspecialchars($search) : '' ?>" style="width: 200px;">
                <button type="submit" class="btn btn-sm btn-primary" style="padding: 5px 10px;"><i class="bi bi-search"></i></button>
                <?php if (!empty($search)): ?>
                    <a href="<?= base_url('workers') ?>" class="btn btn-sm btn-light" style="padding: 5px 10px;"><i class="bi bi-x-circle"></i></a>
                <?php endif; ?>
            </form>
            <span class="badge" style="background:#DBEAFE;color:#1E40AF;padding:6px 12px;border-radius:20px;font-size:0.78rem;"><?= $total_rows ?> Tukang</span>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Tukang</th>
                        <th>Telepon</th>
                        <th>Kategori</th>
                        <th style="text-align:right;">Total Hak Upah</th>
                        <th style="text-align:right;">Total Sudah Ditarik</th>
                        <th style="text-align:right;">Sisa Saldo</th>
                        <th style="text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($workers)): ?>
                    <tr>
                        <td colspan="8" style="text-align:center;padding:40px;color:#9CA3AF;">
                            <i class="bi bi-person-workspace" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
                            Belum ada tukang.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($workers as $i => $w): ?>
                    <tr id="row-worker-<?= $w->id ?>">
                        <td style="color:#9CA3AF;font-size:0.8rem;"><?= (isset($offset) ? $offset : 0) + $i + 1 ?></td>
                        <td>
                            <div style="font-weight:600;"><?= htmlspecialchars($w->nama) ?></div>
                        </td>
                        <td>
                            <?= $w->telepon ? htmlspecialchars($w->telepon) : '<span style="color:#D1D5DB;">-</span>' ?>
                        </td>
                        <td>
                            <span class="badge-status badge-<?= strtolower($w->kategori) ?>">
                                <?= $w->kategori ?>
                            </span>
                        </td>
                        <td style="text-align:right;font-weight:600;color:#065F46;">
                            Rp <?= number_format($w->total_hak_upah, 0, ',', '.') ?>
                        </td>
                        <td style="text-align:right;font-weight:600;color:#991B1B;">
                            Rp <?= number_format($w->total_tarik, 0, ',', '.') ?>
                        </td>
                        <td style="text-align:right;">
                            <span style="font-weight:800;font-size:0.95rem;color:<?= $w->sisa_saldo > 0 ? '#1E40AF' : '#374151' ?>;">
                                Rp <?= number_format($w->sisa_saldo, 0, ',', '.') ?>
                            </span>
                        </td>
                        <td style="text-align:center;">
                            <a href="<?= base_url('workers/detail/' . $w->id) ?>" class="btn-action btn-detail me-1" title="Detail Ledger">
                                <i class="bi bi-eye-fill"></i>
                            </a>
                            <button class="btn-action btn-edit btn-edit-tukang me-1"
                                    data-id="<?= $w->id ?>"
                                    title="Edit">
                                <i class="bi bi-pencil-fill"></i>
                            </button>
                            <button class="btn-action btn-delete btn-delete-tukang"
                                    data-id="<?= $w->id ?>"
                                    data-nama="<?= htmlspecialchars($w->nama) ?>"
                                    title="Hapus">
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
            Menampilkan <?= $offset + 1 ?> - <?= min($offset + $limit, $total_rows) ?> dari <?= $total_rows ?> tukang
        </div>
        <nav aria-label="Page navigation">
            <ul class="pagination pagination-sm mb-0">
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= base_url('workers?q=' . urlencode($search) . '&page=' . ($page - 1)) ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <?php for ($p = 1; $p <= $total_pages; $p++): ?>
                    <li class="page-item <?= $p == $page ? 'active' : '' ?>">
                        <a class="page-link" href="<?= base_url('workers?q=' . urlencode($search) . '&page=' . $p) ?>"><?= $p ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= base_url('workers?q=' . urlencode($search) . '&page=' . ($page + 1)) ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>


<!-- ── Modal Tambah/Edit Tukang ── -->
<div class="modal fade" id="modalTukang" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTukangLabel">
                    <i class="bi bi-person-plus-fill me-2"></i>Tambah Tukang
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formTukang">
                <div class="modal-body">
                    <input type="hidden" id="tukangId" value="">
                    <div class="mb-3">
                        <label for="tukangNama" class="form-label">Nama Tukang <span style="color:#EF4444;">*</span></label>
                        <input type="text" class="form-control" id="tukangNama" name="nama" placeholder="Nama lengkap tukang" required>
                    </div>
                    <div class="mb-3">
                        <label for="tukangTelepon" class="form-label">Nomor Telepon</label>
                        <input type="tel" class="form-control" id="tukangTelepon" name="telepon" placeholder="08xxxxxxxxxx">
                    </div>
                    <div class="mb-3">
                        <label for="tukangKategori" class="form-label">Kategori <span style="color:#EF4444;">*</span></label>
                        <select class="form-select" id="tukangKategori" name="kategori" required>
                            <option value="">-- Pilih Kategori --</option>
                            <option value="Senior">Senior</option>
                            <option value="Junior">Junior</option>
                            <option value="Baru">Baru</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSimpanTukang">
                        <i class="bi bi-save me-1"></i><span>Simpan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ── Modal Transaksi Upah / Tarik Tunai ── -->
<div class="modal fade" id="modalLedger" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background:#10B981;">
                <h5 class="modal-title">
                    <i class="bi bi-cash-stack me-2"></i>Catat Transaksi Upah
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formLedger">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="ledgerWorker" class="form-label">Tukang <span style="color:#EF4444;">*</span></label>
                        <select class="form-select" id="ledgerWorker" name="worker_id" required>
                            <option value="">-- Pilih Tukang --</option>
                            <?php foreach ($workers as $w): ?>
                            <option value="<?= $w->id ?>"><?= htmlspecialchars($w->nama) ?> (Saldo: Rp <?= number_format($w->sisa_saldo, 0, ',', '.') ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="ledgerJenis" class="form-label">Jenis Transaksi <span style="color:#EF4444;">*</span></label>
                        <select class="form-select" id="ledgerJenis" name="jenis" required>
                            <option value="">-- Pilih Jenis --</option>
                            <option value="Hak_Upah">➕ Tambah Hak Upah</option>
                            <option value="Tarik_Tunai">➖ Tarik Tunai</option>
                        </select>
                    </div>
                    <div class="mb-3" id="wrapLedgerProject">
                        <label for="ledgerProject" class="form-label">Proyek Terkait (Opsional)</label>
                        <select class="form-select" id="ledgerProject" name="project_id">
                            <option value="">-- Tidak Terkait Proyek --</option>
                            <?php foreach ($projects_dropdown as $p): ?>
                            <option value="<?= $p->id ?>"><?= htmlspecialchars($p->nama_project) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="ledgerJumlah" class="form-label">Jumlah (Rp) <span style="color:#EF4444;">*</span></label>
                            <input type="number" class="form-control" id="ledgerJumlah" name="jumlah" min="1" step="1000" placeholder="0" required>
                        </div>
                        <div class="col-md-6">
                            <label for="ledgerTgl" class="form-label">Tanggal <span style="color:#EF4444;">*</span></label>
                            <input type="date" class="form-control" id="ledgerTgl" name="tgl" value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label for="ledgerKet" class="form-label">Keterangan</label>
                        <textarea class="form-control" id="ledgerKet" name="keterangan" rows="2" placeholder="Contoh: Upah pengerjaan dapur..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSimpanLedger"
                            style="background:#10B981;border-color:#10B981;">
                        <i class="bi bi-save me-1"></i><span>Simpan Transaksi</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ── Modal Konfirmasi Hapus Tukang ── -->
<div class="modal fade" id="modalHapusTukang" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header" style="background:#EF4444;">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle-fill me-2"></i>Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="text-align:center;padding:28px;">
                <p style="margin:0;font-size:0.9rem;">Hapus tukang <strong id="hapusTukangNama"></strong>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger btn-sm" id="btnKonfirmasiHapusTukang">
                    <i class="bi bi-trash me-1"></i>Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<script>
var deleteTukangId = null;

// Reset modal tukang
$('#btnTambahTukang').on('click', function() {
    $('#formTukang')[0].reset();
    $('#tukangId').val('');
    $('#modalTukangLabel').html('<i class="bi bi-person-plus-fill me-2"></i>Tambah Tukang');
    $('#btnSimpanTukang span').text('Simpan');
});

// ── SUBMIT Tukang ──
$('#formTukang').on('submit', function(e) {
    e.preventDefault();
    var id  = $('#tukangId').val();
    var url = id ? BASE_URL + 'workers/update/' + id : BASE_URL + 'workers/store';
    var btn = $('#btnSimpanTukang');
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...');
    $.ajax({
        url: url, type: 'POST', data: $(this).serialize(), dataType: 'json',
        success: function(res) {
            if (res.status === 'success') {
                showToast(res.message, 'success');
                $('#modalTukang').modal('hide');
                setTimeout(function() { location.reload(); }, 800);
            } else {
                showToast(res.message, 'error');
            }
        },
        complete: function() { btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i><span>Simpan</span>'); }
    });
});

// ── EDIT Tukang ──
$(document).on('click', '.btn-edit-tukang', function() {
    var id = $(this).data('id');
    $.ajax({
        url: BASE_URL + 'workers/get/' + id, type: 'GET', dataType: 'json',
        success: function(res) {
            if (res.status === 'success') {
                var d = res.data;
                $('#tukangId').val(d.id);
                $('#tukangNama').val(d.nama);
                $('#tukangTelepon').val(d.telepon);
                $('#tukangKategori').val(d.kategori);
                $('#modalTukangLabel').html('<i class="bi bi-pencil-fill me-2"></i>Edit Tukang');
                $('#btnSimpanTukang span').text('Update');
                $('#modalTukang').modal('show');
            }
        }
    });
});

// ── HAPUS Tukang ──
$(document).on('click', '.btn-delete-tukang', function() {
    deleteTukangId = $(this).data('id');
    $('#hapusTukangNama').text($(this).data('nama'));
    $('#modalHapusTukang').modal('show');
});

$('#btnKonfirmasiHapusTukang').on('click', function() {
    if (!deleteTukangId) return;
    var btn = $(this);
    btn.prop('disabled', true);
    $.ajax({
        url: BASE_URL + 'workers/destroy/' + deleteTukangId, type: 'POST', dataType: 'json',
        success: function(res) {
            if (res.status === 'success') {
                showToast(res.message, 'success');
                $('#modalHapusTukang').modal('hide');
                $('#row-worker-' + deleteTukangId).fadeOut(400, function() { $(this).remove(); });
            } else {
                showToast(res.message, 'error');
            }
        },
        complete: function() { btn.prop('disabled', false); }
    });
});

// ── SUBMIT Ledger ──
$('#formLedger').on('submit', function(e) {
    e.preventDefault();
    var btn = $('#btnSimpanLedger');
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...');
    $.ajax({
        url: BASE_URL + 'workers/add_ledger', type: 'POST', data: $(this).serialize(), dataType: 'json',
        success: function(res) {
            if (res.status === 'success') {
                showToast(res.message, 'success');
                $('#modalLedger').modal('hide');
                setTimeout(function() { location.reload(); }, 800);
            } else {
                showToast(res.message, 'error');
            }
        },
        complete: function() { btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i><span>Simpan Transaksi</span>'); }
    });
});

$(document).ready(function() {
    // 1. INISIALISASI PENCARIAN DROPDOWN (SELECT2)
    // Catatan: Harus pakai dropdownParent agar input pencariannya bisa diklik di dalam Modal Bootstrap
    if ($('#detailProject').length) {
        $('#detailProject').select2({ dropdownParent: $('#modalLedgerDetail'), width: '100%' });
    }
    if ($('#editLedgerProject').length) {
        $('#editLedgerProject').select2({ dropdownParent: $('#modalEditLedger'), width: '100%' });
    }
    if ($('#ledgerProject').length) {
        $('#ledgerProject').select2({ dropdownParent: $('#modalLedger'), width: '100%' });
    }

    // 2. FUNGSI SEMBUNYIKAN PROYEK JIKA TARIK TUNAI
    function toggleProyek(jenisSelect, wrapId, selectId) {
        if ($(jenisSelect).val() === 'Tarik_Tunai') {
            $(wrapId).slideUp(200); // Sembunyikan dengan animasi halus
            $(selectId).val('').trigger('change'); // Kosongkan pilihan proyek
        } else {
            $(wrapId).slideDown(200); // Tampilkan kembali jika Hak Upah
        }
    }

    // 3. PASANG EVENT LISTENER SAAT JENIS TRANSAKSI DIUBAH
    $('#detailJenis').on('change', function() { toggleProyek(this, '#wrapDetailProject', '#detailProject'); });
    $('#ledgerJenis').on('change', function() { toggleProyek(this, '#wrapLedgerProject', '#ledgerProject'); });
    $('#editLedgerJenis').on('change', function() { toggleProyek(this, '#wrapEditProject', '#editLedgerProject'); });

    // Pemicu khusus untuk Form Edit (saat data sukses dimuat ke modal)
    // Tambahkan ini di dalam AJAX GET Edit (di blok success) setelah value diset:
    // $('#editLedgerJenis').trigger('change');
});
</script>
