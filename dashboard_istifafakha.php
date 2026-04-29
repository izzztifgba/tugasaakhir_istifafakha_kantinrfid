<?php
ob_start();
session_start();
include "koneksi_istifafakha.php";

// 1. Validasi Login & Proteksi Halaman
if (!isset($_SESSION['loginPetugas_istifafakha']) || $_SESSION['loginPetugas_istifafakha'] !== true) {
    ob_end_clean();
    header("Location: loginPetugas_istifafakha.php");
    exit;
}

// Ambil data dari session untuk digunakan di dashboard
$role = $_SESSION['role_istifafakha'] ?? 'petugas';
$nama = $_SESSION['nama_petugas_istifafakha'] ?? 'User';
$id_user_login = $_SESSION['id_user_istifafakha'];

// --- LOGIC STATISTIK (BACKEND) ---
$tanggal_sekarang = date('Y-m-d');

// A. Menghitung Total Siswa Terdaftar
$q_siswa = mysqli_query($koneksi_istifafakha, "SELECT COUNT(*) as total FROM siswa_istifafakha");
$data_siswa = mysqli_fetch_assoc($q_siswa);

// B. Menghitung Pendapatan Hari Ini
if ($role == 'admin') {
    // Admin melihat total semua transaksi dari semua kantin hari ini
    $q_pendapatan = mysqli_query($koneksi_istifafakha, "SELECT SUM(total_bayar) as total FROM transaksi_istifafakha WHERE DATE(waktu) = '$tanggal_sekarang'");
} else {
    // Petugas hanya melihat total penjualannya sendiri hari ini
    $q_pendapatan = mysqli_query($koneksi_istifafakha, "SELECT SUM(total_bayar) as total FROM transaksi_istifafakha WHERE DATE(waktu) = '$tanggal_sekarang' AND id_petugas = '$id_user_login'");
}
$data_pendapatan = mysqli_fetch_assoc($q_pendapatan);
$pendapatan = $data_pendapatan['total'] ?? 0;

