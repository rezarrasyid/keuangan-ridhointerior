<!-- Page Header -->
<div class="page-header">
    <div>
        <h1>Master Klien</h1>
        <p>Kelola data klien workshop Anda</p>
    </div>
    <button class="btn btn-primary" id="btnTambahKlien" data-bs-toggle="modal" data-bs-target="#modalKlien">
        <i class="bi bi-plus-circle me-1"></i> Tambah Klien
    </button>
</div>

<!-- Table Card -->
<!-- Table Card -->
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h5 class="mb-0"><i class="bi bi-people-fill me-2" style="color:var(--primary-light);"></i>Daftar Klien</h5>
        <div class="d-flex align-items-center gap-2">
            <form action="<?= base_url('clients') ?>" method="GET" class="d-flex align-items-center gap-1 mb-0">
                <input type="text" name="q" class="form-control form-control-sm" placeholder="Cari klien..." value="<?= isset($search) ? htmlspecialchars($search) : '' ?>" style="width: 200px;">
                <button type="submit" class="btn btn-sm btn-primary" style="padding: 5px 10px;"><i class="bi bi-search"></i></button>
                <?php if (!empty($search)): ?>
                    <a href="<?= base_url('clients') ?>" class="btn btn-sm btn-light" style="padding: 5px 10px;"><i class="bi bi-x-circle"></i></a>
                <?php endif; ?>
            </form>
            <span class="badge" style="background:#DBEAFE;color:#1E40AF;padding:6px 12px;border-radius:20px;font-size:0.78rem;"><?= $total_rows ?> Klien</span>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0" id="tblKlien">
                <thead>
                    <tr>
                        <th style="width:40px;">#</th>
                        <th>Nama Klien</th>
                        <th>Telepon</th>
                        <th>Alamat</th>
                        <th style="width:120px;text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($clients)): ?>
                    <tr>
                        <td colspan="5" style="text-align:center;padding:40px;color:#9CA3AF;">
                            <i class="bi bi-people" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
                            Belum ada data klien. Tambahkan klien pertama!
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($clients as $i => $c): ?>
                    <tr id="row-client-<?= $c->id ?>">
                        <td style="color:#9CA3AF;font-size:0.8rem;"><?= (isset($offset) ? $offset : 0) + $i + 1 ?></td>
                        <td>
                            <div style="font-weight:600;"><?= htmlspecialchars($c->nama) ?></div>
                        </td>
                        <td>
                            <?php if ($c->telepon): ?>
                            <a href="tel:<?= $c->telepon ?>" style="color:var(--primary-light);text-decoration:none;">
                                <i class="bi bi-telephone me-1"></i><?= htmlspecialchars($c->telepon) ?>
                            </a>
                            <?php else: ?>
                            <span style="color:#D1D5DB;">-</span>
                            <?php endif; ?>
                        </td>
                        <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                            <?= $c->alamat ? htmlspecialchars($c->alamat) : '<span style="color:#D1D5DB;">-</span>' ?>
                        </td>
                        <td style="table-action text-align:center;">
                            <button class="btn-action btn-edit btn-edit-klien me-1"
                                    data-id="<?= $c->id ?>"
                                    title="Edit">
                                <i class="bi bi-pencil-fill"></i>
                            </button>
                            <button class="btn-action btn-delete btn-delete-klien"
                                    data-id="<?= $c->id ?>"
                                    data-nama="<?= htmlspecialchars($c->nama) ?>"
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
            Menampilkan <?= $offset + 1 ?> - <?= min($offset + $limit, $total_rows) ?> dari <?= $total_rows ?> klien
        </div>
        <nav aria-label="Page navigation">
            <ul class="pagination pagination-sm mb-0">
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= base_url('clients?q=' . urlencode($search) . '&page=' . ($page - 1)) ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <?php for ($p = 1; $p <= $total_pages; $p++): ?>
                    <li class="page-item <?= $p == $page ? 'active' : '' ?>">
                        <a class="page-link" href="<?= base_url('clients?q=' . urlencode($search) . '&page=' . $p) ?>"><?= $p ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= base_url('clients?q=' . urlencode($search) . '&page=' . ($page + 1)) ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>

