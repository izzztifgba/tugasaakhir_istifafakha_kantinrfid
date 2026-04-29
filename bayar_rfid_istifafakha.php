<?php
session_start();
include "koneksi_istifafakha.php";

header('Content-Type: application/json');
$response = [];

// 1. Validasi Keranjang & Session Petugas
if (!isset($_SESSION['keranjang_istifafakha']) || count($_SESSION['keranjang_istifafakha']) == 0) {
    echo json_encode(['status' => 'error', 'pesan' => 'Keranjang masih kosong!']);
    exit;
}

$id_petugas = $_SESSION['id_user_istifafakha'] ?? 0;
if ($id_petugas == 0) {
    echo json_encode(['status' => 'error', 'pesan' => 'Sesi petugas habis. Silakan login ulang.']);
    exit;
}

// 2. Tangkap Data
$uid = mysqli_real_escape_string($koneksi_istifafakha, trim($_POST['rfid_uid'] ?? ''));
$total_bayar = (int) ($_POST['total_bayar'] ?? 0);

// 3. Cek Siswa
$query_siswa = mysqli_query($koneksi_istifafakha, "SELECT * FROM siswa_istifafakha WHERE rfid_uid='$uid'");
$siswa = mysqli_fetch_array($query_siswa);

if (!$siswa) {
    echo json_encode(['status' => 'error', 'pesan' => 'Kartu RFID tidak terdaftar!']);
    exit;
}

if ($siswa['saldo'] < $total_bayar) {
    echo json_encode(['status' => 'error', 'pesan' => 'Saldo kurang! Sisa: Rp ' . number_format($siswa['saldo'], 0, ',', '.')]);
    exit;
}

// 4. Proses Database (Transaction)
mysqli_begin_transaction($koneksi_istifafakha);

try {
    // A. Potong Saldo Siswa
    mysqli_query($koneksi_istifafakha, "UPDATE siswa_istifafakha SET saldo = saldo - $total_bayar WHERE rfid_uid='$uid'");

    // D. Simpan Header Transaksi dengan id_kantin
    $q_kantin = mysqli_query($koneksi_istifafakha, "SELECT id_kantin FROM user_istifafakha WHERE id_user = '$id_petugas'");
    $d_kantin = mysqli_fetch_array($q_kantin);
    $id_k = $d_kantin['id_kantin'] ?? null;
    
    mysqli_query($koneksi_istifafakha, "INSERT INTO transaksi_istifafakha (rfid_uid, id_petugas, total_bayar, id_kantin, waktu) VALUES ('$uid', '$id_petugas', '$total_bayar', '$id_k', NOW())");
    $id_transaksi = mysqli_insert_id($koneksi_istifafakha);

    // E. Simpan Detail & Update Stok
    foreach ($_SESSION['keranjang_istifafakha'] as $id_menu => $item) {
        $qty = $item['qty'];
        $sub = $item['subtotal'];
        
        mysqli_query($koneksi_istifafakha, "INSERT INTO detail_transaksi_istifafakha (id_transaksi, id_menu, qty, subtotal) VALUES ('$id_transaksi', '$id_menu', '$qty', '$sub')");
        mysqli_query($koneksi_istifafakha, "UPDATE menu_istifafakha SET stok = stok - $qty WHERE id_menu = '$id_menu'");
    }

    // F. Update Saldo Kantin (sudah benar karena id_kantin sudah ada di transaksi)
    if ($id_k) {
        mysqli_query($koneksi_istifafakha, "UPDATE kantin_istifafakha SET saldo_kantin = saldo_kantin + $total_bayar WHERE id_kantin = '$id_k'");
    }

    mysqli_commit($koneksi_istifafakha);
    unset($_SESSION['keranjang_istifafakha']);

    echo json_encode([
        'status' => 'success',
        'detail_html' => "Siswa: <b>{$siswa['nama_siswa']}</b><br>Total: <b>Rp " . number_format($total_bayar) . "</b>"
    ]);

} catch (Exception $e) {
    mysqli_rollback($koneksi_istifafakha);
    echo json_encode(['status' => 'error', 'pesan' => 'Gagal memproses transaksi.']);
}