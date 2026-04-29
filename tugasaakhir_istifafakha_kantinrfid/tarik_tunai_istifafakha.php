<?php
ob_start();
session_start();
include "koneksi_istifafakha.php";

if (!isset($_SESSION['loginPetugas_istifafakha']) || $_SESSION['role_istifafakha'] !== 'admin') {
    header("location:dashboard_istifafakha.php");
    exit;
}

$role = $_SESSION['role_istifafakha'];
$nama_user = $_SESSION['nama_petugas_istifafakha'];
$id_user_session = $_SESSION['id_user_istifafakha'];

if (isset($_POST['proses_tarik'])) {
    $nominal = (int) $_POST['nominal'];
    $keterangan = mysqli_real_escape_string($koneksi_istifafakha, $_POST['keterangan']);
    $id_kantin = $_POST['id_kantin'];

    $insert = mysqli_query($koneksi_istifafakha, "INSERT INTO tarik_tunai_istifafakha (id_user, id_kantin, nominal_tarik, keterangan, tanggal_tarik) VALUES ('$id_user_session', '$id_kantin', '$nominal', '$keterangan', NOW())");
    if ($insert) {
        echo "<script>alert('Berhasil!'); window.location='tarik_tunai_istifafakha.php';</script>";
    }
}

$query_kantin = mysqli_query($koneksi_istifafakha, "SELECT * FROM kantin_istifafakha");
$query_history = mysqli_query($koneksi_istifafakha, "SELECT t.*, u.nama_petugas FROM tarik_tunai_istifafakha t JOIN user_istifafakha u ON t.id_user = u.id_user ORDER BY t.tanggal_tarik DESC LIMIT 10");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penarikan Uang - Kantin RFID</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&display=swap');

        :root {
            --emerald-dark: #022e1a;
            --emerald-main: #065f37;
            --emerald-light: #50c878;
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
            height: 100vh;
            z-index: 100;
        }

        nav h2 {
            font-weight: 700;
            margin-bottom: 30px;
            padding-left: 10px;
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
            transition: 0.3s;
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
        }

        .logout-btn:hover {
            background: #ff6b6b !important;
            color: white !important;
        }

        main {
            flex: 1;
            margin-left: 280px;
            padding: 40px;
        }

        /* --- CONTAINER CARD --- */
        .card {
            background: white;
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
            border: 1px solid rgba(0, 0, 0, 0.02);
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        input,
        select {
            padding: 12px 15px;
            border: 1px solid #e0e8e5;
            border-radius: 10px;
            background: #f9fbfb;
            width: 100%;
            outline: none;
            font-size: 14px;
            color: #333;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            cursor: pointer;
            /* Panah kustom emerald */
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23065f37' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: calc(100% - 15px) center;
            padding-right: 40px;
        }

        input:focus,
        select:focus {
            border-color: var(--emerald-light);
            background-color: white;
            box-shadow: 0 0 0 4px rgba(80, 200, 120, 0.15);
        }

        input:hover,
        select:hover {
            border-color: var(--emerald-main);
            background-color: #fff;
        }

        label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: var(--emerald-main);
            margin-bottom: 8px;
            margin-left: 5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-submit {
            background: var(--emerald-main);
            color: white;
            padding: 14px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 700;
            grid-column: span 2;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th {
            background: #f8fbf9;
            color: #888;
            padding: 15px;
            font-size: 12px;
            text-transform: uppercase;
            text-align: left;
        }

        table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
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
                <li><a href="laporan_pendapatan_kantin.php">Pendapatan Seluruh</a></li>
                <li><a href="tarik_tunai_istifafakha.php" class="active">Penarikan Uang</a></li>
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
        <h1 style="color: var(--emerald-dark);">Penarikan Uang</h1>
        <div class="card">
            <form method="POST" class="form-grid">
                <select name="id_kantin" required>
                    <option value="">Pilih Kantin</option>
                    <?php while ($k = mysqli_fetch_array($query_kantin))
                        echo "<option value='" . $k['id_kantin'] . "'>" . $k['nama_kantin'] . "</option>"; ?>
                </select>
                <input type="number" name="nominal" placeholder="Nominal Tarik" required>
                <input type="text" name="keterangan" placeholder="Keterangan (Contoh: Tarik Hasil Mingguan)"
                    style="grid-column: span 2;" required>
                <button type="submit" name="proses_tarik" class="btn-submit">PROSES PENARIKAN</button>
            </form>
        </div>
        <div class="card" style="padding: 0; overflow: hidden;">
            <div style="padding: 20px; font-weight: 700; border-bottom: 1px solid #eee;">History Penarikan Terakhir
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Nominal</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_array($query_history)): ?>
                        <tr>
                            <td><?= date('d M Y H:i', strtotime($row['tanggal_tarik'])); ?></td>
                            <td style="color: #e74c3c; font-weight: 700;">Rp
                                <?= number_format($row['nominal_tarik'], 0, ',', '.'); ?>
                            </td>
                            <td><?= $row['keterangan']; ?><br><small>Oleh: <?= $row['nama_petugas']; ?></small></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>

</html>
<?php ob_end_flush(); ?>