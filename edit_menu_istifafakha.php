<?php
session_start();
include "koneksi_istifafakha.php";

$id_istifafakha = $_GET['id'];
$data_istifafakha = mysqli_fetch_array(mysqli_query($koneksi_istifafakha,"SELECT * FROM menu_istifafakha WHERE id_menu='$id_istifafakha'"));

if(isset($_POST['update'])){
    $nama_istifafakha  = $_POST['nama'];
    $harga_istifafakha = $_POST['harga'];
    $stok_istifafakha  = $_POST['stok'];

    mysqli_query($koneksi_istifafakha,"UPDATE menu_istifafakha SET
        nama_makanan='$nama_istifafakha',
        harga='$harga_istifafakha',
        stok='$stok_istifafakha'
        WHERE id_menu='$id_istifafakha'");

    header("location:menu_istifafakha.php");
}
?>

<h2>Edit Menu</h2>

<form method="POST">
Nama Makanan <br>
<input type="text" name="nama" value="<?= $data_istifafakha['nama_makanan']; ?>"><br><br>

Harga <br>
<input type="number" name="harga" value="<?= $data_istifafakha['harga']; ?>"><br><br>

Stok <br>
<input type="number" name="stok" value="<?= $data_istifafakha['stok']; ?>"><br><br>

<button name="update">Update</button>
</form>