<!-- ── Modal Tambah/Edit Klien ── -->
<div class="modal fade" id="modalKlien" tabindex="-1" aria-labelledby="modalKlienLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalKlienLabel">
                    <i class="bi bi-person-plus-fill me-2"></i>Tambah Klien
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formKlien">
                <div class="modal-body">
                    <input type="hidden" id="klienId" value="">
                    <div class="mb-3">
                        <label for="klienNama" class="form-label">Nama Lengkap <span style="color:#EF4444;">*</span></label>
                        <input type="text" class="form-control" id="klienNama" name="nama" placeholder="Contoh: Budi Santoso" required>
                    </div>
                    <div class="mb-3">
                        <label for="klienTelepon" class="form-label">Nomor Telepon</label>
                        <input type="tel" class="form-control" id="klienTelepon" name="telepon" placeholder="08xxxxxxxxxx">
                    </div>
                    <div class="mb-3">
                        <label for="klienAlamat" class="form-label">Alamat</label>
                        <textarea class="form-control" id="klienAlamat" name="alamat" rows="3" placeholder="Masukkan alamat lengkap..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSimpanKlien">
                        <i class="bi bi-save me-1"></i><span>Simpan Klien</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ── Modal Konfirmasi Hapus ── -->
<div class="modal fade" id="modalHapusKlien" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header" style="background:#EF4444;">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle-fill me-2"></i>Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="text-align:center;padding:28px;">
                <p style="margin:0;font-size:0.9rem;">Hapus klien <strong id="hapusKlienNama"></strong>?<br>
                <span style="font-size:0.8rem;color:#6B7280;">Tindakan ini tidak dapat dibatalkan.</span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger btn-sm" id="btnKonfirmasiHapus">
                    <i class="bi bi-trash me-1"></i>Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<script>
var deleteKlienId = null;

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

// Reset modal saat dibuka untuk tambah
$('#btnTambahKlien').on('click', function() {
    $('#formKlien')[0].reset();
    $('#klienId').val('');
    $('#modalKlienLabel').html('<i class="bi bi-person-plus-fill me-2"></i>Tambah Klien');
    $('#btnSimpanKlien span').text('Simpan Klien');
});

// ── TAMBAH / EDIT (Submit Form) ──
$('#formKlien').on('submit', function(e) {
    e.preventDefault();
    var id   = $('#klienId').val();
    var url  = id ? BASE_URL + 'clients/update/' + id : BASE_URL + 'clients/store';
    var btn  = $('#btnSimpanKlien');

    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...');

    $.ajax({
        url: url,
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(res) {
            if (res.status === 'success') {
                showToast(res.message, 'success');
                $('#modalKlien').modal('hide');
                setTimeout(function() { location.reload(); }, 800);
            } else {
                showToast(res.message, 'error');
            }
        },
        // --- TAMBAHKAN BLOK ERROR INI ---
        error: function(xhr, status, error) {
            var errorMessage = 'Terjadi kesalahan pada server.';
            // Jika server mengembalikan JSON (misal dari json_response dengan 422/500)
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.status === 403) {
                errorMessage = 'Akses ditolak! (Mungkin masalah CSRF Token)';
            }
            showToast(errorMessage, 'error');
        },
        // --------------------------------
        complete: function() {
            btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i><span>Simpan Klien</span>');
        }
    });
});

// ── EDIT: Load data ke modal ──
$(document).on('click', '.btn-edit-klien', function() {
    var id = $(this).data('id');
    $.ajax({
        url: BASE_URL + 'clients/get/' + id,
        type: 'GET',
        dataType: 'json',
        success: function(res) {
            if (res.status === 'success') {
                var d = res.data;
                $('#klienId').val(d.id);
                $('#klienNama').val(d.nama);
                $('#klienTelepon').val(d.telepon);
                $('#klienAlamat').val(d.alamat);
                $('#modalKlienLabel').html('<i class="bi bi-pencil-fill me-2"></i>Edit Klien');
                $('#btnSimpanKlien span').text('Update Klien');
                $('#modalKlien').modal('show');
            }
        }
    });
});

// ── HAPUS: Konfirmasi ──
$(document).on('click', '.btn-delete-klien', function() {
    deleteKlienId = $(this).data('id');
    $('#hapusKlienNama').text($(this).data('nama'));
    $('#modalHapusKlien').modal('show');
});

$('#btnKonfirmasiHapus').on('click', function() {
    if (!deleteKlienId) return;
    var btn = $(this);
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

    $.ajax({
        url: BASE_URL + 'clients/destroy/' + deleteKlienId,
        type: 'POST',
        dataType: 'json',
        success: function(res) {
            if (res.status === 'success') {
                showToast(res.message, 'success');
                $('#modalHapusKlien').modal('hide');
                $('#row-client-' + deleteKlienId).fadeOut(400, function() { $(this).remove(); });
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
