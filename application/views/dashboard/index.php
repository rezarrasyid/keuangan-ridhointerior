<?php
function fmt($n) {
    return 'Rp ' . number_format($n, 0, ',', '.');
}
?>

<!-- Page Header -->
<div class="page-header">
    <div>
        <h1>Dashboard</h1>
        <p>Ringkasan keuangan workshop bulan <?= date('F Y') ?></p>
    </div>
    <div style="display:flex;align-items:center;gap:10px;">
        <label for="chartYear" style="font-size:0.825rem;font-weight:600;color:#374151;margin:0;">Tahun Grafik:</label>
        <select id="chartYear" class="form-select form-select-sm" style="width:100px;">
            <?php for ($y = date('Y'); $y >= date('Y') - 3; $y--): ?>
                <option value="<?= $y ?>" <?= $y == date('Y') ? 'selected' : '' ?>><?= $y ?></option>
            <?php endfor; ?>
        </select>
    </div>
</div>

<!-- ── KPI Cards ── -->
<div class="row g-3 mb-4">
    <!-- Total Pemasukan -->
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card" style="border-top:4px solid #10B981;">
            <div class="stat-icon" style="background:#D1FAE5;color:#065F46;">
                <i class="bi bi-arrow-down-circle-fill"></i>
            </div>
            <div class="stat-value"><?= fmt($total_pemasukan) ?></div>
            <div class="stat-label">Pemasukan Bulan Ini</div>
        </div>
    </div>
    <!-- Total Pengeluaran -->
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card" style="border-top:4px solid #EF4444;">
            <div class="stat-icon" style="background:#FEE2E2;color:#991B1B;">
                <i class="bi bi-arrow-up-circle-fill"></i>
            </div>
            <div class="stat-value"><?= fmt($total_pengeluaran) ?></div>
            <div class="stat-label">Pengeluaran Bulan Ini</div>
        </div>
    </div>
    <!-- Net Cashflow -->
    <div class="col-sm-6 col-xl-3">
        <?php $net = $total_pemasukan - $total_pengeluaran; ?>
        <div class="card stat-card" style="border-top:4px solid <?= $net >= 0 ? '#3B82F6' : '#F59E0B' ?>;">
            <div class="stat-icon" style="background:<?= $net >= 0 ? '#DBEAFE' : '#FEF3C7' ?>;color:<?= $net >= 0 ? '#1E40AF' : '#92400E' ?>;">
                <i class="bi bi-wallet2"></i>
            </div>
            <div class="stat-value" style="color:<?= $net >= 0 ? '#1E40AF' : '#92400E' ?>;"><?= fmt($net) ?></div>
            <div class="stat-label">Net Cashflow Bulan Ini</div>
        </div>
    </div>
    <!-- Saldo Tukang & Proyek Aktif -->
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card" style="border-top:4px solid #8B5CF6;">
            <div class="stat-icon" style="background:#EDE9FE;color:#5B21B6;">
                <i class="bi bi-person-workspace"></i>
            </div>
            <div class="stat-value"><?= fmt($saldo_tukang) ?></div>
            <div class="stat-label">Total Saldo Tukang Pending</div>
        </div>
    </div>
</div>

