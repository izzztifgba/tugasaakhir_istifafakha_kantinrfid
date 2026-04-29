<?php
session_start();
include "koneksi_istifafakha.php";

// Proteksi: Hanya Admin yang boleh tambah petugas
if (!isset($_SESSION['loginPetugas_istifafakha']) || $_SESSION['role_istifafakha'] !== 'admin') {
    echo "<script>alert('Akses ditolak! Hanya Admin yang bisa menambah petugas.'); window.location='dashboard_istifafakha.php';</script>";
    exit;
}

if (isset($_POST['simpan_petugas'])) {
    $nama_petugas = mysqli_real_escape_string($koneksi_istifafakha, $_POST['nama_petugas']);
    $username     = mysqli_real_escape_string($koneksi_istifafakha, $_POST['username']);
    $role         = $_POST['role'];
    
    // ENKRIPSI MD5: Mengubah password teks biasa menjadi hash 32 karakter
    $password_md5 = md5($_POST['password']); 

    // Query sesuai struktur tabel: nama_petugas, username, password, role
    $sql = "INSERT INTO petugas_istifafakha (nama_petugas, username, password, role) 
            VALUES ('$nama_petugas', '$username', '$password_md5', '$role')";
    
    $query = mysqli_query($koneksi_istifafakha, $sql);

    if ($query) {
        echo "<script>alert('Petugas berhasil ditambahkan!'); window.location='datapetugas_istifafakha.php';</script>";
    } else {
        echo "Error: " . mysqli_error($koneksi_istifafakha);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Petugas - Kantin Istifa</title>
</head>
<body>

    <h2>➕ TAMBAH PETUGAS BARU</h2>
    <a href="datapetugas_istifafakha.php">⬅ Kembali ke Data Petugas</a>
    <hr>

    <form method="POST">
        <table border="0" cellpadding="10">
            <tr>
                <td>Nama Lengkap</td>
                <td>: <input type="text" name="nama_petugas" required placeholder="Nama Lengkap Petugas"></td>
            </tr>
            <tr>
                <td>Username</td>
                <td>: <input type="text" name="username" required placeholder="Username"></td>
            </tr>
            <tr>
                <td>Password</td>
                <td>: <input type="password" name="password" required placeholder="Password"></td>
            </tr>
            <tr>
                <td>Role / Hak Akses</td>
                <td>: 
                    <select name="role" required>
                        <option value="petugas">Petugas</option>
                        <option value="admin">Admin</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <button type="submit" name="simpan_petugas" style="padding: 10px 20px; background: #2196F3; color: white; border: none; border-radius: 5px; cursor: pointer;">
                        SIMPAN PETUGAS
                    </button>
                </td>
            </tr>
        </table>
    </form>

</body>
</html>