<?php
require '../db/config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['layanan'])) {
    $id_layanan = intval($_POST['layanan']);

    // Ambil nomor terakhir tanpa insert
    $stmt = mysqli_prepare($conn, "SELECT MAX(nomor) AS nomor FROM antrian WHERE id_layanan = ?");
    mysqli_stmt_bind_param($stmt, "i", $id_layanan);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $last_nomor);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    $next = $last_nomor + 1;

    // Ambil nama dan kode layanan
    $res = mysqli_query($conn, "SELECT nama_layanan, kode_layanan FROM layanan WHERE id = $id_layanan");
    $layananData = mysqli_fetch_assoc($res);

    $nama_layanan = $layananData['nama_layanan'] ?? 'Layanan';
    $kode_layanan = $layananData['kode_layanan'] ?? '';

    echo json_encode([
        "success" => true,
        "nomor" => $next,
        "kode" => $kode_layanan,
        "layanan" => $nama_layanan,
        "id_layanan" => $id_layanan // kirim juga ID layanan untuk disimpan nanti
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
}
