<?php
ob_start();
session_start();
include "koneksi_istifafakha.php";

// Validasi session login (Disamakan dengan Dashboard)
if (!isset($_SESSION['loginPetugas_istifafakha']) || $_SESSION['loginPetugas_istifafakha'] !== true) {
    ob_end_clean();
    header("Location: loginPetugas_istifafakha.php");
    exit;
}

// Ambil data dari session
$role = $_SESSION['role_istifafakha'] ?? 'petugas';
$nama = $_SESSION['nama_petugas_istifafakha'] ?? 'User';
$id_kantin_user = $_SESSION['id_kantin'] ?? null;

// Query Menu untuk Kasir - Filter berdasarkan kantin petugas
if ($role === 'admin') {
    // Admin lihat semua menu
    $menu_istifafakha = mysqli_query($koneksi_istifafakha, "SELECT * FROM menu_istifafakha WHERE stok > 0 ORDER BY nama_menu ASC");
} else {
    if ($id_kantin_user) {
        $menu_istifafakha = mysqli_query($koneksi_istifafakha, "SELECT * FROM menu_istifafakha WHERE stok > 0 AND (id_kantin = '$id_kantin_user' OR id_kantin IS NULL) ORDER BY nama_menu ASC");
    } else {
        $menu_istifafakha = mysqli_query($koneksi_istifafakha, "SELECT * FROM menu_istifafakha WHERE stok > 0 ORDER BY nama_menu ASC");
    }
}

if (!isset($_SESSION['keranjang_istifafakha'])) {
    $_SESSION['keranjang_istifafakha'] = [];
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir Penjualan - <?= strtoupper(htmlspecialchars($role)); ?></title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&display=swap');

        :root {
            --emerald-dark: #022e1a;
            --emerald-main: #065f37;
            --emerald-light: #50c878;
            --white: #ffffff;
            --bg-soft: #f4f7f6;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
        }

        body {
            display: flex;
            min-height: 100vh;
            background-color: var(--bg-soft);
        }

        /* --- SIDEBAR (KONSISTEN DENGAN DASHBOARD) --- */
        nav {
            width: 280px;
            background: linear-gradient(180deg, var(--emerald-dark) 0%, var(--emerald-main) 100%);
            color: white;
            padding: 30px 20px;
            display: flex;
            flex-direction: column;
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            height: 100vh;
        }

        nav h3 {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--emerald-light);
            margin: 25px 0 15px 10px;
            font-weight: 700;
        }

        .user-info {
            background: rgba(255, 255, 255, 0.08);
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 25px;
            border-left: 3px solid var(--emerald-light);
        }

        .user-info .user-name {
            font-size: 14px;
            font-weight: 700;
            color: white;
            margin-bottom: 5px;
            word-break: break-word;
        }

        .user-info .user-role {
            font-size: 11px;
            text-transform: uppercase;
            color: var(--emerald-light);
            letter-spacing: 1px;
            font-weight: 600;
        }

        nav ul {
            list-style: none;
        }

        nav ul li {
            margin-bottom: 8px;
        }

        nav ul li a {
            text-decoration: none;
            color: rgba(255, 255, 255, 0.8);
            padding: 12px 15px;
            display: block;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        nav ul li a:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            padding-left: 20px;
        }

        nav ul li a.active {
            background: var(--emerald-light);
            color: var(--emerald-dark);
            font-weight: 700;
        }

        .logout-btn {
            margin-top: auto;
            background: rgba(255, 0, 0, 0.1) !important;
            color: #ff6b6b !important;
            border: 1px solid rgba(255, 107, 107, 0.2);
            border-radius: 12px;
            text-decoration: none;
            transition: 0.3s;
        }

        .logout-btn:hover {
            background: #ff6b6b !important;
            color: white !important;
        }

        /* --- MAIN CONTENT KASIR --- */
        main {
            flex: 1;
            padding: 40px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        /* LAYOUT KASIR 2 KOLOM */
        .kasir-container {
            display: flex;
            gap: 25px;
            flex: 1;
        }

        .card-panel {
            background: white;
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
        }

        /* GRID MENU */
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 15px;
            overflow-y: auto;
            max-height: 60vh;
            padding-right: 10px;
        }

        .menu-item {
            background: var(--bg-soft);
            padding: 15px;
            border-radius: 15px;
            text-align: center;
            border: 1px solid transparent;
            transition: 0.3s;
        }

        .menu-item:hover {
            border-color: var(--emerald-light);
            transform: translateY(-3px);
        }

        .btn-add {
            background: var(--emerald-main);
            color: white;
            border: none;
            padding: 8px;
            width: 100%;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            margin-top: 10px;
        }

        /* KERANJANG */
        .cart-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }

        .qty-control {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-qty {
            width: 25px;
            height: 25px;
            border-radius: 6px;
            border: none;
            background: #ddd;
            cursor: pointer;
            font-weight: bold;
        }

        .btn-qty:hover {
            background: var(--emerald-light);
        }

        .btn-bayar {
            background: var(--emerald-light);
            color: var(--emerald-dark);
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 16px;
            cursor: pointer;
            margin-top: 20px;
        }

        /* MODAL RFID */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background: white;
            margin: 12% auto;
            padding: 40px;
            border-radius: 25px;
            width: 380px;
            text-align: center;
        }

        .rfid-hidden-input {
            position: absolute;
            opacity: 0;
        }
    </style>
