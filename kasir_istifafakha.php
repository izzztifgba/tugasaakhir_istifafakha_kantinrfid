<?php
ob_start();
session_start();
include "koneksi_istifafakha.php";

// 1. Validasi session login
if (!isset($_SESSION['loginPetugas_istifafakha']) || $_SESSION['loginPetugas_istifafakha'] !== true) {
    ob_end_clean();
    header("Location: loginPetugas_istifafakha.php");
    exit;
}

// 2. Ambil data dari session
$role = $_SESSION['role_istifafakha'] ?? 'petugas';
$nama = $_SESSION['nama_petugas_istifafakha'] ?? 'User';
$id_kantin_user = $_SESSION['id_kantin'] ?? null;
$id_user_login = $_SESSION['id_user_istifafakha'];

// Validasi akses untuk non-admin
if ($role !== 'admin' && !$id_kantin_user) {
    header('Location: dashboard_istifafakha.php?msg=' . urlencode('Akses tidak valid, silakan login kembali.') . '&type=error');
    exit;
}

// 3. Ambil semua menu untuk ditampilkan, termasuk stok habis
if ($role === 'admin') {
    $sql_menu = "SELECT * FROM menu_istifafakha ORDER BY nama_menu ASC";
} elseif ($id_kantin_user) {
    $sql_menu = "SELECT * FROM menu_istifafakha WHERE id_kantin = '$id_kantin_user' ORDER BY nama_menu ASC";
} else {
    $sql_menu = "SELECT * FROM menu_istifafakha ORDER BY nama_menu ASC";
}
$query_menu = mysqli_query($koneksi_istifafakha, $sql_menu);

// 4. Ambil data menu habis untuk opsi restock
$out_of_stock_items = [];
$sql_out = "SELECT * FROM menu_istifafakha WHERE stok <= 0";
if ($role !== 'admin' && $id_kantin_user) {
    $sql_out .= " AND id_kantin = '$id_kantin_user'";
}
$sql_out .= " ORDER BY nama_menu ASC";
$result_out = mysqli_query($koneksi_istifafakha, $sql_out);
while ($row = mysqli_fetch_assoc($result_out)) {
    $out_of_stock_items[] = $row;
}

