<?php
function fmt($n) { return 'Rp ' . number_format($n, 0, ',', '.'); }
$kpi = (object) $kpi_tambahan;
$persen_tagihan = $kpi->total_nilai > 0 ? round(($kpi->total_tagihan / $kpi->total_nilai) * 100, 2) : 0;
?>

<!-- Page Header -->
<div class="page-header flex-wrap">
    <div>
        <h1 style="color:var(--primary-dark);"><i class="bi bi-globe me-2"></i>Dashboard Pusat</h1>
        <p>Ringkasan Gabungan Seluruh Cabang (<?= date('d M Y', strtotime($start_date)) ?> s/d <?= date('d M Y', strtotime($end_date)) ?>)</p>
    </div>
    <!-- Filter Date Range -->
    <div style="display:flex;align-items:center;gap:10px;background:#fff;padding:8px 12px;border-radius:10px;border:1px solid var(--border);box-shadow:0 2px 4px rgba(0,0,0,0.02);">
        <input type="date" id="filterStart" class="form-control form-control-sm" value="<?= $start_date ?>" style="width:120px;border:none;background:#F1F5F9;">
        <span style="font-size:0.8rem;color:#6B7280;font-weight:600;">s/d</span>
        <input type="date" id="filterEnd" class="form-control form-control-sm" value="<?= $end_date ?>" style="width:120px;border:none;background:#F1F5F9;">
        <button id="btnFilter" class="btn btn-primary btn-sm ms-1"><i class="bi bi-search"></i></button>
    </div>
</div>

<!-- ── KPI Cards (GLOBAL BAWAAN) ── -->
<div class="row g-3 mb-3">
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card" style="border-top:4px solid #10B981;">
            <div class="stat-icon" style="background:#D1FAE5;color:#065F46;"><i class="bi bi-arrow-down-circle-fill"></i></div>
            <div class="stat-value"><?= fmt($total_pemasukan) ?></div>
            <div class="stat-label">Total Pemasukan (Global)</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card" style="border-top:4px solid #EF4444;">
            <div class="stat-icon" style="background:#FEE2E2;color:#991B1B;"><i class="bi bi-arrow-up-circle-fill"></i></div>
            <div class="stat-value"><?= fmt($total_pengeluaran) ?></div>
            <div class="stat-label">Total Pengeluaran (Global)</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <?php $net = $total_pemasukan - $total_pengeluaran; ?>
        <div class="card stat-card" style="border-top:4px solid <?= $net >= 0 ? '#3B82F6' : '#F59E0B' ?>;">
            <div class="stat-icon" style="background:<?= $net >= 0 ? '#DBEAFE' : '#FEF3C7' ?>;color:<?= $net >= 0 ? '#1E40AF' : '#92400E' ?>;"><i class="bi bi-wallet2"></i></div>
            <div class="stat-value" style="color:<?= $net >= 0 ? '#1E40AF' : '#92400E' ?>;"><?= fmt($net) ?></div>
            <div class="stat-label">Net Cashflow (Global)</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card" style="border-top:4px solid #8B5CF6;">
            <div class="stat-icon" style="background:#EDE9FE;color:#5B21B6;"><i class="bi bi-kanban-fill"></i></div>
            <div class="stat-value"><?= $proyek_aktif ?></div>
            <div class="stat-label">Total Proyek Aktif (Semua Cabang)</div>
        </div>
    </div>
</div>

