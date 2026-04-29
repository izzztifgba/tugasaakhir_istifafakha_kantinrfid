<?php
session_start();
include "koneksi_istifafakha.php";

if (!isset($_POST['username']) || !isset($_POST['password'])) {
    header("Location: loginPetugas_istifafakha.php");
    exit;
}

$username_istifafakha = mysqli_real_escape_string($koneksi_istifafakha, $_POST['username']);
$password_raw = mysqli_real_escape_string($koneksi_istifafakha, $_POST['password']);

// CEK DATABASE KAMU:
$password_final = md5($password_raw); 
$sql_istifafakha = "SELECT * FROM user_istifafakha
                    WHERE username = '$username_istifafakha' 
                    AND PASSWORD = '$password_final'";

$query_istifafakha = mysqli_query($koneksi_istifafakha, $sql_istifafakha);

if (!$query_istifafakha) {
    die("Gagal memproses login: " . mysqli_error($koneksi_istifafakha));
}

$data_istifafakha = mysqli_fetch_array($query_istifafakha);

if ($data_istifafakha) {
    $_SESSION['loginPetugas_istifafakha'] = true;
    $_SESSION['id_user_istifafakha']    = $data_istifafakha['id_user'];
    $_SESSION['nama_petugas_istifafakha']  = $data_istifafakha['nama_petugas'];
    
    // Pastikan kolom 'role' sudah kamu tambahkan di database
    $_SESSION['role_istifafakha']          = isset($data_istifafakha['role']) ? $data_istifafakha['role'] : 'petugas';
    
    // Simpan id_kantin ke session agar menu terpisah per-kantin
    $_SESSION['id_kantin']                 = $data_istifafakha['id_kantin'] ?? null;

    header("Location: dashboard_istifafakha.php");
    exit;
} else {
    echo "<script>
            alert('Username atau password salah! Pastikan data di database sudah di-MD5.');
            window.location='loginPetugas_istifafakha.php';
          </script>";
}
?>