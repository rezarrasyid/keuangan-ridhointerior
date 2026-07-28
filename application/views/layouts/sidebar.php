<?php
$current_uri = $this->uri->uri_string();
$seg1 = $this->uri->segment(1);
?>
<!-- SIDEBAR -->
<nav id="sidebar">
    <div class="sidebar-brand">
        <div style="display:flex;align-items:center;gap:10px;">
            <div style="width:38px;height:38px;background:rgba(255,255,255,0.15);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                <i class="bi bi-house-heart-fill" style="color:#fff;font-size:1.2rem;"></i>
            </div>
            <div>
                <h2>Ridho Interior</h2>
                <p><?= isset($user) ? htmlspecialchars($user['nama_workshop']) : '' ?></p>
            </div>
        </div>
    </div>

    <div class="nav-section-label">Menu Utama</div>

    <a href="<?= base_url('dashboard') ?>" class="nav-link <?= $seg1 === 'dashboard' || $seg1 === '' ? 'active' : '' ?>">
        <i class="bi bi-grid-1x2-fill"></i> Dashboard
    </a>

    <div class="nav-section-label">Master Data</div>

    <a href="<?= base_url('clients') ?>" class="nav-link <?= $seg1 === 'clients' ? 'active' : '' ?>">
        <i class="bi bi-people-fill"></i> Klien
    </a>
    <a href="<?= base_url('workers') ?>" class="nav-link <?= $seg1 === 'workers' ? 'active' : '' ?>">
        <i class="bi bi-person-workspace"></i> Upah Tukang
    </a>

    <div class="nav-section-label">Keuangan</div>

    <a href="<?= base_url('projects') ?>" class="nav-link <?= $seg1 === 'projects' ? 'active' : '' ?>">
        <i class="bi bi-kanban-fill"></i> Proyek & Termin
    </a>
    <a href="<?= base_url('expenses') ?>" class="nav-link <?= $seg1 === 'expenses' ? 'active' : '' ?>">
        <i class="bi bi-arrow-up-right-circle-fill"></i> Pengeluaran
    </a>

    <div class="nav-section-label" style="margin-top:auto;"></div>

    <!-- User info -->
    <div style="padding:16px 20px;margin-top:20px;border-top:1px solid rgba(255,255,255,0.1);">
        <div style="display:flex;align-items:center;gap:10px;">
            <div style="width:34px;height:34px;border-radius:50%;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;">
                <i class="bi bi-person-fill" style="color:#fff;font-size:1rem;"></i>
            </div>
            <div>
                <div style="font-size:0.8rem;font-weight:600;color:#fff;"><?= isset($user) ? htmlspecialchars($user['nama_lengkap']) : '' ?></div>
                <div style="font-size:0.7rem;color:rgba(255,255,255,0.5);"><?= isset($user) ? ucfirst($user['role']) : '' ?></div>
            </div>
        </div>
        <a href="<?= base_url('auth/logout') ?>" class="nav-link" style="margin:10px 0 0;padding:8px 12px;background:rgba(239,68,68,0.15);color:rgba(255,100,100,0.9);">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
    </div>
</nav>

<!-- TOPBAR -->
<div id="topbar">
    <button class="btn btn-sm d-md-none" id="sidebarToggle" style="background:none;border:1px solid var(--border);color:var(--text-muted);border-radius:8px;padding:6px 10px;">
        <i class="bi bi-list"></i>
    </button>
    <div class="topbar-title"><?= isset($title) ? htmlspecialchars($title) : 'Ridho Interior' ?></div>
    <div class="topbar-workshop" style="padding: 4px 10px;">
        <i class="bi bi-geo-alt-fill" style="color:var(--primary-light);"></i>
        <?php if (isset($user) && $user['role'] === 'superadmin' && !empty($all_workshops)): ?>
            <select onchange="window.location.href=this.value" class="form-select form-select-sm" style="border:none; background:transparent; padding:0 24px 0 4px; font-size:0.78rem; font-weight:600; color:var(--text-muted); cursor:pointer; width:auto; display:inline-block; box-shadow:none;">
                <?php foreach ($all_workshops as $ws): ?>
                    <option value="<?= base_url('auth/switch_workshop/' . $ws->id) ?>" <?= $ws->id == $workshop_id ? 'selected' : '' ?>>
                        <?= htmlspecialchars($ws->nama_workshop) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        <?php else: ?>
            <?= isset($user) ? htmlspecialchars($user['nama_workshop']) : '' ?>
        <?php endif; ?>
    </div>
</div>

<!-- MAIN CONTENT WRAPPER -->
<div id="main-content">
