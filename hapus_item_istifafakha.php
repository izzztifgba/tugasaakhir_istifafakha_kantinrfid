<?php
session_start();

if (isset($_GET['id'])) {
    $id_istifafakha = $_GET['id'];

    if (isset($_SESSION['keranjang_istifafakha'][$id_istifafakha])) {
        unset($_SESSION['keranjang_istifafakha'][$id_istifafakha]);

        $_SESSION['keranjang_istifafakha'] = array_values($_SESSION['keranjang_istifafakha']);
    }
}

header("location:kasir_istifafakha.php");
exit();
?>