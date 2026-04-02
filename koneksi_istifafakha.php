<?php
$koneksi_istifafakha = mysqli_connect("localhost", "root", "", "db_kantinrfid_istifafakha", 3306);

if (mysqli_connect_errno()) {
    echo "Koneksi database gagal : " . mysqli_connect_errno();
}

?>