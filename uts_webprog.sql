-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 25 Okt 2024 pada 11.22
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `uts_webprog`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `tasks`
--

CREATE TABLE `tasks` (
  `taskid` int(11) NOT NULL,
  `id` int(11) NOT NULL,
  `task` varchar(100) NOT NULL,
  `done` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tasks`
--

INSERT INTO `tasks` (`taskid`, `id`, `task`, `done`) VALUES
(21, 5, 'a', 1),
(23, 4, 'aku adalah', 0),
(25, 4, 'task description 1', 0),
(54, 5, 'ea', 1),
(56, 5, 'maknyus', 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `user_info`
--

CREATE TABLE `user_info` (
  `id` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `user_info`
--

INSERT INTO `user_info` (`id`, `email`, `username`, `password`) VALUES
(1, 'michaelej2005@gmail.com', 'michael', '$2y$10$8XqMea/drdeRQo5G9tfByON6WQzixb16Ho1PrG'),
(2, 'michaelej2005@gmail.com', 'michael_elbert', '$2y$10$MyJnLZ04LBMnzAA/IEnCVuDXZ4FH3frWhT7B8wBuNqqmj5.Hg6h5e'),
(4, 'michaelej2005@gmail.com', 'okjek', '$2y$10$vVaqHSq3xTFZBEFnhXpwJeQwB7Yfwm4OIUKmQZ2zSAdkicHBa04gi'),
(5, 'michaelej2005@gmail.com', 'tmg', '$2y$10$vBOX/am.o589JITLNj6Yb.SCPdzgV8.W.C8L20NZP4Wzf9/l8TNiy');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`taskid`),
  ADD KEY `id` (`id`);

--
-- Indeks untuk tabel `user_info`
--
ALTER TABLE `user_info`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `tasks`
--
ALTER TABLE `tasks`
  MODIFY `taskid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT untuk tabel `user_info`
--
ALTER TABLE `user_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `id` FOREIGN KEY (`id`) REFERENCES `user_info` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
