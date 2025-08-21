<?php
require '../db/config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Metode tidak diizinkan']);
    exit;
}

$id_layanan = isset($_POST['id_layanan']) ? intval($_POST['id_layanan']) : null;
$loket = isset($_POST['loket']) ? intval($_POST['loket']) : null;

if (!$id_layanan || $loket === null) {
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
    exit;
}

// Ambil info layanan berdasarkan ID
$q_layanan = mysqli_query($conn, "SELECT id, kode_layanan, suara_mp3 FROM layanan WHERE id = $id_layanan LIMIT 1");
if (!$q_layanan || mysqli_num_rows($q_layanan) === 0) {
    echo json_encode(['success' => false, 'message' => 'Layanan tidak ditemukan']);
    exit;
}
$layanan = mysqli_fetch_assoc($q_layanan);

// Ambil antrian pertama yang menunggu untuk layanan ini
$q = mysqli_query($conn, "SELECT * FROM antrian WHERE id_layanan = $id_layanan AND status = 'menunggu' ORDER BY id ASC LIMIT 1");
$data = mysqli_fetch_assoc($q);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Tidak ada antrian tersedia']);
    exit;
}

// Update status antrian
$now = date('Y-m-d H:i:s');
mysqli_query($conn, "UPDATE antrian SET status = 'dipanggil', loket = $loket, waktu_panggil = '$now' WHERE id = {$data['id']}");

// Kirim respon JSON
echo json_encode([
    'success' => true,
    'kode' => $layanan['kode_layanan'],
    'nomor' => $data['nomor'],
    'loket' => $loket,
    'suara' => $layanan['suara_mp3'] ?? 'default.mp3'
]);
