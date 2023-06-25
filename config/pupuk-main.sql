-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 25, 2023 at 01:46 PM
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
(6, 4, 2, 5),
(7, 4, 6, 200),
(8, 5, 2, 10),
(9, 6, 2, 5),
(10, 7, 2, 5),
(11, 8, 2, 3),
(12, 8, 6, 2),
(13, 9, 2, 5),
(14, 9, 6, 2),
(15, 10, 2, 9),
(16, 10, 7, 10),
(17, 11, 2, 5),
(18, 12, 2, 4),
(19, 12, 7, 5);

-- --------------------------------------------------------

--
-- Table structure for table `pembelian`
--

CREATE TABLE `pembelian` (
  `id` int(11) NOT NULL,
  `id_pengguna` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `jumlah_beli_produk` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `pembelian`
--

INSERT INTO `pembelian` (`id`, `id_pengguna`, `id_produk`, `jumlah_beli_produk`) VALUES
(5, 2, 1, 15),
(8, 2, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `pengguna`
--

CREATE TABLE `pengguna` (
  `id` int(11) NOT NULL,
  `nama_lengkap` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `kata_sandi` varchar(255) NOT NULL,
  `role` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `pengguna`
--

INSERT INTO `pengguna` (`id`, `nama_lengkap`, `username`, `kata_sandi`, `role`) VALUES
(1, 'Administrator', 'admin', '$2y$10$jsr4VgXDvaJ/etib7jgA6eAlMTtkoVat0q6n/6cf8U9DSo6Kpux9e', 1),
(2, 'konsumen2', 'konsumen', '$2y$10$jsr4VgXDvaJ/etib7jgA6eAlMTtkoVat0q6n/6cf8U9DSo6Kpux9e', 0),
(5, 'Konsumen100', 'konsumen100', '$2y$10$jsr4VgXDvaJ/etib7jgA6eAlMTtkoVat0q6n/6cf8U9DSo6Kpux9e', 0),
(6, 'Konsumen1012', 'konsumen1012', '$2y$10$jsr4VgXDvaJ/etib7jgA6eAlMTtkoVat0q6n/6cf8U9DSo6Kpux9e', 0),
(8, 'Yogi Prayoga', 'yogi', '$2y$10$WijMAHzP2sAF0xHgGBL/UeiE/pu.b3YJirdjhNImOS30m6uY5WscW', 0),
(9, 'Ngurah', 'ngurah', '$2y$10$356ErTzAiayWOMI1aA2ce./WbRjE/m1urJN3bzE.CnGujblHJnMM2', 0);

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id` int(11) NOT NULL,
  `nama_produk` varchar(255) NOT NULL,
  `gambar_produk` varchar(255) NOT NULL,
  `jumlah_produk_kg` int(11) NOT NULL,
  `harga_produk` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id`, `nama_produk`, `gambar_produk`, `jumlah_produk_kg`, `harga_produk`) VALUES
(2, 'Pupuk Urea - Pak Tani', 'https://img.my-best.id/press_component/item_part_images/e8afa391bf00b2b0516b01c0d1db6870.jpg?ixlib=rails-4.2.0&q=70&lossless=0&w=640&h=640&fit=clip', 23, 70000),
(6, 'Pupuk Organik', 'https://image1ws.indotrading.com/s3/productimages/webp/co250953/p1113557/w425-h425/95683cf0-8de7-4364-a4c7-a10522c0b54a.jpg', 4, 15000),
(7, 'Pupuk Tanah', 'https://storage.googleapis.com/pkg-portal-bucket/images/product/_productThumb/PG_Phonska-151012_2021.png', 90, 10000);

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
(4, 8, '2023-06-23', 85000, 1),
(5, 8, '2023-06-23', 70000, 1),
(6, 8, '2023-06-23', 70000, 1),
(7, 8, '2023-06-23', 70000, 1),
(8, 8, '2023-06-23', 85000, 1),
(9, 8, '2023-06-25', 380000, 1),
(10, 8, '2023-06-25', 730000, 1),
(11, 9, '2023-06-25', 350000, 1),
(12, 9, '2023-06-25', 0, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pembelian`
--
ALTER TABLE `pembelian`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `pembelian`
--
ALTER TABLE `pembelian`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `pengguna`
--
ALTER TABLE `pengguna`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
