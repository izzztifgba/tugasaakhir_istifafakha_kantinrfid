<?php
ob_start();
session_start();
include 'koneksi_istifafakha.php';

// 1. Proteksi Halaman & Ambil Session
if (!isset($_SESSION['loginPetugas_istifafakha'])) {
    header("Location: loginPetugas_istifafakha.php");
    exit;
}

// 2. Definisi Variabel untuk Sidebar
$role = $_SESSION['role_istifafakha'];
$nama_user = $_SESSION['nama_petugas_istifafakha'];

// --- LOGIKA PROSES TAMBAH SISWA ---
if (isset($_POST['simpan_siswa'])) {
    $uid_istifafakha = mysqli_real_escape_string($koneksi_istifafakha, $_POST['rfid_uid']);
    $nama_istifafakha = mysqli_real_escape_string($koneksi_istifafakha, $_POST['nama_siswa']);
    $kelas_istifafakha = mysqli_real_escape_string($koneksi_istifafakha, $_POST['kelas']);
    $saldo_istifafakha = (int) $_POST['saldo'];

    $cek_uid = mysqli_query($koneksi_istifafakha, "SELECT * FROM siswa_istifafakha WHERE rfid_uid='$uid_istifafakha'");
    if (mysqli_num_rows($cek_uid) > 0) {
        echo "<script>alert('Gagal! UID RFID sudah digunakan.');</script>";
    } else {
        $query = "INSERT INTO siswa_istifafakha (rfid_uid, nama_siswa, kelas, saldo) 
                  VALUES ('$uid_istifafakha', '$nama_istifafakha', '$kelas_istifafakha', '$saldo_istifafakha')";
        if (mysqli_query($koneksi_istifafakha, $query)) {
            echo "<script>alert('Siswa Berhasil Didaftarkan!'); window.location='datasiswa_istifafakha.php';</script>";
        }
    }
}

// --- LOGIKA PROSES TOP UP (POP-UP) ---
if (isset($_POST['proses_topup'])) {
    $uid_topup = mysqli_real_escape_string($koneksi_istifafakha, $_POST['uid_topup']);
    $nominal = (int) $_POST['nominal_topup'];

    if ($nominal > 0) {
        $update = mysqli_query($koneksi_istifafakha, "UPDATE siswa_istifafakha SET saldo = saldo + $nominal WHERE rfid_uid = '$uid_topup'");
        if ($update) {
            echo "<script>alert('Top Up Berhasil!'); window.location='datasiswa_istifafakha.php';</script>";
        }
    }
}

// --- LOGIKA HAPUS ---
if (isset($_GET['hapus'])) {
    $uid_hapus = $_GET['hapus'];
    mysqli_query($koneksi_istifafakha, "DELETE FROM siswa_istifafakha WHERE rfid_uid='$uid_hapus'");
    header("Location: datasiswa_istifafakha.php");
}

$query_tampil = "SELECT * FROM siswa_istifafakha ORDER BY nama_siswa ASC";
$result = mysqli_query($koneksi_istifafakha, $query_tampil);
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

        /* --- SIDEBAR KONSISTEN --- */
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
            margin-left: 280px;
            padding: 40px;
        }

        h1 {
            color: var(--emerald-dark);
            margin-bottom: 10px;
        }

        .sub-header {
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
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
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .input-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .input-group label {
            font-size: 13px;
            font-weight: 600;
            color: var(--emerald-main);
        }

        input {
            padding: 12px;
            border: 1px solid #e0e8e5;
            border-radius: 10px;
            background: #f9fbfb;
            outline: none;
            transition: 0.3s;
        }

        input:focus {
            border-color: var(--emerald-light);
            background: white;
        }

        .btn-simpan {
            grid-column: span 2;
            background: var(--emerald-main);
            color: white;
            padding: 14px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 700;
            margin-top: 10px;
            transition: 0.3s;
        }

        .btn-simpan:hover {
            background: var(--emerald-dark);
            transform: translateY(-2px);
        }

        /* Table Styling */
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
            letter-spacing: 1px;
            border-bottom: 2px solid #eee;
        }

        table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
            color: #333;
        }

        code {
            background: #f0f0f0;
            padding: 4px 8px;
            border-radius: 5px;
            font-family: monospace;
            color: #e83e8c;
        }

        .saldo-text {
            color: var(--emerald-main);
            font-weight: 700;
        }

        /* --- STYLES UNTUK POP UP (MODAL) --- */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 25px;
            border-radius: 20px;
            width: 400px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .close {
            float: right;
            font-size: 24px;
            cursor: pointer;
            color: #aaa;
        }

        .btn-konfirmasi {
            background: var(--emerald-light);
            color: var(--emerald-dark);
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            margin-top: 15px;
            cursor: pointer;
        }
    </style>
