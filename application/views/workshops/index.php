<!-- Page Header -->
<div class="page-header">
    <div>
        <h1>Manajemen Workshop</h1>
        <p>Kelola cabang workshop dan akun administrator</p>
    </div>
    <button class="btn btn-primary" id="btnTambahWorkshop" data-bs-toggle="modal" data-bs-target="#modalWorkshop">
        <i class="bi bi-shop me-1"></i> Tambah Workshop
    </button>
</div>

<!-- Table Card -->
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="mb-0"><i class="bi bi-building me-2" style="color:var(--primary-light);"></i>Daftar Cabang & Admin</h5>
        <span class="badge" style="background:#DBEAFE;color:#1E40AF;padding:6px 12px;border-radius:20px;font-size:0.78rem;">
            <?= count($workshops) ?> Workshop
        </span>
    </div>
    
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th style="width:40px;">#</th>
                        <th>Nama Workshop</th>
                        <th>Alamat</th>
                        <th>Akun Admin (Username)</th>
                        <th>Nama Pengelola</th>
                        <th style="width:120px;text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($workshops)): ?>
                    <tr>
                        <td colspan="6" style="text-align:center;padding:40px;color:#9CA3AF;">
                            <i class="bi bi-shop" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
                            Belum ada workshop terdaftar.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($workshops as $i => $w): ?>
                    <tr id="row-workshop-<?= $w->id ?>">
                        <td style="color:#9CA3AF;font-size:0.8rem;"><?= $i + 1 ?></td>
                        <td style="font-weight:600;"><?= htmlspecialchars($w->nama_workshop) ?></td>
                        <td style="font-size:0.825rem;color:#6B7280;"><?= htmlspecialchars($w->alamat) ?></td>
                        <td>
                            <span class="badge-status badge-aktif">
                                @<?= $w->username ? htmlspecialchars($w->username) : 'Belum Ada' ?>
                            </span>
                        </td>
                        <td><?= $w->nama_lengkap ? htmlspecialchars($w->nama_lengkap) : '-' ?></td>
                        <td style="table-action text-align:center;">
                            <button class="btn-action btn-edit btn-edit-ws me-1" data-id="<?= $w->id ?>" title="Edit">
                                <i class="bi bi-pencil-fill"></i>
                            </button>
                            <button class="btn-action btn-delete btn-delete-ws" data-id="<?= $w->id ?>" data-nama="<?= htmlspecialchars($w->nama_workshop) ?>" title="Hapus">
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

<!-- ── Modal Tambah/Edit Workshop ── -->
<div class="modal fade" id="modalWorkshop" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalWsLabel">
                    <i class="bi bi-shop-window me-2"></i>Tambah Workshop Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formWorkshop">
                <div class="modal-body">
                    <input type="hidden" id="wsId" value="">
                    
                    <div style="margin-bottom:16px; border-bottom:1px solid #E5E7EB; padding-bottom:8px;">
                        <h6 style="color:var(--primary);font-weight:700;font-size:0.9rem;margin:0;">Informasi Cabang</h6>
                    </div>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            <label for="wsNama" class="form-label">Nama Workshop <span style="color:#EF4444;">*</span></label>
                            <input type="text" class="form-control" id="wsNama" name="nama_workshop" placeholder="Contoh: Cabang Surabaya" required>
                        </div>
                        <div class="col-md-12">
                            <label for="wsAlamat" class="form-label">Alamat Lengkap <span style="color:#EF4444;">*</span></label>
                            <textarea class="form-control" id="wsAlamat" name="alamat" rows="2" required></textarea>
                        </div>
                    </div>

                    <div style="margin-bottom:16px; border-bottom:1px solid #E5E7EB; padding-bottom:8px;">
                        <h6 style="color:#10B981;font-weight:700;font-size:0.9rem;margin:0;">Akun Administrator Cabang</h6>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="wsAdminName" class="form-label">Nama Pengelola <span style="color:#EF4444;">*</span></label>
                            <input type="text" class="form-control" id="wsAdminName" name="nama_lengkap" placeholder="Nama asli admin" required>
                        </div>
                        <div class="col-md-6">
                            <label for="wsUsername" class="form-label">Username Login <span style="color:#EF4444;">*</span></label>
                            <input type="text" class="form-control" id="wsUsername" name="username" placeholder="Tanpa spasi" required>
                        </div>
                        <div class="col-md-12">
                            <label for="wsPassword" class="form-label">Password <span class="pass-req" style="color:#EF4444;">*</span></label>
                            <input type="password" class="form-control" id="wsPassword" name="password" placeholder="Minimal 5 karakter" required>
                            <div class="form-text mt-1" id="passHelp" style="display:none;font-size:0.75rem;color:#6B7280;">Kosongkan jika tidak ingin mengubah password lama.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSimpanWs">
                        <i class="bi bi-save me-1"></i><span>Simpan Cabang</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ── Modal Konfirmasi Hapus ── -->
