<!-- Page Header -->
<div class="page-header">
    <div>
        <h1>Manajemen Pengeluaran</h1>
        <p>Kelola dan catat pengeluaran operasional serta bahan baku proyek</p>
    </div>
    <button class="btn btn-primary" id="btnTambahPengeluaran" data-bs-toggle="modal" data-bs-target="#modalExpense">
        <i class="bi bi-plus-circle me-1"></i> Catat Pengeluaran
    </button>
</div>

<!-- Table Card -->
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h5 class="mb-0"><i class="bi bi-arrow-up-right-circle-fill me-2" style="color:var(--danger);"></i>Daftar Pengeluaran</h5>
        <div class="d-flex align-items-center gap-2">
            <form action="<?= base_url('expenses') ?>" method="GET" class="d-flex align-items-center gap-1 mb-0">
                <input type="text" name="q" class="form-control form-control-sm" placeholder="Cari pengeluaran..." value="<?= isset($search) ? htmlspecialchars($search) : '' ?>" style="width: 200px;">
                <button type="submit" class="btn btn-sm btn-primary" style="padding: 5px 10px;"><i class="bi bi-search"></i></button>
                <?php if (!empty($search)): ?>
                    <a href="<?= base_url('expenses') ?>" class="btn btn-sm btn-light" style="padding: 5px 10px;"><i class="bi bi-x-circle"></i></a>
                <?php endif; ?>
            </form>
            <span class="badge" style="background:#FEE2E2;color:#991B1B;padding:6px 12px;border-radius:20px;font-size:0.78rem;"><?= $total_rows ?> Item</span>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0" id="tblExpense">
                <thead>
                    <tr>
                        <th style="width:40px;">#</th>
                        <th>Tanggal</th>
                        <th>Kategori</th>
                        <th>Proyek Terkait</th>
                        <th>Keterangan</th>
                        <th style="text-align:right;">Jumlah</th>
                        <th style="width:120px;text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($expenses)): ?>
                    <tr>
                        <td colspan="7" style="text-align:center;padding:40px;color:#9CA3AF;">
                            <i class="bi bi-cash-stack" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
                            Belum ada catatan pengeluaran.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($expenses as $i => $e): ?>
                    <tr id="row-expense-<?= $e->id ?>">
                        <td style="color:#9CA3AF;font-size:0.8rem;"><?= (isset($offset) ? $offset : 0) + $i + 1 ?></td>
                        <td style="font-size:0.825rem;color:#6B7280;">
                            <?= date('d M Y', strtotime($e->tgl)) ?>
                        </td>
                        <td>
                            <span class="badge-status badge-tarik" style="font-weight:600;">
                                <?= htmlspecialchars($e->kategori) ?>
                            </span>
                        </td>
                        <td>
                            <?= $e->nama_project ? htmlspecialchars($e->nama_project) : '<span style="color:#D1D5DB;">Operasional Umum</span>' ?>
                        </td>
                        <td style="font-size:0.825rem;color:#6B7280;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                            <?= $e->keterangan ? htmlspecialchars($e->keterangan) : '-' ?>
                        </td>
                        <td style="text-align:right;font-weight:700;color:#991B1B;">
                            Rp <?= number_format($e->jumlah, 0, ',', '.') ?>
                        </td>
                        <td style="text-align:center;">
                            <button class="btn-action btn-edit btn-edit-expense me-1"
                                    data-id="<?= $e->id ?>"
                                    title="Edit">
                                <i class="bi bi-pencil-fill"></i>
                            </button>
                            <button class="btn-action btn-delete btn-delete-expense"
                                    data-id="<?= $e->id ?>"
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
            Menampilkan <?= $offset + 1 ?> - <?= min($offset + $limit, $total_rows) ?> dari <?= $total_rows ?> item pengeluaran
        </div>
        <nav aria-label="Page navigation">
            <ul class="pagination pagination-sm mb-0">
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= base_url('expenses?q=' . urlencode($search) . '&page=' . ($page - 1)) ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <?php for ($p = 1; $p <= $total_pages; $p++): ?>
                    <li class="page-item <?= $p == $page ? 'active' : '' ?>">
                        <a class="page-link" href="<?= base_url('expenses?q=' . urlencode($search) . '&page=' . $p) ?>"><?= $p ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= base_url('expenses?q=' . urlencode($search) . '&page=' . ($page + 1)) ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>

<!-- ── Modal Tambah/Edit Pengeluaran ── -->
<div class="modal fade" id="modalExpense" tabindex="-1" aria-labelledby="modalExpenseLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background:var(--primary);">
                <h5 class="modal-title" id="modalExpenseLabel">
                    <i class="bi bi-arrow-up-right-circle-fill me-2"></i>Catat Pengeluaran
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formExpense">
                <div class="modal-body">
                    <input type="hidden" id="expenseId" value="">
                    
                    <div class="mb-3">
                        <label for="expenseKategori" class="form-label">Kategori Pengeluaran <span style="color:#EF4444;">*</span></label>
                        <select class="form-select" id="expenseKategori" name="kategori" required>
                            <option value="">-- Pilih Kategori --</option>
                            <option value="Bahan Baku">Bahan Baku</option>
                            <option value="Transportasi">Transportasi</option>
                            <option value="Operasional">Operasional</option>
                            <option value="Gaji / Upah">Gaji / Upah</option>
                            <option value="Alat Kerja">Alat Kerja</option>
                            <option value="Lain-lain">Lain-lain</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="expenseProject" class="form-label">Proyek Terkait (Pilih jika untuk proyek khusus)</label>
                        <select class="form-select" id="expenseProject" name="project_id">
                            <option value="">-- Operasional Umum (Tidak Terkait Proyek) --</option>
                            <?php foreach ($projects_dropdown as $p): ?>
                            <option value="<?= $p->id ?>"><?= htmlspecialchars($p->nama_project) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="expenseJumlah" class="form-label">Jumlah (Rp) <span style="color:#EF4444;">*</span></label>
                            <input type="text" class="form-control input-rupiah" id="expenseJumlah" name="jumlah" placeholder="0" required>
                        </div>
                        <div class="col-md-6">
                            <label for="expenseTgl" class="form-label">Tanggal <span style="color:#EF4444;">*</span></label>
                            <input type="date" class="form-control" id="expenseTgl" name="tgl" value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="expenseKeterangan" class="form-label">Keterangan / Rincian</label>
                        <textarea class="form-control" id="expenseKeterangan" name="keterangan" rows="3" placeholder="Contoh: Pembelian triplek 10 lembar..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSimpanExpense">
                        <i class="bi bi-save me-1"></i><span>Simpan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ── Modal Konfirmasi Hapus ── -->
