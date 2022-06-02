-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 02 Jun 2022 pada 10.25
-- Versi server: 10.4.21-MariaDB
-- Versi PHP: 8.0.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pemrograman_integratif`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `account`
--

CREATE TABLE `account` (
  `account_id` int(11) NOT NULL,
  `account_username` varchar(100) NOT NULL,
  `account_password` varchar(100) NOT NULL,
  `account_pin` int(11) NOT NULL,
  `nomer_hp` varchar(13) DEFAULT NULL,
  `account_money` int(11) NOT NULL DEFAULT 0,
  `account_role` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `account`
--

INSERT INTO `account` (`account_id`, `account_username`, `account_password`, `account_pin`, `nomer_hp`, `account_money`, `account_role`) VALUES
(1, 'admin', 'admin', 123, '081111111111', 0, 1),
(2, 'oke', 'oke', 321, '081111111112', 0, 0),
(4, 'coba', 'coba', 3211, '081111111113', 0, 0),
(8, 'amreganteng', 'amregresik', 364858, '081111111114', 0, 0),
(9, 'ahha', 'ahsiap', 111, '081111111115', 0, 0),
(10, 'pemrograman integratif', 'hihihi', 2147483647, '081111111116', 0, 0),
(11, 'asd', 'asd', 1233, '081111111119', 1000, 0),
(12, 'bisa', 'bisa', 12334, '081111111120', 3000, 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `account_m`
--

CREATE TABLE `account_m` (
  `id_user` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `account_m`
--

INSERT INTO `account_m` (`id_user`, `username`, `password`, `phone`, `date_created`, `date_updated`) VALUES
(1, 'rega', 'rega', '0810912', '2022-06-02 14:42:35', '2022-06-02 14:42:35'),
(2, 'regas', 'regas', '0810913', '2022-06-02 14:52:02', '2022-06-02 14:52:02');

-- --------------------------------------------------------

--
-- Struktur dari tabel `buy_m`
--

CREATE TABLE `buy_m` (
  `id` int(11) NOT NULL,
  `id_buyer` int(11) NOT NULL,
  `id_seller` int(11) NOT NULL,
  `id_item` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_transaction`
--

CREATE TABLE `detail_transaction` (
  `detail_id` int(11) NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `detail_item` varchar(100) NOT NULL,
  `detail_price` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `detail_transaction`
--

INSERT INTO `detail_transaction` (`detail_id`, `transaction_id`, `detail_item`, `detail_price`) VALUES
(1, 1, 'Bakso', 5000),
(2, 1, 'es teh', 7000),
(3, 2, 'tas', 15000);

-- --------------------------------------------------------

--
-- Struktur dari tabel `item_m`
--

CREATE TABLE `item_m` (
  `id` int(11) NOT NULL,
  `id_seller` int(11) NOT NULL,
  `itemname` varchar(255) NOT NULL,
  `price` int(11) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `item_m`
--

INSERT INTO `item_m` (`id`, `id_seller`, `itemname`, `price`, `date_created`, `date_updated`) VALUES
(1, 1, 'keyboard merah', 1000, '2022-06-02 14:50:50', '2022-06-02 14:50:50'),
(2, 1, 'keyboard biru', 1000, '2022-06-02 15:20:40', '2022-06-02 15:20:40');

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaction`
--

CREATE TABLE `transaction` (
  `transaction_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `transaction_marketplace` varchar(100) NOT NULL,
  `transaction_timedate` datetime NOT NULL DEFAULT current_timestamp(),
  `transaction_totalprice` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `transaction`
--

INSERT INTO `transaction` (`transaction_id`, `account_id`, `transaction_marketplace`, `transaction_timedate`, `transaction_totalprice`) VALUES
(1, 2, 'pasar senen', '2022-04-12 10:32:31', 12000),
(2, 4, 'Tunjungan Plaza', '2022-04-12 10:32:31', 15000);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `account`
--
ALTER TABLE `account`
  ADD PRIMARY KEY (`account_id`),
  ADD UNIQUE KEY `account_username` (`account_username`),
  ADD UNIQUE KEY `account_password` (`account_password`),
  ADD UNIQUE KEY `account_pin` (`account_pin`),
  ADD UNIQUE KEY `nomer_hp` (`nomer_hp`);

--
-- Indeks untuk tabel `account_m`
--
ALTER TABLE `account_m`
  ADD PRIMARY KEY (`id_user`);

--
-- Indeks untuk tabel `buy_m`
--
ALTER TABLE `buy_m`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `detail_transaction`
--
ALTER TABLE `detail_transaction`
  ADD PRIMARY KEY (`detail_id`),
  ADD KEY `transcation_id` (`transaction_id`);

--
-- Indeks untuk tabel `item_m`
--
ALTER TABLE `item_m`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `transaction`
--
ALTER TABLE `transaction`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `account_id` (`account_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `account`
--
ALTER TABLE `account`
  MODIFY `account_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `account_m`
--
ALTER TABLE `account_m`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `buy_m`
--
ALTER TABLE `buy_m`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `detail_transaction`
--
ALTER TABLE `detail_transaction`
  MODIFY `detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `item_m`
--
ALTER TABLE `item_m`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `transaction`
--
ALTER TABLE `transaction`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `detail_transaction`
--
ALTER TABLE `detail_transaction`
  ADD CONSTRAINT `transcation_id` FOREIGN KEY (`transaction_id`) REFERENCES `transaction` (`transaction_id`);

--
-- Ketidakleluasaan untuk tabel `transaction`
--
ALTER TABLE `transaction`
  ADD CONSTRAINT `account_id` FOREIGN KEY (`account_id`) REFERENCES `account` (`account_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