</head>

<body>

    <nav>
        <div style="padding-left: 10px; margin-bottom: 30px;">
            <h2 style="font-weight: 700; letter-spacing: -1px;">KANTIN<span
                    style="color: var(--emerald-light);">RFID</span></h2>
        </div>

        <div class="user-info">
            <div class="user-name"><?= htmlspecialchars($nama); ?></div>
            <div class="user-role"><?= strtoupper(htmlspecialchars($role)); ?></div>
        </div>

        <ul>
            <li><a href="dashboard_istifafakha.php">Dashboard Home</a></li>

            <?php if ($role == 'admin'): ?>
                <h3>Administrator</h3>
                <li><a href="datapetugas_istifafakha.php">Data Kantin & Petugas</a></li>
                <li><a href="datasiswa_istifafakha.php">Registrasi & Top-Up</a></li>
                <li><a href="cek_saldo_kantin.php">Cek Saldo Kantin</a></li>
                <li><a href="laporan_pendapatan_kantin.php">Pendapatan Seluruh</a></li>
                <li><a href="tarik_tunai_istifafakha.php">Penarikan Uang</a></li>
            <?php endif; ?>

            <?php if ($role == 'kantin' || $role == 'petugas'): ?>
                <h3>Operasional</h3>
                <li><a href="kasir_istifafakha.php" class="active">Kasir Penjualan</a></li>
                <li><a href="cek_saldo_siswa.php">Cek Saldo Siswa</a></li>
                <li><a href="laporan_penjualan_istifafakha.php">Riwayat Saya</a></li>
            <?php endif; ?>
        </ul>

        <ul style="margin-top: auto;">
            <li>
                <a href="logout_istifafakha.php" class="logout-btn" onclick="return confirm('Yakin ingin logout?')">
                    Logout Sistem
                </a>
            </li>
        </ul>
    </nav>

    <main>
        <div class="header-section">
            <div style="color: var(--emerald-main);">
                <h1 style="font-size: 24px;">Kasir Penjualan</h1>
                <p style="color: #888; font-size: 14px;">Kelola transaksi pelanggan dengan cepat.</p>
            </div>
            <div style="text-align: right;">
                <span style="font-size: 12px; color: #888; display: block;"><?= date('l, d F Y'); ?></span>
                <span class="role-badge"
                    style="background: var(--emerald-light); padding: 5px 15px; border-radius: 20px; font-weight: 700; font-size: 11px;">
                    MODE <?= strtoupper($role); ?>
                </span>
            </div>
        </div>

        <div class="kasir-container">
            <div class="card-panel" style="flex: 1.5;">
                <h4 style="margin-bottom: 20px; color: var(--emerald-main);">Daftar Menu Tersedia</h4>
                <div class="menu-grid">
                    <?php while ($m = mysqli_fetch_array($menu_istifafakha)) { ?>
                        <div class="menu-item">
                            <strong
                                style="font-size: 14px; display: block; margin-bottom: 5px;"><?= $m['nama_menu']; ?></strong>
                            <span style="color: var(--emerald-main); font-weight: 700;">Rp
                                <?= number_format($m['harga'], 0, ',', '.'); ?></span>
                            <form method="POST" action="cart_istifafakha.php">
                                <input type="hidden" name="id_menu" value="<?= $m['id_menu']; ?>">
                                <button type="submit" class="btn-add">Tambah</button>
                            </form>
                        </div>
                    <?php } ?>
                </div>
            </div>
            

            <div class="card-panel" style="flex: 1;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h4 style="color: var(--emerald-main);">Keranjang</h4>
                    <a href="hapus_cart_istifafakha.php"
                        style="font-size: 11px; color: #ff6b6b; text-decoration: none; font-weight: 700;">KOSONGKAN</a>
                </div>

                <div style="flex: 1; overflow-y: auto;">
                    <?php
                    $total = 0;
                    if (!empty($_SESSION['keranjang_istifafakha'])) {
                        foreach ($_SESSION['keranjang_istifafakha'] as $id => $item) {
                            echo "
                            <div class='cart-row'>
                                <div style='flex: 2;'>
                                    <div style='font-size: 13px; font-weight: 600;'>{$item['nama']}</div>
                                    <div style='font-size: 11px; color: #888;'>@" . number_format($item['harga'], 0, ',', '.') . "</div>
                                </div>
                                <div class='qty-control'>
                                    <button class='btn-qty' onclick=\"location.href='cart_istifafakha.php?id=$id&action=minus'\">-</button>
                                    <span style='font-size: 13px; font-weight: 700;'>{$item['qty']}</span>
                                    <button class='btn-qty' onclick=\"location.href='cart_istifafakha.php?id=$id&action=plus'\">+</button>
                                </div>
                                <div style='flex: 1.5; text-align: right; font-weight: 700; font-size: 13px;'>
                                    " . number_format($item['subtotal'], 0, ',', '.') . "
                                </div>
                            </div>";
                            $total += $item['subtotal'];
                        }
                    } else {
                        echo "<div style='text-align: center; color: #ccc; margin-top: 50px; font-size: 13px;'>Keranjang kosong</div>";
                    }
                    ?>
                </div>

                <div style="margin-top: 20px; padding-top: 15px; border-top: 2px dashed #eee;">
                    <div
                        style="display: flex; justify-content: space-between; font-size: 18px; font-weight: 700; color: var(--emerald-dark);">
                        <span>Total</span>
                        <span>Rp <?= number_format($total, 0, ',', '.'); ?></span>
                    </div>
                    <?php if ($total > 0): ?>
                        <button class="btn-bayar" onclick="openModal()">PROSES PEMBAYARAN</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <div id="rfidModal" class="modal">
        <div class="modal-content">
            <h3 style="color: var(--emerald-main);">Scan Kartu RFID</h3>
            <p style="color: #666; margin: 15px 0; font-size: 14px;">Total Tagihan: <br>
                <span style="font-size: 24px; font-weight: 700; color: var(--emerald-dark);">Rp
                    <?= number_format($total, 0, ',', '.'); ?></span>
            </p>
            <form id="formRFID">
                <input type="text" name="rfid_uid" id="rfid_input" class="rfid-hidden-input" autofocus required>
                <input type="hidden" name="total_bayar" value="<?= $total ?>">
                <p style="font-size: 11px; color: #aaa;">Sistem menunggu kartu pelanggan...</p>
                <br>
                <button type="button" onclick="closeModal()"
                    style="background: none; border: none; color: #ff6b6b; cursor: pointer; font-weight: 700; font-size: 12px;">BATALKAN</button>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const modal = document.getElementById("rfidModal");
        const rfidInput = document.getElementById("rfid_input");

        function openModal() {
            modal.style.display = "block";
            rfidInput.value = ""; // Reset input
            rfidInput.focus();
        }

        function closeModal() {
            modal.style.display = "none";
        }

        // Logika AJAX
        $('#formRFID').on('submit', function (e) {
            e.preventDefault(); // Mencegah pindah halaman

            $.ajax({
                url: 'bayar_rfid_istifafakha.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (res) {
                    if (res.status === 'success') {
                        Swal.fire({
                            title: 'Transaksi Berhasil!',
                            html: res.detail_html,
                            icon: 'success'
                        }).then(() => {
                            window.location.reload(); // Refresh halaman
                        });
                    } else {
                        Swal.fire('Gagal!', res.pesan, 'error');
                        rfidInput.value = "";
                        rfidInput.focus();
                    }
                },
                error: function () {
                    Swal.fire('Error', 'Gagal menyambung ke server', 'error');
                }
            });
        });

        window.onclick = function (e) { if (e.target == modal) closeModal(); }
    </script>