</head>

<body>

    <nav>
        <h2>KANTIN<span style="color: var(--emerald-light);">RFID</span></h2>

        <div class="user-info">
            <div class="user-name"><?= htmlspecialchars($_SESSION['nama_petugas_istifafakha']); ?></div>
            <div class="user-role"><?= strtoupper(htmlspecialchars($role)); ?></div>
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
        <h1>Registrasi Siswa</h1>
        <p class="sub-header">Login sebagai: <b><?= htmlspecialchars($nama_user) ?></b> (<?= ucfirst($role) ?>)</p>

        <div class="card">
            <form method="POST" class="form-grid">
                <div class="input-group">
                    <label>UID RFID (Tempelkan Kartu)</label>
                    <input type="text" name="rfid_uid" placeholder="Contoh: 12A34B56" required>
                </div>
                <div class="input-group">
                    <label>Nama Lengkap Siswa</label>
                    <input type="text" name="nama_siswa" placeholder="Masukkan nama siswa" required>
                </div>
                <div class="input-group">
                    <label>Kelas</label>
                    <input type="text" name="kelas" placeholder="Contoh: XII PPLG 1" required>
                </div>
                <div class="input-group">
                    <label>Saldo Awal (Rp)</label>
                    <input type="number" name="saldo" value="0">
                </div>
                <button type="submit" name="simpan_siswa" class="btn-simpan">DAFTARKAN SISWA BARU</button>
            </form>
        </div>

        <div class="card" style="padding:0; overflow:hidden;">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>UID RFID</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Saldo</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $n = 1;
                    while ($row = mysqli_fetch_array($result)) {
                        ?>
                        <tr>
                            <td><?= $n++ ?></td>
                            <td><code><?= $row['rfid_uid'] ?></code></td>
                            <td><b><?= htmlspecialchars($row['nama_siswa']) ?></b></td>
                            <td><?= htmlspecialchars($row['kelas']) ?></td>
                            <td class="saldo-text">Rp <?= number_format($row['saldo'], 0, ',', '.') ?></td>
                            <td style="text-align: center;">
                                <a href="javascript:void(0)"
                                    onclick="openTopUp('<?= $row['rfid_uid'] ?>', '<?= htmlspecialchars($row['nama_siswa']) ?>')"
                                    style="color: var(--emerald-light); text-decoration:none; font-weight:700; margin-right: 10px;">+
                                    TOPUP</a>

                                <?php if ($role == 'admin'): ?>
                                    <a href="?hapus=<?= $row['rfid_uid'] ?>" onclick="return confirm('Hapus data siswa ini?')"
                                        style="color: #ff6b6b; text-decoration:none;">Hapus</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </main>

    <div id="modalTopUp" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeTopUp()">&times;</span>
            <h3 style="margin-bottom: 10px; color: var(--emerald-dark);">Top Up Saldo</h3>
            <p id="labelNamaSiswa" style="font-size: 14px; color: #666; margin-bottom: 20px;"></p>

            <form method="POST">
                <input type="hidden" name="uid_topup" id="inputUidTopUp">
                <div class="input-group">
                    <label>Nominal Top Up (Rp)</label>
                    <input type="number" name="nominal_topup" placeholder="Masukkan jumlah uang" required autofocus>
                </div>
                <button type="submit" name="proses_topup" class="btn-konfirmasi">KONFIRMASI TOP UP</button>
            </form>
        </div>
    </div>

    <script>
        function openTopUp(uid, nama) {
            document.getElementById('modalTopUp').style.display = 'block';
            document.getElementById('inputUidTopUp').value = uid;
            document.getElementById('labelNamaSiswa').innerText = "Siswa: " + nama + " (" + uid + ")";
        }

        function closeTopUp() {
            document.getElementById('modalTopUp').style.display = 'none';
        }

        // Close modal if user clicks outside of it
        window.onclick = function (event) {
            let modal = document.getElementById('modalTopUp');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>

</body>

</html>
<?php ob_end_flush(); ?>