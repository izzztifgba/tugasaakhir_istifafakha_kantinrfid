<?php
ob_start();
session_start();

// Warna styling
$style = "
<style>
    body { font-family: Arial; margin: 20px; background: #f5f5f5; }
    .container { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    h1 { color: #333; border-bottom: 3px solid #065f37; padding-bottom: 10px; }
    .status { padding: 12px; margin: 10px 0; border-radius: 5px; font-weight: bold; }
    .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    .info { background: #d1ecf1; color: #0c5460; border: 1px ssolid #bee5eb; }
    .session-data { background: #f9f9f9; padding: 12px; border-left: 4px solid #065f37; margin: 12px 0; font-family: monospace; }
    .warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
    button { padding: 10px 20px; margin: 5px; background: #065f37; color: white; border: none; border-radius: 5px; cursor: pointer; }
    button:hover { background: #50c878; }
</style>
";

echo $style;
echo "<div class='container'>";
echo "<h1>🔍 DEBUG SESSION KANTIN RFID</h1>";

// 1. Cek Session Active
echo "<h2>Status Session</h2>";
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "<div class='status success'>✓ Session aktif</div>";
} else {
    echo "<div class='status error'>✗ Session tidak aktif</div>";
}

// 2. Session ID
echo "<div class='info'>";
echo "<strong>Session ID:</strong> " . session_id() . "<br>";
echo "<strong>Session Name:</strong> " . session_name() . "<br>";
echo "</div>";

// 3. Cek Login Session
echo "<h2>Status Login</h2>";
if (isset($_SESSION['loginPetugas_istifafakha']) && $_SESSION['loginPetugas_istifafakha'] === true) {
    echo "<div class='status success'>✓ User TERLOGIN</div>";
} else {
    echo "<div class='status error'>✗ User TIDAK TERLOGIN</div>";
}

// 4. Data Session User
echo "<h2>Data Session</h2>";
echo "<div class='session-data'>";
echo "<strong>SESSION Array :</strong><br>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";
echo "</div>";

// 5. Cek Cookies
echo "<h2>Cookies Browser</h2>";
echo "<div class='session-data'>";
echo "<strong>COOKIE Array :</strong><br>";
if (!empty($_COOKIE)) {
    echo "<pre>";
    print_r($_COOKIE);
    echo "</pre>";
} else {
    echo "<div class='warning'>⚠ Tidak ada cookies diterima dari browser</div>";
}
echo "</div>";

// 6. Cek Database
echo "<h2>Status Database</h2>";
include "koneksi_istifafakha.php";

if (mysqli_connect_errno()) {
    echo "<div class='status error'>✗ Database gagal: " . mysqli_connect_errno() . "</div>";
} else {
    echo "<div class='status success'>✓ Database terhubung</div>";

    // Cek tabel user
    $result = mysqli_query($koneksi_istifafakha, "SELECT COUNT(*) as total FROM user_istifafakha");
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        echo "<div class='info'>Total user di database: " . $row['total'] . "</div>";
    }
}

// 7. Rekomendasi
echo "<h2>Rekomendasi</h2>";
echo "<div class='info'>";
echo "<strong>Jika session tidak aktif, cek:</strong><br>";
echo "1. Folder sessions di php sudah bisa ditulis: <code>" . ini_get('session.save_path') . "</code><br>";
echo "2. Browser cookies sudah diaktifkan<br>";
echo "3. Tidak ada output sebelum session_start()<br>";
echo "4. Headers belum dikirim sebelum redirect<br>";
echo "</div>";

echo "<br>";
echo "<button onclick='window.location=\"loginPetugas_istifafakha.php\"'>← Kembali ke Login</button>";
echo "</div>";

ob_end_flush();
?>