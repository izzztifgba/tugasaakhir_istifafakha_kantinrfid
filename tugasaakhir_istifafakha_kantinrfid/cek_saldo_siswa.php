<?php
ob_start();
session_start();
include "koneksi_istifafakha.php";

// Validasi session login (Konsisten dengan Dashboard & Kasir)
if (!isset($_SESSION['loginPetugas_istifafakha']) || $_SESSION['loginPetugas_istifafakha'] !== true) {
    ob_end_clean();
    header("Location: loginPetugas_istifafakha.php");
    exit;
}

$role = $_SESSION['role_istifafakha'] ?? 'petugas';
$nama = $_SESSION['nama_petugas_istifafakha'] ?? 'User';

$siswa_hasil = null;
$pesan_error = '';

// Proses saat kartu di-tap
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['rfid_uid'])) {
    $rfid_uid = mysqli_real_escape_string($koneksi_istifafakha, $_POST['rfid_uid']);
    $query_cari = mysqli_query($koneksi_istifafakha, "SELECT * FROM siswa_istifafakha WHERE rfid_uid = '$rfid_uid'");

    if (mysqli_num_rows($query_cari) > 0) {
        $siswa_hasil = mysqli_fetch_array($query_cari);
    } else {
        $pesan_error = "Kartu dengan UID $rfid_uid tidak terdaftar!";
    }
}

// Ambil data untuk tabel daftar siswa
$query_siswa = mysqli_query($koneksi_istifafakha, "SELECT id_siswa, nama_siswa, kelas, saldo, rfid_uid FROM siswa_istifafakha ORDER BY nama_siswa ASC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Saldo - <?= strtoupper(htmlspecialchars($role)); ?></title>
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

        /* --- SIDEBAR KONSISTEN --- */
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
            margin-bottom: 30px;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }

        .input-tap {
            width: 100%;
            padding: 20px;
            border: 2px dashed var(--emerald-light);
            border-radius: 15px;
            text-align: center;
            font-size: 18px;
            background: #f9fdfb;
            color: var(--emerald-dark);
            outline: none;
            transition: 0.3s;
        }

        .input-tap:focus {
            border-style: solid;
            background: white;
            box-shadow: 0 0 15px rgba(80, 200, 120, 0.2);
        }

        /* Hasil Cek */
        .result-box {
            display: flex;
            align-items: center;
            gap: 20px;
            background: linear-gradient(135deg, var(--emerald-main), var(--emerald-dark));
            color: white;
            padding: 25px;
            border-radius: 20px;
            margin-top: 20px;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .result-info h2 {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .result-info p {
            opacity: 0.8;
            font-size: 14px;
        }

        .result-saldo {
            margin-left: auto;
            text-align: right;
        }

        .result-saldo h1 {
            font-size: 32px;
            color: var(--emerald-light);
        }

        /* Tabel */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table th {
            text-align: left;
            padding: 15px;
            background: #f8fbf9;
            color: #888;
            font-size: 12px;
            text-transform: uppercase;
        }

        table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
            color: #333;
        }

        table tr:hover {
            background: #fdfdfd;
        }

        .badge-uid {
            background: #eee;
            padding: 4px 10px;
            border-radius: 6px;
            font-family: monospace;
            font-size: 12px;
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
            <h2 style="font-weight: 700; letter-spacing: -1px;">KANTIN<span
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
                <li><a href="laporan_pendapatan_kantin.php">Pendapatan Seluruh</a></li>
                <li><a href="tarik_tunai_istifafakha.php">Penarikan Uang</a></li>
            <?php endif; ?>

            <?php if ($role == 'kantin' || $role == 'petugas'): ?>
                <h3>Operasional</h3>
                <li><a href="kasir_istifafakha.php">Kasir Penjualan</a></li>
                <li><a href="cek_saldo_siswa.php" class="active">Cek Saldo Siswa</a></li>
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
            <div>
                <h1 style="color: var(--emerald-main); font-size: 28px;">Cek Saldo Siswa</h1>
                <p style="color: #666;">Tap kartu pada reader untuk melihat informasi saldo.</p>
            </div>
            <div style="text-align: right;">
                <div
                    style="background: var(--emerald-light); color: var(--emerald-dark); padding: 8px 20px; border-radius: 50px; font-size: 12px; font-weight: 700;">
                    <?= strtoupper($role); ?> ACCESS
                </div>
            </div>
        </div>

        <div class="card">
            <h4 style="margin-bottom: 15px; color: #888; font-size: 13px; text-transform: uppercase;">Scan Area</h4>
            <form method="POST" id="rfidForm">
                <input type="text" name="rfid_uid" id="rfidInput" class="input-tap"
                    placeholder="Tap kartu RFID di sini..." autofocus autocomplete="off">
            </form>

            <?php if ($pesan_error): ?>
                <p style="color: #ff4757; text-align: center; margin-top: 15px; font-weight: 600;"><?= $pesan_error; ?></p>
            <?php endif; ?>

            <?php if ($siswa_hasil): ?>
                <div class="result-box">
                    <div
                        style="background: rgba(255,255,255,0.1); width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                        👤
                    </div>
                    <div class="result-info">
                        <h2><?= htmlspecialchars($siswa_hasil['nama_siswa']); ?></h2>
                        <p>Kelas: <?= htmlspecialchars($siswa_hasil['kelas'] ?? '-'); ?> | UID:
                            <?= htmlspecialchars($siswa_hasil['rfid_uid']); ?>
                        </p>
                    </div>
                    <div class="result-saldo">
                        <p style="font-size: 12px; opacity: 0.8;">Sisa Saldo:</p>
                        <h1>Rp <?= number_format($siswa_hasil['saldo'], 0, ',', '.'); ?></h1>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="card">
            <h4 style="margin-bottom: 20px; color: #888; font-size: 13px; text-transform: uppercase;">Daftar Seluruh
                Siswa</h4>
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>RFID UID</th>
                            <th style="text-align: right;">Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        while ($row = mysqli_fetch_array($query_siswa)) {
                            echo "<tr>";
                            echo "<td>$no</td>";
                            echo "<td><b>" . htmlspecialchars($row['nama_siswa']) . "</b></td>";
                            echo "<td>" . htmlspecialchars($row['kelas'] ?? '-') . "</td>";
                            echo "<td><span class='badge-uid'>" . htmlspecialchars($row['rfid_uid']) . "</span></td>";
                            echo "<td style='text-align: right; font-weight: 700; color: var(--emerald-main);'>Rp " . number_format($row['saldo'], 0, ',', '.') . "</td>";
                            echo "</tr>";
                            $no++;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script>
        // Pastikan input selalu fokus meskipun user mengklik tempat lain
        const rfidInput = document.getElementById('rfidInput');

        document.addEventListener('click', () => {
            rfidInput.focus();
        });

        // Submit otomatis saat RFID reader mengirim data (biasanya diakhiri Enter)
        rfidInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                document.getElementById('rfidForm').submit();
            }
        });
    </script>

</body>

</html>
<?php ob_end_flush(); ?>