<?php
include "koneksi_istifafakha.php";

// Menggunakan JOIN agar bisa menampilkan Nama Siswa dari tabel siswa_istifafakha
$query_istifafakha = "SELECT transaksi_istifafakha.*, siswa_istifafakha.nama_siswa 
                      FROM transaksi_istifafakha 
                      JOIN siswa_istifafakha ON transaksi_istifafakha.rfid_uid = siswa_istifafakha.rfid_uid 
                      ORDER BY transaksi_istifafakha.id_transaksi ASC";

$data_istifafakha = mysqli_query($koneksi_istifafakha, $query_istifafakha);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Laporan Transaksi - Kantin RFID</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2 f2 f2;
        }

        .btn-kembali {
            margin-bottom: 20px;
            display: inline-block;
            text-decoration: none;
            color: blue;
        }
    </style>
</head>

<body>

    <h2>LAPORAN TRANSAKSI KANTIN</h2>
    <a href="dashboard_istifafakha.php" class="btn-kembali">⬅ Kembali ke Dashboard</a>

    <table>
        <tr>
            <th>No. Transaksi</th>
            <th>Waktu</th>
            <th>Nama Siswa (RFID)</th>
            <th>Total Bayar</th>
            <th>Aksi</th>
        </tr>

        <?php
        if (mysqli_num_rows($data_istifafakha) > 0) {
            while ($d = mysqli_fetch_array($data_istifafakha)) {
                ?>
                <tr>
                    <td>TRX-<?= $d['id_transaksi']; ?></td>
                    <td><?= $d['waktu']; ?></td>
                    <td><?= $d['nama_siswa']; ?> (<?= $d['rfid_uid']; ?>)</td>
                    <td>Rp <?= number_format($d['total_bayar'], 0, ',', '.'); ?></td>
                    <td>
                        <a href="detail_transaksi_istifafakha.php?id=<?= $d['id_transaksi']; ?>">
                            🔍 Lihat Detail
                        </a>
                    </td>
                </tr>
            <?php
            }
        } else {
            echo "<tr><td colspan='5' align='center'>Belum ada transaksi.</td></tr>";
        }
        ?>
    </table>

</body>

</html>