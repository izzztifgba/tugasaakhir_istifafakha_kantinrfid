-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 09, 2026 at 12:17 PM
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
-- Table structure for table `menu_istifafakha`
--

CREATE TABLE `menu_istifafakha` (
  `id_menu` int(11) NOT NULL,
  `nama_makanan` varchar(100) NOT NULL,
  `harga` int(11) NOT NULL,
  `stok` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_istifafakha`
--

INSERT INTO `menu_istifafakha` (`id_menu`, `nama_makanan`, `harga`, `stok`) VALUES
(1, 'Nasi Goreng', 10000, 20),
(2, 'Es Jeruk', 5000, 30),
(3, 'Ayam Geprek', 15000, 25),
(4, 'Mie Ayam', 12000, 20),
(5, 'Bakso Mercon', 15000, 15),
(6, 'Soto Ayam', 13000, 20),
(7, 'Batagor', 8000, 30),
(8, 'Es Teh Manis', 3000, 100),
(9, 'Jus Alpukat', 10000, 15),
(10, 'Air Mineral', 4000, 50);

-- --------------------------------------------------------

--
-- Table structure for table `petugas_istifafakha`
--

CREATE TABLE `petugas_istifafakha` (
  `id_petugas` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `PASSWORD` varchar(255) NOT NULL,
  `nama_petugas` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `petugas_istifafakha`
--

INSERT INTO `petugas_istifafakha` (`id_petugas`, `username`, `PASSWORD`, `nama_petugas`) VALUES
(1, 'admin', 'admin123', 'Istifafakha');

-- --------------------------------------------------------

--
-- Table structure for table `siswa_istifafakha`
--

CREATE TABLE `siswa_istifafakha` (
  `rfid_uid` varchar(20) NOT NULL,
  `nama_siswa` varchar(100) NOT NULL,
  `kelas` varchar(20) DEFAULT NULL,
  `saldo` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `siswa_istifafakha`
--

INSERT INTO `siswa_istifafakha` (`rfid_uid`, `nama_siswa`, `kelas`, `saldo`, `created_at`) VALUES
('123ABC456', 'Rafly Rizki', 'XI PPLG B', 50000, '2026-02-09 08:45:18'),
('220ABC095', 'Adinda Nafura', 'X DKV B', 100000, '2026-02-09 08:54:15'),
('666ABC243', 'Reflis Aditya', 'XI PPLG A', 50000, '2026-02-09 08:52:33');

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

-- --------------------------------------------------------

--
-- Table structure for table `transaksi_istifafakha`
--

CREATE TABLE `transaksi_istifafakha` (
  `id_transaksi` int(11) NOT NULL,
  `rfid_uid` varchar(20) DEFAULT NULL,
  `id_menu` int(11) DEFAULT NULL,
  `id_petugas` int(11) DEFAULT NULL,
  `total_bayar` int(11) NOT NULL,
  `waktu` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `menu_istifafakha`
--
ALTER TABLE `menu_istifafakha`
  ADD PRIMARY KEY (`id_menu`);

--
-- Indexes for table `petugas_istifafakha`
--
ALTER TABLE `petugas_istifafakha`
  ADD PRIMARY KEY (`id_petugas`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `siswa_istifafakha`
--
ALTER TABLE `siswa_istifafakha`
  ADD PRIMARY KEY (`rfid_uid`);

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
  ADD KEY `fk_trans_menu` (`id_menu`),
  ADD KEY `fk_trans_petugas` (`id_petugas`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `menu_istifafakha`
--
ALTER TABLE `menu_istifafakha`
  MODIFY `id_menu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `petugas_istifafakha`
--
ALTER TABLE `petugas_istifafakha`
  MODIFY `id_petugas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `topup_istifafakha`
--
ALTER TABLE `topup_istifafakha`
  MODIFY `id_topup` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaksi_istifafakha`
--
ALTER TABLE `transaksi_istifafakha`
  MODIFY `id_transaksi` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `topup_istifafakha`
--
ALTER TABLE `topup_istifafakha`
  ADD CONSTRAINT `fk_topup_petugas` FOREIGN KEY (`id_petugas`) REFERENCES `petugas_istifafakha` (`id_petugas`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_topup_siswa` FOREIGN KEY (`rfid_uid`) REFERENCES `siswa_istifafakha` (`rfid_uid`) ON DELETE CASCADE;

--
-- Constraints for table `transaksi_istifafakha`
--
ALTER TABLE `transaksi_istifafakha`
  ADD CONSTRAINT `fk_trans_menu` FOREIGN KEY (`id_menu`) REFERENCES `menu_istifafakha` (`id_menu`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_trans_petugas` FOREIGN KEY (`id_petugas`) REFERENCES `petugas_istifafakha` (`id_petugas`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_trans_siswa` FOREIGN KEY (`rfid_uid`) REFERENCES `siswa_istifafakha` (`rfid_uid`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
