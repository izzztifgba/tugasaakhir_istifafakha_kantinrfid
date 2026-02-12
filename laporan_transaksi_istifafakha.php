<?php
include "koneksi_istifafakha.php";

$data_istifafakha = mysqli_query($koneksi_istifafakha,"SELECT * FROM transaksi_istifafakha ORDER BY id_transaksi DESC");
?>

<h2>LAPORAN TRANSAKSI</h2>

<table border="1" cellpadding="10">
<tr>
    <th>ID Transaksi</th>
    <th>Tanggal</th>
    <th>Total</th>
    <th>Detail</th>
</tr>

<?php while($d_istifafakha = mysqli_fetch_array($data_istifafakha)){ ?>
<tr>
    <td><?= $d_istifafakha['id_transaksi']; ?></td>
    <td><?= $d_istifafakha['tanggal']; ?></td>
    <td>Rp <?= $d_istifafakha['total_harga']; ?></td>
    <td>
        <a href="detail_transaksi.php?id=<?= $d_istifafakha['id_transaksi']; ?>">
            Lihat Detail
        </a>
    </td>
</tr>
<?php } ?>
</table>
