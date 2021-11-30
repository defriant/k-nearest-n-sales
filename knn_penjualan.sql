-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 30 Nov 2021 pada 14.35
-- Versi server: 10.4.17-MariaDB
-- Versi PHP: 8.0.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `knn_data_penjualan`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `data_penjualan`
--

CREATE TABLE `data_penjualan` (
  `id` bigint(20) NOT NULL,
  `periode` date NOT NULL,
  `stok_awal` int(11) NOT NULL,
  `stok_akhir` int(11) NOT NULL,
  `terjual` int(11) NOT NULL,
  `pendapatan` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `data_penjualan`
--

INSERT INTO `data_penjualan` (`id`, `periode`, `stok_awal`, `stok_akhir`, `terjual`, `pendapatan`, `created_at`, `updated_at`) VALUES
(1, '2020-01-01', 367, 53, 314, 15282000, '2021-11-27 11:33:33', '2021-11-27 11:33:33'),
(2, '2020-02-01', 339, 47, 292, 15136000, '2021-11-27 11:53:35', '2021-11-27 11:53:35'),
(3, '2020-03-01', 257, 59, 198, 12481000, '2021-11-27 11:53:54', '2021-11-27 11:53:54'),
(4, '2020-04-01', 253, 82, 172, 9335000, '2021-11-27 11:54:29', '2021-11-27 11:54:29'),
(5, '2020-05-01', 199, 68, 132, 7194000, '2021-11-27 11:54:54', '2021-11-27 11:54:54'),
(6, '2020-06-01', 254, 56, 198, 11660000, '2021-11-27 11:55:18', '2021-11-27 11:55:18'),
(7, '2020-07-01', 247, 65, 182, 11398000, '2021-11-27 11:56:21', '2021-11-27 11:56:21'),
(8, '2020-08-01', 241, 79, 162, 15807000, '2021-11-27 11:56:40', '2021-11-27 11:56:40'),
(9, '2020-09-01', 270, 70, 200, 19379000, '2021-11-27 11:57:01', '2021-11-27 11:57:01'),
(10, '2020-10-01', 257, 80, 177, 15522000, '2021-11-27 11:57:31', '2021-11-27 11:57:31'),
(11, '2020-11-01', 228, 76, 153, 15567000, '2021-11-27 11:57:49', '2021-11-27 11:57:49'),
(12, '2020-12-01', 193, 48, 146, 15319000, '2021-11-27 11:58:10', '2021-11-27 11:58:10');

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2019_12_14_000001_create_personal_access_tokens_table', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin', NULL, '$2y$10$psCiN2Wy7U9sb5Oyx12z../kk45e/fSiesdP8dp8eQWhQd.EpRiIa', NULL, '2021-11-18 13:03:28', '2021-11-18 13:08:24');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `data_penjualan`
--
ALTER TABLE `data_penjualan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
