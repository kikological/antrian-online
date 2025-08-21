-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 27 Bulan Mei 2025 pada 18.26
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `antrianv2`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `antrian`
--

CREATE TABLE `antrian` (
  `id` int(11) NOT NULL,
  `id_layanan` int(11) NOT NULL,
  `nomor` int(11) NOT NULL,
  `status` enum('menunggu','dipanggil') DEFAULT 'menunggu',
  `waktu_ambil` datetime DEFAULT current_timestamp(),
  `waktu_panggil` datetime DEFAULT NULL,
  `loket` int(11) DEFAULT NULL,
  `recall` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `antrian`
--

INSERT INTO `antrian` (`id`, `id_layanan`, `nomor`, `status`, `waktu_ambil`, `waktu_panggil`, `loket`, `recall`) VALUES
(261, 1, 1, 'dipanggil', '2025-05-22 10:00:03', '2025-05-22 08:10:06', 2, 0),
(262, 22, 1, 'dipanggil', '2025-05-22 10:07:07', '2025-05-22 08:10:46', 3, 0),
(263, 22, 2, 'menunggu', '2025-05-22 10:07:45', NULL, NULL, 0),
(264, 22, 3, 'menunggu', '2025-05-22 10:13:21', NULL, NULL, 0),
(265, 1, 2, 'dipanggil', '2025-05-22 10:13:35', '2025-05-22 08:10:25', 2, 0),
(266, 22, 4, 'menunggu', '2025-05-22 10:13:38', NULL, NULL, 0),
(267, 1, 3, 'menunggu', '2025-05-22 10:14:57', NULL, NULL, 0),
(268, 22, 5, 'menunggu', '2025-05-22 10:16:11', NULL, NULL, 0),
(269, 1, 4, 'menunggu', '2025-05-22 10:20:49', NULL, NULL, 0),
(270, 22, 6, 'menunggu', '2025-05-22 10:25:27', NULL, NULL, 0),
(271, 1, 5, 'menunggu', '2025-05-22 10:27:00', NULL, NULL, 0),
(272, 1, 6, 'menunggu', '2025-05-22 10:29:02', NULL, NULL, 0),
(273, 22, 7, 'menunggu', '2025-05-22 10:29:16', NULL, NULL, 0),
(274, 23, 1, 'dipanggil', '2025-05-22 10:29:18', '2025-05-22 08:11:01', 4, 0),
(275, 24, 1, 'dipanggil', '2025-05-22 10:29:21', '2025-05-22 08:11:13', 5, 0),
(276, 25, 1, 'dipanggil', '2025-05-22 10:29:24', '2025-05-22 08:11:27', 6, 0),
(277, 22, 8, 'menunggu', '2025-05-22 10:30:45', NULL, NULL, 0),
(278, 22, 9, 'menunggu', '2025-05-22 10:31:16', NULL, NULL, 0),
(279, 1, 7, 'menunggu', '2025-05-22 10:32:20', NULL, NULL, 0),
(280, 23, 2, 'menunggu', '2025-05-22 10:33:16', NULL, NULL, 0),
(281, 22, 10, 'menunggu', '2025-05-22 10:34:27', NULL, NULL, 0),
(282, 25, 2, 'menunggu', '2025-05-22 10:34:37', NULL, NULL, 0),
(283, 22, 11, 'menunggu', '2025-05-22 10:35:46', NULL, NULL, 0),
(284, 1, 8, 'menunggu', '2025-05-22 10:38:36', NULL, NULL, 0),
(285, 22, 12, 'menunggu', '2025-05-22 10:40:23', NULL, NULL, 0),
(286, 22, 13, 'menunggu', '2025-05-22 10:41:10', NULL, NULL, 0),
(287, 1, 9, 'menunggu', '2025-05-22 10:42:03', NULL, NULL, 0),
(288, 1, 10, 'menunggu', '2025-05-22 10:45:30', NULL, NULL, 0),
(289, 22, 14, 'menunggu', '2025-05-22 10:49:20', NULL, NULL, 0),
(290, 23, 3, 'menunggu', '2025-05-22 10:55:42', NULL, NULL, 0),
(291, 25, 3, 'menunggu', '2025-05-22 10:57:23', NULL, NULL, 0),
(292, 24, 2, 'menunggu', '2025-05-22 10:57:56', NULL, NULL, 0),
(293, 23, 4, 'menunggu', '2025-05-22 10:58:48', NULL, NULL, 0),
(294, 25, 4, 'menunggu', '2025-05-22 11:00:55', NULL, NULL, 0),
(295, 25, 5, 'menunggu', '2025-05-22 11:01:12', NULL, NULL, 0),
(296, 25, 6, 'menunggu', '2025-05-22 11:01:37', NULL, NULL, 0),
(297, 22, 15, 'menunggu', '2025-05-22 11:01:56', NULL, NULL, 0),
(298, 1, 11, 'menunggu', '2025-05-22 11:02:59', NULL, NULL, 0),
(299, 1, 12, 'menunggu', '2025-05-22 11:03:21', NULL, NULL, 0),
(300, 1, 13, 'menunggu', '2025-05-22 11:04:28', NULL, NULL, 0),
(301, 25, 7, 'menunggu', '2025-05-22 11:10:10', NULL, NULL, 0),
(302, 22, 16, 'menunggu', '2025-05-22 11:28:11', NULL, NULL, 0),
(303, 1, 14, 'menunggu', '2025-05-22 13:09:18', NULL, NULL, 0),
(304, 22, 17, 'menunggu', '2025-05-22 13:09:22', NULL, NULL, 0),
(305, 27, 1, 'dipanggil', '2025-05-22 13:13:49', '2025-05-22 08:14:00', 2, 0),
(306, 22, 18, 'menunggu', '2025-05-27 23:01:34', NULL, NULL, 0),
(307, 22, 19, 'menunggu', '2025-05-27 23:10:42', NULL, NULL, 0),
(308, 1, 15, 'menunggu', '2025-05-27 23:10:47', NULL, NULL, 0),
(309, 27, 2, 'menunggu', '2025-05-27 23:10:57', NULL, NULL, 0),
(310, 1, 16, 'menunggu', '2025-05-27 23:11:19', NULL, NULL, 0),
(311, 22, 20, 'menunggu', '2025-05-27 23:11:21', NULL, NULL, 0),
(312, 23, 5, 'menunggu', '2025-05-27 23:11:21', NULL, NULL, 0),
(313, 24, 3, 'menunggu', '2025-05-27 23:11:21', NULL, NULL, 0),
(314, 27, 3, 'menunggu', '2025-05-27 23:11:22', NULL, NULL, 0),
(315, 25, 8, 'menunggu', '2025-05-27 23:11:22', NULL, NULL, 0),
(316, 27, 4, 'menunggu', '2025-05-27 23:11:30', NULL, NULL, 0),
(317, 22, 21, 'menunggu', '2025-05-27 23:13:47', NULL, NULL, 0),
(318, 23, 6, 'menunggu', '2025-05-27 23:13:49', NULL, NULL, 0),
(319, 24, 4, 'menunggu', '2025-05-27 23:13:50', NULL, NULL, 0),
(320, 22, 22, 'menunggu', '2025-05-27 23:14:47', NULL, NULL, 0),
(321, 27, 5, 'menunggu', '2025-05-27 23:15:01', NULL, NULL, 0),
(322, 27, 6, 'menunggu', '2025-05-27 23:15:42', NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `history_antrian`
--

CREATE TABLE `history_antrian` (
  `id` int(11) NOT NULL,
  `id_layanan` int(11) DEFAULT NULL,
  `nomor` varchar(10) DEFAULT NULL,
  `status` enum('menunggu','dipanggil') DEFAULT NULL,
  `waktu_ambil` datetime DEFAULT NULL,
  `waktu_panggil` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `history_antrian`
--

INSERT INTO `history_antrian` (`id`, `id_layanan`, `nomor`, `status`, `waktu_ambil`, `waktu_panggil`) VALUES
(1, 1, '1', 'dipanggil', '2025-05-21 20:37:57', '2025-05-21 15:40:06'),
(2, 1, '2', 'dipanggil', '2025-05-21 20:37:57', '2025-05-21 15:54:03'),
(3, 22, '1', 'dipanggil', '2025-05-21 20:37:58', '2025-05-21 15:38:09'),
(4, 22, '2', 'dipanggil', '2025-05-21 20:37:58', '2025-05-21 16:00:56'),
(5, 1, '3', 'dipanggil', '2025-05-21 20:53:59', '2025-05-21 16:00:42'),
(6, 22, '3', 'dipanggil', '2025-05-21 21:03:38', '2025-05-21 16:03:40'),
(7, 22, '4', 'menunggu', '2025-05-21 21:03:38', NULL),
(8, 1, '1', 'dipanggil', '2025-05-21 21:24:33', '2025-05-21 16:24:47'),
(9, 1, '2', 'dipanggil', '2025-05-21 21:24:33', '2025-05-21 16:24:52'),
(10, 22, '1', 'dipanggil', '2025-05-21 21:24:34', '2025-05-21 16:25:27'),
(11, 22, '2', 'dipanggil', '2025-05-21 21:24:34', '2025-05-21 16:25:40'),
(12, 23, '1', 'dipanggil', '2025-05-21 21:24:35', '2025-05-21 16:26:17'),
(13, 23, '2', 'dipanggil', '2025-05-21 21:24:35', '2025-05-21 16:26:55'),
(14, 24, '1', 'menunggu', '2025-05-21 21:24:35', NULL),
(15, 24, '2', 'menunggu', '2025-05-21 21:24:36', NULL),
(16, 25, '1', 'menunggu', '2025-05-21 21:24:37', NULL),
(17, 25, '2', 'menunggu', '2025-05-21 21:24:37', NULL),
(18, 1, '1', 'menunggu', '2025-05-21 21:32:03', NULL),
(19, 22, '1', 'menunggu', '2025-05-21 21:32:03', NULL),
(20, 23, '1', 'menunggu', '2025-05-21 21:32:04', NULL),
(21, 1, '1', 'dipanggil', '2025-05-21 22:03:35', '2025-05-21 17:03:37'),
(22, 1, '2', 'dipanggil', '2025-05-21 22:03:48', '2025-05-21 17:03:50'),
(23, 1, '3', 'dipanggil', '2025-05-21 22:05:36', '2025-05-21 17:05:38'),
(24, 1, '4', 'dipanggil', '2025-05-21 22:06:06', '2025-05-21 17:06:08'),
(25, 1, '5', 'dipanggil', '2025-05-21 22:24:28', '2025-05-21 17:24:30'),
(26, 1, '6', 'dipanggil', '2025-05-21 22:31:39', '2025-05-21 17:31:46'),
(27, 1, '7', 'dipanggil', '2025-05-21 22:31:39', '2025-05-21 17:31:59'),
(28, 1, '8', 'dipanggil', '2025-05-21 22:39:10', '2025-05-21 17:39:19'),
(29, 1, '9', 'dipanggil', '2025-05-21 22:39:11', '2025-05-21 17:39:33'),
(30, 1, '10', 'dipanggil', '2025-05-21 22:42:16', '2025-05-21 17:42:46'),
(31, 1, '11', 'dipanggil', '2025-05-21 22:42:16', '2025-05-21 17:44:00'),
(32, 1, '12', 'dipanggil', '2025-05-21 22:42:41', '2025-05-21 17:44:18'),
(33, 1, '13', 'dipanggil', '2025-05-21 22:42:42', '2025-05-21 17:44:36'),
(34, 1, '14', 'dipanggil', '2025-05-21 22:44:09', '2025-05-21 17:48:29'),
(35, 1, '15', 'dipanggil', '2025-05-21 22:44:09', '2025-05-21 17:48:44'),
(36, 1, '16', 'dipanggil', '2025-05-21 22:48:23', '2025-05-21 17:49:00'),
(37, 1, '17', 'dipanggil', '2025-05-21 22:48:24', '2025-05-21 17:49:28'),
(38, 1, '18', 'dipanggil', '2025-05-21 22:48:24', '2025-05-21 17:49:42'),
(39, 1, '19', 'dipanggil', '2025-05-21 22:48:38', '2025-05-21 17:49:59'),
(40, 1, '20', 'dipanggil', '2025-05-21 22:48:38', '2025-05-21 17:50:12'),
(41, 1, '21', 'dipanggil', '2025-05-21 22:48:38', '2025-05-21 17:50:23'),
(42, 1, '22', 'dipanggil', '2025-05-21 22:50:58', '2025-05-21 17:51:07'),
(43, 1, '23', 'dipanggil', '2025-05-21 22:50:58', '2025-05-21 17:51:21'),
(44, 1, '24', 'dipanggil', '2025-05-21 22:50:58', '2025-05-21 17:51:36'),
(45, 22, '1', 'dipanggil', '2025-05-21 22:52:51', '2025-05-21 17:52:55'),
(46, 22, '2', 'dipanggil', '2025-05-21 22:52:51', '2025-05-21 17:53:07'),
(47, 1, '1', 'dipanggil', '2025-05-22 07:38:14', '2025-05-22 02:39:06'),
(48, 22, '1', 'menunggu', '2025-05-22 07:38:15', NULL),
(49, 23, '1', 'menunggu', '2025-05-22 07:38:16', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `layanan`
--

CREATE TABLE `layanan` (
  `id` int(11) NOT NULL,
  `kode_layanan` varchar(5) NOT NULL,
  `nama_layanan` varchar(100) NOT NULL,
  `warna` varchar(20) DEFAULT '#cccccc',
  `suara_mp3` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `layanan`
--

INSERT INTO `layanan` (`id`, `kode_layanan`, `nama_layanan`, `warna`, `suara_mp3`) VALUES
(1, 'A', 'Poli Umum', '#6a18cd', 'poli_umum__1_.mp3'),
(22, 'B', 'Poli Khusus', '#71388f', 'poli_khusus.mp3'),
(23, 'C', 'Poli Gigi', '#8e0606', 'poli_gigi__1_.mp3'),
(24, 'D', 'Poli Gizi', '#85871d', 'poli_gizi__1_.mp3'),
(25, 'E', 'Poli Anak', '#a1ac11', 'poli_anak__1_.mp3'),
(27, 'F', 'Lansia', '#b41818', 'f.mp3');

-- --------------------------------------------------------

--
-- Struktur dari tabel `loket_layanan`
--

CREATE TABLE `loket_layanan` (
  `id` int(11) NOT NULL,
  `id_pengguna` int(11) NOT NULL,
  `id_layanan` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `loket_layanan`
--

INSERT INTO `loket_layanan` (`id`, `id_pengguna`, `id_layanan`) VALUES
(36, 3, 22),
(37, 4, 23),
(38, 5, 24),
(39, 6, 25),
(46, 2, 1),
(47, 2, 27);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengaturan_monitor`
--

CREATE TABLE `pengaturan_monitor` (
  `id` int(11) NOT NULL,
  `running_text` text DEFAULT NULL,
  `ukuran_teks` int(11) DEFAULT 24,
  `warna_card_cuaca` varchar(20) DEFAULT '''''''#000000''''''',
  `gradient_color_start` varchar(20) DEFAULT '''''''#000000''''''',
  `gradient_color_end` varchar(20) DEFAULT '''''''#ffffff''''''',
  `video_file` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `logo_2` varchar(255) DEFAULT NULL,
  `logo_3` varchar(255) DEFAULT NULL,
  `logo_4` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengaturan_monitor`
--

INSERT INTO `pengaturan_monitor` (`id`, `running_text`, `ukuran_teks`, `warna_card_cuaca`, `gradient_color_start`, `gradient_color_end`, `video_file`, `logo`, `logo_2`, `logo_3`, `logo_4`) VALUES
(1, 'SELAMAT DATANG DI ANTRIAN ABC', 24, '#d90808', '#646bce', '#291e80', 'uploads/1747890349_tes.mp4', 'uploads/1748356642_halflogo.png', 'uploads/1748358695_Biru Putih Kuning Modern Logo Sekolah.png', 'uploads/1748358695_tutwuri.png', 'uploads/1748358695_pemko.png');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengaturan_printer`
--

CREATE TABLE `pengaturan_printer` (
  `id` int(11) NOT NULL,
  `path_logo` varchar(255) DEFAULT NULL,
  `judul_cadangan` varchar(100) DEFAULT NULL,
  `alamat_header` text DEFAULT NULL,
  `label_antrian` varchar(100) DEFAULT 'NOMOR ANTRIAN',
  `teks_footer` text DEFAULT NULL,
  `aktif` tinyint(1) DEFAULT 1,
  `dibuat_pada` timestamp NULL DEFAULT current_timestamp(),
  `diperbarui_pada` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `nama_printer` varchar(100) DEFAULT 'RONGTA 58mm Series Printer'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengaturan_printer`
--

INSERT INTO `pengaturan_printer` (`id`, `path_logo`, `judul_cadangan`, `alamat_header`, `label_antrian`, `teks_footer`, `aktif`, `dibuat_pada`, `diperbarui_pada`, `nama_printer`) VALUES
(1, 'uploads/logo_1747884320.png', 'PUSKESMAS C', 'Jl. G.Obos, Palangka Raya\r\nKode Pos 73113', 'NOMOR ANTRIAN', 'Silahkan tunggu dipanggil\r\nTerima kasih', 1, '2025-04-05 07:27:14', '2025-05-22 03:25:20', 'RONGTA 58mm Series Printer');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengguna`
--

CREATE TABLE `pengguna` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','loket') DEFAULT 'loket'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengguna`
--

INSERT INTO `pengguna` (`id`, `username`, `nama`, `password`, `role`) VALUES
(1, 'admin', 'Administrator', '$2y$10$VzVVPgAFy30Xy4VeMsiJIu9hCwoySb.Ve5YCX29lGWKjPTG6vcoYS', 'admin'),
(2, 'loket1', 'Loket 1', '$2y$10$KlablRXH2yyUmuIs12B5l.QQ5lAvBGE9Slb.FsZ64UO3VPUWqB9zO', 'loket'),
(3, 'loket2', 'Loket 2', '$2y$10$uYJEiKgjXhK6b0jJUaWUCuczKKKaFlgIHi7nYTvz86HlALHEw9Bga', 'loket'),
(4, 'loket3', 'Loket 3', '$2y$10$Jp47HV8mZW5qn3u9flcLZuGiqJ1LU7.LkcrE/YmzkzbJjNXwHaf4G', 'loket'),
(5, 'loket4', 'Loket 4', '$2y$10$x75qmXfncXpKC4IdH0/c9OJR8zGncFpJZr5qwudulFEwTq4eSdk1C', 'loket'),
(6, 'loket5', 'Loket 5', '$2y$10$XbDsk70Yx/oZSq9Llct51uoQoANEeJ0bZXAn6adsaPo5t7839V1M6', 'loket');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `antrian`
--
ALTER TABLE `antrian`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_antrian_layanan` (`id_layanan`);

--
-- Indeks untuk tabel `history_antrian`
--
ALTER TABLE `history_antrian`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `layanan`
--
ALTER TABLE `layanan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `loket_layanan`
--
ALTER TABLE `loket_layanan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pengguna` (`id_pengguna`),
  ADD KEY `loket_layanan_ibfk_2` (`id_layanan`);

--
-- Indeks untuk tabel `pengaturan_monitor`
--
ALTER TABLE `pengaturan_monitor`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pengaturan_printer`
--
ALTER TABLE `pengaturan_printer`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `antrian`
--
ALTER TABLE `antrian`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=323;

--
-- AUTO_INCREMENT untuk tabel `history_antrian`
--
ALTER TABLE `history_antrian`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT untuk tabel `layanan`
--
ALTER TABLE `layanan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT untuk tabel `loket_layanan`
--
ALTER TABLE `loket_layanan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT untuk tabel `pengaturan_monitor`
--
ALTER TABLE `pengaturan_monitor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `pengaturan_printer`
--
ALTER TABLE `pengaturan_printer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `pengguna`
--
ALTER TABLE `pengguna`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `antrian`
--
ALTER TABLE `antrian`
  ADD CONSTRAINT `fk_antrian_layanan` FOREIGN KEY (`id_layanan`) REFERENCES `layanan` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `loket_layanan`
--
ALTER TABLE `loket_layanan`
  ADD CONSTRAINT `loket_layanan_ibfk_1` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id`),
  ADD CONSTRAINT `loket_layanan_ibfk_2` FOREIGN KEY (`id_layanan`) REFERENCES `layanan` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
