<?php
session_start();
include "koneksi_istifafakha.php";

if(!isset($_SESSION['keranjang_istifafakha'])){
    $_SESSION['keranjang_istifafakha'] = [];
}

$menu_istifafakha = mysqli_query($koneksi_istifafakha,"SELECT * FROM menu_istifafakha");
?>

<h2>KASIR KANTIN</h2>

<form method="POST" action="cart_istifafakha.php">
Pilih Menu :
<select name="id_menu">
<?php while($m_istifafakha = mysqli_fetch_array($menu_istifafakha)){ ?>
    <option value="<?= $m_istifafakha['id_menu']; ?>">
        <?= $m_istifafakha['nama_makanan']; ?> - Rp<?= $m_istifafakha['harga']; ?> (stok <?= $m_istifafakha['stok']; ?>)
    </option>
<?php } ?>
</select>

Jumlah :
<input type="number" name="qty" required>

<button>Tambah</button>
</form>

<hr>

<h3>Keranjang</h3>

<table border="1" cellpadding="10">
    <a href="dashboard_istifafakha.php">⬅ Kembali</a>
<tr>
    <th>Menu</th>
    <th>Qty</th>
    <th>Subtotal</th>
    <th>Aksi</th> 
</tr>


<a href="hapus_cart_istifafakha.php" onclick="return confirm('Yakin ingin mengosongkan semua isi keranjang?')">
    <button>🗑️ Kosongkan Keranjang</button> 
</a>

<?php
$total_istifafakha = 0;

// 1. Cek dulu apakah session keranjang sudah ada dan isinya array
if (isset($_SESSION['keranjang_istifafakha']) && is_array($_SESSION['keranjang_istifafakha'])) {
    
    foreach($_SESSION['keranjang_istifafakha'] as $id_istifafakha => $k_istifafakha) { 
        
        // 2. memastikan $k_istifafakha isinya bukan NULL (kosong)
        if ($k_istifafakha != null) {
            
            // Menggunakan sintaks kurung kurawal {$var} agar lebih aman saat cetak array di dalam echo
            echo "<tr>
                <td>{$k_istifafakha['nama']}</td>
                <td>{$k_istifafakha['qty']}</td>
                <td>" . number_format($k_istifafakha['subtotal'], 0, ',', '.') . "</td>
                <td>
                    <a href='hapus_item_istifafakha.php?id=$id_istifafakha' 
                       onclick='return confirm(\"Hapus item ini?\")' 
                       style='color:red;'>❌ Hapus</a>
                </td>
            </tr>";
            
            $total_istifafakha += $k_istifafakha['subtotal'];
        }
    }
} else {
    // Jika keranjang benar-benar kosong, tampilkan pesan di baris tabel
    echo "<tr><td colspan='4' align='center'>Keranjang masih kosong</td></tr>";
}
?>

<tr>
    <td colspan="2">TOTAL</td>
    <td colspan="2">Rp <?= number_format($total_istifafakha, 0, ',', '.'); ?></td> </tr>
</table>
<br>
<a href="bayar_istifafakha.php">💰 BAYAR SEKARANG</a>