<!-- ── KPI Cards (TAMBAHAN KLIEN - OVERALL GLOBAL) ── -->
<div class="row g-2 mb-4">
    <div class="col-md-4 col-xl-2">
        <div class="card stat-card" style="background:#E27B42; color:#fff; padding:15px; border-radius:10px;">
            <div style="font-size:0.8rem; font-weight:600; opacity:0.9;">Total Nilai Proyek</div>
            <div style="font-size:1.1rem; font-weight:700; margin-top:5px;"><?= fmt($kpi->total_nilai) ?></div>
        </div>
    </div>
    <div class="col-md-4 col-xl-2">
        <div class="card stat-card" style="background:#8C9B6A; color:#fff; padding:15px; border-radius:10px;">
            <div style="font-size:0.8rem; font-weight:600; opacity:0.9;">Total DP</div>
            <div style="font-size:1.1rem; font-weight:700; margin-top:5px;"><?= fmt($kpi->total_dp) ?></div>
        </div>
    </div>
    <div class="col-md-4 col-xl-2">
        <div class="card stat-card" style="background:#7FB3D5; color:#fff; padding:15px; border-radius:10px;">
            <div style="font-size:0.8rem; font-weight:600; opacity:0.9;">Total Pelunasan</div>
            <div style="font-size:1.1rem; font-weight:700; margin-top:5px;"><?= fmt($kpi->total_pelunasan) ?></div>
        </div>
    </div>
    <div class="col-md-4 col-xl-2">
        <div class="card stat-card" style="background:#807B77; color:#fff; padding:15px; border-radius:10px;">
            <div style="font-size:0.8rem; font-weight:600; opacity:0.9;">Total Tagihan</div>
            <div style="font-size:1.1rem; font-weight:700; margin-top:5px;"><?= fmt($kpi->total_tagihan) ?></div>
        </div>
    </div>
    <div class="col-md-4 col-xl-2">
        <div class="card stat-card" style="background:#659E93; color:#fff; padding:15px; border-radius:10px;">
            <div style="font-size:0.8rem; font-weight:600; opacity:0.9;">% Tagihan</div>
            <div style="font-size:1.3rem; font-weight:700; margin-top:5px;"><?= $persen_tagihan ?>%</div>
        </div>
    </div>
    <div class="col-md-4 col-xl-2">
        <div class="card stat-card" style="background:#DCAB56; color:#fff; padding:15px; border-radius:10px;">
            <div style="font-size:0.8rem; font-weight:600; opacity:0.9;">Total Client</div>
            <div style="font-size:1.3rem; font-weight:700; margin-top:5px;"><?= $kpi->total_klien ?> <i class="bi bi-people ms-2"></i></div>
        </div>
    </div>
</div>

<!-- ── CHARTS EXISTING (Line Chart Global & Tabel Cabang) ── -->
<div class="row g-3 mb-4">
    <!-- Line Chart Global -->
    <div class="col-xl-7">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5><i class="bi bi-activity me-2" style="color:var(--primary-light);"></i>Grafik Gabungan Harian</h5>
            </div>
            <div class="card-body" style="padding:20px;">
                <div id="chartContainer" style="position:relative;height:320px;display:none;">
                    <canvas id="financeChart"></canvas>
                </div>
                <div id="chartLoader" style="text-align:center;padding:40px;">
                    <div class="spinner-border text-primary" style="width:28px;height:28px;"></div>
                    <p style="margin:10px 0 0;font-size:0.825rem;color:#6B7280;">Memuat data gabungan...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Performa per Cabang -->
    <div class="col-xl-5">
        <div class="card h-100">
            <div class="card-header">
                <h5><i class="bi bi-shop me-2" style="color:#F59E0B;"></i>Performa per Cabang</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead style="background:#F9FAFB;">
                            <tr>
                                <th style="font-size:0.75rem;padding:12px 16px;">CABANG</th>
                                <th style="font-size:0.75rem;text-align:right;">PEMASUKAN</th>
                                <th style="font-size:0.75rem;text-align:right;">PENGELUARAN</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($performa_cabang as $cabang): ?>
                            <tr>
                                <td style="font-weight:600;font-size:0.85rem;">
                                    <i class="bi bi-geo-alt-fill me-1" style="color:#9CA3AF;"></i> <?= htmlspecialchars($cabang->nama_workshop) ?>
                                </td>
                                <td style="text-align:right;color:#059669;font-weight:600;font-size:0.85rem;">
                                    <?= fmt($cabang->total_pemasukan) ?>
                                </td>
                                <td style="text-align:right;color:#DC2626;font-weight:600;font-size:0.85rem;">
                                    <?= fmt($cabang->total_pengeluaran) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ── CHARTS TAMBAHAN (DARI KLIEN) ── -->
