<?php
ob_start();
session_start();
include "koneksi_istifafakha.php";

// 1. Proteksi: Hanya Admin Pusat
if (!isset($_SESSION['loginPetugas_istifafakha']) || $_SESSION['role_istifafakha'] !== 'admin') {
    header("location:dashboard_istifafakha.php");
    exit;
}

// 2. Definisi variabel untuk Sidebar
$role = $_SESSION['role_istifafakha'];
$nama_user = $_SESSION['nama_petugas_istifafakha'];

// 3. Query SQL Pendapatan
$sql = "SELECT 
            k.id_kantin, 
            k.nama_kantin, 
            k.pemilik,
            (SELECT COALESCE(SUM(total_bayar), 0) FROM transaksi_istifafakha WHERE id_kantin = k.id_kantin) AS total_masuk,
            (SELECT COALESCE(SUM(nominal_tarik), 0) FROM tarik_tunai_istifafakha WHERE id_kantin = k.id_kantin) AS total_ditarik
        FROM kantin_istifafakha k";

$query = mysqli_query($koneksi_istifafakha, $sql);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendapatan Kantin - Kantin RFID</title>
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

        nav {
            width: 280px;
            background: linear-gradient(180deg, var(--emerald-dark) 0%, var(--emerald-main) 100%);
            color: white;
            padding: 30px 20px;
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
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

        nav ul li a:hover,
        nav ul li a.active {
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
            text-align: left;
        }

        main {
            flex: 1;
            margin-left: 280px;
            padding: 40px;
        }

        .card {
            background: white;
            padding: 0;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th {
            background: #f8fbf9;
            color: #888;
            padding: 15px;
            text-align: left;
            font-size: 12px;
            text-transform: uppercase;
        }

        table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        .saldo-box {
            background: rgba(6, 95, 55, 0.05);
            padding: 5px 10px;
            border-radius: 6px;
            font-weight: 700;
            color: var(--emerald-main);
        }
    </style>
</head>

<body>
    <nav>
        <h2>KANTIN<span style="color: var(--emerald-light);">RFID</span></h2>
        <div class="user-info">
            <div class="user-name"><?= htmlspecialchars($nama_user); ?></div>
            <div class="user-role"><?= strtoupper(htmlspecialchars($role)); ?></div>
        </div>
        <ul>
            <li><a href="dashboard_istifafakha.php">Dashboard Home</a></li>
            <?php if ($role == 'admin'): ?>
                <h3>Administrator</h3>
                <li><a href="datapetugas_istifafakha.php">Data Kantin & Petugas</a></li>
                <li><a href="datasiswa_istifafakha.php">Registrasi & Top-Up</a></li>
                <li><a href="cek_saldo_kantin.php">Cek Saldo Kantin</a></li>
                <li><a href="laporan_pendapatan_kantin.php" class="active">Pendapatan Seluruh</a></li>
                <li><a href="tarik_tunai_istifafakha.php">Penarikan Uang</a></li>
            <?php endif; ?>
        </ul>
        <ul style="margin-top: auto;">
            <li><a href="logout_istifafakha.php" class="logout-btn" onclick="return confirm('Logout?')">Logout
                    Sistem</a></li>
        </ul>
    </nav>

    <main>
        <h1 style="color: var(--emerald-dark); margin-bottom: 10px;">Laporan Pendapatan</h1>
        <p style="color: #666; margin-bottom: 30px; font-size: 14px;">Akumulasi pendapatan seluruh gerai kantin.</p>
        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Kantin</th>
                        <th>Pemilik</th>
                        <th>Total Masuk</th>
                        <th>Total Ditarik</th>
                        <th>Saldo di Admin</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $n = 1;
                    $grand_total = 0;
                    while ($d = mysqli_fetch_array($query)) {
                        $saldo_sekarang = $d['total_masuk'] - $d['total_ditarik'];
                        $grand_total += $saldo_sekarang;
                        ?>
                        <tr>
                            <td><?= $n++; ?></td>
                            <td><b><?= $d['nama_kantin']; ?></b></td>
                            <td><?= $d['pemilik']; ?></td>
                            <td>Rp <?= number_format($d['total_masuk'], 0, ',', '.'); ?></td>
                            <td style="color: #e74c3c;">Rp <?= number_format($d['total_ditarik'], 0, ',', '.'); ?></td>
                            <td><span class="saldo-box">Rp <?= number_format($saldo_sekarang, 0, ',', '.'); ?></span></td>
                        </tr>
                    <?php } ?>
                </tbody>
                <tfoot style="background: #f9f9f9; font-weight: 700;">
                    <tr>
                        <td colspan="5" align="right" style="padding: 25px;">TOTAL KESELURUHAN DI ADMIN:</td>
                        <td style="color: var(--emerald-main); padding: 25px;">Rp
                            <?= number_format($grand_total, 0, ',', '.'); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </main>
</body>

</html>
<?php ob_end_flush(); ?>