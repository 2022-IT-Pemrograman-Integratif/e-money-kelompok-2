-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 06, 2022 at 07:08 AM
-- Server version: 10.4.21-MariaDB
-- PHP Version: 8.0.11

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
-- Table structure for table `account`
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
-- Dumping data for table `account`
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
-- Table structure for table `account_m`
--

CREATE TABLE `account_m` (
  `id_user` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `account_m`
--

INSERT INTO `account_m` (`id_user`, `username`, `password`, `email`, `phone`, `date_created`, `date_updated`) VALUES
(1, 'regas', 'regas', '', '0810913', '2022-06-02 22:39:42', '2022-06-02 22:39:42'),
(2, 'kel2_dipidi', 'dipidi123', 'buskidi_ahh@buska.com', '00088811133322', '2022-06-03 22:58:45', '2022-06-03 22:58:45');

-- --------------------------------------------------------

--
-- Table structure for table `buy_m`
--

CREATE TABLE `buy_m` (
  `id` int(11) NOT NULL,
  `id_buyer` int(11) NOT NULL,
  `id_seller` int(11) NOT NULL,
  `id_item` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `emoney` varchar(255) NOT NULL,
  `status` int(11) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `buy_m`
--

INSERT INTO `buy_m` (`id`, `id_buyer`, `id_seller`, `id_item`, `amount`, `emoney`, `status`, `date_created`, `date_updated`) VALUES
(1, 2, 1, 1, 1, 'KCN Pay', 1, '2022-06-03 23:06:45', '2022-06-03 23:06:45'),
(2, 2, 1, 1, 1, 'KCN Pay', 1, '2022-06-04 00:34:12', '2022-06-04 00:34:12'),
(3, 2, 1, 1, 1, 'Buski Coins', 1, '2022-06-04 00:49:52', '2022-06-04 00:49:52'),
(9, 2, 1, 1, 1, 'CuanIND', 1, '2022-06-05 00:46:05', '2022-06-05 00:46:05'),
(10, 2, 1, 1, 1, 'KCN Pay', 1, '2022-06-05 02:12:01', '2022-06-05 02:12:01'),
(11, 2, 1, 1, 1, 'KCN Pay', 1, '2022-06-05 02:20:37', '2022-06-05 02:20:37'),
(12, 2, 1, 1, 1, 'Buski Coins', 1, '2022-06-05 02:26:48', '2022-06-05 02:26:48'),
(13, 2, 1, 1, 1, 'CuanIND', 1, '2022-06-05 02:32:00', '2022-06-05 02:32:00'),
(14, 2, 1, 1, 1, 'MoneyZ', 1, '2022-06-05 03:02:44', '2022-06-05 03:02:44'),
(15, 2, 1, 1, 1, 'KCN Pay', 1, '2022-06-05 05:03:51', '2022-06-05 05:03:51'),
(16, 2, 1, 1, 1, 'Buski Coins', 1, '2022-06-05 05:04:05', '2022-06-05 05:04:05'),
(17, 2, 1, 1, 1, 'PeacePay', 1, '2022-06-05 13:23:41', '2022-06-05 13:23:41'),
(18, 2, 1, 1, 1, 'PadPay', 1, '2022-06-05 14:07:24', '2022-06-05 14:07:24'),
(19, 2, 1, 1, 1, 'PayPhone', 1, '2022-06-05 16:45:04', '2022-06-05 16:45:04'),
(20, 2, 1, 1, 1, 'PayFresh', 1, '2022-06-05 20:26:18', '2022-06-05 20:26:18'),
(21, 2, 1, 1, 1, 'PayFresh', 1, '2022-06-06 00:01:05', '2022-06-06 00:01:05'),
(22, 2, 1, 1, 1, 'PayFresh', 1, '2022-06-06 00:02:23', '2022-06-06 00:02:23'),
(23, 2, 1, 1, 1, 'PayFresh', 1, '2022-06-06 00:03:01', '2022-06-06 00:03:01'),
(24, 2, 1, 1, 1, 'PayFresh', 1, '2022-06-06 00:05:09', '2022-06-06 00:05:09'),
(25, 2, 1, 1, 1, 'PayFresh', 1, '2022-06-06 00:06:04', '2022-06-06 00:06:04');

-- --------------------------------------------------------

--
-- Table structure for table `detail_transaction`
--

CREATE TABLE `detail_transaction` (
  `detail_id` int(11) NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `detail_item` varchar(100) NOT NULL,
  `detail_price` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `detail_transaction`
--

INSERT INTO `detail_transaction` (`detail_id`, `transaction_id`, `detail_item`, `detail_price`) VALUES
(1, 1, 'Bakso', 5000),
(2, 1, 'es teh', 7000),
(3, 2, 'tas', 15000);

-- --------------------------------------------------------

--
-- Table structure for table `history`
--

CREATE TABLE `history` (
  `history_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `reciever_id` int(11) NOT NULL,
  `history_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unknown',
  `history_amount` int(11) DEFAULT NULL,
  `history_description` text COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '\'no description added \'',
  `history_timedate` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `item_m`
--

CREATE TABLE `item_m` (
  `id` int(11) NOT NULL,
  `id_seller` int(11) NOT NULL,
  `itemname` varchar(255) NOT NULL,
  `stock` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `item_m`
--

INSERT INTO `item_m` (`id`, `id_seller`, `itemname`, `stock`, `price`, `date_created`, `date_updated`) VALUES
(1, 1, 'keyboard biru', 91, 1000, '2022-06-02 22:44:02', '2022-06-02 22:44:02'),
(2, 1, 'keyboard merah', 10, 1000, '2022-06-02 23:37:02', '2022-06-02 23:37:02');

-- --------------------------------------------------------

--
-- Table structure for table `transaction`
--

CREATE TABLE `transaction` (
  `transaction_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `transaction_marketplace` varchar(100) NOT NULL,
  `transaction_timedate` datetime NOT NULL DEFAULT current_timestamp(),
  `transaction_totalprice` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `transaction`
--

INSERT INTO `transaction` (`transaction_id`, `account_id`, `transaction_marketplace`, `transaction_timedate`, `transaction_totalprice`) VALUES
(1, 2, 'pasar senen', '2022-04-12 10:32:31', 12000),
(2, 4, 'Tunjungan Plaza', '2022-04-12 10:32:31', 15000);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account`
--
ALTER TABLE `account`
  ADD PRIMARY KEY (`account_id`),
  ADD UNIQUE KEY `account_username` (`account_username`),
  ADD UNIQUE KEY `account_password` (`account_password`),
  ADD UNIQUE KEY `account_pin` (`account_pin`),
  ADD UNIQUE KEY `nomer_hp` (`nomer_hp`);

--
-- Indexes for table `account_m`
--
ALTER TABLE `account_m`
  ADD PRIMARY KEY (`id_user`);

--
-- Indexes for table `buy_m`
--
ALTER TABLE `buy_m`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `detail_transaction`
--
ALTER TABLE `detail_transaction`
  ADD PRIMARY KEY (`detail_id`),
  ADD KEY `transcation_id` (`transaction_id`);

--
-- Indexes for table `history`
--
ALTER TABLE `history`
  ADD PRIMARY KEY (`history_id`);

--
-- Indexes for table `item_m`
--
ALTER TABLE `item_m`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transaction`
--
ALTER TABLE `transaction`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `account_id` (`account_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `account`
--
ALTER TABLE `account`
  MODIFY `account_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `account_m`
--
ALTER TABLE `account_m`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `buy_m`
--
ALTER TABLE `buy_m`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `detail_transaction`
--
ALTER TABLE `detail_transaction`
  MODIFY `detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `history`
--
ALTER TABLE `history`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `item_m`
--
ALTER TABLE `item_m`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `transaction`
--
ALTER TABLE `transaction`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_transaction`
--
ALTER TABLE `detail_transaction`
  ADD CONSTRAINT `transcation_id` FOREIGN KEY (`transaction_id`) REFERENCES `transaction` (`transaction_id`);

--
-- Constraints for table `transaction`
--
ALTER TABLE `transaction`
  ADD CONSTRAINT `account_id` FOREIGN KEY (`account_id`) REFERENCES `account` (`account_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
