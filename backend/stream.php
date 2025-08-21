<?php
require '../db/config.php';

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Access-Control-Allow-Origin: *');

while (true) {
    $result = mysqli_query($conn, "
        SELECT 
            l.kode_layanan AS kode,
            a.nomor, 
            l.suara_mp3, 
            a.recall, 
            a.id
        FROM layanan l
        LEFT JOIN (
            SELECT a1.*
            FROM antrian a1
            INNER JOIN (
                SELECT id_layanan, MAX(waktu_panggil) AS max_waktu
                FROM antrian
                WHERE status = 'dipanggil' OR recall = 1
                GROUP BY id_layanan
            ) a2 ON a1.id_layanan = a2.id_layanan AND a1.waktu_panggil = a2.max_waktu
        ) a ON a.id_layanan = l.id
        ORDER BY l.id ASC
    ");

    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        if ($row['nomor']) {
            $data[] = [
                'kode' => $row['kode'],
                'nomor' => $row['nomor'],
                'loket_audio' => $row['suara_mp3'] ?? null,
                'recall' => (int)$row['recall'],
                'id' => (int)$row['id']
            ];
        }
    }

    echo "data: " . json_encode($data) . "\n\n";
    ob_flush();
    flush();
    sleep(1);
}
