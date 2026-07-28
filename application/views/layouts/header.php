<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Ridho Interior - Aplikasi Manajemen Keuangan Workshop Interior">
    <title><?= isset($title) ? $title : 'Ridho Interior' ?></title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Select2 CSS & JS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Custom CSS -->
    <style>
        :root {
            --primary:       #1E3A8A;
            --primary-light: #2563EB;
            --primary-dark:  #1e2d6b;
            --accent:        #3B82F6;
            --bg-page:       #F1F5F9;
            --bg-card:       #FFFFFF;
            --text-primary:  #111827;
            --text-muted:    #6B7280;
            --border:        #E5E7EB;
            --success:       #10B981;
            --warning:       #F59E0B;
            --danger:        #EF4444;
            --sidebar-width: 260px;
            --topbar-height: 64px;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-page);
            color: var(--text-primary);
            margin: 0;
            padding: 0;
        }

        /* ── SIDEBAR ── */
        #sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, var(--primary-dark) 0%, var(--primary) 50%, #1d4ed8 100%);
            z-index: 1000;
            overflow-y: auto;
            transition: transform 0.3s ease;
            box-shadow: 4px 0 20px rgba(0,0,0,0.15);
        }

        #sidebar .sidebar-brand {
            padding: 24px 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        #sidebar .sidebar-brand h2 {
            color: #fff;
            font-size: 1.2rem;
            font-weight: 800;
            margin: 0;
            letter-spacing: -0.3px;
        }

        #sidebar .sidebar-brand p {
            color: rgba(255,255,255,0.6);
            font-size: 0.72rem;
            margin: 4px 0 0;
        }

        #sidebar .nav-section-label {
            color: rgba(255,255,255,0.4);
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            padding: 20px 20px 8px;
        }

        #sidebar .nav-link {
            color: rgba(255,255,255,0.75);
            padding: 11px 20px;
            border-radius: 8px;
            margin: 2px 12px;
            font-size: 0.875rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        #sidebar .nav-link:hover {
            background: rgba(255,255,255,0.12);
            color: #fff;
            transform: translateX(2px);
        }

        #sidebar .nav-link.active {
            background: rgba(255,255,255,0.18);
            color: #fff;
            font-weight: 600;
            box-shadow: inset 3px 0 0 rgba(255,255,255,0.5);
        }

        #sidebar .nav-link i { font-size: 1rem; width: 20px; text-align: center; }

        /* ── TOPBAR ── */
        #topbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: var(--topbar-height);
            background: var(--bg-card);
            border-bottom: 1px solid var(--border);
            z-index: 999;
            display: flex;
            align-items: center;
            padding: 0 28px;
            gap: 16px;
            box-shadow: 0 1px 8px rgba(0,0,0,0.05);
        }

        #topbar .topbar-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--text-primary);
            flex: 1;
        }

        #topbar .topbar-workshop {
            font-size: 0.78rem;
            color: var(--text-muted);
            background: var(--bg-page);
            padding: 6px 12px;
            border-radius: 20px;
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* ── MAIN CONTENT ── */
        #main-content {
            margin-left: var(--sidebar-width);
            margin-top: var(--topbar-height);
            padding: 28px;
            min-height: calc(100vh - var(--topbar-height));
        }

        /* ── CARDS ── */
        .card {
            border: 1px solid var(--border);
            border-radius: 12px;
            box-shadow: 0 1px 6px rgba(0,0,0,0.05);
            background: var(--bg-card);
        }

        .card-header {
            background: var(--bg-card);
            border-bottom: 1px solid var(--border);
            padding: 16px 20px;
            border-radius: 12px 12px 0 0 !important;
        }

        .card-header h5 {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0;
        }

        /* ── STAT CARDS ── */
        .stat-card {
            border-radius: 14px;
            padding: 22px;
            position: relative;
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }

        .stat-card .stat-icon {
            width: 48px; height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            margin-bottom: 14px;
        }

        .stat-card .stat-value {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text-primary);
            letter-spacing: -0.5px;
        }

        .stat-card .stat-label {
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 4px;
        }

        /* ── TABLES ── */
        .table-container { border-radius: 12px; overflow: hidden; }

        .table thead th {
            background: var(--bg-page);
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--border);
            padding: 12px 16px;
            white-space: nowrap;
        }

        .table tbody td {
            padding: 13px 16px;
            font-size: 0.875rem;
            vertical-align: middle;
            border-bottom: 1px solid #F3F4F6;
        }

        .table tbody tr:last-child td { border-bottom: none; }

        .table tbody tr:hover { background: #F9FAFB; }

        /* ── BADGES ── */
        .badge-status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.72rem;
            font-weight: 600;
        }

        .badge-lunas    { background: #D1FAE5; color: #065F46; }
        .badge-belum    { background: #FEF3C7; color: #92400E; }
        .badge-aktif    { background: #DBEAFE; color: #1E40AF; }
        .badge-selesai  { background: #D1FAE5; color: #065F46; }
        .badge-ditunda  { background: #FEE2E2; color: #991B1B; }
        .badge-senior   { background: #EDE9FE; color: #5B21B6; }
        .badge-junior   { background: #DBEAFE; color: #1E40AF; }
        .badge-baru     { background: #FEF3C7; color: #92400E; }
        .badge-upah     { background: #D1FAE5; color: #065F46; }
        .badge-tarik    { background: #FEE2E2; color: #991B1B; }

        /* ── BUTTONS ── */
        .btn-primary {
            background: var(--primary);
            border-color: var(--primary);
            color: #fff;
            font-weight: 600;
            font-size: 0.875rem;
            border-radius: 8px;
            padding: 8px 16px;
            transition: all 0.2s;
        }
        .btn-primary:hover {
            background: var(--primary-light);
            border-color: var(--primary-light);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3);
        }

        .btn-action {
            width: 32px; height: 32px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            transition: all 0.2s;
        }
        .btn-edit   { background: #DBEAFE; color: var(--primary); }
        .btn-delete { background: #FEE2E2; color: var(--danger); }
        .btn-detail { background: #D1FAE5; color: var(--success); }
        .btn-action:hover { opacity: 0.8; transform: scale(1.05); }

        /* ── MODALS ── */
        .modal-content {
            border: none;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        }
        .modal-header {
            background: var(--primary);
            color: #fff;
            border-radius: 16px 16px 0 0;
            padding: 18px 24px;
        }
        .modal-header .modal-title { font-weight: 700; font-size: 1rem; }
        .modal-header .btn-close { filter: invert(1); }
        .modal-body { padding: 24px; }
        .modal-footer { padding: 16px 24px; border-top: 1px solid var(--border); }

        .form-label { font-size: 0.825rem; font-weight: 600; color: var(--text-primary); margin-bottom: 6px; }
        .form-control, .form-select {
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 0.875rem;
            padding: 9px 12px;
            color: var(--text-primary);
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }

        /* ── TOAST ── */
        #toast-container {
            position: fixed;
            bottom: 24px; right: 24px;
            z-index: 9999;
        }
        .toast-notif {
            min-width: 300px;
            padding: 14px 18px;
            border-radius: 10px;
            font-size: 0.875rem;
            font-weight: 500;
            margin-top: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideIn 0.3s ease;
            box-shadow: 0 4px 20px rgba(0,0,0,0.12);
        }
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to   { transform: translateX(0); opacity: 1; }
        }
        .toast-success { background: #ECFDF5; color: #065F46; border-left: 4px solid var(--success); }
        .toast-error   { background: #FEF2F2; color: #991B1B; border-left: 4px solid var(--danger); }

        /* ── PAGE HEADER ── */
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }
        .page-header h1 {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text-primary);
            margin: 0;
        }
        .page-header p {
            font-size: 0.825rem;
            color: var(--text-muted);
            margin: 4px 0 0;
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 768px) {
            #sidebar { transform: translateX(-100%); }
            #sidebar.show { transform: translateX(0); }
            #topbar { left: 0; }
            #main-content { margin-left: 0; }
        }

        /* ── SCROLLBAR ── */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #94A3B8; }
    </style>
</head>
<body>

<!-- Toast Container -->
<div id="toast-container"></div>
