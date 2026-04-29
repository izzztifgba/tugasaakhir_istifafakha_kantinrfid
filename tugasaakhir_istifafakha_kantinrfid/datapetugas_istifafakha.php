<?php
ob_start();
session_start();
include "koneksi_istifafakha.php";

// Proteksi: Hanya Admin yang bisa mengelola petugas
if (!isset($_SESSION['loginPetugas_istifafakha']) || $_SESSION['role_istifafakha'] !== 'admin') {
    echo "<script>alert('Akses Ditolak!'); window.location='dashboard_istifafakha.php';</script>";
    exit;
}

$role = $_SESSION['role_istifafakha'];
$nama_session = $_SESSION['nama_petugas_istifafakha'];

// 1. PROSES TAMBAH
if (isset($_POST['tambah'])) {
    $user = mysqli_real_escape_string($koneksi_istifafakha, $_POST['username']);
    $no_telp = mysqli_real_escape_string($koneksi_istifafakha, $_POST['no_telp']);
    $nama = mysqli_real_escape_string($koneksi_istifafakha, $_POST['nama_petugas']);
    $role_input = $_POST['role'];
    $pass = md5($_POST['password']);

    $query_tambah = mysqli_query($koneksi_istifafakha, "INSERT INTO user_istifafakha (username, no_telp, password, nama_petugas, role) VALUES ('$user', '$no_telp', '$pass', '$nama', '$role_input')");
    if ($query_tambah) {
        echo "<script>alert('Berhasil Tambah!'); window.location='datapetugas_istifafakha.php';</script>";
    }
}

// 2. PROSES UPDATE (DARI POP-UP)
if (isset($_POST['update_petugas'])) {
    $id = $_POST['id_user'];
    $nama = mysqli_real_escape_string($koneksi_istifafakha, $_POST['nama_petugas']);
    $user = mysqli_real_escape_string($koneksi_istifafakha, $_POST['username']);
    $no_telp = mysqli_real_escape_string($koneksi_istifafakha, $_POST['no_telp']);
    $role_input = $_POST['role'];

    if (!empty($_POST['password'])) {
        $pass = md5($_POST['password']);
        $sql = "UPDATE user_istifafakha SET username='$user', no_telp='$no_telp', nama_petugas='$nama', role='$role_input', password='$pass' WHERE id_user='$id'";
    } else {
        $sql = "UPDATE user_istifafakha SET username='$user', no_telp='$no_telp', nama_petugas='$nama', role='$role_input' WHERE id_user='$id'";
    }

    if (mysqli_query($koneksi_istifafakha, $sql)) {
        echo "<script>alert('Data Diupdate!'); window.location='datapetugas_istifafakha.php';</script>";
    }
}

