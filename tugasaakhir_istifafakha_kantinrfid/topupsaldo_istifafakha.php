<?php
ob_start();
session_start();
include "koneksi_istifafakha.php";

// 1. Proteksi Login
if (!isset($_SESSION['loginPetugas_istifafakha'])) {
    header("location:loginPetugas_istifafakha.php");
    exit;
}

// Ambil data session dengan aman
$role = isset($_SESSION['role_istifafakha']) ? $_SESSION['role_istifafakha'] : '';
$nama_user = isset($_SESSION['nama_petugas_istifafakha']) ? $_SESSION['nama_petugas_istifafakha'] : 'User';

// --- LOGIKA PROSES (TAMBAH SISWA) ---
if (isset($_POST['tambah_siswa'])) {
    $rfid = mysqli_real_escape_string($koneksi_istifafakha, $_POST['rfid_uid']);
    $nama = mysqli_real_escape_string($koneksi_istifafakha, $_POST['nama_siswa']);
    $kelas = mysqli_real_escape_string($koneksi_istifafakha, $_POST['kelas']);
    $saldo = $_POST['saldo'];

    $query = mysqli_query($koneksi_istifafakha, "INSERT INTO siswa_istifafakha (rfid_uid, nama_siswa, kelas, saldo) VALUES ('$rfid', '$nama', '$kelas', '$saldo')");

    if ($query) {
        echo "<script>alert('Siswa Berhasil Didaftarkan!'); window.location='datasiswa_istifafakha.php';</script>";
    }
}

// --- LOGIKA HAPUS ---
if (isset($_GET['hapus'])) {
    $uid = $_GET['hapus'];
    mysqli_query($koneksi_istifafakha, "DELETE FROM siswa_istifafakha WHERE rfid_uid='$uid'");
    header("location:datasiswa_istifafakha.php");
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Siswa - Kantin RFID</title>
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
            background-color: var(--bg-soft);
            display: flex;
            /* Penting agar sidebar dan main bersebelahan */
            min-height: 100vh;
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
        }

        nav h2 {
            font-weight: 700;
            letter-spacing: -1px;
            color: white;
            padding-left: 10px;
            margin-bottom: 30px;
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
        }

        .logout-btn:hover {
            background: #ff6b6b !important;
            color: white !important;
        }

        .logout-btn:hover {
            background: #ff6b6b;
            color: white;
        }

        /* --- MAIN CONTENT --- */
        main {
            margin-left: 280px;
            /* Lebar yang sama dengan nav */
            flex: 1;
            padding: 40px;
        }

        .header-title {
            margin-bottom: 30px;
        }

        .header-title h1 {
            color: var(--emerald-main);
            font-size: 24px;
            font-weight: 700;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .input-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .input-group label {
            font-size: 12px;
            font-weight: 600;
            color: #666;
        }

        input {
            padding: 12px;
            border-radius: 10px;
            border: 1.5px solid #eee;
            outline: none;
        }

        .btn-submit {
            grid-column: span 2;
            background: var(--emerald-main);
            color: white;
            padding: 15px;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-submit:hover {
            background: var(--emerald-light);
            color: var(--emerald-dark);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 15px;
            overflow: hidden;
        }

        table thead {
            background: var(--emerald-main);
            color: white;
        }

        table th,
        table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .btn-topup {
            background: #e3fcef;
            color: #065f37;
            padding: 6px 12px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 700;
        }

        .btn-hapus {
            color: #e74c3c;
            text-decoration: none;
            font-weight: 600;
            margin-left: 10px;
        }
    </style>
</head>

<body>

    <nav>
        <div style="padding-left: 10px; margin-bottom: 30px;">
            <h2 style="font-weight: 700; letter-spacing: -1px;">KANTIN<span
                    style="color: var(--emerald-light);">RFID</span></h2>
        </div>

        <ul>
            <li><a href="dashboard_istifafakha.php">Dashboard Home</a></li>

            <?php if ($role == 'admin'): ?>
                <h3>Administrator</h3>
                <li><a href="datapetugas_istifafakha.php">Data Kantin & Petugas</a></li>
                <li><a href="datasiswa_istifafakha.php" class="active">Registrasi & Top-Up</a></li>
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
        <div class="header-title">
            <h1>Registrasi Siswa & Saldo</h1>
            <p style="color: #888;">Halo, <b><?= $nama_user ?></b>. Kelola data kartu siswa di sini.</p>
        </div>

        <div class="card">
            <h3 style="margin-bottom: 20px;">Tambah Siswa Baru</h3>
            <form method="POST" class="form-grid">
                <div class="input-group">
                    <label>UID Kartu RFID</label>
                    <input type="text" name="rfid_uid" placeholder="Tap kartu..." required>
                </div>
                <div class="input-group">
                    <label>Nama Lengkap Siswa</label>
                    <input type="text" name="nama_siswa" placeholder="Nama siswa..." required>
                </div>
                <div class="input-group">
                    <label>Kelas</label>
                    <input type="text" name="kelas" placeholder="Cth: XII PPLG 1" required>
                </div>
                <div class="input-group">
                    <label>Saldo Awal (Rp)</label>
                    <input type="number" name="saldo" value="0" required>
                </div>
                <button type="submit" name="tambah_siswa" class="btn-submit">DAFTARKAN SISWA</button>
            </form>
        </div>

        <div class="card" style="padding: 0; overflow: hidden;">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>UID RFID</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Saldo</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $sql = mysqli_query($koneksi_istifafakha, "SELECT * FROM siswa_istifafakha ORDER BY nama_siswa ASC");
                    while ($s = mysqli_fetch_array($sql)) { ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><code><?= $s['rfid_uid']; ?></code></td>
                            <td><strong><?= htmlspecialchars($s['nama_siswa']); ?></strong></td>
                            <td><?= htmlspecialchars($s['kelas']); ?></td>
                            <td style="color: var(--emerald-main); font-weight: 700;">
                                Rp <?= number_format($s['saldo'], 0, ',', '.'); ?>
                            </td>
                            <td>
                                <a href="topup_istifafakha.php?uid=<?= $s['rfid_uid']; ?>" class="btn-topup">+ TOP UP</a>
                                <?php if ($role == 'admin'): ?>
                                    <a href="?hapus=<?= $s['rfid_uid']; ?>" class="btn-hapus"
                                        onclick="return confirm('Hapus data?')">Hapus</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </main>

</body>

</html>
<?php ob_end_flush(); ?>