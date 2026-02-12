<?php
session_start();
include "koneksi_istifafakha.php";

if (!isset($_SESSION['keranjang']) || count($_SESSION['keranjang']) == 0) {
    die("Keranjang kosong!");
}

$uid_istifafakha = trim($_POST['rfid_uid']);
$id_petugas_istifafakha = isset($_SESSION['id_petugas_istifafakha']) ? $_SESSION['id_petugas_istifafakha'] : 0;
$total_istifafakha = 0;
$keranjang_temp_istifafakha = $_SESSION['keranjang_istifafakha'];

foreach ($_SESSION['keranjang_istifafakha'] as $k_istifafakha) {
    $total_istifafakha += $k_istifafakha['subtotal'];
}

error_log("DEBUG: UID yang dikirim = '" . $uid_istifafakha . "'");

$query_siswa_istifafakha = mysqli_query($koneksi_istifafakha, "SELECT * FROM siswa_istifafakha WHERE rfid_uid='$uid_istifafakha'");
$siswa_istifafakha = mysqli_fetch_array($query_siswa_istifafakha);

if (!$siswa_istifafakha) {
    $debug_query_istifafakha = mysqli_query($koneksi_istifafakha, "SELECT rfid_uid, nama_siswa FROM siswa_istifafakha LIMIT 5");
    $debug_list_istifafakha = "";
    while ($d_istifafakha = mysqli_fetch_array($debug_query_istifafakha)) {
        $debug_list_istifafakha .= "UID: '" . $d_istifafakha['rfid_uid'] . "' (" . $d_istifafakha['nama_siswa'] . "), ";
    }
    die("Kartu tidak terdaftar! UID yang dicari: '" . $uid_istifafakha . "'. Contoh UID yang ada: " . $debug_list_istifafakha);
}

if ($siswa_istifafakha['saldo'] < $total_istifafakha) {
    die("Saldo tidak cukup!");
}

$saldo_sebelumnya_istifafakha = $siswa_istifafakha['saldo'];

$update_saldo_istifafakha = mysqli_query($koneksi_istifafakha, "UPDATE siswa_istifafakha SET saldo = saldo - $total_istifafakha WHERE rfid_uid='$uid_istifafakha'");

if (!$update_saldo_istifafakha) {
    die("Error update saldo: " . mysqli_error($koneksi_istifafakha));
}

$insert_transaksi_istifafakha = mysqli_query($koneksi_istifafakha, "INSERT INTO transaksi_istifafakha (rfid_uid, id_petugas, total_bayar, tanggal) VALUES ('$uid_istifafakha', '$id_petugas_istifafakha', '$total_istifafakha', NOW())");

if (!$insert_transaksi_istifafakha) {
    die("Error insert transaksi: " . mysqli_error($koneksi_istifafakha));
}

$id_transaksi_istifafakha = mysqli_insert_id($koneksi_istifafakha);

foreach ($keranjang_temp_istifafakha as $k_istifafakha) {
    mysqli_query($koneksi_istifafakha, "INSERT INTO detail_transaksi_istifafakha (id_transaksi, id_menu, qty, subtotal) VALUES ('$id_transaksi_istifafakha', '$k_istifafakha[id_menu]', '$k_istifafakha[qty]', '$k_istifafakha[subtotal]')");
    
    mysqli_query($koneksi_istifafakha, "UPDATE menu_istifafakha SET stok = stok - $k_istifafakha[qty] WHERE id_menu='$k_istifafakha[id_menu]'");
}

$query_cek_lagi_istifafakha = mysqli_query($koneksi_istifafakha, "SELECT saldo FROM siswa_istifafakha WHERE rfid_uid='$uid_istifafakha'");
$data_baru_istifafakha = mysqli_fetch_array($query_cek_lagi_istifafakha);

unset($_SESSION['keranjang_istifafakha']);

?>
<h2>✅ Transaksi Berhasil!</h2>
<hr>
<h3>Detail Pembayaran</h3>
<table border="1" cellpadding="10">
    <tr>
        <th>Menu</th>
        <th>Qty</th>
        <th>Harga Satuan</th>
        <th>Subtotal</th>
    </tr>
<?php
foreach ($keranjang_temp_istifafakha as $k_istifafakha) {
    echo "<tr>";
    echo "<td>" . $k_istifafakha['nama'] . "</td>";
    echo "<td>" . $k_istifafakha['qty'] . "</td>";
    echo "<td>Rp " . number_format($k_istifafakha['harga'], 0, ',', '.') . "</td>";
    echo "<td>Rp " . number_format($k_istifafakha['subtotal'], 0, ',', '.') . "</td>";
    echo "</tr>";
}
?>
    <tr style="font-weight: bold; background-color: #ffeb3b;">
        <td colspan="3">TOTAL PEMBAYARAN</td>
        <td>Rp <?= number_format($total_istifafakha, 0, ',', '.'); ?></td>
    </tr>
</table>

<hr>
<h3>Info Siswa</h3>
<p><strong>Nama Siswa:</strong> <?= $siswa_istifafakha['nama_siswa']; ?></p>
<p><strong>Saldo Sebelumnya:</strong> Rp <?= number_format($saldo_sebelumnya_istifafakha, 0, ',', '.'); ?></p>
<p><strong>Saldo Sekarang:</strong> Rp <?= number_format($data_baru_istifafakha['saldo'], 0, ',', '.'); ?></p>
<p><strong>ID Transaksi:</strong> <?= $id_transaksi_istifafakha; ?></p>

<hr>
<a href='kasir_istifafakha.php'><button>Kembali ke Kasir</button></a>