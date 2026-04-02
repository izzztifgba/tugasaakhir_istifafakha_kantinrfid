<?php
session_start();
include "koneksi_istifafakha.php";

// 1. CEK KERANJANG
if (!isset($_SESSION['keranjang_istifafakha']) || count($_SESSION['keranjang_istifafakha']) == 0) {
    die("Keranjang kosong!");
}

// 2. AMBIL DATA DARI FORM & SESSION
$uid_istifafakha = trim($_POST['rfid_uid']);
$keranjang_temp_istifafakha = $_SESSION['keranjang_istifafakha'];
$total_istifafakha = 0;

// Hitung total bayar
foreach ($keranjang_temp_istifafakha as $k_istifafakha) {
    $total_istifafakha += $k_istifafakha['subtotal'];
}

// SQL INJECTION (agar karakter-karakter khusus tidak bisa memanipulasi query database)
$uid_istifafakha = mysqli_real_escape_string($koneksi_istifafakha, trim($_POST['rfid_uid']));

// 3. CARI DATA SISWA
$query_siswa_istifafakha = mysqli_query($koneksi_istifafakha, "SELECT * FROM siswa_istifafakha WHERE rfid_uid='$uid_istifafakha'");
$siswa_istifafakha = mysqli_fetch_array($query_siswa_istifafakha);

if (!$siswa_istifafakha) {
    die("Kartu tidak terdaftar!");
}

// 4. CEK SALDO
if ($siswa_istifafakha['saldo'] < $total_istifafakha) {
    die("Saldo tidak cukup!");
}

// 5. PROSES POTONG SALDO
mysqli_query($koneksi_istifafakha, "UPDATE siswa_istifafakha SET saldo = saldo - $total_istifafakha WHERE rfid_uid='$uid_istifafakha'");

// 6. SIMPAN HEADER TRANSAKSI
$id_petugas_istifafakha = $_SESSION['id_petugas_istifafakha'] ?? 0;
mysqli_query($koneksi_istifafakha, "INSERT INTO transaksi_istifafakha (rfid_uid, id_petugas, total_bayar, waktu) VALUES ('$uid_istifafakha', '$id_petugas_istifafakha', '$total_istifafakha', NOW())");
$id_transaksi_istifafakha = mysqli_insert_id($koneksi_istifafakha);

// 7. LOOPING UNTUK DETAIL & STOK
$jumlah_item_istifafakha = count($keranjang_temp_istifafakha);

for ($i_istifafakha = 0; $i_istifafakha < $jumlah_item_istifafakha; $i_istifafakha++) {
    $item_istifafakha = $keranjang_temp_istifafakha[$i_istifafakha];
    $id_m_istifafakha = $item_istifafakha['id_menu'];
    $qty_istifafakha  = $item_istifafakha['qty'];
    $sub_istifafakha  = $item_istifafakha['subtotal'];

    // Simpan detail
    mysqli_query($koneksi_istifafakha, "INSERT INTO detail_transaksi_istifafakha (id_transaksi, id_menu, qty, subtotal) VALUES ('$id_transaksi_istifafakha', '$k_istifafakha[id_menu]', '$k_istifafakha[qty]', '$k_istifafakha[subtotal]')");
    
    // Kurangi stok
    mysqli_query($koneksi_istifafakha, "UPDATE menu_istifafakha SET stok = stok - $qty_istifafakha WHERE id_menu='$id_m_istifafakha'");
}

// 8. SELESAI
unset($_SESSION['keranjang_istifafakha']);
?>

<h2>✅ Transaksi Berhasil!</h2>
<p>Siswa: <?= $siswa_istifafakha['nama_siswa']; ?></p>
<p>Total: Rp <?= number_format($total_istifafakha); ?></p>
<a href="kasir_istifafakha.php">Kembali</a>
