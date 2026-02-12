<?php
session_start();
include "koneksi_istifafakha.php";

if(!isset($_SESSION['loginPetugas_istifafakha'])){
    header("location:loginPetugas_istifafakha.php");
}

if(isset($_POST['simpan'])){
    $nama_istifafakha  = $_POST['nama'];
    $harga_istifafakha = $_POST['harga'];
    $stok_istifafakha  = $_POST['stok'];

    mysqli_query($koneksi_istifafakha,"INSERT INTO menu_istifafakha
    (nama_makanan,harga,stok)
    VALUES('$nama_istifafakha','$harga_istifafakha','$stok_istifafakha')");

    header("location:menu_istifafakha.php");
}
?>

<h2>Tambah Menu</h2>

<form method="POST">
Nama Makanan <br>
<input type="text" name="nama" required><br><br>

Harga <br>
<input type="number" name="harga" required><br><br>

Stok <br>
<input type="number" name="stok" required><br><br>

<button name="simpan">Simpan</button>
</form>
