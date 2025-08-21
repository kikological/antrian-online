-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 29 Bulan Mei 2025 pada 13.05
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
(405, 1, 1, 'dipanggil', '2025-05-29 14:38:13', '2025-05-29 09:43:33', 2, 0),
(406, 1, 2, 'menunggu', '2025-05-29 14:38:34', NULL, NULL, 0),
(407, 29, 1, 'dipanggil', '2025-05-29 14:46:03', '2025-05-29 09:47:38', 6, 0),
(408, 29, 2, 'dipanggil', '2025-05-29 14:46:28', '2025-05-29 09:47:58', 6, 0),
(409, 22, 1, 'dipanggil', '2025-05-29 14:49:32', '2025-05-29 09:50:00', 3, 0),
(410, 23, 1, 'dipanggil', '2025-05-29 14:50:08', '2025-05-29 09:50:11', 4, 0),
(411, 24, 1, 'dipanggil', '2025-05-29 14:50:15', '2025-05-29 09:50:23', 5, 0);

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
(114, 1, '1', 'dipanggil', '2025-05-28 20:39:23', '2025-05-28 15:41:48'),
(115, 22, '1', 'dipanggil', '2025-05-28 20:39:27', '2025-05-28 15:42:01'),
(116, 23, '1', 'dipanggil', '2025-05-28 20:39:30', '2025-05-28 16:23:13'),
(117, 1, '2', 'dipanggil', '2025-05-28 21:18:40', '2025-05-28 16:21:38'),
(118, 1, '3', 'dipanggil', '2025-05-28 21:18:43', '2025-05-28 16:21:53'),
(119, 1, '4', 'dipanggil', '2025-05-28 21:20:49', '2025-05-28 16:22:05'),
(120, 1, '5', 'dipanggil', '2025-05-28 21:20:52', '2025-05-28 16:22:16'),
(121, 1, '6', 'menunggu', '2025-05-28 21:20:55', NULL),
(122, 1, '7', 'menunggu', '2025-05-28 21:21:08', NULL),
(123, 1, '8', 'menunggu', '2025-05-28 21:21:11', NULL),
(124, 1, '9', 'menunggu', '2025-05-28 21:21:13', NULL),
(125, 22, '2', 'dipanggil', '2025-05-28 21:22:36', '2025-05-28 16:22:44'),
(126, 22, '3', 'dipanggil', '2025-05-28 21:22:39', '2025-05-28 16:22:55'),
(127, 23, '2', 'menunggu', '2025-05-28 21:28:27', NULL),
(128, 24, '1', 'dipanggil', '2025-05-28 21:28:38', '2025-05-28 16:28:43'),
(129, 28, '1', 'dipanggil', '2025-05-28 21:30:12', '2025-05-28 16:30:32'),
(130, 22, '1', 'menunggu', '2025-05-28 22:13:21', NULL),
(131, 24, '1', 'menunggu', '2025-05-28 22:13:24', NULL),
(132, 22, '1', 'menunggu', '2025-05-29 22:14:30', NULL),
(133, 24, '1', 'menunggu', '2025-05-29 22:14:32', NULL),
(134, 22, '1', 'menunggu', '2025-05-27 22:15:10', NULL),
(135, 24, '1', 'menunggu', '2025-05-27 22:15:13', NULL),
(136, 28, '1', 'menunggu', '2025-05-26 22:15:15', NULL),
(137, 1, '1', 'menunggu', '2025-05-26 22:15:18', NULL),
(138, 22, '1', 'menunggu', '2025-05-25 22:16:21', NULL),
(139, 23, '1', 'menunggu', '2025-05-25 22:16:24', NULL),
(140, 28, '1', 'menunggu', '2025-05-24 22:16:26', NULL),
(141, 1, '1', 'menunggu', '2025-05-24 22:16:29', NULL),
(142, 24, '1', 'menunggu', '2025-05-23 22:16:32', NULL),
(143, 28, '2', 'menunggu', '2025-05-23 22:16:34', NULL),
(144, 23, '1', 'menunggu', '2025-05-22 22:17:39', NULL),
(145, 24, '1', 'menunggu', '2025-05-22 22:17:41', NULL),
(146, 22, '1', 'menunggu', '2025-05-22 22:17:44', NULL),
(147, 1, '1', 'dipanggil', '2025-05-28 22:58:12', '2025-05-28 17:58:37'),
(148, 22, '1', 'dipanggil', '2025-05-28 22:58:15', '2025-05-28 17:59:31'),
(149, 23, '1', 'dipanggil', '2025-05-28 22:58:18', '2025-05-28 18:00:05'),
(150, 24, '1', 'dipanggil', '2025-05-28 22:58:21', '2025-05-28 17:58:59'),
(151, 28, '1', 'dipanggil', '2025-05-28 22:58:23', '2025-05-28 18:00:33'),
(152, 22, '2', 'dipanggil', '2025-05-28 22:58:26', '2025-05-28 17:59:42'),
(153, 24, '2', 'dipanggil', '2025-05-28 22:58:28', '2025-05-28 17:59:12');

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
(1, 'A', 'Poli Umum', '#212c5f', 'poli_umum__1_.mp3'),
(22, 'B', 'Poli Khusus', '#ffc91b', 'poli_khusus.mp3'),
(23, 'C', 'Poli Gigi', '#263788', 'poli_gigi__1_.mp3'),
(24, 'D', 'Poli Gizi', '#c82a0e', 'poli_gizi__1_.mp3'),
(28, 'E', 'Poli Anak', '#152a8a', 'poli_anak__1_.mp3'),
(29, 'F', 'Lansia', '#695d5d', 'f.mp3');

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
(55, 2, 1),
(56, 2, 22),
(58, 6, 29);

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
(1, 'TITIKOMA ID; MENERIMA JASA PEMBUATAN WEB DAN APLIKASI - HUBUNGI 08558826121', 24, '#c89221', '#212c5f', '#070f32', 'https://youtu.be/onVhbeY7nLM?si=fj4aQrjSGGhsrh02', 'uploads/1748356642_halflogo.png', '', '', '');

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
  `nama_printer` varchar(100) DEFAULT 'RONGTA 58mm Series Printer',
  `mode_printer` enum('escpos','biasa') DEFAULT 'escpos'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengaturan_printer`
--

INSERT INTO `pengaturan_printer` (`id`, `path_logo`, `judul_cadangan`, `alamat_header`, `label_antrian`, `teks_footer`, `aktif`, `dibuat_pada`, `diperbarui_pada`, `nama_printer`, `mode_printer`) VALUES
(1, 'uploads/logo_1747884320.png', 'PUSKESMAS C', 'Jl. G.Obos, Palangka Raya\r\nKode Pos 73113', 'NOMOR ANTRIAN', 'Silahkan tunggu dipanggil\r\nTerima kasih', 1, '2025-04-05 07:27:14', '2025-05-29 07:49:53', 'RONGTA 58mm Series Printer', 'escpos');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=412;

--
-- AUTO_INCREMENT untuk tabel `history_antrian`
--
ALTER TABLE `history_antrian`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=154;

--
-- AUTO_INCREMENT untuk tabel `layanan`
--
ALTER TABLE `layanan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT untuk tabel `loket_layanan`
--
ALTER TABLE `loket_layanan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

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
