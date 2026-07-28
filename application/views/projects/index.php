<!-- Page Header -->
<div class="page-header">
    <div>
        <h1>Manajemen Proyek</h1>
        <p>Kelola proyek dan status pembayaran termin</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalProyek" id="btnTambahProyek">
        <i class="bi bi-plus-circle me-1"></i> Tambah Proyek
    </button>
</div>

<!-- Table Card -->
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h5 class="mb-0"><i class="bi bi-kanban-fill me-2" style="color:var(--primary-light);"></i>Daftar Proyek</h5>
        <span class="badge" style="background:#DBEAFE;color:#1E40AF;padding:6px 12px;border-radius:20px;font-size:0.78rem;"><?= $total_rows ?> Proyek</span>
    </div>
    
    <!-- Filter Section -->
    <div style="padding: 16px 20px; border-bottom: 1px solid var(--border); background: #FAFBFD;">
        <form action="<?= base_url('projects') ?>" method="GET" class="row g-2 align-items-end mb-0">
            <div class="col-md-3">
                <label class="form-label" style="font-size:0.75rem; margin-bottom:4px;">Pencarian</label>
                <input type="text" name="q" class="form-control form-control-sm" placeholder="Cari proyek / klien..." value="<?= isset($search) ? htmlspecialchars($search) : '' ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label" style="font-size:0.75rem; margin-bottom:4px;">Filter Bulan</label>
                <input type="month" name="month" class="form-control form-control-sm" value="<?= isset($month) ? htmlspecialchars($month) : '' ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label" style="font-size:0.75rem; margin-bottom:4px;">Tanggal Mulai</label>
                <input type="date" name="start_date" class="form-control form-control-sm" value="<?= isset($start_date) ? htmlspecialchars($start_date) : '' ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label" style="font-size:0.75rem; margin-bottom:4px;">Tanggal Selesai</label>
                <input type="date" name="end_date" class="form-control form-control-sm" value="<?= isset($end_date) ? htmlspecialchars($end_date) : '' ?>">
            </div>
            <div class="col-md-3 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-primary w-100" style="padding: 6px;"><i class="bi bi-funnel-fill me-1"></i>Filter</button>
                <?php if (!empty($search) || !empty($month) || !empty($start_date) || !empty($end_date)): ?>
                    <a href="<?= base_url('projects') ?>" class="btn btn-sm btn-light" style="padding: 6px 12px;" title="Reset"><i class="bi bi-arrow-counterclockwise"></i></a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Proyek</th>
                        <th>Klien</th>
                        <th style="text-align:right;">Biaya Total</th>
                        <th style="text-align:right;">Terbayar</th>
                        <th style="text-align:right;">Sisa Tagihan</th>
                        <th>Status Bayar</th>
                        <th>Status Proyek</th>
                        <th style="text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($projects)): ?>
                    <tr>
                        <td colspan="9" style="text-align:center;padding:40px;color:#9CA3AF;">
                            <i class="bi bi-kanban" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
                            Belum ada proyek.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($projects as $i => $p): ?>
                    <tr id="row-project-<?= $p->id ?>">
                        <td style="color:#9CA3AF;font-size:0.8rem;"><?= (isset($offset) ? $offset : 0) + $i + 1 ?></td>
                        <td>
                            <div style="font-weight:600;"><?= htmlspecialchars($p->nama_project) ?></div>
                            <?php if ($p->tgl_mulai): ?>
                            <div style="font-size:0.75rem;color:#9CA3AF;">
                                <?= date('d M Y', strtotime($p->tgl_mulai)) ?>
                            </div>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($p->nama_client) ?></td>
                        <td style="text-align:right;font-weight:600;">
                            Rp <?= number_format($p->biaya_total, 0, ',', '.') ?>
                        </td>
                        <td style="text-align:right;color:#065F46;font-weight:600;">
                            Rp <?= number_format($p->total_terbayar, 0, ',', '.') ?>
                        </td>
                        <td style="text-align:right;">
                            <span style="font-weight:700;color:<?= $p->sisa_tagihan > 0 ? '#991B1B' : '#065F46' ?>;">
                                Rp <?= number_format($p->sisa_tagihan, 0, ',', '.') ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge-status badge-<?= $p->status_pembayaran === 'Lunas' ? 'lunas' : 'belum' ?>">
                                <?= $p->status_pembayaran ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge-status badge-<?= strtolower($p->status_project) ?>">
                                <?= $p->status_project ?>
                            </span>
                        </td>
                        <td style="text-align:center;">
                            <a href="<?= base_url('projects/detail/' . $p->id) ?>" class="btn-action btn-detail me-1" title="Detail & Termin">
                                <i class="bi bi-eye-fill"></i>
                            </a>
                            <button class="btn-action btn-delete btn-delete-project"
                                    data-id="<?= $p->id ?>"
                                    data-nama="<?= htmlspecialchars($p->nama_project) ?>"
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
            Menampilkan <?= $offset + 1 ?> - <?= min($offset + $limit, $total_rows) ?> dari <?= $total_rows ?> proyek
        </div>
        <nav aria-label="Page navigation">
            <ul class="pagination pagination-sm mb-0">
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= base_url('projects?q=' . urlencode($search) . '&month=' . urlencode($month) . '&start_date=' . urlencode($start_date) . '&end_date=' . urlencode($end_date) . '&page=' . ($page - 1)) ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <?php for ($p = 1; $p <= $total_pages; $p++): ?>
                    <li class="page-item <?= $p == $page ? 'active' : '' ?>">
                        <a class="page-link" href="<?= base_url('projects?q=' . urlencode($search) . '&month=' . urlencode($month) . '&start_date=' . urlencode($start_date) . '&end_date=' . urlencode($end_date) . '&page=' . $p) ?>"><?= $p ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= base_url('projects?q=' . urlencode($search) . '&month=' . urlencode($month) . '&start_date=' . urlencode($start_date) . '&end_date=' . urlencode($end_date) . '&page=' . ($page + 1)) ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>

