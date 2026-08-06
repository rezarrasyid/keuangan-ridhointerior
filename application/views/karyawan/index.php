<!-- Page Header -->
<div class="page-header">
    <div>
        <h1>Manajemen Karyawan & Penggajian</h1>
        <p>Kelola data karyawan, absensi harian, dan kalkulasi gaji bulanan</p>
    </div>
</div>

<!-- Navigasi Tabs -->
<ul class="nav nav-tabs mb-4" id="karyawanTab" role="tablist" style="border-bottom: 1px solid var(--border);">
    <li class="nav-item">
        <a class="nav-link <?= $active_tab == 'data' ? 'active' : '' ?>" href="<?= base_url('karyawan?tab=data') ?>" style="font-weight: 600;">
            <i class="bi bi-people-fill me-1"></i> Data Karyawan
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $active_tab == 'absensi' ? 'active' : '' ?>" href="<?= base_url('karyawan?tab=absensi') ?>" style="font-weight: 600;">
            <i class="bi bi-calendar-check-fill me-1"></i> Input Absensi
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $active_tab == 'penggajian' ? 'active' : '' ?>" href="<?= base_url('karyawan?tab=penggajian') ?>" style="font-weight: 600;">
            <i class="bi bi-wallet-fill me-1"></i> Rekap Gaji
        </a>
    </li>
</ul>

