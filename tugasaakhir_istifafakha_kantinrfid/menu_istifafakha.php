<?php
session_start();
include "koneksi_istifafakha.php";

if(!isset($_SESSION['loginPetugas_istifafakha'])){
    header("location:loginPetugas_istifafakha.php");
}

$role = $_SESSION['role_istifafakha'] ?? 'petugas';
$id_kantin_user = $_SESSION['id_kantin'] ?? null;
?>

<h1>Data Menu Kantin</h1>
<a href="dashboard_istifafakha.php">⬅ Kembali</a>
<br><br>

<a href="tambah_menu_istifafakha.php">+ Tambah Menu</a>

<table border="1" cellpadding="10">
<tr>
    <th>No</th>
    <th>Kantin</th>
    <th>Nama Menu</th>
    <th>Harga</th>
    <th>Stok</th>
    <th>Aksi</th>
</tr>

<?php
$no_istifafakha=1;

// Jika admin, tampilkan semua menu. Jika petugas, tampilkan menu berdasarkan kantinnya
if ($role === 'admin') {
    $data_istifafakha = mysqli_query($koneksi_istifafakha,"SELECT m.*, k.nama_kantin FROM menu_istifafakha m LEFT JOIN kantin_istifafakha k ON m.id_kantin = k.id_kantin ORDER BY m.id_menu");
} else {
    // Petugas lihat menu di kantinnya ATAU menu yang belum punya kantin
    if ($id_kantin_user) {
        $data_istifafakha = mysqli_query($koneksi_istifafakha,"SELECT m.*, k.nama_kantin FROM menu_istifafakha m LEFT JOIN kantin_istifafakha k ON m.id_kantin = k.id_kantin WHERE m.id_kantin = '$id_kantin_user' OR m.id_kantin IS NULL ORDER BY m.id_menu");
    } else {
        $data_istifafakha = mysqli_query($koneksi_istifafakha,"SELECT m.*, k.nama_kantin FROM menu_istifafakha m LEFT JOIN kantin_istifafakha k ON m.id_kantin = k.id_kantin ORDER BY m.id_menu");
    }
}

while($d_istifafakha = mysqli_fetch_array($data_istifafakha)){
?>

<tr>
    <td><?= $no_istifafakha++; ?></td>
    <td><?= $d_istifafakha['nama_kantin'] ?? '-'; ?></td>
    <td><?= $d_istifafakha['nama_menu']; ?></td>
    <td>Rp <?= number_format($d_istifafakha['harga']); ?></td>
    <td><?= $d_istifafakha['stok']; ?></td>
    <td>
        <a href="edit_menu_istifafakha.php?id=<?= $d_istifafakha['id_menu']; ?>">Edit</a> |
        <a href="hapus_menu_istifafakha.php?id=<?= $d_istifafakha['id_menu']; ?>">Hapus</a>
    </td>
</tr>

<?php } ?>
</table>
