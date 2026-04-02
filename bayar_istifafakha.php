<?php
session_start();
include "koneksi_istifafakha.php";
if(!isset($_SESSION['keranjang_istifafakha']) || count($_SESSION['keranjang_istifafakha']) == 0){
    header("location:kasir_istifafakha.php");
    exit;
}
?>
<h2>Pembayaran RFID</h2>
<form method="POST" action="bayar_rfid_istifafakha.php">
    <input type="text" name="rfid_uid" placeholder="Tempel kartu..." autofocus required>
    <button type="submit">KONFIRMASI BAYAR</button>
</form>
<?php
$total_istifafakha = 0;
foreach($_SESSION['keranjang_istifafakha'] as $k_istifafakha){
    $total_istifafakha += $k_istifafakha['subtotal'];
}
echo "Total yang akan dibayar: Rp " . number_format($total_istifafakha, 0, ',', '.');
?>
