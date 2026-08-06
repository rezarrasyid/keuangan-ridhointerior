<?php
$pct_terbayar = $project->biaya_total > 0 ? min(100, ($project->total_terbayar / $project->biaya_total) * 100) : 0;
?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" style="margin-bottom:16px;">
    <ol class="breadcrumb" style="font-size:0.825rem;background:none;padding:0;margin:0;">
        <li class="breadcrumb-item"><a href="<?= base_url('projects') ?>" style="color:var(--primary-light);">Proyek</a></li>
        <li class="breadcrumb-item active"><?= htmlspecialchars($project->nama_project) ?></li>
    </ol>
</nav>

<!-- Page Header -->
<div class="page-header">
    <div>
        <h1><?= htmlspecialchars($project->nama_project) ?></h1>
        <p>Klien: <strong><?= htmlspecialchars($project->nama_client) ?></strong>
            <?= $project->telepon_client ? ' · ' . htmlspecialchars($project->telepon_client) : '' ?>
        </p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTermin">
        <i class="bi bi-plus-circle me-1"></i> Tambah Termin
    </button>
</div>

<!-- Project Info Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card stat-card" style="border-top:4px solid var(--primary);">
            <div class="stat-icon" style="background:#DBEAFE;color:var(--primary);"><i class="bi bi-currency-dollar"></i></div>
            <div class="stat-value" style="font-size:1.2rem;">Rp <?= number_format($project->biaya_total, 0, ',', '.') ?></div>
            <div class="stat-label">Biaya Total Proyek</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card" style="border-top:4px solid #10B981;">
            <div class="stat-icon" style="background:#D1FAE5;color:#065F46;"><i class="bi bi-check-circle-fill"></i></div>
            <div class="stat-value" style="font-size:1.2rem;">Rp <?= number_format($project->total_terbayar, 0, ',', '.') ?></div>
            <div class="stat-label">Total Sudah Terbayar</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card" style="border-top:4px solid <?= $project->sisa_tagihan > 0 ? '#EF4444' : '#10B981' ?>;">
            <div class="stat-icon" style="background:<?= $project->sisa_tagihan > 0 ? '#FEE2E2' : '#D1FAE5' ?>;color:<?= $project->sisa_tagihan > 0 ? '#991B1B' : '#065F46' ?>;"><i class="bi bi-hourglass-split"></i></div>
            <div class="stat-value" style="font-size:1.2rem;color:<?= $project->sisa_tagihan > 0 ? '#991B1B' : '#065F46' ?>;">
                Rp <?= number_format($project->sisa_tagihan, 0, ',', '.') ?>
            </div>
            <div class="stat-label">Sisa Tagihan</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card" style="padding:22px;border-top:4px solid #F59E0B;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
                <span style="font-size:0.78rem;font-weight:700;color:#6B7280;text-transform:uppercase;letter-spacing:0.5px;">Status</span>
                <span class="badge-status badge-<?= $project->status_pembayaran === 'Lunas' ? 'lunas' : 'belum' ?>">
                    <?= $project->status_pembayaran ?>
                </span>
            </div>
            <div style="background:#F3F4F6;border-radius:10px;height:10px;overflow:hidden;margin-bottom:8px;">
                <div style="width:<?= round($pct_terbayar) ?>%;height:100%;background:linear-gradient(90deg,#10B981,#3B82F6);border-radius:10px;transition:width 0.8s ease;"></div>
            </div>
            <div style="font-size:0.8rem;font-weight:700;color:var(--primary);"><?= round($pct_terbayar) ?>% Terbayar</div>
        </div>
    </div>
</div>

