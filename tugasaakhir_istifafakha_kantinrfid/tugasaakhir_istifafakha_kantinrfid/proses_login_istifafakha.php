<?php
session_start();
include "koneksi_istifafakha.php";

// Cek apakah data dikirim dari form login, jika tidak balikkan ke halaman login
if (!isset($_POST['username']) || !isset($_POST['password'])) {
    header("Location: loginPetugas_istifafakha.php");
    exit;
}

//PROTEKSI SQL INJECTION DIMULAI (agar karakter-karakter khusus tidak bisa memanipulasi query database)
$username_istifafakha = mysqli_real_escape_string($koneksi_istifafakha, $_POST['username']);
$password_istifafakha = mysqli_real_escape_string($koneksi_istifafakha, $_POST['password']);
//PROTEKSI SQL INJECTION SELESAI

// Query untuk mencari petugas berdasarkan username dan password
$sql_istifafakha = "SELECT * FROM petugas_istifafakha 
                    WHERE username = '$username_istifafakha' 
                    AND password = '$password_istifafakha'";

$query_istifafakha = mysqli_query($koneksi_istifafakha, $sql_istifafakha);

// Cek jika ada error pada database
if (!$query_istifafakha) {
    die("Gagal memproses login: " . mysqli_error($koneksi_istifafakha));
}

// Ambil data hasil query
$data_istifafakha = mysqli_fetch_array($query_istifafakha);

// Jika data ditemukan (Login Berhasil)
if ($data_istifafakha) {
    // Simpan data ke dalam session
    $_SESSION['loginPetugas_istifafakha'] = true;
    $_SESSION['id_petugas_istifafakha']    = $data_istifafakha['id_petugas'];
    $_SESSION['nama_petugas_istifafakha']  = $data_istifafakha['nama_petugas'];

    // Lempar ke halaman dashboard
    header("Location: dashboard_istifafakha.php");
    exit;
} else {
    // Jika data tidak ditemukan (Login Gagal)
    echo "<script>
            alert('Username atau password salah!');
            window.location='loginPetugas_istifafakha.php';
          </script>";
}

exit();
?>
