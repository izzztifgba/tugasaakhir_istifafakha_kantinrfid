<?php
session_start();
include "koneksi_istifafakha.php";

$id_istifafakha = $_GET['id'];
$data_istifafakha = mysqli_fetch_array(mysqli_query($koneksi_istifafakha,"SELECT * FROM menu_istifafakha WHERE id_menu='$id_istifafakha'"));

// Ambil data kantin untuk dropdown
$kantin = mysqli_query($koneksi_istifafakha, "SELECT * FROM kantin_istifafakha ORDER BY nama_kantin");

if(isset($_POST['update'])){
    $nama_istifafakha  = $_POST['nama'];
    $harga_istifafakha = $_POST['harga'];
    $stok_istifafakha  = $_POST['stok'];
    $id_kantin        = $_POST['id_kantin'];

    mysqli_query($koneksi_istifafakha,"UPDATE menu_istifafakha SET
        nama_menu='$nama_istifafakha',
        harga='$harga_istifafakha',
        stok='$stok_istifafakha',
        id_kantin='$id_kantin'
        WHERE id_menu='$id_istifafakha'");

    header("location:menu_istifafakha.php");
}
?>

<h2>Edit Menu</h2>

<form method="POST">
Pilih Kantin <br>
<select name="id_kantin" required>
    <option value="">-- Pilih Kantin --</option>
    <?php while($k = mysqli_fetch_array($kantin)) { ?>
    <option value="<?= $k['id_kantin']; ?>" <?= ($k['id_kantin'] == $data_istifafakha['id_kantin']) ? 'selected' : ''; ?>>
        <?= $k['nama_kantin']; ?> (<?= $k['pemilik']; ?>)
    </option>
    <?php } ?>
</select>
<br><br>

Nama Menu <br>
<input type="text" name="nama" value="<?= $data_istifafakha['nama_menu']; ?>"><br><br>

Harga <br>
<input type="number" name="harga" value="<?= $data_istifafakha['harga']; ?>"><br><br>

Stok <br>
<input type="number" name="stok" value="<?= $data_istifafakha['stok']; ?>"><br><br>

<button name="update">Update</button>
</form>
