<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if(!isset($_SESSION['loginPetugas_istifafakha'])){
    header("location:loginPetugas_istifafakha.php");
    exit; 
}
?>

<h1>Dashboard Kasir Kantin</h1>
<p>Halo, <?= $_SESSION['nama_petugas_istifafakha']; ?></p>

<hr>

<a href="menu_istifafakha.php">🍱 Data Menu</a><br><br>
<a href="kasir_istifafakha.php">💰 Transaksi Kasir</a><br><br>
<a href="logout_istifafakha.php">🚪 Logout</a>

<script>
    window.addEventListener('pageshow', function (event) {
        if (event.persisted || (typeof window.performance != 'undefined' && window.performance.navigation.type === 2)) {
            window.location.reload();
        }
    });
</script>