<div class="modal fade" id="modalHapusWs" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header" style="background:#EF4444;">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle-fill me-2"></i>Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="text-align:center;padding:24px;">
                <p style="margin:0;font-size:0.875rem;">Hapus workshop <strong id="hapusWsNama"></strong>?<br>
                <span style="font-size:0.8rem;color:#6B7280;display:block;margin-top:8px;"><strong>PERINGATAN:</strong> Semua Klien, Proyek, dan Akun Admin terkait cabang ini akan <strong>terhapus permanen!</strong></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger btn-sm" id="btnKonfirmasiHapusWs">
                    <i class="bi bi-trash me-1"></i>Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<script>
var deleteWsId = null;

// Reset form saat modal tambah dibuka
$('#btnTambahWorkshop').on('click', function() {
    $('#formWorkshop')[0].reset();
    $('#wsId').val('');
    $('#modalWsLabel').html('<i class="bi bi-shop-window me-2"></i>Tambah Workshop Baru');
    $('#btnSimpanWs span').text('Simpan Cabang');
    
    // Wajib isi password saat tambah baru
    $('#wsPassword').prop('required', true);
    $('.pass-req').show();
    $('#passHelp').hide();
});

// SUBMIT Form (Create / Update)
$('#formWorkshop').on('submit', function(e) {
    e.preventDefault();
    var id  = $('#wsId').val();
    var url = id ? BASE_URL + 'workshops/update/' + id : BASE_URL + 'workshops/store';
    var btn = $('#btnSimpanWs');

    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...');

    $.ajax({
        url: url,
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(res) {
            if (res.status === 'success') {
                showToast(res.message, 'success');
                $('#modalWorkshop').modal('hide');
                setTimeout(function() { location.reload(); }, 800);
            } else {
                showToast(res.message, 'error');
            }
        },
        complete: function() { 
            btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i><span>Simpan Cabang</span>'); 
        }
    });
});

// EDIT Button Click
$(document).on('click', '.btn-edit-ws', function() {
    var id = $(this).data('id');
    $.ajax({
        url: BASE_URL + 'workshops/get/' + id,
        type: 'GET',
        dataType: 'json',
        success: function(res) {
            if (res.status === 'success') {
                var d = res.data;
                $('#wsId').val(d.id);
                $('#wsNama').val(d.nama_workshop);
                $('#wsAlamat').val(d.alamat);
                $('#wsAdminName').val(d.nama_lengkap);
                $('#wsUsername').val(d.username);
                
                // Password tidak wajib diisi saat edit
                $('#wsPassword').val('').prop('required', false);
                $('.pass-req').hide();
                $('#passHelp').show();

                $('#modalWsLabel').html('<i class="bi bi-pencil-fill me-2"></i>Edit Workshop');
                $('#btnSimpanWs span').text('Update Data');
                $('#modalWorkshop').modal('show');
            }
        }
    });
});

// DELETE Button Click
$(document).on('click', '.btn-delete-ws', function() {
    deleteWsId = $(this).data('id');
    $('#hapusWsNama').text($(this).data('nama'));
    $('#modalHapusWs').modal('show');
});

// KONFIRMASI DELETE
$('#btnKonfirmasiHapusWs').on('click', function() {
    if (!deleteWsId) return;
    var btn = $(this);
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

    $.ajax({
        url: BASE_URL + 'workshops/destroy/' + deleteWsId,
        type: 'POST',
        dataType: 'json',
        success: function(res) {
            if (res.status === 'success') {
                showToast(res.message, 'success');
                $('#modalHapusWs').modal('hide');
                $('#row-workshop-' + deleteWsId).fadeOut(400, function() { $(this).remove(); });
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