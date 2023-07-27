-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 27, 2023 at 02:36 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pupuk-main`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `alamat` varchar(100) DEFAULT NULL,
  `id_pengguna` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `nama_lengkap`, `alamat`, `id_pengguna`) VALUES
(1, 'Administrator', 'Tabanan', 1);

-- --------------------------------------------------------

--
-- Table structure for table `detail_transaksi`
--

CREATE TABLE `detail_transaksi` (
  `id` int(11) NOT NULL,
  `id_transaksi` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `kuantitas` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `detail_transaksi`
--

INSERT INTO `detail_transaksi` (`id`, `id_transaksi`, `id_produk`, `kuantitas`) VALUES
(4, 4, 1, 10),
(5, 5, 2, 2),
(6, 5, 1, 3);

-- --------------------------------------------------------

--
-- Table structure for table `pengguna`
--

CREATE TABLE `pengguna` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `kata_sandi` varchar(255) NOT NULL,
  `role` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `pengguna`
--

INSERT INTO `pengguna` (`id`, `username`, `kata_sandi`, `role`) VALUES
(1, 'admin', '$2y$10$G5XmBX7jYHzOTBs1UPan5OQVVXsHBPNmXRbMvOgRvjij0lzuYsJjy', 1),
(6, 'yogi', '$2y$10$ntIiPBd5BP.Ww73jx4yR4Oht7OTu4v5Db7UGuhDeTfw0qcBu4o99C', 0),
(7, 'komang', '$2y$10$icK18Clv0fOQiv34Vy9sIeK1qfJsp0yJditjXif0YbS2osaUp/yoK', 0);

-- --------------------------------------------------------

--
-- Table structure for table `petani`
--

CREATE TABLE `petani` (
  `id` int(11) NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `alamat` varchar(100) DEFAULT NULL,
  `id_pengguna` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `petani`
--

INSERT INTO `petani` (`id`, `nama_lengkap`, `alamat`, `id_pengguna`) VALUES
(3, 'Yogi Prayoga', 'Tabanan', 6),
(4, 'Komang', 'Tabanan', 7);

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id` int(11) NOT NULL,
  `nama_produk` varchar(255) NOT NULL,
  `gambar_produk` varchar(255) NOT NULL,
  `jumlah_produk_kg` int(11) NOT NULL,
  `harga_produk` int(11) NOT NULL,
  `harga_produk_asli` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id`, `nama_produk`, `gambar_produk`, `jumlah_produk_kg`, `harga_produk`, `harga_produk_asli`) VALUES
(1, 'Pupuk Organik', 'https://storage.googleapis.com/pkg-portal-bucket/images/news/2019-03/pupuk-organik-demi-keberlanjutan-pertanian/pg_pupuk-petroganik.jpg', 96, 10000, 9000),
(2, 'Pupuk Kompos', 'https://siplahtelkom.com/public/products/130908/3903409/pupuk-kompos-or.1669105490.jpg', 98, 15000, 13000);

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id` int(11) NOT NULL,
  `id_pengguna` int(11) NOT NULL,
  `tanggal_transaksi` date NOT NULL,
  `total` int(11) NOT NULL,
  `is_done` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id`, `id_pengguna`, `tanggal_transaksi`, `total`, `is_done`) VALUES
(4, 6, '2023-07-15', 100000, 1),
(5, 6, '2023-07-26', 60000, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_admin_pengguna` (`id_pengguna`);

--
-- Indexes for table `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_detail_transaksi_transaksi` (`id_transaksi`),
  ADD KEY `fk_detail_transaksi_produk` (`id_produk`);

--
-- Indexes for table `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `petani`
--
ALTER TABLE `petani`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_petani_pengguna` (`id_pengguna`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_transaksi_pengguna` (`id_pengguna`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `pengguna`
--
ALTER TABLE `pengguna`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `petani`
--
ALTER TABLE `petani`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `fk_admin_pengguna` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  ADD CONSTRAINT `fk_detail_transaksi_produk` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_detail_transaksi_transaksi` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `petani`
--
ALTER TABLE `petani`
  ADD CONSTRAINT `fk_petani_pengguna` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `fk_transaksi_pengguna` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