<div class="tab-content">
    
    <!-- ── TAB 1: DATA KARYAWAN ── -->
    <div class="tab-pane fade <?= $active_tab == 'data' ? 'show active' : '' ?>" id="data">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h5 class="mb-0"><i class="bi bi-person-vcard-fill me-2" style="color:var(--primary-light);"></i>Daftar Karyawan</h5>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahKaryawan">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Karyawan
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama & Posisi</th>
                                <th style="text-align:right;">Gaji Pokok</th>
                                <th style="text-align:right;">Lembur /Jam</th>
                                <th style="text-align:right;">Pot. Alfa</th>
                                <th style="text-align:center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($karyawan)): ?>
                            <tr>
                                <td colspan="6" style="text-align:center;padding:40px;color:#9CA3AF;">
                                    <i class="bi bi-people" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
                                    Belum ada data karyawan.
                                </td>
                            </tr>
                            <?php else: ?>
                            <?php foreach($karyawan as $i => $k): ?>
                            <tr>
                                <td style="color:#9CA3AF;font-size:0.8rem;"><?= $i + 1 ?></td>
                                <td>
                                    <div style="font-weight:600;"><?= htmlspecialchars($k->nama_lengkap) ?></div>
                                    <div style="font-size:0.75rem;color:#9CA3AF;"><?= htmlspecialchars($k->posisi) ?></div>
                                </td>
                                <td style="text-align:right;font-weight:600;">Rp <?= number_format($k->gaji_pokok, 0, ',', '.') ?></td>
                                <td style="text-align:right;color:#065F46;">Rp <?= number_format($k->upah_lembur_per_jam, 0, ',', '.') ?></td>
                                <td style="text-align:right;color:#991B1B;">Rp <?= number_format($k->potongan_alfa, 0, ',', '.') ?></td>
                                <td style="table-action text-align:center;">
                                    <button class="btn-action btn-detail me-1" data-bs-toggle="modal" data-bs-target="#modalEditKaryawan<?= $k->id ?>" title="Edit">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    <a href="<?= base_url('karyawan/hapus_karyawan/'.$k->id) ?>" class="btn-action btn-delete" onclick="return confirm('Yakin ingin menghapus data ini?')" title="Hapus">
                                        <i class="bi bi-trash-fill"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ── TAB 2: INPUT ABSENSI ── -->
    <div class="tab-pane fade <?= $active_tab == 'absensi' ? 'show active' : '' ?>" id="absensi">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h5 class="mb-0"><i class="bi bi-calendar-check-fill me-2" style="color:var(--primary-light);"></i>Status Kehadiran</h5>
            </div>
            
            <!-- Filter Section -->
            <div style="padding: 16px 20px; border-bottom: 1px solid var(--border); background: #FAFBFD;">
                <form action="<?= base_url('karyawan') ?>" method="GET" class="row g-2 align-items-end mb-0">
                    <input type="hidden" name="tab" value="absensi">
                    <div class="col-md-3">
                        <label class="form-label" style="font-size:0.75rem; margin-bottom:4px;">Filter Tanggal Absen</label>
                        <input type="date" name="tgl" class="form-control form-control-sm" value="<?= $tanggal_absen ?>">
                    </div>
                    <div class="col-md-2 d-flex gap-1">
                        <button type="submit" class="btn btn-sm btn-primary w-100" style="padding: 6px;"><i class="bi bi-funnel-fill me-1"></i>Tampilkan</button>
                    </div>
                </form>
            </div>

            <div class="card-body p-0">
                <form action="<?= base_url('karyawan/simpan_absensi') ?>" method="POST" id="formAbsensi">
                    <!-- PERBAIKAN: Token CSRF untuk form simpan absensi -->
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>" style="display: none">
                    
                    <input type="hidden" name="tanggal" value="<?= $tanggal_absen ?>">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama Karyawan</th>
                                    <th>Status Tersimpan</th>
                                    <th style="width: 200px;">Ubah Status</th>
                                    <th style="width: 150px;">Jam Lembur</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($absensi)): ?>
                                <tr>
                                    <td colspan="5" style="text-align:center;padding:40px;color:#9CA3AF;">Belum ada data.</td>
                                </tr>
                                <?php else: ?>
                                <?php foreach($absensi as $i => $a): ?>
                                <tr>
                                    <td style="color:#9CA3AF;font-size:0.8rem;"><?= $i + 1 ?></td>
                                    <td><div style="font-weight:600;"><?= htmlspecialchars($a->nama_lengkap) ?></div></td>
                                    <td>
                                        <span class="badge" style="background:<?= $a->status == 'Belum Input' ? '#F3F4F6' : ($a->status == 'Masuk' ? '#D1FAE5' : ($a->status == 'Alfa' ? '#FEE2E2' : '#FEF3C7')) ?>; color:<?= $a->status == 'Belum Input' ? '#4B5563' : ($a->status == 'Masuk' ? '#065F46' : ($a->status == 'Alfa' ? '#991B1B' : '#92400E')) ?>; padding:6px 12px; border-radius:20px; font-size:0.78rem;">
                                            <?= $a->status ?>
                                        </span>
                                    </td>
                                    <td>
                                        <input type="hidden" name="karyawan_id[]" value="<?= $a->karyawan_id ?>">
                                        <select name="status[]" class="form-select form-select-sm">
                                            <option value="Masuk" <?= $a->status == 'Masuk' ? 'selected' : '' ?>>Masuk</option>
                                            <option value="Izin" <?= $a->status == 'Izin' ? 'selected' : '' ?>>Izin</option>
                                            <option value="Sakit" <?= $a->status == 'Sakit' ? 'selected' : '' ?>>Sakit</option>
                                            <option value="Alfa" <?= $a->status == 'Alfa' ? 'selected' : '' ?>>Alfa</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="lembur_jam[]" class="form-control form-control-sm" placeholder="0" value="<?= $a->lembur_jam ?>">
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if (!empty($absensi)): ?>
                    <div class="card-footer" style="background:var(--bg-card); border-top:1px solid var(--border); padding: 12px 20px; text-align:right;">
                        <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-save me-1"></i>Simpan Absensi</button>
                    </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <!-- ── TAB 3: REKAP PENGGAJIAN ── -->
    <div class="tab-pane fade <?= $active_tab == 'penggajian' ? 'show active' : '' ?>" id="penggajian">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h5 class="mb-0"><i class="bi bi-wallet-fill me-2" style="color:var(--primary-light);"></i>Rekapitulasi Gaji Bulanan</h5>
            </div>
            
            <!-- Filter Section -->
            <div style="padding: 16px 20px; border-bottom: 1px solid var(--border); background: #FAFBFD;">
                <form action="<?= base_url('karyawan') ?>" method="GET" class="row g-2 align-items-end mb-0">
                    <input type="hidden" name="tab" value="penggajian">
                    <div class="col-md-3">
                        <label class="form-label" style="font-size:0.75rem; margin-bottom:4px;">Pilih Bulan</label>
                        <select name="bulan" class="form-select form-select-sm">
                            <?php
                            $nama_bulan = ['01'=>'Januari', '02'=>'Februari', '03'=>'Maret', '04'=>'April', '05'=>'Mei', '06'=>'Juni', '07'=>'Juli', '08'=>'Agustus', '09'=>'September', '10'=>'Oktober', '11'=>'November', '12'=>'Desember'];
                            foreach ($nama_bulan as $num => $name) {
                                $selected = ($bulan_gaji == $num) ? 'selected' : '';
                                echo "<option value='$num' $selected>$name</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" style="font-size:0.75rem; margin-bottom:4px;">Tahun</label>
                        <input type="number" name="tahun" class="form-control form-control-sm" value="<?= $tahun_gaji ?>" required>
                    </div>
                    <div class="col-md-2 d-flex gap-1">
                        <button type="submit" class="btn btn-sm btn-primary w-100" style="padding: 6px;"><i class="bi bi-funnel-fill me-1"></i>Kalkulasi</button>
                    </div>
                </form>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead style="background: #F9FAFB;">
                            <tr>
                                <th>Nama Karyawan</th>
                                <th style="text-align:right;">Gaji Pokok</th>
                                <th style="text-align:center;">Masuk</th>
                                <th style="text-align:center;">Lembur (Jam)</th>
                                <th style="text-align:center;">Alfa</th>
                                <th style="text-align:right;">Total Diterima</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($rekap_gaji)): ?>
                            <tr>
                                <td colspan="6" style="text-align:center;padding:40px;color:#9CA3AF;">Belum ada data rekap gaji.</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach($rekap_gaji as $r): ?>
                            <tr>
                                <td>
                                    <div style="font-weight:600;"><?= htmlspecialchars($r->nama_lengkap) ?></div>
                                    <div style="font-size:0.75rem;color:#9CA3AF;"><?= htmlspecialchars($r->posisi) ?></div>
                                </td>
                                <td style="text-align:right;">Rp <?= number_format($r->gaji_pokok, 0, ',', '.') ?></td>
                                <td style="text-align:center;"><?= $r->total_masuk ?> Hari</td>
                                <td style="text-align:center;">
                                    <?= $r->total_lembur_jam ?> Jam<br>
                                    <span style="font-size:0.75rem;color:#065F46;">(+ Rp <?= number_format($r->total_lembur_jam * $r->upah_lembur_per_jam, 0, ',', '.') ?>)</span>
                                </td>
                                <td style="text-align:center;">
                                    <?= $r->total_alfa ?> Hari<br>
                                    <span style="font-size:0.75rem;color:#991B1B;">(- Rp <?= number_format($r->total_alfa * $r->potongan_alfa, 0, ',', '.') ?>)</span>
                                </td>
                                <td style="text-align:right;">
                                    <span style="font-weight:700;color:#065F46;font-size:1.1rem;">
                                        Rp <?= number_format($r->total_gaji_bersih, 0, ',', '.') ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ── Modal Tambah Karyawan ── -->
