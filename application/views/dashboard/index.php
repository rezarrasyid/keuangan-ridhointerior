<?php
function fmt($n) {
    return 'Rp ' . number_format($n, 0, ',', '.');
}
?>

<!-- Page Header -->
<div class="page-header flex-wrap">
    <div>
        <h1>Dashboard</h1>
        <p>Ringkasan keuangan dari <?= date('d M Y', strtotime($start_date)) ?> s/d <?= date('d M Y', strtotime($end_date)) ?></p>
    </div>
    <!-- Filter Date Range -->
    <div style="display:flex;align-items:center;gap:10px;background:#fff;padding:8px 12px;border-radius:10px;border:1px solid var(--border);">
        <input type="date" id="filterStart" class="form-control form-control-sm" value="<?= $start_date ?>" style="width:120px;border:none;background:#F1F5F9;">
        <span style="font-size:0.8rem;color:#6B7280;font-weight:600;">s/d</span>
        <input type="date" id="filterEnd" class="form-control form-control-sm" value="<?= $end_date ?>" style="width:120px;border:none;background:#F1F5F9;">
        <button id="btnFilter" class="btn btn-primary btn-sm ms-1" title="Terapkan Filter">
            <i class="bi bi-search"></i>
        </button>
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
            <div class="stat-label">Pemasukan Periode Ini</div>
        </div>
    </div>
    <!-- Total Pengeluaran -->
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card" style="border-top:4px solid #EF4444;">
            <div class="stat-icon" style="background:#FEE2E2;color:#991B1B;">
                <i class="bi bi-arrow-up-circle-fill"></i>
            </div>
            <div class="stat-value"><?= fmt($total_pengeluaran) ?></div>
            <div class="stat-label">Pengeluaran Periode Ini</div>
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
            <div class="stat-label">Cashflow Periode Ini</div>
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
                <h5><i class="bi bi-bar-chart-fill me-2" style="color:var(--primary-light);"></i>Grafik Keuangan Harian</h5>
                <span id="chartRangeLabel" class="badge" style="background:#EFF6FF;color:#1E40AF;font-size:0.75rem;padding:5px 10px;border-radius:20px;"></span>
            </div>
            <div class="card-body" style="padding:20px;">
                <div id="chartContainer" style="position:relative;height:300px;display:none;">
                    <canvas id="financeChart"></canvas>
                </div>
                <div id="chartLoader" style="text-align:center;padding:40px;">
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
                <h5><i class="bi bi-trophy-fill me-2" style="color:#F59E0B;"></i>Top Client (Keseluruhan)</h5>
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

<script>
// ── Handle Filter URL ──
$('#btnFilter').on('click', function() {
    var start = $('#filterStart').val();
    var end   = $('#filterEnd').val();
    if(start && end) {
        window.location.href = BASE_URL + 'dashboard?start_date=' + start + '&end_date=' + end;
    }
});

// ── Chart.js Setup ──
var chartInstance = null;

function loadChart(start, end) {
    $('#chartLoader').show();
    $('#chartContainer').hide();
    
    // Format label kecil di atas grafik
    $('#chartRangeLabel').text(start + ' s/d ' + end);

    // Menggunakan $.ajax agar lebih mudah menangkap error
    $.ajax({
        url: BASE_URL + 'dashboard/chart_data',
        type: 'GET',
        data: { start_date: start, end_date: end },
        dataType: 'json',
        success: function(res) {
            $('#chartLoader').hide();
            $('#chartContainer').show();

            if (chartInstance) chartInstance.destroy();

            var ctx = document.getElementById('financeChart').getContext('2d');
            chartInstance = new Chart(ctx, {
                type: 'line', // Ganti ke line chart lebih cocok untuk harian
                data: {
                    labels: res.labels,
                    datasets: [
                        {
                            label: 'Pemasukan',
                            data: res.pemasukan,
                            backgroundColor: 'rgba(16,185,129,0.1)',
                            borderColor: '#10B981',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.3, // Membuat garis lebih melengkung
                            pointRadius: 3
                        },
                        {
                            label: 'Pengeluaran',
                            data: res.pengeluaran,
                            backgroundColor: 'rgba(239,68,68,0.1)',
                            borderColor: '#EF4444',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.3,
                            pointRadius: 3
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
                                font: { family: 'Inter', size: 12, weight: '600' }
                            }
                        },
                        tooltip: {
                            backgroundColor: '#1F2937',
                            padding: 12,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(ctx) {
                                    return ' ' + ctx.dataset.label + ': Rp ' + ctx.parsed.y.toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { family: 'Inter', size: 11 }, color: '#6B7280' }
                        },
                        y: {
                            grid: { color: '#F3F4F6' },
                            beginAtZero: true,
                            ticks: {
                                font: { family: 'Inter', size: 11 },
                                color: '#6B7280',
                                callback: function(val) { return 'Rp ' + (val/1000000).toFixed(0) + 'Jt'; }
                            }
                        }
                    }
                }
            });
        },
        error: function(xhr, status, error) {
            $('#chartLoader').hide();
            // Pesan error dari global ajaxSetup sudah cukup
            console.error("Gagal memuat grafik:", error);
        }
    });
}

// Initial load menggunakan value dari input PHP
$(document).ready(function() {
    var initialStart = $('#filterStart').val();
    var initialEnd   = $('#filterEnd').val();
    loadChart(initialStart, initialEnd);
});
</script>