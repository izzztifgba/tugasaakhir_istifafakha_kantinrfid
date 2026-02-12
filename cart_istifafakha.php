<?php
session_start();
include "koneksi_istifafakha.php";

$id_istifafakha  = $_POST['id_menu'];
$qty_istifafakha = $_POST['qty'];

$data_istifafakha = mysqli_fetch_array(mysqli_query($koneksi_istifafakha,"SELECT * FROM menu_istifafakha WHERE id_menu='$id_istifafakha'"));

$item_istifafakha = [
    "id_menu" => $id_istifafakha,
    "nama" => $data_istifafakha['nama_makanan'],
    "harga" => $data_istifafakha['harga'],
    "qty" => $qty_istifafakha,
    "subtotal" => $qty_istifafakha * $data_istifafakha['harga']
];

$_SESSION['keranjang_istifafakha'][] = $item_istifafakha;

header("location:kasir_istifafakha.php");
