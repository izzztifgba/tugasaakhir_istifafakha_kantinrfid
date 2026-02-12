<?php
session_start();
include "koneksi_istifafakha.php";

if(!isset($_SESSION['loginPetugas_istifafakha'])){
    header("location:loginPetugas_istifafakha.php");
}
?>

<h1>Data Menu Kantin</h1>
<a href="dashboard_istifafakha.php">⬅ Kembali</a>
<br><br>

<a href="tambah_menu_istifafakha.php">+ Tambah Menu</a>

<table border="1" cellpadding="10">
<tr>
    <th>No</th>
    <th>Nama Makanan</th>
    <th>Harga</th>
    <th>Stok</th>
    <th>Aksi</th>
</tr>

<?php
$no_istifafakha=1;
$data_istifafakha = mysqli_query($koneksi_istifafakha,"SELECT * FROM menu_istifafakha");

while($d_istifafakha = mysqli_fetch_array($data_istifafakha)){
?>

<tr>
    <td><?= $no_istifafakha++; ?></td>
    <td><?= $d_istifafakha['nama_makanan']; ?></td>
    <td>Rp <?= number_format($d_istifafakha['harga']); ?></td>
    <td><?= $d_istifafakha['stok']; ?></td>
    <td>
        <a href="edit_menu_istifafakha.php?id=<?= $d_istifafakha['id_menu']; ?>">Edit</a> |
        <a href="hapus_menu_istifafakha.php?id=<?= $d_istifafakha['id_menu']; ?>">Hapus</a>
    </td>
</tr>

<?php } ?>
</table>
