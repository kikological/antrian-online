<?php
require '../db/config.php';
header('Content-Type: application/json');

// Ambil parameter dari URL
$id_layanan = $_GET['id_layanan'] ?? null;
$loket = $_GET['loket'] ?? null;

if (!$id_layanan || !$loket) {
    echo json_encode(['success' => false, 'message' => 'ID layanan atau loket tidak lengkap']);
    exit;
}

// Cari antrian terakhir yang dipanggil untuk id_layanan tersebut
$sql = "SELECT * FROM antrian 
        WHERE id_layanan = ? AND status = 'dipanggil'
        ORDER BY waktu_panggil DESC LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_layanan);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

if ($row) {
    $id = $row['id'];
    $update = $conn->prepare("UPDATE antrian SET recall = 1 WHERE id = ?");
    $update->bind_param("i", $id);
    $update->execute();

    echo json_encode(['success' => true, 'message' => 'Recall berhasil disetel']);
} else {
    echo json_encode(['success' => false, 'message' => 'Tidak ditemukan antrian yang dipanggil']);
}