<!-- Daftar Termin / Pembayaran -->
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5><i class="bi bi-receipt me-2" style="color:var(--primary-light);"></i>Riwayat Pembayaran / Termin</h5>
        <span class="badge" style="background:#DBEAFE;color:#1E40AF;padding:6px 12px;border-radius:20px;font-size:0.78rem;"><?= count($payments) ?> Termin</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Pembayaran</th>
                        <th>Jenis</th>
                        <th>Tanggal</th>
                        <th>Keterangan</th>
                        <th style="text-align:right;">Jumlah</th>
                        <th style="text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tbodyTermin">
                    <?php if (empty($payments)): ?>
                    <tr id="emptyTermin">
                        <td colspan="7" style="text-align:center;padding:40px;color:#9CA3AF;">
                            <i class="bi bi-receipt" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
                            Belum ada pembayaran. Tambah termin/DP pertama!
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($payments as $i => $pay): ?>
                    <tr id="row-payment-<?= $pay->id ?>">
                        <td style="color:#9CA3AF;font-size:0.8rem;"><?= $i + 1 ?></td>
                        <td style="font-weight:600;"><?= htmlspecialchars($pay->nama_pembayaran) ?></td>
                        <td>
                            <span class="badge-status" style="background:<?= $pay->jenis === 'DP' ? '#EDE9FE' : '#DBEAFE' ?>;color:<?= $pay->jenis === 'DP' ? '#5B21B6' : '#1E40AF' ?>;">
                                <?= $pay->jenis ?>
                            </span>
                        </td>
                        <td style="font-size:0.825rem;color:#6B7280;">
                            <?= date('d M Y', strtotime($pay->tgl)) ?>
                        </td>
                        <td style="font-size:0.825rem;color:#6B7280;">
                            <?= $pay->keterangan ? htmlspecialchars($pay->keterangan) : '-' ?>
                        </td>
                        <td style="text-align:right;font-weight:700;color:#065F46;">
                            Rp <?= number_format($pay->jumlah, 0, ',', '.') ?>
                        </td>
                        <td style="table-actiontext-align:center;">
                            <button class="btn-action btn-edit btn-edit-payment me-1"
                                    data-id="<?= $pay->id ?>"
                                    title="Edit Termin">
                                <i class="bi bi-pencil-fill"></i>
                            </button>
                            <button class="btn-action btn-delete btn-delete-payment"
                                    data-id="<?= $pay->id ?>"
                                    title="Hapus Termin">
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
</div>

<!-- ── Modal Tambah Termin ── -->
<div class="modal fade" id="modalTermin" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-circle-fill me-2"></i>Tambah Termin / Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formTermin">
                <div class="modal-body">
                    <input type="hidden" name="project_id" value="<?= $project->id ?>">

                    <!-- Sisa Tagihan Info -->
                    <div style="background:#EFF6FF;border:1px solid #BFDBFE;border-radius:10px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:10px;">
                        <i class="bi bi-info-circle-fill" style="color:var(--primary-light);font-size:1.1rem;"></i>
                        <div>
                            <div style="font-size:0.8rem;font-weight:600;color:#1E40AF;">Sisa Tagihan Saat Ini</div>
                            <div style="font-size:1.1rem;font-weight:800;color:var(--primary);">
                                Rp <?= number_format($project->sisa_tagihan, 0, ',', '.') ?>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-7">
                            <label for="terminNama" class="form-label">Nama Pembayaran <span style="color:#EF4444;">*</span></label>
                            <input type="text" class="form-control" id="terminNama" name="nama_pembayaran"
                                   placeholder="Contoh: DP, Termin 1, Pelunasan" required>
                        </div>
                        <div class="col-md-5">
                            <label for="terminJenis" class="form-label">Jenis <span style="color:#EF4444;">*</span></label>
                            <select class="form-select" id="terminJenis" name="jenis" required>
                                <option value="DP">DP</option>
                                <option value="Termin">Termin</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="terminJumlah" class="form-label">Jumlah (Rp) <span style="color:#EF4444;">*</span></label>
                            <input type="text" class="form-control input-rupiah" id="terminJumlah" name="jumlah" placeholder="0" required>
                        </div>
                        <div class="col-md-6">
                            <label for="terminTgl" class="form-label">Tanggal <span style="color:#EF4444;">*</span></label>
                            <input type="date" class="form-control" id="terminTgl" name="tgl"
                                   value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-12">
                            <label for="terminKet" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="terminKet" name="keterangan" rows="2"
                                      placeholder="Keterangan tambahan..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSimpanTermin">
                        <i class="bi bi-save me-1"></i><span>Simpan Termin</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Hapus Termin -->
<div class="modal fade" id="modalHapusTermin" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header" style="background:#EF4444;">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle-fill me-2"></i>Hapus Termin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="text-align:center;padding:24px;">
                <p style="margin:0;font-size:0.875rem;">Hapus entri termin ini?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger btn-sm" id="btnKonfirmasiHapusTermin">Hapus</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Termin -->
<div class="modal fade" id="modalEditTermin" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil-fill me-2"></i>Edit Termin / Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditTermin">
                <div class="modal-body">
                    <input type="hidden" id="editTerminId" name="id">
                    <div class="row g-3">
                        <div class="col-md-7">
                            <label for="editTerminNama" class="form-label">Nama Pembayaran <span style="color:#EF4444;">*</span></label>
                            <input type="text" class="form-control" id="editTerminNama" name="nama_pembayaran" required>
                        </div>
                        <div class="col-md-5">
                            <label for="editTerminJenis" class="form-label">Jenis <span style="color:#EF4444;">*</span></label>
                            <select class="form-select" id="editTerminJenis" name="jenis" required>
                                <option value="DP">DP</option>
                                <option value="Termin">Termin</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="editTerminJumlah" class="form-label">Jumlah (Rp) <span style="color:#EF4444;">*</span></label>
                            <input type="text" class="form-control input-rupiah" id="editTerminJumlah" name="jumlah" placeholder="0" required>
                        </div>
                        <div class="col-md-6">
                            <label for="editTerminTgl" class="form-label">Tanggal <span style="color:#EF4444;">*</span></label>
                            <input type="date" class="form-control" id="editTerminTgl" name="tgl" required>
                        </div>
                        <div class="col-12">
                            <label for="editTerminKet" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="editTerminKet" name="keterangan" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnUpdateTermin">
                        <i class="bi bi-save me-1"></i><span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