// 3. PROSES HAPUS
if (isset($_GET['hapus'])) {
    $id_hapus = $_GET['hapus'];
    mysqli_query($koneksi_istifafakha, "DELETE FROM user_istifafakha WHERE id_user='$id_hapus'");
    header("Location: datapetugas_istifafakha.php");
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Petugas - Kantin RFID</title>
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
            gap: 15px;
        }

        input,
        select {
            padding: 12px;
            border: 1px solid #e0e8e5;
            border-radius: 10px;
            background: #f9fbfb;
            width: 100%;
            outline: none;
        }

        .btn-tambah {
            background: var(--emerald-main);
            color: white;
            padding: 12px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 700;
            grid-column: span 2;
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
            font-size: 11px;
            text-transform: uppercase;
        }

        table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        .badge {
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .badge-admin {
            background: #e0f2fe;
            color: #0369a1;
        }

        .badge-petugas {
            background: #f0fdf4;
            color: #15803d;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background: white;
            margin: 5% auto;
            padding: 30px;
            border-radius: 20px;
            width: 500px;
            animation: slideIn 0.3s;
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
    </style>
</head>

<body>

    <nav>
        <h2>KANTIN<span style="color: var(--emerald-light);">RFID</span></h2>
        <div class="user-info">
            <div class="user-name"><?= htmlspecialchars($nama_session); ?></div>
            <div class="user-role"><?= strtoupper($role); ?></div>
        </div>
        <ul>
            <li><a href="dashboard_istifafakha.php">Dashboard Home</a></li>
            <h3>Administrator</h3>
            <li><a href="datapetugas_istifafakha.php" class="active">Data Kantin & Petugas</a></li>
            <li><a href="datasiswa_istifafakha.php">Registrasi & Top-Up</a></li>
            <li><a href="cek_saldo_kantin.php">Cek Saldo Kantin</a></li>
            <li><a href="laporan_pendapatan_kantin.php">Pendapatan Seluruh</a></li>
            <li><a href="tarik_tunai_istifafakha.php">Penarikan Uang</a></li>
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
        <h1 style="color: var(--emerald-dark); margin-bottom: 20px;">Manajemen Petugas</h1>

        <div class="card">
            <h4 style="margin-bottom: 15px; color: var(--emerald-main);">Tambah Petugas Baru</h4>
            <form method="POST" class="form-grid">
                <input type="text" name="username" placeholder="Username" required>
                <input type="text" name="no_telp" placeholder="No. Telepon" required>
                <input type="text" name="nama_petugas" placeholder="Nama Lengkap" required>
                <select name="role">
                    <option value="petugas">Petugas Kantin</option>
                    <option value="admin">Administrator</option>
                </select>
                <input type="password" name="password" placeholder="Password" style="grid-column: span 2;" required>
                <button type="submit" name="tambah" class="btn-tambah">SIMPAN PETUGAS</button>
            </form>
        </div>

        <div class="card" style="padding: 0; overflow: hidden;">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Username</th>
                        <th>Nama Petugas</th>
                        <th>Role</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $sql = mysqli_query($koneksi_istifafakha, "SELECT * FROM user_istifafakha");
                    while ($d = mysqli_fetch_array($sql)) {
                        ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><strong><?= $d['username']; ?></strong><br><small><?= $d['no_telp']; ?></small></td>
                            <td><?= $d['nama_petugas']; ?></td>
                            <td><span
                                    class="badge <?= ($d['role'] == 'admin') ? 'badge-admin' : 'badge-petugas'; ?>"><?= $d['role']; ?></span>
                            </td>
                            <td>
                                <a href="javascript:void(0)"
                                    onclick="openEditModal('<?= $d['id_user'] ?>', '<?= $d['username'] ?>', '<?= $d['nama_petugas'] ?>', '<?= $d['no_telp'] ?>', '<?= $d['role'] ?>')"
                                    style="color: var(--emerald-main); text-decoration: none; font-weight: 700; margin-right: 10px;">Edit</a>
                                <a href="?hapus=<?= $d['id_user']; ?>" onclick="return confirm('Hapus?')"
                                    style="color: #ff6b6b; text-decoration: none;">Hapus</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </main>

    <div id="modalEdit" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h3 style="margin-bottom: 20px; color: var(--emerald-dark);">Edit Data Petugas</h3>
            <form method="POST" class="form-grid">
                <input type="hidden" name="id_user" id="edit_id">
                <div style="grid-column: span 1;">
                    <label style="font-size: 12px; color: #888;">Username</label>
                    <input type="text" name="username" id="edit_username" required>
                </div>
                <div style="grid-column: span 1;">
                    <label style="font-size: 12px; color: #888;">No. Telp</label>
                    <input type="text" name="no_telp" id="edit_telp" required>
                </div>
                <div style="grid-column: span 2;">
                    <label style="font-size: 12px; color: #888;">Nama Lengkap</label>
                    <input type="text" name="nama_petugas" id="edit_nama" required>
                </div>
                <div style="grid-column: span 2;">
                    <label style="font-size: 12px; color: #888;">Role</label>
                    <select name="role" id="edit_role">
                        <option value="petugas">Petugas</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div style="grid-column: span 2;">
                    <label style="font-size: 12px; color: #888;">Password (Kosongkan jika tidak ganti)</label>
                    <input type="password" name="password" placeholder="Password Baru">
                </div>
                <button type="submit" name="update_petugas" class="btn-tambah">UPDATE DATA</button>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(id, user, nama, telp, role) {
            document.getElementById('modalEdit').style.display = 'block';
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_username').value = user;
            document.getElementById('edit_nama').value = nama;
            document.getElementById('edit_telp').value = telp;
            document.getElementById('edit_role').value = role;
        }

        function closeModal() {
            document.getElementById('modalEdit').style.display = 'none';
        }

        window.onclick = function (event) {
            if (event.target == document.getElementById('modalEdit')) {
                closeModal();
            }
        }
    </script>
</body>

</html>