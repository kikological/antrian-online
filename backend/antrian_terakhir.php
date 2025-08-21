<?php
require '../db/config.php';
header('Content-Type: application/json');

$query = "SELECT 
            a.nomor,
            a.loket,
            l.kode_layanan,
            l.nama_layanan
          FROM antrian a
          JOIN layanan l ON a.id_layanan = l.id
          WHERE a.status = 'dipanggil'
          AND a.waktu_panggil IS NOT NULL
          AND a.waktu_panggil = (
              SELECT MAX(waktu_panggil)
              FROM antrian
              WHERE id_layanan = a.id_layanan AND status = 'dipanggil'
          )
          GROUP BY a.id_layanan";

$result = $conn->query($query);
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = [
        'kode_layanan' => $row['kode_layanan'],
        'nama_layanan' => $row['nama_layanan'],
        'nomor' => $row['nomor'],
        'loket' => $row['loket'],
        'kode' => $row['kode_layanan']
    ];
}

echo json_encode($data);
