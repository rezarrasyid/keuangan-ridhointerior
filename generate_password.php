<?php
/**
 * Script helper untuk generate password hash yang benar
 * Jalankan sekali di terminal: php generate_password.php
 * Lalu update kolom password di tabel users dengan hash yang dihasilkan
 */

$passwords = [
    'password' => password_hash('password', PASSWORD_BCRYPT),
    'admin123' => password_hash('admin123', PASSWORD_BCRYPT),
];

echo "=== Password Hash Generator ===\n\n";
foreach ($passwords as $plain => $hash) {
    echo "Plain:  {$plain}\n";
    echo "Hash:   {$hash}\n";
    echo "Verify: " . (password_verify($plain, $hash) ? "✅ VALID" : "❌ INVALID") . "\n";
    echo "\n";
}

echo "=== SQL UPDATE untuk akun demo ===\n\n";
$hash = password_hash('password', PASSWORD_BCRYPT);
echo "UPDATE users SET password = '{$hash}' WHERE username IN ('superadmin', 'admin_jakarta', 'admin_bandung');\n";