<!-- ── Charts + Top Client ── -->
<div class="row g-3">
    <!-- Bar Chart -->
    <div class="col-xl-8">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5><i class="bi bi-bar-chart-fill me-2" style="color:var(--primary-light);"></i>Pemasukan vs Pengeluaran</h5>
                <span id="chartYearLabel" class="badge" style="background:#EFF6FF;color:#1E40AF;font-size:0.75rem;padding:5px 10px;border-radius:20px;"></span>
            </div>
            <div class="card-body" style="padding:20px;">
                <div style="position:relative;height:300px;">
                    <canvas id="financeChart"></canvas>
                </div>
                <div id="chartLoader" style="text-align:center;padding:40px;display:none;">
                    <div class="spinner-border text-primary" style="width:28px;height:28px;"></div>
                    <p style="margin:10px 0 0;font-size:0.825rem;color:#6B7280;">Memuat data grafik...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Client -->
    <div class="col-xl-4">
        <div class="card h-100">
            <div class="card-header">
                <h5><i class="bi bi-trophy-fill me-2" style="color:#F59E0B;"></i>Top Client</h5>
            </div>
            <div class="card-body" style="padding:0;">
                <?php if (empty($top_clients)): ?>
                <div style="padding:40px;text-align:center;color:#9CA3AF;">
                    <i class="bi bi-people" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
                    Belum ada data klien
                </div>
                <?php else: ?>
                <div style="padding:8px 0;">
                    <?php foreach ($top_clients as $i => $c): ?>
                    <div style="padding:12px 20px;display:flex;align-items:center;gap:12px;<?= $i < count($top_clients)-1 ? 'border-bottom:1px solid #F3F4F6;' : '' ?>">
                        <div style="width:32px;height:32px;border-radius:50%;background:<?= $i===0 ? '#FEF3C7' : ($i===1 ? '#F3F4F6' : '#EDE9FE') ?>;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:0.85rem;color:<?= $i===0 ? '#92400E' : ($i===1 ? '#374151' : '#5B21B6') ?>;flex-shrink:0;">
                            <?= $i + 1 ?>
                        </div>
                        <div style="flex:1;min-width:0;">
                            <div style="font-size:0.875rem;font-weight:600;color:#111827;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= htmlspecialchars($c->nama) ?></div>
                            <div style="font-size:0.75rem;color:#6B7280;"><?= $c->total_project ?> proyek</div>
                        </div>
                        <div style="font-size:0.78rem;font-weight:700;color:var(--primary);text-align:right;flex-shrink:0;">
                            <?= 'Rp ' . number_format($c->total_nilai ?? 0, 0, ',', '.') ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- ── Proyek Aktif Info ── -->
<div class="row g-3 mt-1">
    <div class="col-12">
        <div class="card">
            <div class="card-body d-flex align-items-center gap-3" style="padding:16px 20px;">
                <div style="width:44px;height:44px;background:#DBEAFE;border-radius:12px;display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-kanban-fill" style="font-size:1.2rem;color:var(--primary);"></i>
                </div>
                <div>
                    <div style="font-size:1.4rem;font-weight:800;color:var(--primary);"><?= $proyek_aktif ?> Proyek Aktif</div>
                    <div style="font-size:0.825rem;color:#6B7280;">sedang berjalan di workshop ini</div>
                </div>
                <a href="<?= base_url('projects') ?>" class="btn btn-primary ms-auto" style="padding:8px 18px;">
                    <i class="bi bi-arrow-right me-1"></i> Lihat Proyek
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// ── Chart.js Setup ──
var chartInstance = null;

function loadChart(year) {
    $('#chartLoader').show();
    $('#financeChart').hide();
    $('#chartYearLabel').text('Tahun ' + year);

    $.getJSON(BASE_URL + 'dashboard/chart_data', { year: year }, function(res) {
        $('#chartLoader').hide();
        $('#financeChart').show();

        if (chartInstance) chartInstance.destroy();

        var ctx = document.getElementById('financeChart').getContext('2d');
        chartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: res.labels,
                datasets: [
                    {
                        label: 'Pemasukan',
                        data: res.pemasukan,
                        backgroundColor: 'rgba(16,185,129,0.8)',
                        borderColor: '#10B981',
                        borderWidth: 2,
                        borderRadius: 6,
                        borderSkipped: false,
                    },
                    {
                        label: 'Pengeluaran',
                        data: res.pengeluaran,
                        backgroundColor: 'rgba(239,68,68,0.8)',
                        borderColor: '#EF4444',
                        borderWidth: 2,
                        borderRadius: 6,
                        borderSkipped: false,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: { family: 'Inter', size: 12, weight: '600' },
                            padding: 20,
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                return ' ' + ctx.dataset.label + ': Rp ' + ctx.parsed.y.toLocaleString('id-ID');
                            }
                        },
                        backgroundColor: '#1F2937',
                        titleFont: { family: 'Inter', size: 12 },
                        bodyFont: { family: 'Inter', size: 12 },
                        padding: 12,
                        cornerRadius: 8,
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Inter', size: 11 }, color: '#6B7280' }
                    },
                    y: {
                        grid: { color: '#F3F4F6' },
                        ticks: {
                            font: { family: 'Inter', size: 11 },
                            color: '#6B7280',
                            callback: function(val) { return 'Rp ' + (val/1000000).toFixed(0) + 'Jt'; }
                        }
                    }
                }
            }
        });
    });
}

// Initial load
loadChart($('#chartYear').val());

// Reload chart on year change
$('#chartYear').on('change', function() {
    loadChart($(this).val());
});
</script>
