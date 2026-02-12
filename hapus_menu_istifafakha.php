<?php
include "koneksi_istifafakha.php";

$id_istifafakha = $_GET['id'];

mysqli_query($koneksi_istifafakha,"DELETE FROM menu_istifafakha WHERE id_menu='$id_istifafakha'");
header("location:menu_istifafakha.php");
?>
