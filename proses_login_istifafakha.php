<?php
session_start();
include "koneksi_istifafakha.php";

$username_istifafakha = $_POST['username'];
$password_istifafakha = $_POST['password'];

$sql_istifafakha = "SELECT * FROM petugas_istifafakha 
WHERE username='$username_istifafakha'
AND password='$password_istifafakha'";

$query_istifafakha = mysqli_query($koneksi_istifafakha, $sql_istifafakha);

if (!$query_istifafakha) {
    die("MYSQL ERROR: " . mysqli_error($koneksi_istifafakha));
}

$data_istifafakha = mysqli_fetch_array($query_istifafakha);

if ($data_istifafakha) {
    $_SESSION['loginPetugas_istifafakha'] = true;
    $_SESSION['id_petugas_istifafakha'] = $data_istifafakha['id_petugas'];
    $_SESSION['nama_petugas_istifafakha'] = $data_istifafakha['nama_petugas'];

    header("Location: dashboard_istifafakha.php");
    exit;
} else {
    echo "Username atau password salah";
}
exit();
?>