<div class="row g-3">
    <!-- Pie Chart Status Project -->
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header"><h5>Status per Project (Keseluruhan)</h5></div>
            <div class="card-body">
                <canvas id="pieStatusChart" style="max-height:250px;"></canvas>
            </div>
        </div>
    </div>

    <!-- Top 10 Klien Tagihan Tertinggi -->
    <div class="col-md-8">
        <div class="card h-100">
            <div class="card-header"><h5>Top 10 Klien dengan Tagihan Tertinggi (Keseluruhan)</h5></div>
            <div class="card-body">
                <canvas id="barTagihanChart" style="max-height:250px;"></canvas>
            </div>
        </div>
    </div>

    <!-- Distribusi Pembayaran -->
    <div class="col-12">
        <div class="card">
            <div class="card-header"><h5>Distribusi Pembayaran (Keseluruhan)</h5></div>
            <div class="card-body">
                <canvas id="stackedDistribusiChart" style="max-height:150px;"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
$('#btnFilter').on('click', function() {
    var start = $('#filterStart').val();
    var end   = $('#filterEnd').val();
    if(start && end) {
        window.location.href = BASE_URL + 'dashboard/pusat?start_date=' + start + '&end_date=' + end;
    }
});

var chartInstance = null;

function loadChart(start, end) {
    $('#chartLoader').show();
    $('#chartContainer').hide();
    
    $.ajax({
        url: BASE_URL + 'dashboard/chart_data_pusat',
        type: 'GET',
        data: { start_date: start, end_date: end },
        dataType: 'json',
        success: function(res) {
            $('#chartLoader').hide();
            $('#chartContainer').show();

            if (chartInstance) chartInstance.destroy();

            var ctx = document.getElementById('financeChart').getContext('2d');
            chartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: res.labels,
                    datasets: [
                        {
                            label: 'Pemasukan Global',
                            data: res.pemasukan,
                            backgroundColor: 'rgba(16,185,129,0.1)',
                            borderColor: '#10B981',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.3,
                            pointRadius: 3
                        },
                        {
                            label: 'Pengeluaran Global',
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
                        legend: { position: 'top' },
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
                            ticks: {
                                autoSkip: true,
                                maxTicksLimit: 12,
                                maxRotation: 45,
                                minRotation: 45
                            }
                        },
                        y: {
                            grid: { color: '#F3F4F6' },
                            beginAtZero: true,
                            ticks: { 
                                callback: function(value) { 
                                    if (value >= 1000000) {
                                        return 'Rp ' + (value / 1000000).toFixed(1).replace('.0', '') + ' Jt';
                                    }
                                    return 'Rp ' + value.toLocaleString('id-ID'); 
                                } 
                            }
                        }
                    }
                }
            });
        }
    });
}

$(document).ready(function() {
    var initialStart = $('#filterStart').val();
    var initialEnd   = $('#filterEnd').val();
    loadChart(initialStart, initialEnd);

    // ── CHARTS TAMBAHAN (DARI KLIEN) ──
    var statusData = <?= json_encode($status_project) ?>;
    var tagihanData = <?= json_encode($top_tagihan) ?>;
    var distribusiData = <?= json_encode($distribusi) ?>;

    // 1. PIE CHART (Status Project)
    new Chart(document.getElementById('pieStatusChart').getContext('2d'), {
        type: 'pie',
        data: {
            labels: ['LUNAS', 'BELUM LUNAS'],
            datasets: [{
                data: [statusData.lunas, statusData.belum_lunas],
                backgroundColor: ['#DCAB56', '#E27B42']
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    // 2. BAR CHART (Top 10 Tagihan)
    var tagihanLabels = tagihanData.map(item => item.nama);
    var tagihanValues = tagihanData.map(item => item.tagihan);
    new Chart(document.getElementById('barTagihanChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: tagihanLabels,
            datasets: [{
                label: 'Total Tagihan (Belum Dibayar)',
                data: tagihanValues,
                backgroundColor: '#7FB3D5'
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } }
        }
    });

    // 3. STACKED BAR CHART (Distribusi Pembayaran)
    var distDatasets = distribusiData.map((item, index) => {
        const colors = ['#7FB3D5', '#E27B42', '#8C9B6A', '#DCAB56', '#807B77', '#659E93'];
        return {
            label: item.termin,
            data: [item.total],
            backgroundColor: colors[index % colors.length]
        };
    });

    new Chart(document.getElementById('stackedDistribusiChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: ['Distribusi'],
            datasets: distDatasets
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { stacked: true },
                y: { stacked: true, display: false }
            }
        }
    });
});
</script>