// 5. Ambil daftar kantin ketika admin
$kantin_list = [];
if ($role === 'admin') {
    $result_kantin = mysqli_query($koneksi_istifafakha, "SELECT * FROM kantin_istifafakha ORDER BY nama_kantin ASC");
    while ($row = mysqli_fetch_assoc($result_kantin)) {
        $kantin_list[] = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form_action = $_POST['form_action'] ?? '';

    if ($form_action === 'add_menu') {
        $nama_menu = mysqli_real_escape_string($koneksi_istifafakha, trim($_POST['nama'] ?? ''));
        $harga = (int) ($_POST['harga'] ?? 0);
        $stok = (int) ($_POST['stok'] ?? 0);
        $id_kantin = $role === 'admin' ? (int) ($_POST['id_kantin'] ?? 0) : (int) $id_kantin_user;

        if ($nama_menu !== '' && $harga > 0 && $stok >= 0 && $id_kantin > 0) {
            mysqli_query($koneksi_istifafakha, "INSERT INTO menu_istifafakha (nama_menu,harga,stok,id_kantin) VALUES ('$nama_menu',$harga,$stok,$id_kantin)");
            header('Location: kasir_istifafakha.php?msg=' . urlencode('Menu baru berhasil ditambahkan.') . '&type=success');
            exit;
        }

        header('Location: kasir_istifafakha.php?msg=' . urlencode('Lengkapi data menu dengan benar.') . '&type=error');
        exit;
    }

    if ($form_action === 'restock_menu') {
        $id_menu_restock = (int) ($_POST['menu_id'] ?? 0);
        $restock_qty = (int) ($_POST['restock_qty'] ?? 0);

        if ($id_menu_restock > 0 && $restock_qty > 0) {
            $result = mysqli_query($koneksi_istifafakha, "UPDATE menu_istifafakha SET stok = stok + $restock_qty WHERE id_menu = '$id_menu_restock'");
            if ($result) {
                header('Location: kasir_istifafakha.php?msg=' . urlencode('Stok menu berhasil ditambahkan.') . '&type=success');
                exit;
            } else {
                header('Location: kasir_istifafakha.php?msg=' . urlencode('Gagal menambahkan stok menu.') . '&type=error');
                exit;
            }
        }

        header('Location: kasir_istifafakha.php?msg=' . urlencode('Pilih menu dan jumlah stok yang valid.') . '&type=error');
        exit;
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
            --danger: #e74c3c;
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

        /* --- SIDEBAR (TIDAK DIUBAH SESUAI PERMINTAAN) --- */
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
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--emerald-light);
            margin: 25px 0 10px 10px;
            font-weight: 700;
            opacity: 0.8;
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
            transition: 0.3s;
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

        /* --- MAIN CONTENT  --- */
        main {
            flex: 1;
            padding: 40px;
            overflow-y: auto;
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 35px;
        }

        .kasir-layout {
            display: flex;
            gap: 30px;
            align-items: flex-start;
        }

        /* Container Menu */
        .menu-container {
            flex: 1.6;
            background: white;
            padding: 30px;
            border-radius: 24px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.03);
        }

        .menu-card.out-of-stock {
            opacity: 0.6;
            filter: grayscale(0.8);
            cursor: not-allowed;
            border-color: #eee;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .menu-card {
            background: #fdfdfd;
            border: 1px solid #f0f3f2;
            padding: 20px;
            border-radius: 20px;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .menu-card:hover {
            transform: translateY(-5px);
            border-color: var(--emerald-light);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.04);
            background: white;
        }

        .menu-card .price {
            color: var(--emerald-main);
            font-weight: 700;
            font-size: 14px;
            margin: 8px 0;
            display: block;
        }

        .stok-label {
            font-size: 10px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 6px;
            background: #f1f1f1;
            color: #777;
            display: inline-block;
        }

        .btn-habis {
            background: #e0e0e0;
            color: #888;
            border: none;
            padding: 10px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 12px;
            width: 100%;
            margin-top: 15px;
            text-align: center;
            display: block;
        }

        /* Tombol Kelola/Tambah Menu Baru */
        .btn-manage-menu {
            background: white;
            color: var(--emerald-main);
            border: 2px solid var(--emerald-main);
            padding: 8px 15px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 12px;
            text-decoration: none;
            transition: 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-manage-menu:hover {
            background: var(--emerald-main);
            color: white;
        }

        .btn-tambah {
            background: var(--emerald-main);
            color: white;
            border: none;
            padding: 10px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 12px;
            cursor: pointer;
            margin-top: 15px;
            width: 100%;
            transition: 0.2s;
        }

        .btn-tambah:hover {
            background: var(--emerald-dark);
        }

        .btn-tambah:disabled {
            background: #e0e0e0;
            color: #aaa;
            cursor: not-allowed;
        }

        /* Container Keranjang */
        .cart-container {
            flex: 1;
            background: white;
            padding: 30px;
            border-radius: 24px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.03);
            position: sticky;
            top: 40px;
        }

        .cart-list {
            max-height: 350px;
            overflow-y: auto;
            margin: 20px 0;
            padding-right: 5px;
        }

        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #f8f8f8;
        }

        .qty-box {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #f5f5f5;
            padding: 5px;
            border-radius: 8px;
        }

        .qty-btn {
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            border-radius: 5px;
            text-decoration: none;
            color: var(--emerald-dark);
            font-weight: 700;
            font-size: 14px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .total-section {
            border-top: 2px dashed #eee;
            margin-top: 20px;
            padding-top: 20px;
        }

        .btn-checkout {
            background: var(--emerald-light);
            color: var(--emerald-dark);
            width: 100%;
            padding: 18px;
            border: none;
            border-radius: 15px;
            font-weight: 700;
            font-size: 15px;
            cursor: pointer;
            margin-top: 20px;
            box-shadow: 0 10px 20px rgba(80, 200, 120, 0.2);
        }

        /* MODAL */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(8px);
        }

        .modal-content {
            background: white;
            margin: 10% auto;
            padding: 40px;
            border-radius: 30px;
            width: 400px;
            text-align: center;
            border: 1px solid #eee;
        }

        .message-box {
            margin-bottom: 25px;
            padding: 18px 20px;
            border-radius: 18px;
            font-size: 13px;
            color: #2f4f4f;
            background: #ecf9f0;
            border: 1px solid #c9eed8;
        }

        .message-box.error {
            background: #ffeaea;
            border-color: #f5c2c2;
            color: #9b2727;
        }

        .tab-switcher {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin: 20px 0 30px;
        }

        .tab-button {
            flex: 1;
            padding: 10px 12px;
            border-radius: 14px;
            border: 1px solid #e0ebe7;
            background: #f7fdf7;
            color: #065f37;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.2s;
        }

        .tab-button.active {
            background: var(--emerald-main);
            color: white;
            border-color: var(--emerald-main);
        }

        .tab-panel {
            text-align: left;
        }

        .tab-panel.hidden {
            display: none;
        }

        .form-group {
            margin-bottom: 18px;
            text-align: left;
        }

        .form-group label {
            font-size: 12px;
            color: #555;
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #ddd;
            border-radius: 14px;
            font-size: 13px;
            color: #333;
            outline: none;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: var(--emerald-light);
            box-shadow: 0 0 0 3px rgba(80, 200, 120, 0.12);
        }

        .rfid-input-hidden {
            position: absolute;
            opacity: 0;
        }

        /* Animasi Scan */
        .scan-animation {
            position: relative;
            width: 100px;
            height: 100px;
            margin: 0 auto 20px;
            background: #f0f7f4;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border: 2px solid #e0eee7;
        }

        .card-icon {
            font-size: 50px;
            z-index: 1;
        }

        .scan-line {
            position: absolute;
            width: 100%;
            height: 3px;
            background: var(--emerald-light);
            box-shadow: 0 0 15px var(--emerald-light);
            top: 0;
            z-index: 2;
            animation: scanMove 2s infinite ease-in-out;
        }

        @keyframes scanMove {
            0% {
                top: 0;
            }

            50% {
                top: 100%;
            }

            100% {
                top: 0;
            }
        }

        /* Tombol Batal yang lebih bersih */
        .btn-batal {
            background: none;
            border: none;
            color: #e74c3c;
            cursor: pointer;
            font-weight: 700;
            font-size: 11px;
            letter-spacing: 1px;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 8px;
            transition: 0.3s;
        }

        .btn-batal:hover {
            background: #fff5f5;
            color: #c0392b;
        }

        .rfid-input-hidden {
            position: absolute;
            top: -1000px;
            left: -1000px;
            opacity: 0;
        }
    </style>
