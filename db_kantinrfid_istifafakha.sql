-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 28, 2026 at 03:54 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_kantinrfid_istifafakha`
--

-- --------------------------------------------------------

--
-- Table structure for table `detail_transaksi_istifafakha`
--

CREATE TABLE `detail_transaksi_istifafakha` (
  `id_detail` int(11) NOT NULL,
  `id_transaksi` int(11) NOT NULL,
  `id_menu` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `subtotal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_transaksi_istifafakha`
--

INSERT INTO `detail_transaksi_istifafakha` (`id_detail`, `id_transaksi`, `id_menu`, `qty`, `subtotal`) VALUES
(1, 30, 5, 1, 15000),
(2, 31, 3, 2, 30000),
(3, 31, 8, 4, 12000),
(4, 31, 1, 2, 20000),
(5, 31, 4, 3, 36000),
(6, 31, 9, 2, 20000),
(7, 32, 3, 1, 15000),
(8, 33, 3, 1, 15000),
(9, 33, 5, 1, 15000),
(10, 34, 3, 2, 30000),
(11, 35, 6, 1, 13000),
(12, 35, 4, 1, 12000);

-- --------------------------------------------------------

--
-- Table structure for table `kantin_istifafakha`
--

CREATE TABLE `kantin_istifafakha` (
  `id_kantin` int(11) NOT NULL,
  `nama_kantin` varchar(100) NOT NULL,
  `pemilik` varchar(100) DEFAULT NULL,
  `saldo_kantin` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kantin_istifafakha`
--

INSERT INTO `kantin_istifafakha` (`id_kantin`, `nama_kantin`, `pemilik`, `saldo_kantin`) VALUES
(1, 'Kantin_1', 'Ariza Fahrezi', 0),
(2, 'Kantin_2', 'Fara Warza', 25000),
(3, 'Kantin_3', 'Harfanisya', 75000),
(4, 'Kantin_4', 'Ghania Yarin', 0);

-- --------------------------------------------------------

--
-- Table structure for table `menu_istifafakha`
--

