<?php
ob_start();
session_start();
include "koneksi_istifafakha.php";

// 1. Validasi Login
if (!isset($_SESSION['loginPetugas_istifafakha']) || $_SESSION['loginPetugas_istifafakha'] !== true) {
    ob_end_clean();
    header("Location: loginPetugas_istifafakha.php");
    exit;
}

$role = $_SESSION['role_istifafakha'] ?? 'petugas';
$nama = $_SESSION['nama_petugas_istifafakha'] ?? 'User';
$id_user_login = $_SESSION['id_user_istifafakha'];

$today = date('Y-m-d');

// --- DATA LAPORAN ---
$sql_stats = "SELECT COUNT(*) as total_transaksi, SUM(total_bayar) as total_nilai 
              FROM transaksi_istifafakha 
              WHERE DATE(waktu) = '$today'";

if ($role !== 'admin') {
    $sql_stats .= " AND id_petugas = '$id_user_login'";
}

$query_transaksi = mysqli_query($koneksi_istifafakha, $sql_stats);
$data_transaksi = mysqli_fetch_array($query_transaksi);

$query_menu = mysqli_query($koneksi_istifafakha, "
    SELECT m.nama_menu, SUM(d.qty) AS terjual
    FROM detail_transaksi_istifafakha d
    JOIN menu_istifafakha m ON d.id_menu = m.id_menu 
    JOIN transaksi_istifafakha t ON d.id_transaksi = t.id_transaksi
    WHERE DATE(t.waktu) = '$today'
    " . ($role !== 'admin' ? " AND t.id_petugas = '$id_user_login'" : "") . "
    GROUP BY m.id_menu, m.nama_menu
    ORDER BY terjual DESC
    LIMIT 5;
");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan - <?= strtoupper(htmlspecialchars($role)); ?></title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&display=swap');

        :root {
            --emerald-dark: #022e1a;
            --emerald-main: #065f37;
            --emerald-light: #50c878;
            --white: #ffffff;
            --bg-soft: #f4f7f6;
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

        /* --- SIDEBAR--- */
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
            transition: 0.3s;
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

        /* --- MAIN CONTENT --- */
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
            color: var(--emerald-main);
            font-size: 28px;
            font-weight: 700;
        }

        .role-badge {
            background: var(--emerald-light);
            color: var(--emerald-dark);
            padding: 8px 20px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-top: 20px;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 24px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.03);
            border: 1px solid rgba(0, 0, 0, 0.02);
        }

        .card h4 {
            color: #888;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 15px;
        }

        .card .value {
            font-size: 28px;
            font-weight: 700;
            color: var(--emerald-dark);
        }

        .table-container {
            background: white;
            padding: 30px;
            border-radius: 24px;
            margin-top: 30px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.03);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            text-align: left;
            padding: 15px;
            background: #f8faf9;
            color: var(--emerald-main);
            font-weight: 700;
            font-size: 14px;
            border-bottom: 2px solid #eee;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
            color: #444;
            font-size: 15px;
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
        <div style="padding-left: 10px; margin-bottom: 30px;">
            <h2 style="font-weight: 700; letter-spacing: -1px; color: white;">KANTIN<span
                    style="color: var(--emerald-light);">RFID</span></h2>
        </div>

        <div class="user-info">
            <div class="user-name"><?= htmlspecialchars($_SESSION['nama_petugas_istifafakha']); ?></div>
            <div class="user-role"><?= strtoupper(htmlspecialchars($role)); ?></div>
        </div>

        <ul>
            <li><a href="dashboard_istifafakha.php">Dashboard Home</a></li>

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
                <li><a href="laporan_penjualan_istifafakha.php" class="active">Riwayat Saya</a></li>
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
                <h1>Laporan Penjualan 📈</h1>
                <p style="color: #666; margin-top: 5px;">Data transaksi hari ini:
                    <?= date('d F Y', strtotime($today)); ?>
                </p>
            </div>
            <div class="role-badge">
                <?= strtoupper(htmlspecialchars($role)); ?> ACCESS
            </div>
        </div>

        <div class="grid-container">
            <div class="card">
                <h4>Total Transaksi</h4>
                <div class="value"><?= number_format($data_transaksi['total_transaksi']); ?> <span
                        style="font-size:14px; font-weight:400; color:#aaa;">x</span></div>
            </div>

            <div class="card">
                <h4><?= ($role == 'admin') ? 'Total Pendapatan' : 'Penjualan Saya'; ?></h4>
                <div class="value" style="color: var(--emerald-main);">
                    Rp <?= number_format($data_transaksi['total_nilai'] ?? 0, 0, ',', '.'); ?>
                </div>
            </div>
        </div>

        <div class="table-container">
            <h3 style="color: var(--emerald-dark);">🔥 Menu Terlaris Hari Ini</h3>
            <table>
                <thead>
                    <tr>
                        <th width="80">No</th>
                        <th>Nama Menu</th>
                        <th style="text-align: center;">Terjual</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    if (mysqli_num_rows($query_menu) > 0) {
                        while ($row = mysqli_fetch_array($query_menu)) {
                            echo "<tr>";
                            echo "<td>$no</td>";
                            echo "<td><b>" . htmlspecialchars($row['nama_menu']) . "</b></td>";
                            echo "<td style='text-align: center;'>" . $row['terjual'] . " porsi</td>";
                            echo "</tr>";
                            $no++;
                        }
                    } else {
                        echo "<tr><td colspan='3' style='text-align: center; color: #ccc; padding: 40px;'>Belum ada transaksi hari ini</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Tabel Riwayat Transaksi dengan Nama Siswa -->
        <div class="table-container" style="margin-top: 30px;">
            <h3 style="color: var(--emerald-dark);">📋 Riwayat Transaksi Hari Ini</h3>
            <table>
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Total</th>
                        <th>Waktu</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query_transaksi_detail = mysqli_query($koneksi_istifafakha, "
                        SELECT t.*, s.nama_siswa, s.kelas, k.nama_kantin
                        FROM transaksi_istifafakha t
                        LEFT JOIN siswa_istifafakha s ON t.rfid_uid = s.rfid_uid
                        LEFT JOIN kantin_istifafakha k ON t.id_kantin = k.id_kantin
                        WHERE DATE(t.waktu) = '$today'
                        " . ($role !== 'admin' ? " AND t.id_petugas = '$id_user_login'" : "") . "
                        ORDER BY t.waktu DESC
                    ");
                    
                    $no2 = 1;
                    if (mysqli_num_rows($query_transaksi_detail) > 0) {
                        while ($row2 = mysqli_fetch_array($query_transaksi_detail)) {
                            echo "<tr>";
                            echo "<td>$no2</td>";
                            echo "<td><b>" . htmlspecialchars($row2['nama_siswa'] ?? 'Tidak dikenal') . "</b></td>";
                            echo "<td>" . htmlspecialchars($row2['kelas'] ?? '-') . "</td>";
                            echo "<td>Rp " . number_format($row2['total_bayar'], 0, ',', '.') . "</td>";
                            echo "<td>" . date('H:i', strtotime($row2['waktu'])) . "</td>";
                            echo "<td><a href='detail_transaksi_istifafakha.php?id=" . $row2['id_transaksi'] . "' style='background: var(--emerald-main); color: white; padding: 5px 12px; border-radius: 6px; text-decoration: none; font-size: 12px;'>Detail</a></td>";
                            echo "</tr>";
                            $no2++;
                        }
                    } else {
                        echo "<tr><td colspan='6' style='text-align: center; color: #ccc; padding: 40px;'>Belum ada transaksi hari ini</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>

</body>

</html>
<?php ob_end_flush(); ?>