<div class="modal fade" id="modalTambahKaryawan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-person-plus-fill me-2"></i>Tambah Karyawan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('karyawan/tambah_karyawan') ?>" method="POST">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>" style="display: none">
                            <label class="form-label">Nama Lengkap <span style="color:#EF4444;">*</span></label>
                            <input type="text" name="nama_lengkap" class="form-control" placeholder="Nama Lengkap" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Posisi <span style="color:#EF4444;">*</span></label>
                            <input type="text" name="posisi" class="form-control" placeholder="Contoh: Staff Admin" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Gaji Pokok (Rp) <span style="color:#EF4444;">*</span></label>
                            <input type="text" name="gaji_pokok" class="form-control input-rupiah" placeholder="0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Lembur per Jam (Rp) <span style="color:#EF4444;">*</span></label>
                            <input type="text" name="upah_lembur_per_jam" class="form-control input-rupiah" placeholder="0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Potongan Alfa (Rp) <span style="color:#EF4444;">*</span></label>
                            <input type="text" name="potongan_alfa" class="form-control input-rupiah" placeholder="0" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i><span>Simpan Data</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Karyawan (di-generate untuk masing-masing karyawan) -->
<?php foreach($karyawan as $k): ?>
<div class="modal fade" id="modalEditKaryawan<?= $k->id ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Data Karyawan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('karyawan/edit_karyawan/'.$k->id) ?>" method="POST">
                <div class="modal-body">
                    <!-- PERBAIKAN: Token CSRF untuk form edit karyawan -->
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>" style="display: none">
                    
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Nama Lengkap <span style="color:#EF4444;">*</span></label>
                            <input type="text" name="nama_lengkap" class="form-control" value="<?= htmlspecialchars($k->nama_lengkap) ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Posisi <span style="color:#EF4444;">*</span></label>
                            <input type="text" name="posisi" class="form-control" value="<?= htmlspecialchars($k->posisi) ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Gaji Pokok (Rp) <span style="color:#EF4444;">*</span></label>
                            <input type="text" name="gaji_pokok" class="form-control input-rupiah" value="<?= number_format($k->gaji_pokok, 0, ',', '.') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Lembur per Jam (Rp) <span style="color:#EF4444;">*</span></label>
                            <input type="text" name="upah_lembur_per_jam" class="form-control input-rupiah" value="<?= number_format($k->upah_lembur_per_jam, 0, ',', '.') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Potongan Alfa (Rp) <span style="color:#EF4444;">*</span></label>
                            <input type="text" name="potongan_alfa" class="form-control input-rupiah" value="<?= number_format($k->potongan_alfa, 0, ',', '.') ?>" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i><span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<script>
// Menggunakan logika Auto-format angka berbasis jQuery persis seperti template proyek
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
</script>