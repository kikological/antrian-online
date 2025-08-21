<?php
session_start();
include '../db/config.php'; // Pastikan ini menghubungkan ke DB dengan mysqli

if (isset($_GET['logo'])) {
    $logoKey = $_GET['logo'];

    // Validasi agar hanya key yang diizinkan
    $allowed_keys = ['logo', 'logo_2', 'logo_3', 'logo_4'];
    if (!in_array($logoKey, $allowed_keys)) {
        die("Permintaan tidak valid.");
    }

    // Ambil path logo dari database
    $query = "SELECT `$logoKey` FROM pengaturan_monitor LIMIT 1";
    $result = mysqli_query($conn, $query);
    if ($row = mysqli_fetch_assoc($result)) {
        $filePath = '../' . $row[$logoKey];

        // Hapus file jika ada
        if (!empty($filePath) && file_exists($filePath)) {
            unlink($filePath);
        }

        // Kosongkan kolom logo di DB
        $update = "UPDATE pengaturan_monitor SET `$logoKey` = ''";
        mysqli_query($conn, $update);
    }
}

header("Location: pengaturan_monitor"); // Kembali ke halaman pengaturan
exit;
