<?php
session_start();
include "koneksi_istifafakha.php";

// Proteksi login petugas
if (!isset($_SESSION['loginPetugas_istifafakha'])) {
    header("location:loginPetugas_istifafakha.php");
    exit;
}

$role = $_SESSION['role_istifafakha'] ?? 'petugas';

if (isset($_POST['simpan_siswa'])) {
    $uid_istifafakha = mysqli_real_escape_string($koneksi_istifafakha, $_POST['rfid_uid']);
    $nama_istifafakha = mysqli_real_escape_string($koneksi_istifafakha, $_POST['nama_siswa']);
    $kelas_istifafakha = mysqli_real_escape_string($koneksi_istifafakha, $_POST['kelas']);
    $saldo_istifafakha = $_POST['saldo'];

    // Cek apakah UID sudah terdaftar sebelumnya
    $cek_uid = mysqli_query($koneksi_istifafakha, "SELECT * FROM siswa_istifafakha WHERE rfid_uid='$uid_istifafakha'");

    if (mysqli_num_rows($cek_uid) > 0) {
        echo "<script>alert('Gagal! UID RFID sudah digunakan siswa lain.'); window.history.back();</script>";
    } else {
        $query = "INSERT INTO siswa_istifafakha (rfid_uid, nama_siswa, kelas, saldo, created_at) 
                  VALUES ('$uid_istifafakha', '$nama_istifafakha', '$kelas_istifafakha', '$saldo_istifafakha', NOW())";

        if (mysqli_query($koneksi_istifafakha, $query)) {
            echo "<script>alert('Data Siswa Berhasil Ditambahkan!'); window.location='datasiswa_istifafakha.php';</script>";
        } else {
            echo "Error: " . mysqli_error($koneksi_istifafakha);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Siswa - Kantin RFID</title>
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

        /* --- MAIN CONTENT --- */
        main {
            flex: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .card {
            background: white;
            padding: 40px;
            border-radius: 24px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 500px;
        }

        .card h2 {
            color: var(--emerald-main);
            margin-bottom: 10px;
            text-align: center;
        }

        .card p {
            color: #888;
            text-align: center;
            margin-bottom: 30px;
            font-size: 14px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .input-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .input-group label {
            font-size: 12px;
            font-weight: 700;
            color: var(--emerald-main);
            text-transform: uppercase;
        }

        input {
            padding: 15px;
            border-radius: 12px;
            border: 2px solid #f0f0f0;
            background: #f9f9f9;
            outline: none;
            transition: 0.3s;
            font-size: 15px;
        }

        input:focus {
            border-color: var(--emerald-light);
            background: white;
            box-shadow: 0 0 10px rgba(80, 200, 120, 0.1);
        }

        .btn-submit {
            padding: 18px;
            border: none;
            border-radius: 12px;
            background: var(--emerald-main);
            color: white;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
        }

        .btn-submit:hover {
            background: var(--emerald-light);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(6, 95, 55, 0.2);
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #888;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
        }

        .back-link:hover {
            color: var(--emerald-main);
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
        <div class="card">
            <h2>Registrasi Siswa</h2>
            <p>Daftarkan kartu RFID baru ke dalam sistem</p>

            <form method="POST">
                <div class="input-group">
                    <label>UID RFID (Scan Kartu)</label>
                    <input type="text" name="rfid_uid" placeholder="Tempel kartu ke reader..." required autofocus>
                </div>

                <div class="input-group">
                    <label>Nama Lengkap Siswa</label>
                    <input type="text" name="nama_siswa" placeholder="Masukkan nama lengkap" required>
                </div>

                <div class="input-group">
                    <label>Kelas</label>
                    <input type="text" name="kelas" placeholder="Contoh: XII PPLG A" required>
                </div>

                <div class="input-group">
                    <label>Saldo Awal (Rp)</label>
                    <input type="number" name="saldo" value="0" min="0" required>
                </div>

                <button type="submit" name="simpan_siswa" class="btn-submit">DAFTARKAN SEKARANG</button>
            </form>

            <a href="datasiswa_istifafakha.php" class="back-link">⬅ Kembali ke Daftar Siswa</a>
        </div>
    </main>

</body>

</html>