var deletePaymentId = null;

// Auto-format angka dengan titik ribuan
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

// ── SUBMIT Termin ──
$('#formTermin').on('submit', function(e) {
    e.preventDefault();
    
    var inputJumlah = $(this).find('.input-rupiah');
    var angkaBersih = inputJumlah.val().replace(/\./g, '');
    inputJumlah.val(angkaBersih);

    var formData = $(this).serialize();
    inputJumlah.val(angkaBersih.replace(/\B(?=(\d{3})+(?!\d))/g, "."));

    var btn = $('#btnSimpanTermin');
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...');
    $.ajax({
        url: BASE_URL + 'projects/add_payment', 
        type: 'POST', 
        data: formData, 
        dataType: 'json',
        success: function(res) {
            if (res.status === 'success') {
                showToast(res.message, 'success');
                $('#modalTermin').modal('hide');
                setTimeout(function() { location.reload(); }, 800);
            } else {
                showToast(res.message, 'error');
            }
        },
        complete: function() { btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i><span>Simpan Termin</span>'); }
    });
});

// ── EDIT Termin (Click) ──
$(document).on('click', '.btn-edit-payment', function() {
    var id = $(this).data('id');
    $.ajax({
        url: BASE_URL + 'projects/get_payment/' + id,
        type: 'GET',
        dataType: 'json',
        success: function(res) {
            if (res.status === 'success') {
                var d = res.data;
                $('#editTerminId').val(d.id);
                $('#editTerminNama').val(d.nama_pembayaran);
                $('#editTerminJenis').val(d.jenis);
                
                // Buang desimal (.00) dari database dengan mengubahnya ke angka bulat (Integer)
                var angkaBulat = Math.round(d.jumlah);

                // Masukkan angka yang sudah dibulatkan, lalu pancing format Rupiah otomatis
                $('#editTerminJumlah').val(angkaBulat);
                $('#editTerminJumlah').trigger('keyup');
                
                $('#editTerminTgl').val(d.tgl);
                $('#editTerminKet').val(d.keterangan || '');
                $('#modalEditTermin').modal('show');
            }
        }
    });
});

// ── UPDATE Termin ──
$('#formEditTermin').on('submit', function(e) {
    e.preventDefault();
    
    var inputJumlah = $(this).find('.input-rupiah');
    var angkaBersih = inputJumlah.val().replace(/\./g, '');
    inputJumlah.val(angkaBersih);
    
    var formData = $(this).serialize();
    inputJumlah.val(angkaBersih.replace(/\B(?=(\d{3})+(?!\d))/g, "."));

    var id = $('#editTerminId').val();
    var btn = $('#btnUpdateTermin');
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...');
    $.ajax({
        url: BASE_URL + 'projects/update_payment/' + id,
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(res) {
            if (res.status === 'success') {
                showToast(res.message, 'success');
                $('#modalEditTermin').modal('hide');
                setTimeout(function() { location.reload(); }, 800);
            } else {
                showToast(res.message, 'error');
            }
        },
        complete: function() { btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i><span>Simpan Perubahan</span>'); }
    });
});

// ── HAPUS Termin ──
$(document).on('click', '.btn-delete-payment', function() {
    deletePaymentId = $(this).data('id');
    $('#modalHapusTermin').modal('show');
});

$('#btnKonfirmasiHapusTermin').on('click', function() {
    if (!deletePaymentId) return;
    var btn = $(this);
    btn.prop('disabled', true);
    $.ajax({
        url: BASE_URL + 'projects/delete_payment/' + deletePaymentId, type: 'POST', dataType: 'json',
        success: function(res) {
            if (res.status === 'success') {
                showToast(res.message, 'success');
                $('#modalHapusTermin').modal('hide');
                $('#row-payment-' + deletePaymentId).fadeOut(400, function() { $(this).remove(); });
                setTimeout(function() { location.reload(); }, 900);
            } else {
                showToast(res.message, 'error');
            }
        },
        complete: function() { btn.prop('disabled', false); }
    });
});
</script>
