<?php
session_start();

// Menghapus hanya session keranjang saja
if (isset($_SESSION['keranjang_istifafakha'])) {
    unset($_SESSION['keranjang_istifafakha']);
}

// Setelah dihapus, balikkan user ke halaman kasir
header("location:kasir_istifafakha.php");
exit();
?>