<div class="modal fade" id="modalHapusExpense" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header" style="background:#EF4444;">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle-fill me-2"></i>Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="text-align:center;padding:28px;">
                <p style="margin:0;font-size:0.9rem;">Hapus catatan pengeluaran ini?<br>
                <span style="font-size:0.8rem;color:#6B7280;">Tindakan ini tidak dapat dibatalkan.</span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger btn-sm" id="btnKonfirmasiHapusExpense">
                    <i class="bi bi-trash me-1"></i>Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<script>
var deleteExpenseId = null;

// ── AUTO-FORMAT RUPIAH ──
$(document).on('keyup', '.input-rupiah', function(e) {
    var value = $(this).val().replace(/[^,\d]/g, '');
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

// ── INISIALISASI SELECT2 SAAT MODAL TERBUKA ──
$('#modalExpense').on('shown.bs.modal', function () {
    $('#expenseKategori').select2({ dropdownParent: $('#modalExpense'), width: '100%' });
    $('#expenseProject').select2({ dropdownParent: $('#modalExpense'), width: '100%' });
});

// Reset modal saat dibuka untuk tambah
$('#btnTambahPengeluaran').on('click', function() {
    $('#formExpense')[0].reset();
    $('#expenseId').val('');
    
    // Reset Select2 ke state awal
    $('#expenseKategori').val('').trigger('change.select2');
    $('#expenseProject').val('').trigger('change.select2');
    
    $('#modalExpenseLabel').html('<i class="bi bi-arrow-up-right-circle-fill me-2"></i>Catat Pengeluaran');
    $('#btnSimpanExpense span').text('Simpan');
});

// ── TAMBAH / EDIT Submit ──
$('#formExpense').on('submit', function(e) {
    e.preventDefault();
    var id   = $('#expenseId').val();
    var url  = id ? BASE_URL + 'expenses/update/' + id : BASE_URL + 'expenses/store';
    
    // Bersihkan titik dari angka sebelum dikirim ke server
    var inputJumlah = $(this).find('.input-rupiah');
    var angkaBersih = inputJumlah.val().replace(/\./g, '');
    inputJumlah.val(angkaBersih);

    var formData = $(this).serialize();
    
    // Kembalikan titik di tampilan form jika proses loading AJAX memakan waktu
    inputJumlah.val(angkaBersih.replace(/\B(?=(\d{3})+(?!\d))/g, "."));

    var btn  = $('#btnSimpanExpense');
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...');

    $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(res) {
            if (res.status === 'success') {
                showToast(res.message, 'success');
                $('#modalExpense').modal('hide');
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

// ── EDIT Click ──
$(document).on('click', '.btn-edit-expense', function() {
    var id = $(this).data('id');
    $.ajax({
        url: BASE_URL + 'expenses/get/' + id,
        type: 'GET',
        dataType: 'json',
        success: function(res) {
            if (res.status === 'success') {
                var d = res.data;
                $('#expenseId').val(d.id);
                
                // Set nilai dan trigger perubahan untuk Select2
                $('#expenseKategori').val(d.kategori).trigger('change.select2');
                $('#expenseProject').val(d.project_id || '').trigger('change.select2');
                
                // Format angka dari database menggunakan titik
                var jumlahFormatted = d.jumlah.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                $('#expenseJumlah').val(jumlahFormatted);
                
                $('#expenseTgl').val(d.tgl);
                $('#expenseKeterangan').val(d.keterangan || '');
                $('#modalExpenseLabel').html('<i class="bi bi-pencil-fill me-2"></i>Edit Pengeluaran');
                $('#btnSimpanExpense span').text('Update');
                $('#modalExpense').modal('show');
            }
        }
    });
});

// ── DELETE Click ──
$(document).on('click', '.btn-delete-expense', function() {
    deleteExpenseId = $(this).data('id');
    $('#modalHapusExpense').modal('show');
});

$('#btnKonfirmasiHapusExpense').on('click', function() {
    if (!deleteExpenseId) return;
    var btn = $(this);
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

    $.ajax({
        url: BASE_URL + 'expenses/destroy/' + deleteExpenseId,
        type: 'POST',
        dataType: 'json',
        success: function(res) {
            if (res.status === 'success') {
                showToast(res.message, 'success');
                $('#modalHapusExpense').modal('hide');
                $('#row-expense-' + deleteExpenseId).fadeOut(400, function() { $(this).remove(); });
            } else {
                showToast(res.message, 'error');
            }
        },
        complete: function() {
            btn.prop('disabled', false).html('<i class="bi bi-trash me-1"></i>Hapus');
        }
    });
});
</script>