CREATE TABLE `menu_istifafakha` (
  `id_menu` int(11) NOT NULL,
  `nama_menu` varchar(100) NOT NULL,
  `harga` int(11) NOT NULL,
  `stok` int(11) DEFAULT 0,
  `id_kantin` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_istifafakha`
--

INSERT INTO `menu_istifafakha` (`id_menu`, `nama_menu`, `harga`, `stok`, `id_kantin`) VALUES
(1, 'Nasi Goreng', 10000, 5, 1),
(2, 'Es Jeruk', 5000, 27, 1),
(3, 'Ayam Geprek', 15000, 3, 1),
(4, 'Mie Ayam', 12000, 6, 2),
(5, 'Bakso Mercon', 15000, 12, 2),
(6, 'Soto Ayam', 13000, 18, 2),
(7, 'Batagor', 8000, 30, 3),
(8, 'Es Teh Manis', 3000, 96, 3),
(9, 'Jus Alpukat', 10000, 12, 3),
(10, 'Air Mineral', 4000, 47, 4);

-- --------------------------------------------------------

--
-- Table structure for table `siswa_istifafakha`
--

CREATE TABLE `siswa_istifafakha` (
  `id_siswa` int(11) NOT NULL,
  `rfid_uid` varchar(20) NOT NULL,
  `nama_siswa` varchar(100) NOT NULL,
  `kelas` varchar(20) DEFAULT NULL,
  `saldo` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `siswa_istifafakha`
--

INSERT INTO `siswa_istifafakha` (`id_siswa`, `rfid_uid`, `nama_siswa`, `kelas`, `saldo`, `created_at`) VALUES
(1, '0001461705', 'Adinda Nafura', 'X DKV B', 386000, '2026-02-09 08:54:15'),
(3, '666ABC243', 'Reflis Aditya', 'XI PPLG A', 70000, '2026-02-09 08:52:33'),
(5, '0001707462', 'Suparman', 'X DKV A', 750000, '2026-04-27 13:51:39');

-- --------------------------------------------------------

--
-- Table structure for table `tarik_tunai_istifafakha`
--

CREATE TABLE `tarik_tunai_istifafakha` (
  `id_tarik` int(11) NOT NULL,
  `rfid_uid` varchar(20) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL,
  `id_kantin` int(11) DEFAULT NULL,
  `nominal_tarik` int(11) DEFAULT NULL,
  `saldo_akhir` int(11) DEFAULT NULL,
  `tanggal_tarik` datetime DEFAULT NULL,
  `keterangan` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `topup_istifafakha`
--

CREATE TABLE `topup_istifafakha` (
  `id_topup` int(11) NOT NULL,
  `rfid_uid` varchar(20) DEFAULT NULL,
  `id_petugas` int(11) DEFAULT NULL,
  `jumlah_topup` int(11) NOT NULL,
  `waktu` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `topup_istifafakha`
--

INSERT INTO `topup_istifafakha` (`id_topup`, `rfid_uid`, `id_petugas`, `jumlah_topup`, `waktu`) VALUES
(3, '666ABC243', 1, 10000, '2026-04-15 04:20:52');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi_istifafakha`
--

CREATE TABLE `transaksi_istifafakha` (
  `id_transaksi` int(11) NOT NULL,
  `rfid_uid` varchar(20) DEFAULT NULL,
  `id_petugas` int(11) DEFAULT NULL,
  `total_bayar` int(11) NOT NULL,
  `waktu` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_kantin` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksi_istifafakha`
--

INSERT INTO `transaksi_istifafakha` (`id_transaksi`, `rfid_uid`, `id_petugas`, `total_bayar`, `waktu`, `id_kantin`) VALUES
(30, '0001461705', 3, 15000, '2026-04-23 06:22:12', NULL),
(31, '0001461705', 3, 118000, '2026-04-23 06:22:35', NULL),
(32, '0001461705', 4, 15000, '2026-04-23 06:26:20', NULL),
(33, '0001461705', 4, 30000, '2026-04-23 06:31:59', NULL),
(34, '0001707462', 4, 30000, '2026-04-28 11:48:31', 3),
(35, '0001461705', 3, 25000, '2026-04-28 11:55:26', 2);

-- --------------------------------------------------------

--
-- Table structure for table `user_istifafakha`
--

CREATE TABLE `user_istifafakha` (
  `id_user` int(11) NOT NULL,
  `id_kantin` int(11) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `no_telp` varchar(14) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','petugas') NOT NULL DEFAULT 'petugas',
  `nama_petugas` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_istifafakha`
--

INSERT INTO `user_istifafakha` (`id_user`, `id_kantin`, `username`, `no_telp`, `password`, `role`, `nama_petugas`) VALUES
(1, NULL, 'istifafakha', '081233973583', '202cb962ac59075b964b07152d234b70', 'admin', 'Istifafakha'),
(2, 1, 'kantin_1', '089526636495', '202cb962ac59075b964b07152d234b70', 'petugas', 'Ariza Fahrezi'),
(3, 2, 'kantin_2', '081255675342', '202cb962ac59075b964b07152d234b70', 'petugas', 'Fara Warza'),
(4, 3, 'kantin_3', '089654542342', '202cb962ac59075b964b07152d234b70', 'petugas', 'Harfanisya Harim'),
(5, 4, 'kantin_4', '089765253721', '202cb962ac59075b964b07152d234b70', 'petugas', 'Ghania Yarin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `detail_transaksi_istifafakha`
--
ALTER TABLE `detail_transaksi_istifafakha`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `fk_detail_transaksi` (`id_transaksi`),
  ADD KEY `fk_detail_menu` (`id_menu`);

--
-- Indexes for table `kantin_istifafakha`
--
ALTER TABLE `kantin_istifafakha`
  ADD PRIMARY KEY (`id_kantin`);

--
-- Indexes for table `menu_istifafakha`
--
ALTER TABLE `menu_istifafakha`
  ADD PRIMARY KEY (`id_menu`);

--
-- Indexes for table `siswa_istifafakha`
--
ALTER TABLE `siswa_istifafakha`
  ADD PRIMARY KEY (`id_siswa`),
  ADD UNIQUE KEY `rfid_uid` (`rfid_uid`);

--
-- Indexes for table `tarik_tunai_istifafakha`
--
ALTER TABLE `tarik_tunai_istifafakha`
  ADD PRIMARY KEY (`id_tarik`),
  ADD KEY `rfid_uid` (`rfid_uid`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `fk_tarik_kantin` (`id_kantin`);

--
-- Indexes for table `topup_istifafakha`
--
ALTER TABLE `topup_istifafakha`
  ADD PRIMARY KEY (`id_topup`),
  ADD KEY `fk_topup_siswa` (`rfid_uid`),
  ADD KEY `fk_topup_petugas` (`id_petugas`);

--
-- Indexes for table `transaksi_istifafakha`
--
ALTER TABLE `transaksi_istifafakha`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `fk_trans_siswa` (`rfid_uid`),
  ADD KEY `fk_trans_petugas` (`id_petugas`);

--
-- Indexes for table `user_istifafakha`
--
ALTER TABLE `user_istifafakha`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `fk_kantin_petugas` (`id_kantin`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `detail_transaksi_istifafakha`
--
ALTER TABLE `detail_transaksi_istifafakha`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `kantin_istifafakha`
--
ALTER TABLE `kantin_istifafakha`
  MODIFY `id_kantin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `menu_istifafakha`
--
ALTER TABLE `menu_istifafakha`
  MODIFY `id_menu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `siswa_istifafakha`
--
ALTER TABLE `siswa_istifafakha`
  MODIFY `id_siswa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tarik_tunai_istifafakha`
--
ALTER TABLE `tarik_tunai_istifafakha`
  MODIFY `id_tarik` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `topup_istifafakha`
--
ALTER TABLE `topup_istifafakha`
  MODIFY `id_topup` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `transaksi_istifafakha`
--
ALTER TABLE `transaksi_istifafakha`
  MODIFY `id_transaksi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `user_istifafakha`
--
ALTER TABLE `user_istifafakha`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_transaksi_istifafakha`
--
ALTER TABLE `detail_transaksi_istifafakha`
  ADD CONSTRAINT `fk_detail_menu` FOREIGN KEY (`id_menu`) REFERENCES `menu_istifafakha` (`id_menu`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_detail_transaksi` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi_istifafakha` (`id_transaksi`) ON DELETE CASCADE;

--
-- Constraints for table `tarik_tunai_istifafakha`
--
ALTER TABLE `tarik_tunai_istifafakha`
  ADD CONSTRAINT `fk_tarik_kantin` FOREIGN KEY (`id_kantin`) REFERENCES `kantin_istifafakha` (`id_kantin`) ON DELETE CASCADE,
  ADD CONSTRAINT `tarik_tunai_istifafakha_ibfk_1` FOREIGN KEY (`rfid_uid`) REFERENCES `siswa_istifafakha` (`rfid_uid`) ON DELETE CASCADE,
  ADD CONSTRAINT `tarik_tunai_istifafakha_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user_istifafakha` (`id_user`) ON DELETE CASCADE;

--
-- Constraints for table `topup_istifafakha`
--
ALTER TABLE `topup_istifafakha`
  ADD CONSTRAINT `fk_topup_petugas` FOREIGN KEY (`id_petugas`) REFERENCES `user_istifafakha` (`id_user`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_topup_siswa` FOREIGN KEY (`rfid_uid`) REFERENCES `siswa_istifafakha` (`rfid_uid`) ON DELETE CASCADE;

--
-- Constraints for table `transaksi_istifafakha`
--
ALTER TABLE `transaksi_istifafakha`
  ADD CONSTRAINT `fk_trans_petugas` FOREIGN KEY (`id_petugas`) REFERENCES `user_istifafakha` (`id_user`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_trans_siswa` FOREIGN KEY (`rfid_uid`) REFERENCES `siswa_istifafakha` (`rfid_uid`) ON DELETE CASCADE;

--
-- Constraints for table `user_istifafakha`
--
ALTER TABLE `user_istifafakha`
  ADD CONSTRAINT `fk_kantin_petugas` FOREIGN KEY (`id_kantin`) REFERENCES `kantin_istifafakha` (`id_kantin`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
