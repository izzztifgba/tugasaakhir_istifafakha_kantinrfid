<?php
ob_start();
session_start();
include "koneksi_istifafakha.php";

// Validasi session login
if (!isset($_SESSION['loginPetugas_istifafakha']) || $_SESSION['loginPetugas_istifafakha'] !== true) {
    ob_end_clean();
    header("Location: loginPetugas_istifafakha.php");
    exit;
}

if (!isset($_SESSION['keranjang_istifafakha'])) {
    $_SESSION['keranjang_istifafakha'] = [];
}

// 1. Logika Tambah dari Katalog
if (isset($_POST['id_menu'])) {
    $id = $_POST['id_menu'];
    $qty = (isset($_POST['qty']) && $_POST['qty'] > 0) ? $_POST['qty'] : 1;

    $query = mysqli_query($koneksi_istifafakha, "SELECT * FROM menu_istifafakha WHERE id_menu='$id'");
    $m = mysqli_fetch_array($query);

    if ($m) {
        if ($m['stok'] <= 0) {
            // Stok habis, redirect dengan pesan error
            header("Location: kasir_istifafakha.php?msg=" . urlencode('Stok menu sudah habis, tidak bisa ditambahkan.') . "&type=error");
            exit;
        }

        if (isset($_SESSION['keranjang_istifafakha'][$id])) {
            if ($_SESSION['keranjang_istifafakha'][$id]['qty'] + $qty > $m['stok']) {
                // Jika total qty melebihi stok, set ke maksimal stok
                $_SESSION['keranjang_istifafakha'][$id]['qty'] = $m['stok'];
            } else {
                $_SESSION['keranjang_istifafakha'][$id]['qty'] += $qty;
            }
        } else {
            $_SESSION['keranjang_istifafakha'][$id] = [
                'nama' => $m['nama_menu'],
                'qty' => $qty,
                'harga' => $m['harga'] // Simpan harga asli untuk hitung subtotal
            ];
        }
        // Hitung ulang subtotal berdasarkan harga asli database
        $_SESSION['keranjang_istifafakha'][$id]['subtotal'] = $_SESSION['keranjang_istifafakha'][$id]['qty'] * $m['harga'];
    }
}

// 2. Logika Tombol Plus / Minus
if (isset($_GET['id']) && isset($_GET['action'])) {
    $id = $_GET['id'];
    $action = $_GET['action'];

    if (isset($_SESSION['keranjang_istifafakha'][$id])) {
        $query = mysqli_query($koneksi_istifafakha, "SELECT * FROM menu_istifafakha WHERE id_menu='$id'");
        $m = mysqli_fetch_array($query);

        if ($action == 'plus') {
            if ($_SESSION['keranjang_istifafakha'][$id]['qty'] < $m['stok']) {
                $_SESSION['keranjang_istifafakha'][$id]['qty']++;
            }
        } elseif ($action == 'minus') {
            if ($_SESSION['keranjang_istifafakha'][$id]['qty'] > 1) {
                $_SESSION['keranjang_istifafakha'][$id]['qty']--;
            } else {
                // Jika qty 1 ditekan minus, hapus item
                unset($_SESSION['keranjang_istifafakha'][$id]);
            }
        }

        // Update subtotal jika item masih ada
        if (isset($_SESSION['keranjang_istifafakha'][$id])) {
            $_SESSION['keranjang_istifafakha'][$id]['subtotal'] = $_SESSION['keranjang_istifafakha'][$id]['qty'] * $m['harga'];
        }
    }
}

ob_end_clean();
header("Location: kasir_istifafakha.php");
exit;
?>