<!-- ── Modal Tambah Proyek ── -->
<div class="modal fade" id="modalProyek" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-kanban-fill me-2"></i>Tambah Proyek Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formProyek">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label for="proyekNama" class="form-label">Nama Proyek <span style="color:#EF4444;">*</span></label>
                            <input type="text" class="form-control" id="proyekNama" name="nama_project" placeholder="Contoh: Renovasi Ruang Tamu" required>
                        </div>
                        <div class="col-md-4">
                            <label for="proyekClient" class="form-label">Klien <span style="color:#EF4444;">*</span></label>
                            <select class="form-select" id="proyekClient" name="client_id" required>
                                <option value="">-- Pilih Klien --</option>
                                <?php foreach ($clients_dropdown as $c): ?>
                                <option value="<?= $c->id ?>"><?= htmlspecialchars($c->nama) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="proyekBiaya" class="form-label">Biaya Total (Rp) <span style="color:#EF4444;">*</span></label>
                            <input type="number" class="form-control" id="proyekBiaya" name="biaya_total" min="0" step="1000" placeholder="0" required>
                        </div>
                        <div class="col-md-3">
                            <label for="proyekStatus" class="form-label">Status Proyek</label>
                            <select class="form-select" id="proyekStatus" name="status_project">
                                <option value="Aktif">Aktif</option>
                                <option value="Ditunda">Ditunda</option>
                                <option value="Selesai">Selesai</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="proyekTglMulai" class="form-label">Tanggal Mulai</label>
                            <input type="date" class="form-control" id="proyekTglMulai" name="tgl_mulai" value="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-12">
                            <label for="proyekDeskripsi" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="proyekDeskripsi" name="deskripsi" rows="2" placeholder="Deskripsi singkat proyek..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSimpanProyek">
                        <i class="bi bi-save me-1"></i><span>Simpan Proyek</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ── Modal Konfirmasi Hapus ── -->
<div class="modal fade" id="modalHapusProyek" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header" style="background:#EF4444;">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle-fill me-2"></i>Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="text-align:center;padding:24px;">
                <p style="margin:0;font-size:0.875rem;">Hapus proyek <strong id="hapusProyekNama"></strong>?<br>
                <span style="font-size:0.8rem;color:#6B7280;">Semua data termin akan ikut terhapus.</span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger btn-sm" id="btnKonfirmasiHapusProyek">
                    <i class="bi bi-trash me-1"></i>Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<script>
var deleteProyekId = null;

// ── SUBMIT Proyek ──
$('#formProyek').on('submit', function(e) {
    e.preventDefault();
    var btn = $('#btnSimpanProyek');
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...');
    $.ajax({
        url: BASE_URL + 'projects/store', type: 'POST', data: $(this).serialize(), dataType: 'json',
        success: function(res) {
            if (res.status === 'success') {
                showToast(res.message, 'success');
                $('#modalProyek').modal('hide');
                setTimeout(function() { location.reload(); }, 800);
            } else {
                showToast(res.message, 'error');
            }
        },
        complete: function() { btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i><span>Simpan Proyek</span>'); }
    });
});

// ── HAPUS Proyek ──
$(document).on('click', '.btn-delete-project', function() {
    deleteProyekId = $(this).data('id');
    $('#hapusProyekNama').text($(this).data('nama'));
    $('#modalHapusProyek').modal('show');
});

$('#btnKonfirmasiHapusProyek').on('click', function() {
    if (!deleteProyekId) return;
    var btn = $(this);
    btn.prop('disabled', true);
    $.ajax({
        url: BASE_URL + 'projects/destroy/' + deleteProyekId, type: 'POST', dataType: 'json',
        success: function(res) {
            if (res.status === 'success') {
                showToast(res.message, 'success');
                $('#modalHapusProyek').modal('hide');
                $('#row-project-' + deleteProyekId).fadeOut(400, function() { $(this).remove(); });
            } else {
                showToast(res.message, 'error');
            }
        },
        complete: function() { btn.prop('disabled', false); }
    });
});
</script>
