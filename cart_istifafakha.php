<?php
session_start();
include "koneksi_istifafakha.php";

$id_istifafakha  = $_POST['id_menu'];
$qty_istifafakha = $_POST['qty'];

$data_istifafakha = mysqli_fetch_array(mysqli_query($koneksi_istifafakha,"SELECT * FROM menu_istifafakha WHERE id_menu='$id_istifafakha'"));

//cek stok
if ($data_istifafakha['stok'] < $qty_istifafakha) {
    echo "<script>alert('Stok tidak mencukupi!'); window.location='kasir_istifafakha.php';</script>";
    exit;
}

// Ambil data dari database
$query_menu_istifafakha = mysqli_query($koneksi_istifafakha, "SELECT * FROM menu_istifafakha WHERE id_menu='$id_istifafakha'");
$data_istifafakha = mysqli_fetch_array($query_menu_istifafakha);

// SESUAIKAN NAMA KOLOM (Cek apakah di DB namanya 'nama_makanan' atau 'nama')
// ... (kode ambil data menu tetap sama) ...
$id_istifafakha = $_POST['id_menu'];
$qty_baru = $_POST['qty'];

// 1. Cek apakah menu ini sudh di keranjang
$ketemu_istifafakha = false;

if (isset($_SESSION['keranjang_istifafakha'])) {
    foreach ($_SESSION['keranjang_istifafakha'] as &$item_lama_istifafakha) {
        if ($item_lama_istifafakha['id_menu'] == $id_istifafakha) {
            // Jika ketemu, tambah Qty dan hitung ulang Subtotal
            $item_lama_istifafakha['qty'] += $qty_baru;
            $item_lama_istifafakha['subtotal'] = $item_lama_istifafakha['qty'] * $item_lama_istifafakha['harga'];
            $ketemu_istifafakha = true;
            break;
        }
    }
}

// 2. Jika BELUM ADA, baru buat baris baru (seperti kode lama kamu)
if (!$ketemu_istifafakha) {
    $item_baru_istifafakha = [
        "id_menu" => $id_istifafakha,
        "nama"    => $data_istifafakha['nama_makanan'],
        "harga"   => $data_istifafakha['harga'],
        "qty"     => $qty_baru,
        "subtotal" => $qty_baru * $data_istifafakha['harga']
    ];
    $_SESSION['keranjang_istifafakha'][] = $item_baru_istifafakha;
}

header("location:kasir_istifafakha.php");