// C. Menghitung Jumlah Transaksi Hari Ini
if ($role == 'admin') {
    $q_transaksi = mysqli_query($koneksi_istifafakha, "SELECT COUNT(*) as jml FROM transaksi_istifafakha WHERE DATE(waktu) = '$tanggal_sekarang'");
} else {
    $q_transaksi = mysqli_query($koneksi_istifafakha, "SELECT COUNT(*) as jml FROM transaksi_istifafakha WHERE DATE(waktu) = '$tanggal_sekarang' AND id_petugas = '$id_user_login'");
}
$data_transaksi = mysqli_fetch_assoc($q_transaksi);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?= strtoupper(htmlspecialchars($role)); ?></title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&display=swap');

        :root {
            --emerald-dark: #022e1a;
            --emerald-main: #065f37;
            --emerald-light: #50c878;
            --white: #ffffff;
            --bg-soft: #f0f4f3;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
        }

        body {
            display: flex;
            min-height: 100vh;
            background-color: var(--bg-soft);
        }

        /* --- SIDEBAR (KONSISTEN DENGAN KASIR) --- */
        nav {
            width: 280px;
            background: linear-gradient(180deg, var(--emerald-dark) 0%, var(--emerald-main) 100%);
            color: white;
            padding: 30px 20px;
            display: flex;
            flex-direction: column;
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            height: 100vh;
            z-index: 100;
        }

        nav h2 {
            font-weight: 700;
            letter-spacing: -1px;
            color: white;
            padding-left: 10px;
            margin-bottom: 30px;
        }

        .user-info {
            background: rgba(255, 255, 255, 0.08);
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 25px;
            border-left: 3px solid var(--emerald-light);
        }

        .user-info .user-name {
            font-size: 14px;
            font-weight: 700;
            color: white;
            margin-bottom: 5px;
            word-break: break-word;
        }

        .user-info .user-role {
            font-size: 11px;
            text-transform: uppercase;
            color: var(--emerald-light);
            letter-spacing: 1px;
            font-weight: 600;
        }

        nav h3 {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--emerald-light);
            margin: 25px 0 15px 10px;
            font-weight: 700;
        }

        nav ul {
            list-style: none;
        }

        nav ul li {
            margin-bottom: 8px;
        }

        nav ul li a {
            text-decoration: none;
            color: rgba(255, 255, 255, 0.8);
            padding: 12px 15px;
            display: block;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        nav ul li a:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            padding-left: 20px;
        }

        nav ul li a.active {
            background: var(--emerald-light);
            color: var(--emerald-dark);
            font-weight: 700;
        }

        .logout-btn {
            margin-top: auto;
            background: rgba(255, 0, 0, 0.1) !important;
            color: #ff6b6b !important;
            border: 1px solid rgba(255, 107, 107, 0.2);
            border-radius: 12px;
            text-decoration: none;
            transition: 0.3s;
        }

        .logout-btn:hover {
            background: #ff6b6b !important;
            color: white !important;
        }

        /* --- MAIN CONTENT STYLE --- */
        main {
            flex: 1;
            padding: 40px;
            overflow-y: auto;
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        .welcome-text h1 {
            font-size: 32px;
            color: var(--emerald-dark);
            font-weight: 700;
        }

        .role-badge {
            background: white;
            border: 2px solid var(--emerald-light);
            color: var(--emerald-main);
            padding: 10px 25px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 700;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        /* Grid Statistik */
        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 24px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(0, 0, 0, 0.02);
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
        }

        .card h4 {
            font-size: 14px;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 15px;
        }

        .card .value {
            font-size: 32px;
            font-weight: 700;
            color: var(--emerald-dark);
        }

        .card .sub-value {
            margin-top: 10px;
            font-size: 13px;
            color: #999;
        }

        /* Info Banner */
        .info-banner {
            background: linear-gradient(135deg, #ffffff 0%, #f9fdfc 100%);
            padding: 50px;
            border-radius: 30px;
            text-align: center;
            border: 1px solid #e0e8e5;
            box-shadow: 0 15px 35px rgba(6, 95, 55, 0.05);
        }

        .info-banner h3 {
            font-size: 24px;
            color: var(--emerald-main);
            margin-bottom: 15px;
        }

        .user-info {
            background: rgba(255, 255, 255, 0.08);
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 25px;
            border-left: 3px solid var(--emerald-light);
        }

        .user-info .user-name {
            font-size: 14px;
            font-weight: 700;
            color: white;
            margin-bottom: 5px;
            word-break: break-word;
        }

        .user-info .user-role {
            font-size: 11px;
            text-transform: uppercase;
            color: var(--emerald-light);
            letter-spacing: 1px;
            font-weight: 600;
        }
    </style>
</head>

<body>

    <nav>
        <div>
            <h2>KANTIN<span style="color: var(--emerald-light);">RFID</span></h2>
        </div>

        <div class="user-info">
            <div class="user-name"><?= htmlspecialchars($_SESSION['nama_petugas_istifafakha']); ?></div>
            <div class="user-role"><?= strtoupper(htmlspecialchars($role)); ?></div>
        </div>

        <ul>
            <li><a href="dashboard_istifafakha.php" class="active">Dashboard Home</a></li>

            <?php if ($role == 'admin'): ?>
                <h3>Administrator</h3>
                <li><a href="datapetugas_istifafakha.php">Data Kantin & Petugas</a></li>
                <li><a href="datasiswa_istifafakha.php">Registrasi & Top-Up</a></li>
                <li><a href="cek_saldo_kantin.php">Cek Saldo Kantin</a></li>
                <li><a href="laporan_pendapatan_kantin.php">Pendapatan Seluruh</a></li>
                <li><a href="tarik_tunai_istifafakha.php">Penarikan Uang</a></li>
            <?php endif; ?>

            <?php if ($role == 'kantin' || $role == 'petugas'): ?>
                <h3>Operasional</h3>
                <li><a href="kasir_istifafakha.php">Kasir Penjualan</a></li>
                <li><a href="cek_saldo_siswa.php">Cek Saldo Siswa</a></li>
                <li><a href="laporan_penjualan_istifafakha.php">Riwayat Saya</a></li>
            <?php endif; ?>
        </ul>

        <ul style="margin-top: auto;">
            <li>
                <a href="logout_istifafakha.php" class="logout-btn" onclick="return confirm('Yakin ingin logout?')">
                    Logout Sistem
                </a>
            </li>
        </ul>
    </nav>

    <main>
        <div class="header-section">
            <div class="welcome-text">
                <h1>Halo, <?= htmlspecialchars($nama); ?>! 👋</h1>
                <p>Pantau performa kantin secara real-time hari ini.</p>
            </div>
            <div class="role-badge">
                ID: #<?= str_pad($id_user_login, 3, '0', STR_PAD_LEFT); ?> | <?= strtoupper(htmlspecialchars($role)); ?>
            </div>
        </div>

        <div class="grid-container">
            <div class="card">
                <h4>Total Siswa</h4>
                <div class="value"><?= number_format($data_siswa['total'], 0, ',', '.'); ?></div>
                <div class="sub-value">Siswa terdaftar aktif</div>
            </div>

            <div class="card">
                <h4><?= ($role == 'admin') ? 'Pendapatan Hari Ini' : 'Penjualan Saya'; ?></h4>
                <div class="value" style="color: var(--emerald-main);">
                    Rp <?= number_format($pendapatan, 0, ',', '.'); ?>
                </div>
                <div class="sub-value">Per tanggal <?= date('d M Y'); ?></div>
            </div>

            <div class="card">
                <h4>Volume Transaksi</h4>
                <div class="value"><?= $data_transaksi['jml']; ?></div>
                <div class="sub-value">Transaksi sukses hari ini</div>
            </div>
        </div>

        <div class="info-banner">
            <div style="font-size: 40px; margin-bottom: 20px;">📊</div>
            <h3>Selamat Datang di Panel Kendali</h3>
            <p>Sistem ini membantu Anda mengelola transaksi RFID dengan aman dan cepat. <br> Silakan pilih menu di
                samping untuk memulai aktivitas Anda.</p>
        </div>
    </main>

</body>

</html>
<?php ob_end_flush(); ?>