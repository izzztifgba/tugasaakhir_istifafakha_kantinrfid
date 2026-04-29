<?php
session_start();
include "koneksi_istifafakha.php";

// Proteksi agar hanya petugas yang login bisa melihat detail
if (!isset($_SESSION['loginPetugas_istifafakha'])) {
    header("location:loginPetugas_istifafakha.php");
    exit;
}

// Ambil ID Transaksi dari URL
$id_trx_istifafakha = $_GET['id'];

// 1. Ambil info utama transaksi, Nama Siswa, dan Kantin
$query_header = mysqli_query($koneksi_istifafakha, "
    SELECT t.*, s.nama_siswa, s.kelas, k.nama_kantin, p.nama_petugas
    FROM transaksi_istifafakha t
    LEFT JOIN siswa_istifafakha s ON t.rfid_uid = s.rfid_uid
    LEFT JOIN kantin_istifafakha k ON t.id_kantin = k.id_kantin
    LEFT JOIN user_istifafakha p ON t.id_petugas = p.id_user
    WHERE t.id_transaksi = '$id_trx_istifafakha'
");
$header_istifafakha = mysqli_fetch_array($query_header);

// 2. Ambil rincian menu yang dibeli
$query_detail = mysqli_query($koneksi_istifafakha, "SELECT 
    d.*, 
    m.nama_menu, 
    m.harga 
    FROM detail_transaksi_istifafakha d
    JOIN menu_istifafakha m ON d.id_menu = m.id_menu 
    WHERE d.id_transaksi = '$id_trx_istifafakha'");
?>

<!DOCTYPE html>
<html>

<head>
    <title>Detail Transaksi #<?= $id_trx_istifafakha; ?></title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
        }

        body {
            background: #f4f7f6;
            padding: 20px;
        }

        .nota-box {
            background: white;
            border-radius: 20px;
            padding: 30px;
            width: 100%;
            max-width: 600px;
            margin: auto;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .header-title {
            text-align: center;
            margin-bottom: 25px;
        }

        .header-title h2 {
            color: #065f37;
            font-size: 24px;
            margin-bottom: 5px;
        }

        .header-title p {
            color: #888;
            font-size: 12px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 25px;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
        }

        .info-item {
            display: flex;
            flex-direction: column;
        }

        .info-label {
            font-size: 11px;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .info-value {
            font-size: 14px;
            font-weight: 600;
            color: #333;
            margin-top: 3px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }

        th {
            background-color: #065f37;
            color: white;
            padding: 12px;
            text-align: left;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        th:first-child {
            border-radius: 8px 0 0 0;
        }

        th:last-child {
            border-radius: 0 8px 0 0;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .total-row {
            background-color: #065f37;
            color: white;
            font-weight: 700;
        }

        .total-row td {
            border-bottom: none;
            padding: 15px 12px;
        }

        .menu-name {
            font-weight: 600;
        }

        .menu-price {
            color: #666;
            font-size: 13px;
        }

        .qty-badge {
            background: #e8f5e9;
            color: #065f37;
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 13px;
        }

        .subtotal {
            font-weight: 600;
            color: #065f37;
        }

        .footer-note {
            margin-top: 25px;
            padding: 15px;
            background: #fff3e0;
            border-radius: 10px;
            font-size: 12px;
            color: #e65100;
            text-align: center;
        }

        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 25px;
        }

        .btn {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            font-size: 14px;
            transition: 0.3s;
        }

        .btn-back {
            background: #e0e0e0;
            color: #333;
        }

        .btn-back:hover {
            background: #d0d0d0;
        }

        .btn-print {
            background: #065f37;
            color: white;
        }

        .btn-print:hover {
            background: #044a29;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .nota-box {
                box-shadow: none;
                max-width: 100%;
            }
            
            .btn-group {
                display: none;
            }
            
            .footer-note {
                background: none;
                padding: 10px 0;
            }
        }
    </style>
</head>

<body>

    <div class="nota-box">
        <div class="header-title">
            <h2>🧾 STRUK PEMBELIAN</h2>
            <p>Kantin RFID - Bukti Transaksi</p>
        </div>

        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">No. Transaksi</span>
                <span class="info-value">TRX-<?= str_pad($header_istifafakha['id_transaksi'], 4, '0', STR_PAD_LEFT); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Kantin</span>
                <span class="info-value"><?= $header_istifafakha['nama_kantin'] ?? 'Umum'; ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Nama Siswa</span>
                <span class="info-value"><?= $header_istifafakha['nama_siswa'] ?? 'Tidak dikenal'; ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Kelas</span>
                <span class="info-value"><?= $header_istifafakha['kelas'] ?? '-'; ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Waktu</span>
                <span class="info-value"><?= date('d/m/Y H:i', strtotime($header_istifafakha['waktu'])); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Petugas</span>
                <span class="info-value"><?= $header_istifafakha['nama_petugas'] ?? '-'; ?></span>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Menu</th>
                    <th>Harga</th>
                    <th style="text-align: center;">Qty</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = mysqli_fetch_array($query_detail)) {
                    ?>
                    <tr>
                        <td>
                            <div class="menu-name"><?= $row['nama_menu']; ?></div>
                        </td>
                        <td>
                            <span class="menu-price">Rp <?= number_format($row['harga'], 0, ',', '.'); ?></span>
                        </td>
                        <td style="text-align: center;">
                            <span class="qty-badge"><?= $row['qty']; ?></span>
                        </td>
                        <td>
                            <span class="subtotal">Rp <?= number_format($row['subtotal'], 0, ',', '.'); ?></span>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="3" style="text-align: right;">TOTAL BAYAR</td>
                    <td>Rp <?= number_format($header_istifafakha['total_bayar'], 0, ',', '.'); ?></td>
                </tr>
            </tfoot>
        </table>

        <div class="footer-note">
            ✓ Transaksi berhasil dibayar menggunakan saldo RFID<br>
            ✓ Simpan struk ini sebagai bukti pembayaran
        </div>

        <div class="btn-group">
            <a href="laporan_penjualan_istifafakha.php" class="btn btn-back">⬅ Kembali</a>
            <button onclick="window.print()" class="btn btn-print">🖨️ Cetak Struk</button>
        </div>
    </div>

</body>

</html>