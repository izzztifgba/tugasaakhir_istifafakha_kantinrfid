<?php
session_start();
include "koneksi_istifafakha.php";

if(!isset($_SESSION['loginPetugas_istifafakha'])){
    header("location:loginPetugas_istifafakha.php");
}

// Ambil data kantin untuk dropdown
$kantin = mysqli_query($koneksi_istifafakha, "SELECT * FROM kantin_istifafakha ORDER BY nama_kantin");

if(isset($_POST['simpan'])){
    $nama_istifafakha  = $_POST['nama'];
    $harga_istifafakha = $_POST['harga'];
    $stok_istifafakha  = $_POST['stok'];
    $id_kantin        = $_POST['id_kantin'];

    mysqli_query($koneksi_istifafakha,"INSERT INTO menu_istifafakha
    (nama_menu,harga,stok,id_kantin)
    VALUES('$nama_istifafakha','$harga_istifafakha','$stok_istifafakha','$id_kantin')");

    header("location:menu_istifafakha.php");
}
?>

<h2>Tambah Menu</h2>

<form method="POST">
Pilih Kantin <br>
<select name="id_kantin" required>
    <option value="">-- Pilih Kantin --</option>
    <?php while($k = mysqli_fetch_array($kantin)) { ?>
    <option value="<?= $k['id_kantin']; ?>"><?= $k['nama_kantin']; ?> (<?= $k['pemilik']; ?>)</option>
    <?php } ?>
</select>
<br><br>

Nama Menu <br>
<input type="text" name="nama" required><br><br>

Harga <br>
<input type="number" name="harga" required><br><br>

Stok <br>
<input type="number" name="stok" required><br><br>

<button name="simpan">Simpan</button>
</form>