</head>

<body>

    <nav>
        <div style="padding-left: 10px; margin-bottom: 30px;">
            <h2 style="font-weight: 700; letter-spacing: 1px;">KANTIN<span
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
                <li><a href="laporan_pendapatan_kantin.php">Laporan Pendapatan</a></li>
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
            <div>
                <h1 style="font-size: 28px; color: var(--emerald-dark); font-weight: 700;">Kasir Penjualan</h1>
                <p style="color: #999; font-size: 14px; margin-top: 5px;">Pilih menu makanan dan lakukan pembayaran
                    RFID.</p>
            </div>
            <div style="text-align: right;">
                <div style="font-weight: 600; color: #444;"><?= date('d F Y'); ?></div>
                <div style="color: var(--emerald-main); font-size: 12px; font-weight: 700;">Point of Sale System</div>
            </div>
        </div>

        <div class="kasir-layout">
            <div class="menu-container">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; gap: 15px; flex-wrap: wrap;">
                    <div>
                        <h3 style="font-size: 16px; color: #333;">Menu Tersedia</h3>
                        <p style="font-size: 11px; color: #999;">Klik item untuk menambahkan ke keranjang</p>
                    </div>
                    <button type="button" class="btn-manage-menu" onclick="openMenuModal()">
                        <span>+</span> TAMBAH MENU
                    </button>
                </div>

                <div class="menu-grid">
                    <?php while ($m = mysqli_fetch_array($query_menu)) {
                        $is_out = ($m['stok'] <= 0);
                        ?>
                        <div class="menu-card <?= $is_out ? 'out-of-stock' : ''; ?>">
                            <div>
                                <strong style="font-size: 15px; color: #333; display: block;"><?= htmlspecialchars($m['nama_menu']); ?></strong>
                                <span class="price">Rp <?= number_format($m['harga'], 0, ',', '.'); ?></span>

                                <?php if ($is_out): ?>
                                    <span class="stok-label" style="background: #ffeaea; color: #e74c3c;">Stok Habis</span>
                                <?php else: ?>
                                    <span class="stok-label" style="<?= ($m['stok'] <= 5) ? 'background:#fff4e6; color:#d97706;' : ''; ?>">
                                        Stok: <?= $m['stok']; ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <?php if (!$is_out): ?>
                                <form method="POST" action="cart_istifafakha.php">
                                    <input type="hidden" name="id_menu" value="<?= $m['id_menu']; ?>">
                                    <button type="submit" class="btn-tambah">Tambah</button>
                                </form>
                            <?php else: ?>
                                <div class="btn-habis">Stok Sudah Habis</div>
                            <?php endif; ?>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <div class="cart-container">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <h3 style="font-size: 16px; color: #333;">Item Pesanan</h3>
                    <a href="hapus_cart_istifafakha.php" style="font-size: 11px; color: var(--danger); font-weight: 700; text-decoration: none;">Bersihkan</a>
                </div>

                <?php if (isset($_GET['msg'])): ?>
                    <div class="message-box <?= (isset($_GET['type']) && $_GET['type'] === 'error') ? 'error' : ''; ?>">
                        <?= htmlspecialchars($_GET['msg']); ?>
                    </div>
                <?php endif; ?>

                <div class="cart-list">
                    <?php
                    $total = 0;
                    if (!empty($_SESSION['keranjang_istifafakha'])) {
                        foreach ($_SESSION['keranjang_istifafakha'] as $id => $item) {
                            $total += $item['subtotal']; ?>
                            <div class="cart-item">
                                <div style="flex: 1;">
                                    <div style="font-size: 13px; font-weight: 600;"><?= htmlspecialchars($item['nama']); ?></div>
                                    <div style="font-size: 11px; color: #aaa;">@<?= number_format($item['harga'], 0, ',', '.'); ?></div>
                                </div>
                                <div class="qty-box">
                                    <a href="cart_istifafakha.php?id=<?= $id ?>&action=minus" class="qty-btn">-</a>
                                    <span style="font-size: 13px; font-weight: 700; min-width: 15px; text-align: center;"><?= $item['qty']; ?></span>
                                    <a href="cart_istifafakha.php?id=<?= $id ?>&action=plus" class="qty-btn">+</a>
                                </div>
                                <div style="width: 80px; text-align: right; font-weight: 700; color: var(--emerald-main); font-size: 13px;">
                                    <?= number_format($item['subtotal'], 0, ',', '.'); ?>
                                </div>
                            </div>
                        <?php }
                    } else {
                        echo "<div style='text-align: center; color: #ccc; margin-top: 40px; font-size: 13px;'>Keranjang Kosong</div>";
                    } ?>
                </div>

                <div class="total-section">
                    <div style="display: flex; justify-content: space-between; font-size: 20px; font-weight: 700; color: var(--emerald-dark);">
                        <span>Total</span>
                        <span>Rp <?= number_format($total, 0, ',', '.'); ?></span>
                    </div>
                    <?php if ($total > 0): ?>
                        <button class="btn-checkout" onclick="openModal()">PROSES PEMBAYARAN</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <div id="menuModal" class="modal">
        <div class="modal-content">
            <h3 style="color: var(--emerald-main); margin-bottom: 10px;">Tambah Menu / Restock</h3>
            <p style="color: #888; font-size: 13px;">Bisa tambah menu baru atau tambah stok menu yang sudah habis.</p>

            <div class="tab-switcher">
                <button type="button" class="tab-button active" data-tab="new-menu">Menu Baru</button>
                <button type="button" class="tab-button" data-tab="restock">Tambah Stok</button>
            </div>

            <form id="menuForm" method="POST">
                <input type="hidden" name="form_action" id="form_action" value="add_menu">

                <div id="tab_new-menu" class="tab-panel">
                    <?php if ($role === 'admin'): ?>
                        <div class="form-group">
                            <label for="id_kantin">Pilih Kantin</label>
                            <select id="id_kantin" name="id_kantin" required>
                                <option value="">-- Pilih Kantin --</option>
                                <?php foreach ($kantin_list as $kantin): ?>
                                    <option value="<?= $kantin['id_kantin']; ?>"><?= htmlspecialchars($kantin['nama_kantin']); ?>
                                        (<?= htmlspecialchars($kantin['pemilik']); ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php else: ?>
                        <input type="hidden" name="id_kantin" value="<?= htmlspecialchars($id_kantin_user); ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="nama">Nama Menu</label>
                        <input id="nama" type="text" name="nama" required>
                    </div>
                    <div class="form-group">
                        <label for="harga">Harga</label>
                        <input id="harga" type="number" name="harga" min="1000" required>
                    </div>
                    <div class="form-group">
                        <label for="stok">Stok Awal</label>
                        <input id="stok" type="number" name="stok" min="0" value="0" required>
                    </div>
                </div>

                <div id="tab_restock" class="tab-panel hidden">
                    <?php if (!empty($out_of_stock_items)): ?>
                        <div class="form-group">
                            <label for="menu_id">Pilih Menu Habis</label>
                            <select id="menu_id" name="menu_id" required>
                                <option value="">-- Pilih Menu --</option>
                                <?php foreach ($out_of_stock_items as $item): ?>
                                    <option value="<?= $item['id_menu']; ?>"><?= htmlspecialchars($item['nama_menu']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="restock_qty">Jumlah Tambah Stok</label>
                            <input id="restock_qty" type="number" name="restock_qty" min="1" value="1" required>
                        </div>
                    <?php else: ?>
                        <div style="padding: 20px; background: #f7f7f7; border-radius: 18px; color: #555; font-size: 13px;">
                            Tidak ada menu habis untuk diisi ulang saat ini.
                        </div>
                    <?php endif; ?>
                </div>

                <div style="display: flex; gap: 10px; justify-content: center; margin-top: 20px; flex-wrap: wrap;">
                    <button type="submit" name="simpan_menu" class="btn-tambah">Simpan</button>
                    <button type="button" class="btn-batal" onclick="closeMenuModal()">BATALKAN</button>
                </div>
            </form>
        </div>
    </div>

    <div id="rfidModal" class="modal">
        <div class="modal-content">
            <h3 style="color: var(--emerald-main); margin-bottom: 10px;">Scan Kartu RFID</h3>
            <p style="color: #888; font-size: 13px;">Tempelkan kartu RFID siswa pada reader</p>

            <div
                style="margin: 25px 0; padding: 20px; background: #fdfdfd; border: 1px solid #eee; border-radius: 20px;">
                <span style="font-size: 10px; color: #999; display: block; margin-bottom: 5px; font-weight: 700;">TOTAL
                    PEMBAYARAN</span>
                <span style="font-size: 28px; font-weight: 700; color: var(--emerald-dark);">Rp
                    <?= number_format($total, 0, ',', '.'); ?></span>
            </div>

            <form id="formRFID">
                <div class="scan-animation">
                    <div class="card-icon">💳</div>
                    <div class="scan-line"></div>
                </div>

                <input type="text" name="rfid_uid" id="rfid_input" class="rfid-input-hidden" autocomplete="off"
                    required>
                <input type="hidden" name="total_bayar" value="<?= $total ?>">

                <div style="margin-top: 20px;">
                    <button type="button" onclick="closeModal()" class="btn-batal">
                        BATALKAN TRANSAKSI
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const rfidModal = document.getElementById("rfidModal");
        const menuModal = document.getElementById("menuModal");
        const rfidInput = document.getElementById("rfid_input");
        const activeTabButtons = document.querySelectorAll('.tab-button');

        function openModal() {
            rfidModal.style.display = "block";
            rfidInput.value = "";
            setTimeout(() => rfidInput.focus(), 200);
        }

        function closeModal() { rfidModal.style.display = "none"; }

        function openMenuModal() {
            menuModal.style.display = "block";
            document.getElementById('form_action').value = 'add_menu';
            switchTab('new-menu');
        }

        function closeMenuModal() { menuModal.style.display = "none"; }

        function switchTab(tabName) {
            activeTabButtons.forEach(button => {
                button.classList.toggle('active', button.dataset.tab === tabName);
            });
            document.querySelectorAll('.tab-panel').forEach(panel => {
                panel.classList.toggle('hidden', panel.id !== 'tab_' + tabName);
            });
            document.getElementById('form_action').value = tabName === 'restock' ? 'restock_menu' : 'add_menu';

            // Validasi untuk restock tab
            const submitBtn = document.querySelector('#menuForm button[type="submit"]');
            if (tabName === 'restock') {
                const menuSelect = document.getElementById('menu_id');
                if (menuSelect && menuSelect.options.length <= 1) { // Hanya option default
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Tidak Ada Menu Habis';
                } else {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Simpan';
                }
            } else {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Simpan';
            }
        }

        activeTabButtons.forEach(button => {
            button.addEventListener('click', () => switchTab(button.dataset.tab));
        });

        $('#menuForm').on('submit', function(e) {
            const activeTab = document.querySelector('.tab-button.active').dataset.tab;
            document.getElementById('form_action').value = activeTab === 'restock' ? 'restock_menu' : 'add_menu';

            if (activeTab === 'new-menu') {
                const nama = $('#nama').val().trim();
                const harga = parseInt($('#harga').val());
                const stok = parseInt($('#stok').val());
                const idKantin = $('#id_kantin').val() || '<?= $id_kantin_user ?>';

                if (!nama || harga < 1000 || stok < 0 || !idKantin) {
                    e.preventDefault();
                    alert('Lengkapi data menu dengan benar.');
                    return false;
                }
            } else if (activeTab === 'restock') {
                const menuId = $('#menu_id').val();
                const qty = parseInt($('#restock_qty').val());

                if (!menuId || qty < 1) {
                    e.preventDefault();
                    alert('Pilih menu dan jumlah stok yang valid.');
                    return false;
                }
            }
        });

        window.onclick = function (e) {
            if (e.target == rfidModal) closeModal();
            if (e.target == menuModal) closeMenuModal();
        }
    </script>
</body>

</html>