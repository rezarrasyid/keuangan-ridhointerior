<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Login - Ridho Interior Financial Management">
    <title><?= $title ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #1E3A8A;
            --primary-light: #2563EB;
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0f1f5c 0%, #1E3A8A 40%, #1d4ed8 70%, #3B82F6 100%);
            position: relative;
            overflow: hidden;
        }
        /* Decorative circles */
        body::before, body::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            background: rgba(255,255,255,0.04);
        }
        body::before { width: 600px; height: 600px; top: -200px; right: -200px; }
        body::after  { width: 400px; height: 400px; bottom: -150px; left: -150px; }

        .login-card {
            background: rgba(255,255,255,0.97);
            border-radius: 20px;
            box-shadow: 0 25px 60px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 420px;
            padding: 44px 40px;
            position: relative;
            z-index: 10;
            animation: cardUp 0.5s ease;
        }
        @keyframes cardUp {
            from { transform: translateY(30px); opacity: 0; }
            to   { transform: translateY(0); opacity: 1; }
        }
        .login-logo {
            width: 56px; height: 56px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 20px;
            box-shadow: 0 8px 20px rgba(30,58,138,0.3);
        }
        .login-logo i { font-size: 1.6rem; color: #fff; }
        .login-card h1 {
            font-size: 1.6rem;
            font-weight: 800;
            color: #111827;
            margin-bottom: 4px;
        }
        .login-card p.sub {
            font-size: 0.875rem;
            color: #6B7280;
            margin-bottom: 28px;
        }
        .form-label {
            font-size: 0.825rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }
        .form-control {
            border: 1.5px solid #E5E7EB;
            border-radius: 10px;
            padding: 11px 14px;
            font-size: 0.9rem;
            transition: all 0.2s;
        }
        .form-control:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(37,99,235,0.12);
        }
        .input-group-text {
            border: 1.5px solid #E5E7EB;
            background: #F9FAFB;
            border-radius: 10px 0 0 10px;
            color: #9CA3AF;
        }
        .btn-login {
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border: none;
            color: #fff;
            font-size: 0.95rem;
            font-weight: 700;
            letter-spacing: 0.3px;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 8px;
            box-shadow: 0 4px 15px rgba(30,58,138,0.3);
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(30,58,138,0.4);
        }
        .alert-error {
            background: #FEF2F2;
            color: #991B1B;
            border: 1px solid #FECACA;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 0.85rem;
            font-weight: 500;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .demo-hint {
            margin-top: 24px;
            padding: 12px;
            background: #EFF6FF;
            border-radius: 10px;
            font-size: 0.78rem;
            color: #1E40AF;
            border: 1px solid #BFDBFE;
        }
        .demo-hint strong { display: block; margin-bottom: 4px; font-size: 0.8rem; }
    </style>
</head>
<body>
<div class="login-card">
    <div class="login-logo">
        <i class="bi bi-house-heart-fill"></i>
    </div>
    <h1>Ridho Interior</h1>
    <p class="sub">Sistem Manajemen Keuangan Workshop</p>

    <?php if ($this->session->flashdata('error')): ?>
    <div class="alert-error">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <?= $this->session->flashdata('error') ?>
    </div>
    <?php endif; ?>

    <form action="<?= base_url('auth/do_login') ?>" method="POST">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                <input type="text"
                       id="username"
                       name="username"
                       class="form-control"
                       placeholder="Masukkan username"
                       required
                       autocomplete="username"
                       style="border-radius:0 10px 10px 0;border-left:none;">
            </div>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                <input type="password"
                       id="password"
                       name="password"
                       class="form-control"
                       placeholder="Masukkan password"
                       required
                       autocomplete="current-password"
                       style="border-radius:0 10px 10px 0;border-left:none;">
            </div>
        </div>
        <button type="submit" class="btn-login">
            <i class="bi bi-box-arrow-in-right me-2"></i>Masuk ke Sistem
        </button>
    </form>

    <div class="demo-hint">
        <strong><i class="bi bi-info-circle me-1"></i>Akun Demo:</strong>
        <span>Username: <code>admin_jakarta</code>&nbsp; | &nbsp;Password: <code>password</code></span>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
