<?php
require '../session.php';
require '../db/config.php';

// Matikan laporan error ke pengguna
error_reporting(0);
ini_set('display_errors', 0);

if (!is_admin()) {
    header("Location: ../login.php");
    exit;
}

$id = $_GET['id'] ?? null;

if ($id) {
    // Hapus antrian yang berelasi terlebih dahulu
    $deleteAntrian = $conn->prepare("DELETE FROM antrian WHERE id_layanan = ?");
    if ($deleteAntrian) {
        $deleteAntrian->bind_param("i", $id);
        $deleteAntrian->execute();
        $deleteAntrian->close();
    }

    // Hapus relasi di tabel loket_layanan jika ada
    $deleteRelasi = $conn->prepare("DELETE FROM loket_layanan WHERE id_layanan = ?");
    if ($deleteRelasi) {
        $deleteRelasi->bind_param("i", $id);
        $deleteRelasi->execute();
        $deleteRelasi->close();
    }

    // Hapus layanan utama
    $stmt = $conn->prepare("DELETE FROM layanan WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
}

header("Location: layanan.php");
exit;
